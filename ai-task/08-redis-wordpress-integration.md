# Task 2: Redis WordPress Integration

## Objective

Install dan konfigurasi **Redis Object Cache** plugin di WordPress, setup `wp-config.php` constants, deploy `object-cache.php` drop-in, dan verifikasi bahwa WordPress Object Cache sudah menggunakan Redis.

---

## Prerequisites

- [x] Task 1 selesai (Redis server running + PHP Redis extension loaded)
- [x] Redis server accessible dari PHP (`redis-cli ping` → `PONG`)
- [x] MU-Plugin `08-redis-config.php` sudah dibuat
- [ ] WordPress admin accessible

---

## Step-by-Step Implementation

### 2.1 Konfigurasi wp-config.php

Tambahkan Redis constants di `wp-config.php` **sebelum** baris `/* That's all, stop editing! */`:

> [!IMPORTANT]
> Constants **harus** didefinisikan di `wp-config.php` (bukan hanya di MU-Plugin) karena `object-cache.php` drop-in di-load **sebelum** MU-Plugins. Drop-in adalah file pertama yang di-load oleh WordPress setelah `wp-config.php`.

```php
// === Redis Object Cache Configuration ===
// Auto-detect environment: Docker vs Laragon
if ( file_exists( '/.dockerenv' ) || getenv( 'WORDPRESS_DB_HOST' ) ) {
    define( 'WP_REDIS_HOST', 'wp_redis' );       // Docker container hostname
} else {
    define( 'WP_REDIS_HOST', '127.0.0.1' );      // Laragon / local
}
define( 'WP_REDIS_PORT', 6379 );
define( 'WP_REDIS_DATABASE', 0 );
define( 'WP_REDIS_TIMEOUT', 1 );                  // 1 second connection timeout
define( 'WP_REDIS_READ_TIMEOUT', 1 );             // 1 second read timeout
define( 'WP_REDIS_PREFIX', 'cnp_' );              // Match database table prefix
define( 'WP_REDIS_DISABLE_BANNERS', true );
define( 'WP_REDIS_MAXTTL', 86400 );               // Max TTL: 24 hours

// Enable WordPress Object Cache
define( 'WP_CACHE', true );
```

#### Load Order WordPress (penting dipahami):

```
1. wp-config.php          ← Redis constants defined here
2. wp-settings.php
3. object-cache.php       ← Redis drop-in, needs constants from step 1
4. mu-plugins/*.php       ← MU-Plugins (08-redis-config.php sebagai fallback)
5. plugins/*.php          ← Regular plugins (Redis Object Cache plugin)
6. theme functions.php    ← Theme
```

---

### 2.2 Install Redis Object Cache Plugin

#### 2.2.1 Download Plugin

```bash
# Via WP-CLI (jika available)
wp plugin install redis-cache --activate

# Atau manual download
# 1. Download dari https://wordpress.org/plugins/redis-cache/
# 2. Extract ke wp-content/plugins/redis-cache/
# 3. Aktifkan via WP Admin → Plugins
```

#### 2.2.2 Alternatif: Download via PHP Script

Jika WP-CLI tidak tersedia, gunakan script:

```php
<?php
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

$plugin_url = 'https://downloads.wordpress.org/plugin/redis-cache.latest-stable.zip';
$zip_path   = WP_CONTENT_DIR . '/plugins/redis-cache.zip';

// Download
$response = wp_remote_get($plugin_url, array('timeout' => 120));
if (is_wp_error($response)) {
    die('Download failed: ' . $response->get_error_message());
}

file_put_contents($zip_path, wp_remote_retrieve_body($response));

// Extract
WP_Filesystem();
$result = unzip_file($zip_path, WP_CONTENT_DIR . '/plugins/');
unlink($zip_path);

if (is_wp_error($result)) {
    die('Extract failed: ' . $result->get_error_message());
}

// Activate
activate_plugin('redis-cache/redis-cache.php');
echo "Redis Object Cache plugin installed and activated!\n";
```

---

### 2.3 Enable Object Cache Drop-in

