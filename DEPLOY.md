# 🚀 Guide Deployment Docker — The Canopy Room

Panduan komprehensif ini menjelaskan tata cara deployment aplikasi **The Canopy Room** (WordPress + WooCommerce + Redis FPC + MariaDB 11) ke server VPS (Ubuntu / Debian) menggunakan Docker.

---

## 📋 1. Persyaratan Server (Prerequisites)

- **OS**: Ubuntu 22.04 / 24.04 LTS (atau Debian 12)
- **Spesifikasi VPS Minimum**: 2 vCPU, 2 GB RAM (Direkomendasikan 4 GB RAM)
- **Software**: Docker & Docker Compose Plugin (v2)
- **Domain & DNS**: A Record domain (misal: `thecanopy-room.com` & `www.thecanopy-room.com`) sudah di-pointing ke IP Public VPS.

---

## 🛠️ 2. Persiapan Server & Instalasi Docker

Jika Docker belum ter-install di VPS, jalankan perintah berikut:

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install Docker & Docker Compose
curl -fsSL https://get.docker.com | sh

# Masukkan user ke group docker (agar tidak perlu sudo)
sudo usermod -aG docker $USER
newgrp docker
```

Buat network eksternal Docker untuk menghubungkan container:

```bash
docker network create wordpress_default
```

---

## 📁 3. Clone Repository & Setup Environment

1. Clone project ke VPS:
   ```bash
   git clone <REPOSITORY_URL> /var/www/tcr-wordpress
   cd /var/www/tcr-wordpress
   ```

2. Buat file `.env` di dalam folder `docker/`:
   ```bash
   cp docker/.env.example docker/.env  # Atau buat manual docker/.env
   nano docker/.env
   ```

3. Isi `docker/.env` dengan kredensial database yang aman:
   ```env
   DB_ROOT_PASSWORD=GantiPasswordRootYangKuat123!
   DB_PASSWORD=GantiPasswordUserDBYangKuat123!
   ```

4. Atur permission folder agar WordPress (user `www-data`) dapat mengunggah media:
   ```bash
   sudo chown -R 33:33 /var/www/tcr-wordpress
   sudo chmod -R 755 /var/www/tcr-wordpress
   ```
   *(Catatan: `33:33` adalah UID:GID untuk user `www-data` di dalam container Linux)*

---

## 🐳 4. Menjalankan Container Docker

Jalankan container secara berurutan:

```bash
# 1. Menjalankan MariaDB Database
docker compose -f docker/docker-compose-mariadb.yml up -d

# 2. Cek status database (tunggu hingga status "healthy")
docker ps

# 3. Menjalankan WordPress App & Redis Cache
docker compose -f docker/docker-compose.yml up -d --build
```

---

## 🗄️ 5. Impor Database & Penyesuaian Domain

Jika Anda melakukan migrasi database dari server lama/lokal ke VPS:

1. Copy file dump database (`wordpress.sql`) ke VPS.
2. Impor database ke container `wp_db`:
   ```bash
   docker exec -i wp_db mysql -u wp_user -p"$(grep DB_PASSWORD docker/.env | cut -d'=' -f2)" wordpress < wordpress.sql
   ```
3. Jika domain berubah, replace URL di database menggunakan WP-CLI:
   ```bash
   docker exec -it wp_app wp option update home "https://thecanopy-room.com" --allow-root
   docker exec -it wp_app wp option update siteurl "https://thecanopy-room.com" --allow-root
   docker exec -it wp_app wp search-replace "http://tcr-wordpress.test" "https://thecanopy-room.com" --allow-root
   ```

---

## 🔒 6. Setup Nginx Reverse Proxy & SSL Certbot (HTTPS)

Install Nginx & Certbot di Host VPS untuk mengurus SSL:

```bash
sudo apt install nginx certbot python3-certbot-nginx -y
```

Buat konfig Nginx di `/etc/nginx/sites-available/thecanopyroom`:

```nginx
server {
    server_name thecanopy-room.com www.thecanopy-room.com;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        client_max_body_size 256M;
    }
}
```

Aktifkan konfigurasi Nginx dan dapatkan Sertifikat SSL Gratis dari Let's Encrypt:

```bash
sudo ln -s /etc/nginx/sites-available/thecanopyroom /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# Install SSL Gratis Let's Encrypt
sudo certbot --nginx -d thecanopy-room.com -d www.thecanopy-room.com
```

---

## ⚡ 7. Verifikasi Full-Page Cache (Redis FPC)

1. Bersihkan cache Redis setelah deploy:
   ```bash
   docker exec -it wp_redis redis-cli FLUSHALL
   ```

2. Tes kecepatan & header FPC menggunakan `curl`:
   ```bash
   curl -I https://thecanopy-room.com/
   ```
   **Output Sukses**:
   ```http
   HTTP/2 200
   x-canopy-fpc: HIT (guest)
   x-canopy-fpc-time: 0.038s
   ```

---

## 🛠️ 8. Command Penting Maintenance

- **Melihat Log Aplikasi**:
  ```bash
  docker logs -f wp_app
  ```
- **Melihat Log Database**:
  ```bash
  docker logs -f wp_db
  ```
- **Restart Seluruh Service**:
  ```bash
  docker compose -f docker/docker-compose.yml restart
  ```
- **Backup Database**:
  ```bash
  docker exec wp_db mysqldump -u wp_user -p"$(grep DB_PASSWORD docker/.env | cut -d'=' -f2)" wordpress > backup_$(date +%F).sql
  ```
