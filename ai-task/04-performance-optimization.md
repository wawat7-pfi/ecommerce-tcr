# Task 4: Performance Optimization & Monitoring

## Objective

Optimasi performa keseluruhan setelah Elasticsearch dan AJAX loading terimplementasi. Meliputi: caching layer, Elasticsearch tuning, query optimization, dan monitoring dashboard.

---

## Prerequisites

- [x] Task 1–3 selesai
- [x] ElasticPress + AJAX loading berjalan
- [ ] Baseline performance metrics diambil

---

## Step-by-Step Implementation

### 4.1 Baseline Performance Measurement

Sebelum optimasi, catat baseline metrics:

#### 4.1.1 TTFB (Time To First Byte)

Ukur menggunakan Chrome DevTools → Network tab → pertama kali load `/shop/`:

| Metric | Sebelum ES | Setelah ES | Target |
|--------|-----------|------------|--------|
| TTFB (Shop page) | ??? ms | ??? ms | < 300ms |
| TTFB (Category page) | ??? ms | ??? ms | < 300ms |
| TTFB (Search results) | ??? ms | ??? ms | < 200ms |
| Full page load | ??? ms | ??? ms | < 2s |

#### 4.1.2 Database Queries

Pasang **Query Monitor** plugin untuk melihat jumlah SQL queries:

```bash
# Install via WP Admin: Plugins → Add New → "Query Monitor"
```

| Metric | Sebelum ES | Setelah ES | Target |
|--------|-----------|------------|--------|
| SQL Queries (Shop page) | ??? | ??? | < 30 |
| Peak Memory | ??? MB | ??? MB | < 128MB |
| DB Query Time | ??? ms | ??? ms | < 50ms |

---

### 4.2 Elasticsearch Index Tuning

#### 4.2.1 Optimasi Index Settings

Buat script untuk mengoptimasi Elasticsearch index settings:

**File: `wp-content/mu-plugins/07-canopy-es-tuning.php`**

```php
<?php
/**
 * Canopy Room — Elasticsearch Index Tuning.
 *
 * Optimizes ES index settings for WooCommerce product search:
 * - Custom analyzer for Indonesian + English mixed content
 * - Synonym support for fashion terms
 * - Optimized mapping for product fields
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Customize Elasticsearch index settings.
 */
add_filter( 'ep_index_settings', function ( $settings ) {
    // Increase number of shards for better parallel search
    // (1 shard is fine for < 1000 products)
    $settings['index']['number_of_shards']   = 1;
    $settings['index']['number_of_replicas'] = 0; // No replicas in development

    // Optimize refresh interval (reduce write overhead)
    $settings['index']['refresh_interval'] = '5s';

    // Add custom analyzer for fashion-specific terms
    $settings['analysis']['analyzer']['canopy_analyzer'] = array(
        'type'      => 'custom',
        'tokenizer' => 'standard',
        'filter'    => array( 'lowercase', 'canopy_synonyms', 'asciifolding' ),
    );

    $settings['analysis']['filter']['canopy_synonyms'] = array(
        'type'     => 'synonym',
        'synonyms' => array(
            'dress, gaun, dresses',
            'skirt, rok',
            'top, atasan, blouse, blus',
            'pants, celana, trousers',
            'midi, mid-length',
            'maxi, long, panjang',
            'mini, short, pendek',
            'jacket, jaket, blazer, outer',
            'bag, tas',
            'shoe, shoes, sepatu',
            'scarf, syal, shawl',
        ),
    );

    return $settings;
} );

/**
 * Optimize product mapping — exclude unnecessary fields from indexing.
 */
add_filter( 'ep_prepare_meta_data', function ( $meta, $post ) {
    if ( 'product' !== get_post_type( $post->ID ) ) {
        return $meta;
    }

    // Remove heavy meta keys that we don't need in search
    $exclude_keys = array(
        '_product_image_gallery',
        '_edit_lock',
        '_edit_last',
        '_wp_old_slug',
        '_wp_old_date',
    );

    foreach ( $exclude_keys as $key ) {
        unset( $meta[ $key ] );
    }

    return $meta;
}, 10, 2 );
```

---

### 4.3 WordPress Object Cache (Transient Caching)

Cache hasil REST API response agar tidak query Elasticsearch setiap request:

**Tambahkan di `06-canopy-ajax-products.php` (method `get_products`):**

```php
/**
 * Generate cache key from request parameters.
 */
private function get_cache_key( $request ) {
    $params = $request->get_params();
    ksort( $params );
    return 'canopy_products_' . md5( wp_json_encode( $params ) );
}

/**
 * Updated get_products with transient caching.
 */
public function get_products_cached( $request ) {
    $cache_key = $this->get_cache_key( $request );
    $cached = get_transient( $cache_key );

    if ( false !== $cached ) {
        return rest_ensure_response( $cached );
    }

    // Original query logic...
    $result = $this->get_products_uncached( $request );

    // Cache for 5 minutes
    set_transient( $cache_key, $result, 5 * MINUTE_IN_SECONDS );

    return rest_ensure_response( $result );
}
```

#### 4.3.1 Cache Invalidation

```php
/**
 * Clear product cache when any product is updated.
 */
add_action( 'save_post_product', function ( $post_id ) {
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_canopy_products_%' OR option_name LIKE '_transient_timeout_canopy_products_%'"
    );
}, 20 );

add_action( 'woocommerce_update_product', function () {
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_canopy_products_%' OR option_name LIKE '_transient_timeout_canopy_products_%'"
    );
} );
```

---