Setelah plugin aktif, deploy `object-cache.php` drop-in:

#### 2.3.1 Via WP Admin

1. Buka **WP Admin** → **Settings** → **Redis**
2. Klik tombol **"Enable Object Cache"**
3. Status harus berubah menjadi **"Connected"**

#### 2.3.2 Via PHP Script (Alternatif)

```php
<?php
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

// Copy drop-in file
$source = WP_CONTENT_DIR . '/plugins/redis-cache/includes/object-cache.php';
$dest   = WP_CONTENT_DIR . '/object-cache.php';

if (file_exists($source)) {
    copy($source, $dest);
    echo "object-cache.php drop-in deployed to wp-content/\n";
} else {
    echo "ERROR: Source file not found at {$source}\n";
}
```

---

### 2.4 Verifikasi Redis Connection

#### 2.4.1 Via WP Admin

1. Buka **WP Admin** → **Settings** → **Redis**
2. Harus menampilkan:
   - **Status**: Connected
   - **Client**: PhpRedis (PECL)
   - **Host**: `127.0.0.1` (Laragon) atau `wp_redis` (Docker)
   - **Port**: 6379
   - **Database**: 0
   - **Key Prefix**: `cnp_`

#### 2.4.2 Via PHP Script

```php
<?php
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

echo "=== Redis Object Cache Status ===\n";
echo "WP_CACHE: " . (defined('WP_CACHE') && WP_CACHE ? 'ENABLED' : 'DISABLED') . "\n";
echo "WP_REDIS_HOST: " . (defined('WP_REDIS_HOST') ? WP_REDIS_HOST : 'NOT SET') . "\n";
echo "WP_REDIS_PORT: " . (defined('WP_REDIS_PORT') ? WP_REDIS_PORT : 'NOT SET') . "\n";

// Check if Redis is used as object cache backend
global $wp_object_cache;
$cache_class = get_class($wp_object_cache);
echo "Object Cache Backend: {$cache_class}\n";

// Test Redis connection directly
if (class_exists('Redis')) {
    $redis = new Redis();
    try {
        $redis->connect(WP_REDIS_HOST, WP_REDIS_PORT, 1);
        echo "Redis Connection: SUCCESS (PONG = " . $redis->ping() . ")\n";
        echo "Redis Server Info:\n";
        $info = $redis->info('server');
        echo "  - Version: " . $info['redis_version'] . "\n";
        echo "  - Uptime: " . $info['uptime_in_seconds'] . " seconds\n";
        
        $info_mem = $redis->info('memory');
        echo "  - Used Memory: " . round($info_mem['used_memory'] / 1024 / 1024, 2) . " MB\n";
        
        $info_stats = $redis->info('stats');
        echo "  - Total Keys: " . $redis->dbSize() . "\n";
        echo "  - Cache Hits: " . ($info_stats['keyspace_hits'] ?? 0) . "\n";
        echo "  - Cache Misses: " . ($info_stats['keyspace_misses'] ?? 0) . "\n";
        
        $redis->close();
    } catch (RedisException $e) {
        echo "Redis Connection: FAILED (" . $e->getMessage() . ")\n";
    }
} else {
    echo "PHP Redis Extension: NOT LOADED\n";
}

// Test wp_cache functions
wp_cache_set('canopy_test_key', 'hello_redis', 'canopy', 60);
$result = wp_cache_get('canopy_test_key', 'canopy');
echo "\nwp_cache_set/get test: " . ($result === 'hello_redis' ? 'PASS ✓' : 'FAIL ✗') . "\n";
wp_cache_delete('canopy_test_key', 'canopy');
```

---

### 2.5 Konfigurasi Cache Groups

Buat / update file `wp-content/mu-plugins/08-redis-config.php` untuk mengoptimasi cache groups:

