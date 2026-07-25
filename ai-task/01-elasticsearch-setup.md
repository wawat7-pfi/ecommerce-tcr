# Task 1: Elasticsearch Setup & Initial Indexing

## Objective

Install dan konfigurasi ElasticPress plugin sebagai bridge antara WooCommerce dan Elasticsearch, lalu jalankan initial indexing untuk seluruh produk.

---

## Prerequisites

- [x] Elasticsearch 8.17 running di Docker (`wp_elasticsearch:9200`)
- [x] WordPress + WooCommerce aktif
- [ ] Laragon / Docker environment running

---

## Step-by-Step Implementation

### 1.1 Verifikasi Elasticsearch Container

Pastikan Elasticsearch berjalan dan accessible:

```bash
# Dari host machine
curl http://localhost:9200

# Expected response:
# {
#   "name" : "xxx",
#   "cluster_name" : "docker-cluster",
#   "version" : { "number" : "8.17.0" ... }
# }
```

```bash
# Cek health
curl http://localhost:9200/_cluster/health?pretty
```

> [!IMPORTANT]
> Jika Elasticsearch belum running, jalankan Docker Compose:
> ```bash
> cd c:/laragon/www/tcr-wordpress/docker
> docker compose up -d elasticsearch kibana
> ```

---

### 1.2 Install ElasticPress Plugin

#### Option A: Via WP Admin (Recommended)
1. Buka `https://tcr-wordpress.test/wp-admin/plugin-install.php`
2. Search: **"ElasticPress"** (by 10up)
3. Klik **Install Now** → **Activate**

#### Option B: Via WP-CLI (jika tersedia)
```bash
wp plugin install elasticpress --activate
```

#### Option C: Manual Download
1. Download dari https://wordpress.org/plugins/elasticpress/
2. Extract ke `wp-content/plugins/elasticpress/`
3. Activate via WP Admin

---

### 1.3 Konfigurasi ElasticPress

#### 1.3.1 Set Elasticsearch Host

Setelah plugin aktif, buka **ElasticPress → Settings**:

| Setting | Value |
|---------|-------|
| Elasticsearch Host | `http://localhost:9200` |
| Index Language | `English` (atau sesuaikan) |

> [!WARNING]
> **Host URL berbeda tergantung environment:**
> - **Dari Laragon (host machine)**: `http://localhost:9200`
> - **Dari Docker WordPress container**: `http://wp_elasticsearch:9200`
> 
> Karena WordPress local Anda berjalan via **Laragon** (bukan Docker), gunakan `http://localhost:9200`.
> 
> Jika nanti dipindah ke Docker, ubah ke `http://wp_elasticsearch:9200`.

#### 1.3.2 Alternatif: Set via wp-config.php

Tambahkan di `wp-config.php` agar tidak perlu setting dari admin:

```php
/** ElasticPress Configuration */
define( 'EP_HOST', 'http://localhost:9200' );
```

#### 1.3.3 Tambahkan MU-Plugin untuk Environment-Aware ES Host

Buat file `wp-content/mu-plugins/04-elasticpress-config.php`:

```php
<?php
/**
 * ElasticPress environment-aware configuration.
 * 
 * Detects whether WordPress is running inside Docker or on Laragon
 * and sets the appropriate Elasticsearch host URL.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_filter( 'ep_host', function ( $host ) {
    // If running inside Docker container, use internal network hostname
    if ( file_exists( '/.dockerenv' ) ) {
        return 'http://wp_elasticsearch:9200';
    }
    // Laragon / local development
    return 'http://localhost:9200';
} );
```

---

### 1.4 Enable WooCommerce Features

Buka **ElasticPress → Features** dan aktifkan:

| Feature | Status | Deskripsi |
|---------|--------|-----------|
| Post Search | ✅ Enable | Core search functionality |
| WooCommerce | ✅ Enable | Product search, filtering, sorting |
| Autosuggest | ✅ Enable | Live search autocomplete |
| Custom Search Results | ✅ Enable | Customize search result display |
| Did You Mean | ⬜ Optional | Typo correction suggestions |
| Protected Content | ⬜ Disable | Not needed for products |

> [!IMPORTANT]
> **WooCommerce feature** harus di-enable agar ElasticPress mengintercept `WP_Query` untuk produk dan mengarahkannya ke Elasticsearch.

---

### 1.5 Initial Sync / Indexing