### 4.4 Redis Object Cache (Future Enhancement)

> [!NOTE]
> Redis belum ada di stack saat ini. Ini adalah enhancement untuk fase berikutnya.

Ketika Redis sudah ditambahkan ke Docker stack:

```yaml
# Tambahkan di docker-compose.yml
  redis:
    image: redis:7-alpine
    container_name: wp_redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    networks:
      - wp_network
```

Lalu install plugin **Redis Object Cache** (by Till Krüss):
- Semua `get_transient()` / `set_transient()` akan otomatis menggunakan Redis
- Query cache WooCommerce akan jauh lebih cepat

---

### 4.5 Elasticsearch Query Monitoring

#### 4.5.1 Slow Query Logging

```php
/**
 * Log slow Elasticsearch queries for debugging.
 */
add_action( 'ep_valid_response', function ( $response, $query, $args, $scope ) {
    if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
        return;
    }

    $took = $response['took'] ?? 0; // milliseconds
    if ( $took > 100 ) { // Log queries > 100ms
        error_log( sprintf(
            '[Canopy ES Slow Query] %dms | Query: %s',
            $took,
            wp_json_encode( $query )
        ) );
    }
}, 10, 4 );
```

#### 4.5.2 Kibana Dashboard

Buat monitoring dashboard di Kibana (`http://localhost:5601`):

1. **Index Pattern**: Buat index pattern untuk `tcr-wordpress-*`
2. **Discover**: Lihat data produk yang terindeks
3. **Dashboard**: Buat visualisasi untuk:
   - Total documents indexed
   - Search query latency (avg, p95, p99)
   - Index size (MB)
   - Search requests per minute

---

### 4.6 WooCommerce Query Optimization

#### 4.6.1 Rebuild Product Lookup Table

Lookup table saat ini hanya berisi **1 row** (seharusnya 550+):

```php
// Jalankan sekali via WP-CLI atau scratch script:
// WooCommerce → Status → Tools → "Regenerate product lookup tables"
```

Atau via script:

```php
<?php
define('WP_USE_THEMES', false);
require_once 'c:/laragon/www/tcr-wordpress/wp-load.php';

// Trigger lookup table regeneration
if ( class_exists( 'WC_Install' ) ) {
    \WC_Install::create_tables();
}

// Regenerate lookup data
$regenerator = new \Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore();
// Or use WooCommerce scheduled action
WC()->queue()->schedule_single( time(), 'wc_update_product_lookup_tables' );
echo "Lookup table regeneration scheduled.\n";
```

#### 4.6.2 Optimize WooCommerce Transients

```php
/**
 * Clean up expired WooCommerce transients periodically.
 */
add_action( 'init', function () {
    if ( ! wp_next_scheduled( 'canopy_cleanup_transients' ) ) {
        wp_schedule_event( time(), 'daily', 'canopy_cleanup_transients' );
    }
} );

add_action( 'canopy_cleanup_transients', function () {
    global $wpdb;
    $wpdb->query(
        "DELETE a, b FROM {$wpdb->options} a 
         JOIN {$wpdb->options} b ON b.option_name = CONCAT('_transient_timeout_', SUBSTRING(a.option_name, 12)) 
         WHERE a.option_name LIKE '_transient_%' 
         AND b.option_value < UNIX_TIMESTAMP()"
    );
} );
```

---

### 4.7 Frontend Performance

#### 4.7.1 Lazy Load Product Images

Pastikan gambar produk menggunakan `loading="lazy"`:

```php
/**
 * Add lazy loading to product images on shop pages.
 * WordPress 5.5+ sudah support ini secara native, tapi pastikan aktif.
 */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment, $size ) {
    if ( is_shop() || is_product_taxonomy() ) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    return $attr;
}, 10, 3 );
```

#### 4.7.2 Prefetch Elasticsearch Results

```javascript
// Tambahkan di ajax-products.js
// Prefetch next page saat user mendekati bottom
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const nextPage = currentParams.page + 1;
            // Prefetch next page
            fetch(`${config.restUrl}?page=${nextPage}&per_page=${config.perPage}`)
                .then(res => res.json())
                .catch(() => {});
        }
    });
}, { rootMargin: '200px' });

// Observe last product card
const lastProduct = document.querySelector('.products .product:last-child');
if (lastProduct) observer.observe(lastProduct);
```

---

## Checklist Verifikasi

- [ ] Baseline metrics dicatat (TTFB, query count, memory)
- [ ] ES index settings dioptimasi (synonyms, analyzers)
- [ ] Transient caching berfungsi untuk REST API responses
- [ ] Cache invalidation berjalan saat produk di-update
- [ ] Slow query logging aktif (queries > 100ms di-log)
- [ ] WooCommerce product lookup table terisi (550+ rows)
- [ ] Product images lazy-loaded
- [ ] Query Monitor menunjukkan query count berkurang

---

## File yang Dibuat/Dimodifikasi

| File | Aksi | Deskripsi |
|------|------|-----------|
| `wp-content/mu-plugins/07-canopy-es-tuning.php` | **NEW** | ES index settings, synonym, field optimization |
| `wp-content/mu-plugins/06-canopy-ajax-products.php` | **MODIFY** | Add transient caching + cache invalidation |
| `wp-content/themes/canopy-child/assets/js/ajax-products.js` | **MODIFY** | Add prefetch next page |

---

> [!TIP]
> Setelah Task 4 selesai, lanjut ke **[Task 5: Testing & Deployment](file:///c:/laragon/www/tcr-wordpress/ai-task/05-testing-deployment.md)**.