```php
<?php
/**
 * Plugin Name: Canopy Redis Configuration
 * Description: Redis Object Cache configuration and optimization.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Configure non-persistent cache groups.
 * 
 * These groups are NOT stored in Redis because they are:
 * - Request-specific (counts, comment feeds)
 * - Too volatile (should not persist across requests)
 */
add_action( 'init', function () {
    wp_cache_add_non_persistent_groups( array(
        'counts',
        'plugins',
        'themes',
    ) );
}, 1 );

/**
 * Flush Redis cache when critical WooCommerce events occur.
 */
add_action( 'woocommerce_update_product', function ( $product_id ) {
    // Flush product-related transients
    wp_cache_delete( 'canopy_products_page_1_per_page_12', 'canopy_rest' );
    
    // WooCommerce will handle its own transient cleanup
}, 20 );

/**
 * Add Redis connection info to admin bar for debugging.
 */
add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    global $wp_object_cache;
    $is_redis = ( isset( $wp_object_cache->redis ) || 
                  ( is_object( $wp_object_cache ) && method_exists( $wp_object_cache, 'redis_status' ) ) );
    
    $status = $is_redis ? '🟢 Redis' : '🔴 No Cache';

    $wp_admin_bar->add_node( array(
        'id'    => 'canopy-redis-status',
        'title' => $status,
        'href'  => admin_url( 'options-general.php?page=redis-cache' ),
    ) );
}, 100 );
```

---

### 2.6 Optimasi wp-config.php (Performance Extras)

Tambahkan juga optimasi tambahan yang bekerja sinergis dengan Redis:

```php
// === Performance Optimizations ===

// Limit WordPress post revisions (reduces DB bloat)
define( 'WP_POST_REVISIONS', 5 );

// Increase autosave interval (reduces DB writes)
define( 'AUTOSAVE_INTERVAL', 120 ); // 2 minutes

// Disable WordPress file editor (security + reduces admin overhead)
define( 'DISALLOW_FILE_EDIT', true );
```

---

## Verifikasi Checklist

- [ ] `wp-config.php` constants ditambahkan (WP_REDIS_HOST, WP_CACHE, dll)
- [ ] Redis Object Cache plugin terinstall dan aktif
- [ ] `object-cache.php` drop-in sudah ada di `wp-content/`
  ```bash
  ls wp-content/object-cache.php
  ```
- [ ] WP Admin → Settings → Redis menampilkan **"Connected"**
- [ ] `wp_cache_set/get` test berhasil (`PASS ✓`)
- [ ] MU-Plugin `08-redis-config.php` berfungsi
- [ ] Redis mulai menyimpan keys
  ```bash
  redis-cli dbsize
  # Expected: > 0
  ```
- [ ] Admin bar menampilkan status Redis (🟢 Redis)

---

## Troubleshooting

### Status "Not Connected" di WP Admin

```bash
# 1. Cek apakah Redis server running
redis-cli ping

# 2. Cek WP_REDIS_HOST di wp-config.php
grep WP_REDIS wp-config.php

# 3. Cek object-cache.php drop-in exists
ls -la wp-content/object-cache.php

# 4. Cek PHP Redis extension
php -m | grep redis
```

### "Call to undefined method Redis::connect()"

```
PHP Redis extension tidak ter-load. Cek:
1. php.ini: extension=redis
2. Restart web server (Apache / Laragon)
3. Pastikan php_redis.dll ada di ext/ folder
```

### "Connection refused to 127.0.0.1:6379"

```
Redis server tidak running. Cek:
1. Laragon: Menu → Redis → Start Redis
2. Docker: docker compose up -d redis
3. Port 6379: netstat -an | findstr 6379
```

---

## Output Task Ini

Setelah task ini selesai, kita akan memiliki:

1. ✅ WordPress Object Cache menggunakan Redis sebagai backend
2. ✅ `object-cache.php` drop-in aktif di `wp-content/`
3. ✅ WP Admin menampilkan Redis status "Connected"
4. ✅ Cache groups dikonfigurasi optimal
5. ✅ Siap untuk Task 3: WooCommerce Optimization

---

> [!TIP]
> Lanjut ke **Task 3 (09-redis-woocommerce-optimization.md)** untuk optimasi WooCommerce-specific caching (sessions, cart, fragments).
