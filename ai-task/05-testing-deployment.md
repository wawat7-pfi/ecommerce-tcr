# Task 5: Testing, Benchmarking & Deployment

## Objective

Memastikan seluruh implementasi Elasticsearch berjalan dengan benar, stabil, dan siap deploy ke production. Meliputi: functional testing, performance benchmarking, regression testing, dan deployment checklist.

---

## Prerequisites

- [x] Task 1–4 selesai
- [x] Semua fitur berfungsi di local environment
- [ ] Akses ke production server

---

## Step-by-Step Implementation

### 5.1 Functional Testing

#### 5.1.1 Search Functionality

| # | Test Case | Expected Result | ✅/❌ |
|---|-----------|----------------|-------|
| 1 | Search "midi skirt" | Produk dengan "midi skirt" muncul di atas | |
| 2 | Search "s-m" (size term) | Produk dengan size S-M muncul | |
| 3 | Search typo "middi skirt" | Masih menampilkan hasil relevan | |
| 4 | Search kosong | Tampil semua produk | |
| 5 | Search produk tidak ada | Pesan "No products found" | |
| 6 | Autosuggest typing "pow" | Dropdown muncul "Power Play Midi Skirt" | |

#### 5.1.2 Shop Page Loading

| # | Test Case | Expected Result | ✅/❌ |
|---|-----------|----------------|-------|
| 1 | Buka /shop/ | Produk tampil (server-rendered initial) | |
| 2 | Klik page 2 | Produk berubah via AJAX, halaman tidak reload | |
| 3 | Ubah sorting ke "Price: Low to High" | Produk re-sort via AJAX | |
| 4 | Ubah sorting ke "Newest" | Produk terbaru muncul duluan | |
| 5 | Buka /product-category/bottom/ | Produk kategori Bottom tampil | |

#### 5.1.3 Filter Functionality

| # | Test Case | Expected Result | ✅/❌ |
|---|-----------|----------------|-------|
| 1 | Filter by Size: S-M | Hanya produk size S-M tampil | |
| 2 | Filter by Color | Hanya produk color tertentu tampil | |
| 3 | Filter by Price Range | Produk dalam range harga tampil | |
| 4 | Multiple filters (Size + Color) | Intersection filter berfungsi | |
| 5 | Clear all filters | Semua produk tampil kembali | |

#### 5.1.4 Edge Cases

| # | Test Case | Expected Result | ✅/❌ |
|---|-----------|----------------|-------|
| 1 | Matikan Elasticsearch container | Fallback ke MariaDB, produk tetap tampil | |
| 2 | Buka shop dengan JavaScript disabled | Server-rendered products tampil (SSR) | |
| 3 | Buka shop di mobile | Layout responsive, filter accessible | |
| 4 | Buka 2 tab bersamaan dengan filter berbeda | Masing-masing tab independent | |
| 5 | Edit produk di admin → cek frontend | Produk ter-update otomatis di ES | |

---

### 5.2 Performance Benchmarking

#### 5.2.1 Buat Benchmark Script

**File: `ai-task/benchmark.sh`** (jalankan dari host machine)

```bash
#!/bin/bash
# Canopy Room — Elasticsearch Performance Benchmark
# Run: bash ai-task/benchmark.sh

BASE_URL="https://tcr-wordpress.test"
REST_URL="${BASE_URL}/wp-json/canopy/v1/products"

echo "========================================="
echo "  Canopy Room Performance Benchmark"
echo "========================================="
echo ""

# Test 1: Shop page TTFB
echo "--- Test 1: Shop Page TTFB ---"
for i in 1 2 3; do
    curl -o /dev/null -s -w "Run $i: TTFB=%{time_starttransfer}s Total=%{time_total}s\n" \
        -k "${BASE_URL}/shop/"
done
echo ""

# Test 2: REST API response time
echo "--- Test 2: REST API Response Time ---"
for i in 1 2 3; do
    curl -o /dev/null -s -w "Run $i: TTFB=%{time_starttransfer}s Total=%{time_total}s\n" \
        -k "${REST_URL}?per_page=12&page=1"
done
echo ""

# Test 3: REST API with search
echo "--- Test 3: REST API Search ---"
for i in 1 2 3; do
    curl -o /dev/null -s -w "Run $i: TTFB=%{time_starttransfer}s Total=%{time_total}s\n" \
        -k "${REST_URL}?search=midi+skirt&per_page=12"
done
echo ""

# Test 4: REST API with filters
echo "--- Test 4: REST API with Filters ---"
for i in 1 2 3; do
    curl -o /dev/null -s -w "Run $i: TTFB=%{time_starttransfer}s Total=%{time_total}s\n" \
        -k "${REST_URL}?attribute_pa_size=s-m&per_page=12"
done
echo ""

# Test 5: Elasticsearch direct query
echo "--- Test 5: Elasticsearch Direct Query ---"
for i in 1 2 3; do
    curl -o /dev/null -s -w "Run $i: TTFB=%{time_starttransfer}s Total=%{time_total}s\n" \
        -X POST "http://localhost:9200/_search" \
        -H "Content-Type: application/json" \
        -d '{"query":{"match":{"post_title":"midi skirt"}},"size":12}'
done
echo ""

echo "========================================="
echo "  Benchmark Complete!"
echo "========================================="
```

