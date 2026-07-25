# Task 1: Redis Infrastructure Setup

## Objective

Install dan konfigurasi Redis server di kedua environment (Laragon lokal + Docker), serta install PHP Redis extension agar PHP bisa berkomunikasi dengan Redis.

---

## Prerequisites

- [x] Docker Compose environment running (`wp_app`, `wp_db`, `wp_elasticsearch`)
- [x] Laragon PHP 8.3 running
- [ ] Docker Desktop running

---

## Step-by-Step Implementation

### 1.1 Setup Redis Server di Docker

#### 1.1.1 Update `docker-compose.yml`

Tambahkan service Redis ke file `docker/docker-compose.yml`:

```yaml
  redis:
    image: redis:7-alpine
    container_name: wp_redis
    restart: unless-stopped
    command: >
      redis-server
      --maxmemory 256mb
      --maxmemory-policy allkeys-lru
      --save 60 1000
      --save 300 100
      --loglevel warning
      --tcp-keepalive 60
      --timeout 300
    volumes:
      - redis_data:/data
    ports:
      - "6379:6379"
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5
    networks:
      - wp_network
```

Tambahkan juga volume `redis_data` ke section `volumes`:

```yaml
volumes:
  es_data:
  redis_data:
```

Tambahkan `depends_on` Redis ke service `wordpress`:

```yaml
  wordpress:
    # ... existing config ...
    depends_on:
      redis:
        condition: service_healthy
```

> [!IMPORTANT]
> **Penjelasan konfigurasi Redis:**
> - `maxmemory 256mb` — Batasi penggunaan RAM Redis agar tidak memakan semua memory server
> - `maxmemory-policy allkeys-lru` — Saat memori penuh, hapus key yang paling lama tidak diakses (Least Recently Used)
> - `save 60 1000` — Simpan snapshot ke disk setiap 60 detik jika ada ≥1000 key berubah
> - `save 300 100` — Simpan snapshot ke disk setiap 300 detik jika ada ≥100 key berubah
> - `tcp-keepalive 60` — Kirim TCP keepalive setiap 60 detik untuk deteksi koneksi mati
> - `timeout 300` — Tutup koneksi idle setelah 5 menit

#### 1.1.2 Start Redis Container

```bash
cd c:/laragon/www/tcr-wordpress/docker
docker compose up -d redis
```

#### 1.1.3 Verifikasi Redis Container

```bash
# Cek container running
docker ps | grep wp_redis

# Test koneksi dari host
docker exec wp_redis redis-cli ping
# Expected: PONG

# Cek info server
docker exec wp_redis redis-cli info server | head -20

# Test dari WordPress container
docker exec wp_app bash -c "apt-get update && apt-get install -y redis-tools && redis-cli -h wp_redis ping"
# Expected: PONG
```

---

### 1.2 Install PHP Redis Extension di Docker (WordPress Container)

WordPress container (`wordpress:php8.3-apache`) belum memiliki PHP Redis extension. Kita perlu membuat custom Dockerfile.

#### 1.2.1 Buat Custom Dockerfile

Buat file `docker/Dockerfile.wordpress`:

```dockerfile
FROM wordpress:php8.3-apache

# Install PHP Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Verify installation
RUN php -m | grep redis
```

#### 1.2.2 Update docker-compose.yml untuk Build Custom Image

Ubah service `wordpress` di `docker-compose.yml`:

```yaml
  wordpress:
    build:
      context: .
      dockerfile: Dockerfile.wordpress
    container_name: wp_app
    # ... rest of config tetap sama ...
```

> [!WARNING]
> Setelah mengubah Dockerfile, perlu rebuild image:
> ```bash
> cd c:/laragon/www/tcr-wordpress/docker
> docker compose build wordpress
> docker compose up -d wordpress
> ```

#### 1.2.3 Verifikasi PHP Redis Extension di Docker

```bash
docker exec wp_app php -m | grep redis
# Expected: redis

docker exec wp_app php -r "echo phpversion('redis');"
# Expected: 6.x.x
```

---

### 1.3 Setup Redis di Laragon (Local Development)

#### 1.3.1 Enable Redis di Laragon

Laragon sudah menyertakan Redis server secara built-in:

1. Buka **Laragon** → Klik kanan tray icon → **Preferences**
2. Tab **Services & Ports** → Centang **Redis**
3. Atau langsung: **Menu** → **Redis** → **Start Redis**

Alternatif: Redis otomatis start saat Laragon dinyalakan jika sudah di-enable.

#### 1.3.2 Enable PHP Redis Extension di Laragon

1. Buka **Laragon** → **Menu** → **PHP** → **Extensions**
2. Cari dan centang **redis**
3. Restart Laragon

Atau manual: Edit file `php.ini` Laragon:

```ini
; Lokasi: C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.ini
; Uncomment atau tambahkan:
extension=redis
```

#### 1.3.3 Verifikasi PHP Redis Extension di Laragon

```bash
# Dari terminal Laragon
php -m | grep redis
# Expected: redis

php -r "echo phpversion('redis');"
# Expected: 5.x.x atau 6.x.x

# Test koneksi ke Redis server
php -r "$r = new Redis(); $r->connect('127.0.0.1', 6379); echo $r->ping();"
# Expected: +PONG atau true
```