#### Via WP Admin
1. Buka **ElasticPress → Sync**
2. Klik **"Start Sync"** (atau **"Index All"**)
3. Tunggu proses selesai — dengan 550 produk + 1,355 variasi, estimasi **2-5 menit**

#### Via WP-CLI (lebih cepat)
```bash
wp elasticpress index --setup --yes
```

#### Monitoring Progress
- Cek progress di halaman ElasticPress → Sync
- Atau via Kibana: `http://localhost:5601` → Dev Tools:

```json
GET /_cat/indices?v

// Expected: ada index baru seperti 'tcr-wordpress-post-1' dll
```

---

### 1.6 Verifikasi Indexing

#### 1.6.1 Cek Index di Elasticsearch

```bash
# Cek semua indices
curl http://localhost:9200/_cat/indices?v

# Cek jumlah dokumen di index
curl http://localhost:9200/_cat/count/*post*?v

# Search test
curl -X POST "http://localhost:9200/_search" -H "Content-Type: application/json" -d '{
  "query": {
    "match": {
      "post_title": "midi skirt"
    }
  },
  "size": 3
}'
```

#### 1.6.2 Cek di Kibana

1. Buka `http://localhost:5601`
2. Navigasi ke **Dev Tools**
3. Jalankan query:

```json
GET /_cat/indices?v
GET /tcr-wordpress-post-1/_count
GET /tcr-wordpress-post-1/_search
{
  "query": {
    "term": {
      "post_type.raw": "product"
    }
  },
  "size": 0
}
```

#### 1.6.3 Cek di WordPress

1. Buka halaman **Shop** (`/shop/`) — produk harus tetap tampil normal
2. Coba **search** produk — hasilnya harus lebih cepat dan relevan
3. Cek **ElasticPress → Health** — semua indikator harus hijau

---

### 1.7 Konfigurasi Auto-Sync

ElasticPress secara default akan **otomatis sync** ketika:
- Produk baru ditambahkan
- Produk di-update (harga, stok, judul, dll)
- Produk dihapus
- Variasi berubah

Pastikan hooks berikut aktif (sudah bawaan ElasticPress):
- `save_post` → sync individual post
- `delete_post` → remove from index
- `woocommerce_update_product` → sync product
- `woocommerce_product_set_stock` → sync stock changes

---

## Checklist Verifikasi

- [ ] Elasticsearch accessible di `http://localhost:9200`
- [ ] ElasticPress plugin terinstall dan aktif
- [ ] ES Host terkonfigurasi dengan benar
- [ ] WooCommerce feature di-enable
- [ ] Initial indexing selesai tanpa error
- [ ] Index berisi ~550 produk (cek via Kibana)
- [ ] Halaman Shop tetap tampil normal
- [ ] Search produk berfungsi
- [ ] Auto-sync berjalan ketika edit produk

---

## Troubleshooting

### ElasticPress tidak bisa connect ke Elasticsearch
```
Error: Elasticsearch connection failed
```
**Solusi**: 
- Pastikan Docker container `wp_elasticsearch` running: `docker ps | grep elasticsearch`
- Pastikan port 9200 tidak diblock firewall
- Coba akses langsung: `curl http://localhost:9200`

### Indexing gagal / timeout
**Solusi**:
- Naikkan `max_execution_time` di PHP config
- Gunakan WP-CLI untuk indexing (lebih stabil)
- Cek memory limit Elasticsearch (`ES_JAVA_OPTS=-Xms512m -Xmx512m`)

### Produk tidak muncul setelah indexing
**Solusi**:
- Re-index: ElasticPress → Sync → Delete All Data and Start Fresh
- Cek apakah produk status = `publish`
- Cek ElasticPress → Features → WooCommerce harus enabled

---

## File yang Dibuat/Dimodifikasi

| File | Aksi | Deskripsi |
|------|------|-----------|
| `wp-content/mu-plugins/04-elasticpress-config.php` | **NEW** | Environment-aware ES host config |
| `wp-config.php` | **MODIFY** (optional) | Add `EP_HOST` constant |
| `wp-content/plugins/elasticpress/` | **NEW** (install) | ElasticPress plugin |

---

> [!TIP]
> Setelah Task 1 selesai dan semua checklist hijau, lanjut ke **[Task 2: Search Integration](file:///c:/laragon/www/tcr-wordpress/ai-task/02-search-integration.md)**.