#### 5.2.2 Performance Targets

| Metric | Target | Acceptable |
|--------|--------|------------|
| Shop page TTFB | < 300ms | < 500ms |
| REST API response | < 150ms | < 300ms |
| REST API + search | < 200ms | < 400ms |
| REST API + filters | < 200ms | < 400ms |
| ES direct query | < 30ms | < 50ms |
| Full page load (LCP) | < 2.0s | < 3.0s |
| Total SQL queries | < 30 | < 50 |
| PHP memory peak | < 128MB | < 200MB |

---

### 5.3 Regression Testing

Pastikan fitur-fitur WooCommerce yang ada tidak rusak:

| # | Area | Test | ✅/❌ |
|---|------|------|-------|
| 1 | Cart | Tambah produk ke cart dari shop page | |
| 2 | Cart | Tambah produk variabel (pilih size dulu) | |
| 3 | Wishlist | Tambah/hapus dari wishlist | |
| 4 | Single Product | Halaman produk single tampil normal | |
| 5 | Checkout | Checkout flow berjalan normal | |
| 6 | My Account | Login, register berfungsi | |
| 7 | Admin | Edit produk → cek sync ke ES | |
| 8 | Admin | Tambah produk baru → muncul di shop | |
| 9 | Admin | Hapus produk → hilang dari shop | |
| 10 | Mobile | Semua test di atas pada mobile viewport | |

---

### 5.4 Pre-Deployment Checklist

#### 5.4.1 Local Environment

- [ ] Semua functional tests passed
- [ ] Performance benchmarks met
- [ ] No PHP errors/warnings di `debug.log`
- [ ] No JavaScript console errors
- [ ] Regression tests passed
- [ ] Code reviewed

#### 5.4.2 Code Changes Summary

| File | Type | Deskripsi |
|------|------|-----------|
| `wp-content/mu-plugins/04-elasticpress-config.php` | MU-Plugin | ES host config (environment-aware) |
| `wp-content/mu-plugins/05-canopy-es-search.php` | MU-Plugin | Search enhancements, faceted filter |
| `wp-content/mu-plugins/06-canopy-ajax-products.php` | MU-Plugin | REST API + AJAX product loading |
| `wp-content/mu-plugins/07-canopy-es-tuning.php` | MU-Plugin | ES index optimization |
| `wp-content/themes/canopy-child/assets/js/ajax-products.js` | Theme JS | Frontend AJAX controller |
| `wp-content/plugins/elasticpress/` | Plugin | ElasticPress (install via WP Admin) |

---

### 5.5 Production Deployment Plan

#### Phase 1: Prepare Production

```bash
# 1. Pastikan Elasticsearch running di production Docker
docker compose -f docker-compose.yml up -d elasticsearch kibana

# 2. Verify ES accessible
curl http://wp_elasticsearch:9200/_cluster/health?pretty
```

#### Phase 2: Deploy Code

```bash
# 1. Copy mu-plugins ke production
scp wp-content/mu-plugins/04-elasticpress-config.php production:/var/www/html/wp-content/mu-plugins/
scp wp-content/mu-plugins/05-canopy-es-search.php production:/var/www/html/wp-content/mu-plugins/
scp wp-content/mu-plugins/06-canopy-ajax-products.php production:/var/www/html/wp-content/mu-plugins/
scp wp-content/mu-plugins/07-canopy-es-tuning.php production:/var/www/html/wp-content/mu-plugins/

# 2. Copy theme assets
scp -r wp-content/themes/canopy-child/assets/ production:/var/www/html/wp-content/themes/canopy-child/

# 3. Install ElasticPress di production
# Via WP Admin → Plugins → Add New → ElasticPress → Install → Activate
```

#### Phase 3: Initial Indexing (Production)

```bash
# Via WP-CLI (recommended untuk production)
wp elasticpress index --setup --yes --network-wide

# Monitor progress
wp elasticpress stats
```