> [!TIP]
> Jika `php_redis.dll` belum ada di folder `ext`, download dari:
> - https://pecl.php.net/package/redis
> - Pilih versi Windows DLL yang sesuai (PHP 8.3, Thread Safe, vs16, x64)
> - Copy `php_redis.dll` ke `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\ext\`

---

### 1.4 Buat Environment-Aware Redis Config (MU-Plugin)

Buat file `wp-content/mu-plugins/08-redis-config.php`:

```php
<?php
/**
 * Plugin Name: Canopy Redis Configuration
 * Description: Environment-aware Redis host configuration for Laragon and Docker.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Auto-detect Redis host based on environment.
 *
 * Laragon (local): 127.0.0.1:6379
 * Docker:          wp_redis:6379
 */
function canopy_get_redis_host() {
    // Docker environment check
    if ( file_exists( '/.dockerenv' ) || getenv( 'WORDPRESS_DB_HOST' ) ) {
        return 'wp_redis';
    }

    // Default: Laragon / local
    return '127.0.0.1';
}

/**
 * Define Redis constants if not already defined.
 * These must be defined BEFORE Redis Object Cache plugin loads.
 */
if ( ! defined( 'WP_REDIS_HOST' ) ) {
    define( 'WP_REDIS_HOST', canopy_get_redis_host() );
}

if ( ! defined( 'WP_REDIS_PORT' ) ) {
    define( 'WP_REDIS_PORT', 6379 );
}

if ( ! defined( 'WP_REDIS_DATABASE' ) ) {
    define( 'WP_REDIS_DATABASE', 0 ); // Use database 0 for WordPress
}

if ( ! defined( 'WP_REDIS_TIMEOUT' ) ) {
    define( 'WP_REDIS_TIMEOUT', 1 ); // 1 second connection timeout
}

if ( ! defined( 'WP_REDIS_READ_TIMEOUT' ) ) {
    define( 'WP_REDIS_READ_TIMEOUT', 1 ); // 1 second read timeout
}

// Prefix all Redis keys to avoid collision with other apps sharing same Redis
if ( ! defined( 'WP_REDIS_PREFIX' ) ) {
    define( 'WP_REDIS_PREFIX', 'cnp_' );
}

// Disable Redis banners in WP Admin
if ( ! defined( 'WP_REDIS_DISABLE_BANNERS' ) ) {
    define( 'WP_REDIS_DISABLE_BANNERS', true );
}
```

> [!IMPORTANT]
> File ini di-load sangat awal oleh WordPress (MU-Plugins load sebelum regular plugins). Namun, karena `WP_REDIS_HOST` idealnya didefinisikan di `wp-config.php` (sebelum semua plugin), kita juga perlu menambahkan konstanta di sana sebagai fallback. Lihat Task 2 untuk detail konfigurasi `wp-config.php`.

---

## Verifikasi Checklist

- [ ] Redis container (`wp_redis`) running di Docker
  ```bash
  docker exec wp_redis redis-cli ping
  # Expected: PONG
  ```

- [ ] Redis server running di Laragon
  ```bash
  redis-cli ping
  # Expected: PONG
  ```

- [ ] PHP Redis extension loaded di Docker
  ```bash
  docker exec wp_app php -m | grep redis
  # Expected: redis
  ```

- [ ] PHP Redis extension loaded di Laragon
  ```bash
  php -m | grep redis
  # Expected: redis
  ```

- [ ] Koneksi PHP → Redis berhasil
  ```bash
  php -r "$r = new Redis(); $r->connect('127.0.0.1', 6379); echo $r->ping();"
  # Expected: +PONG
  ```

- [ ] MU-Plugin `08-redis-config.php` created
- [ ] Tidak ada error di WordPress setelah aktivasi

---

## Troubleshooting

### Redis container tidak start

```bash
# Cek logs
docker logs wp_redis

# Kemungkinan: port 6379 sudah dipakai
# Solusi: Matikan Redis Laragon dulu, atau ubah port mapping di docker-compose.yml
```

### PHP Redis extension tidak mau load

```bash
# Cek apakah DLL ada
ls C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\ext\php_redis.dll

# Cek php.ini path
php --ini

# Cek error log
# C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\logs\php_error.log
```

### Redis connection refused

```bash
# Cek port listening
netstat -an | findstr 6379

# Cek firewall Windows
# Windows Defender Firewall → Inbound Rules → Allow TCP 6379
```

---

## Output Task Ini

Setelah task ini selesai, kita akan memiliki:

1. ✅ Redis 7 server running (baik di Docker maupun Laragon)
2. ✅ PHP Redis extension loaded dan functional
3. ✅ PHP bisa connect dan read/write ke Redis
4. ✅ MU-Plugin environment-aware configuration
5. ✅ Siap untuk Task 2: WordPress Integration

---

> [!TIP]
> Lanjut ke **Task 2 (08-redis-wordpress-integration.md)** untuk menginstall Redis Object Cache plugin dan mengaktifkan caching di WordPress.