> [!CAUTION]
> **Initial indexing di production bisa memakan waktu lebih lama** jika jumlah produk lebih banyak. Jalankan pada jam sepi (malam hari) untuk menghindari dampak ke user.

#### Phase 4: Verify & Monitor

```bash
# Cek index health
curl http://wp_elasticsearch:9200/_cat/indices?v

# Cek dokumen count
curl http://wp_elasticsearch:9200/_cat/count/*post*?v

# Monitor slow queries di debug.log
tail -f /var/www/html/wp-content/debug.log | grep "Canopy ES"
```

#### Phase 5: Rollback Plan

Jika terjadi masalah serius:

```bash
# 1. Deactivate ElasticPress
wp plugin deactivate elasticpress

# 2. Remove mu-plugins
rm wp-content/mu-plugins/04-elasticpress-config.php
rm wp-content/mu-plugins/05-canopy-es-search.php
rm wp-content/mu-plugins/06-canopy-ajax-products.php
rm wp-content/mu-plugins/07-canopy-es-tuning.php

# 3. WooCommerce akan kembali menggunakan MariaDB queries (default behavior)
```

> [!IMPORTANT]
> Rollback **tidak menyebabkan data loss** karena MariaDB tetap menjadi source of truth. Elasticsearch hanya berperan sebagai search index.

---

### 5.6 Post-Deployment Monitoring

#### 5.6.1 Health Check Cron

Buat cron job untuk monitoring kesehatan Elasticsearch:

```php
/**
 * Monitor Elasticsearch health and alert if down.
 * Runs every 5 minutes via WP Cron.
 */
add_action( 'init', function () {
    if ( ! wp_next_scheduled( 'canopy_es_health_check' ) ) {
        wp_schedule_event( time(), 'five_minutes', 'canopy_es_health_check' );
    }
} );

add_filter( 'cron_schedules', function ( $schedules ) {
    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => __( 'Every 5 minutes' ),
    );
    return $schedules;
} );

add_action( 'canopy_es_health_check', function () {
    $response = wp_remote_get( 'http://localhost:9200/_cluster/health', array(
        'timeout' => 5,
    ) );

    if ( is_wp_error( $response ) ) {
        error_log( '[Canopy ES Health] Elasticsearch is DOWN! Error: ' . $response->get_error_message() );
        // Optional: send email/Slack notification
        return;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( isset( $body['status'] ) && 'red' === $body['status'] ) {
        error_log( '[Canopy ES Health] Elasticsearch cluster status is RED!' );
    }
} );
```

---

## Checklist Verifikasi Final

### Functional
- [ ] Search berfungsi (basic, autocomplete, typo tolerance)
- [ ] Filter berfungsi (size, color, price, category)
- [ ] Sorting berfungsi (semua opsi)
- [ ] Pagination AJAX berfungsi
- [ ] Fallback ke MariaDB berfungsi saat ES down
- [ ] SEO: Server-rendered products visible in page source

### Performance
- [ ] TTFB < 500ms (shop page)
- [ ] REST API response < 300ms
- [ ] SQL queries < 50 per page
- [ ] PHP memory < 200MB

### Regression
- [ ] Add to cart berfungsi
- [ ] Checkout berfungsi
- [ ] Single product page normal
- [ ] Admin product edit → sync to ES

### Deployment
- [ ] All mu-plugins deployed
- [ ] ElasticPress installed & configured
- [ ] Initial indexing complete
- [ ] Health monitoring active
- [ ] Rollback plan documented

---

## Summary: Semua File yang Dibuat

| # | File | Type | Deskripsi |
|---|------|------|-----------|
| 1 | `wp-content/mu-plugins/04-elasticpress-config.php` | MU-Plugin | Environment-aware ES host |
| 2 | `wp-content/mu-plugins/05-canopy-es-search.php` | MU-Plugin | Search, autosuggest, faceted filter, sorting |
| 3 | `wp-content/mu-plugins/06-canopy-ajax-products.php` | MU-Plugin | REST API + AJAX product loading |
| 4 | `wp-content/mu-plugins/07-canopy-es-tuning.php` | MU-Plugin | ES index settings, synonyms, optimization |
| 5 | `wp-content/themes/canopy-child/assets/js/ajax-products.js` | Theme JS | Frontend AJAX controller |
| 6 | `wp-content/plugins/elasticpress/` | Plugin | ElasticPress (install via WP Admin) |

---

> [!TIP]
> Kembali ke **[Master Plan](file:///c:/laragon/www/tcr-wordpress/ai-task/00-master-plan.md)** untuk overview keseluruhan.
