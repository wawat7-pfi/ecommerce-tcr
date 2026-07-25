# Task 2: Search Integration — Autocomplete, Faceted Filtering & Sorting

## Objective

Konfigurasi fitur search yang didukung Elasticsearch: autocomplete (live search), faceted filtering (filter berdasarkan size, color, category, harga), dan sorting yang cepat.

---

## Prerequisites

- [x] Task 1 selesai — ElasticPress terinstall & indexing selesai
- [x] Elasticsearch berisi 550+ produk
- [ ] Ecomus theme search template diperiksa

---

## Step-by-Step Implementation

### 2.1 Autocomplete / Live Search (Autosuggest)

ElasticPress memiliki fitur **Autosuggest** built-in yang menampilkan hasil search secara real-time saat user mengetik.

#### 2.1.1 Enable Autosuggest

1. Buka **ElasticPress → Features**
2. Aktifkan **"Autosuggest"**
3. Konfigurasi:

| Setting | Value |
|---------|-------|
| Autosuggest | ✅ Enabled |
| Google Autosuggest | ⬜ Disabled (tidak perlu) |

#### 2.1.2 Custom Autosuggest untuk Ecomus Theme

Ecomus theme memiliki search form sendiri. Buat custom integration di canopy-child:

**File: `wp-content/mu-plugins/05-canopy-es-search.php`**

```php
<?php
/**
 * Canopy Room — Elasticsearch Search Enhancements.
 *
 * Integrates ElasticPress autosuggest with Ecomus theme search form
 * and adds WooCommerce-specific search improvements.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Add ElasticPress autosuggest selector for Ecomus theme search forms.
 */
add_filter( 'ep_autosuggest_options', function ( $options ) {
    // Target Ecomus theme search input selectors
    $options['inputSelectors'] = '.search-field, .ecomus-search-form input, .header-search input';
    return $options;
} );

/**
 * Customize autosuggest query to prioritize WooCommerce products.
 */
add_filter( 'ep_autosuggest_query_args', function ( $args ) {
    $args['post_type'] = array( 'product' );
    return $args;
} );

/**
 * Add product price and image to autosuggest results.
 */
add_filter( 'ep_post_sync_args', function ( $post_args, $post_id ) {
    if ( 'product' !== get_post_type( $post_id ) ) {
        return $post_args;
    }

    $product = wc_get_product( $post_id );
    if ( ! $product ) {
        return $post_args;
    }

    // Add extra fields for autosuggest display
    $post_args['meta']['_price_display'] = $product->get_price_html();
    $post_args['meta']['_stock_status']  = $product->get_stock_status();

    return $post_args;
}, 10, 2 );
```

---

### 2.2 Faceted Filtering (Filter Sidebar)

ElasticPress WooCommerce feature otomatis meng-intercept widget filter bawaan WooCommerce sehingga filtering menggunakan Elasticsearch (bukan SQL query).

#### 2.2.1 Widget Filter yang Didukung

| Widget | Supported | Catatan |
|--------|-----------|---------|
| Filter by Price | ✅ | Range slider, powered by ES |
| Filter by Attribute (Size) | ✅ | Checkbox/swatch list |
| Filter by Attribute (Color) | ✅ | Swatch colors |
| Filter by Rating | ✅ | Star rating filter |
| Filter by Category | ✅ | Category tree |
| Filter by Stock Status | ✅ | In stock / Out of stock |

#### 2.2.2 Pastikan Ecomus Sidebar Filter Aktif

Theme Ecomus sudah memiliki sidebar filter bawaan. ElasticPress akan secara otomatis mengintercept `WP_Query` yang digunakan oleh filter widget tersebut.

Verifikasi di **Appearance → Widgets** (atau **Ecomus → Customizer → Shop Sidebar**):
- Pastikan widget **"Filter Products by Attribute"** ada untuk Size dan Color
- Pastikan widget **"Filter Products by Price"** ada

#### 2.2.3 Custom Faceted Filter via Elasticsearch Aggregations

Jika Ecomus sidebar filter tidak cukup, tambahkan aggregation support:

**Tambahkan di `05-canopy-es-search.php`:**

```php
/**
 * Add custom aggregations for faceted search.
 * This enables count-aware filtering (e.g., "Size S (45)").
 */
add_filter( 'ep_formatted_args', function ( $formatted_args, $args ) {
    if ( ! is_shop() && ! is_product_taxonomy() ) {
        return $formatted_args;
    }

    // Add size aggregation
    $formatted_args['aggs']['size_filter'] = array(
        'terms' => array(
            'field' => 'terms.pa_size.slug',
            'size'  => 50,
        ),
    );

    // Add color aggregation
    $formatted_args['aggs']['color_filter'] = array(
        'terms' => array(
            'field' => 'terms.pa_color.slug',
            'size'  => 50,
        ),
    );

    // Add category aggregation
    $formatted_args['aggs']['category_filter'] = array(
        'terms' => array(
            'field' => 'terms.product_cat.slug',
            'size'  => 100,
        ),
    );

    // Add price range aggregation
    $formatted_args['aggs']['price_range'] = array(
        'stats' => array(
            'field' => 'meta._price.long',
        ),
    );

    return $formatted_args;
}, 10, 2 );
```

---

### 2.3 Sorting Optimization

#### 2.3.1 Default WooCommerce Sorting Options

ElasticPress otomatis mendukung sorting berikut via Elasticsearch:

| Sort Option | ES Field | Status |
|-------------|----------|--------|
| Default (menu_order) | `menu_order` | ✅ Auto |
| Popularity (sales) | `meta.total_sales.long` | ✅ Auto |
| Average rating | `meta._wc_average_rating.double` | ✅ Auto |
| Newest | `post_date` | ✅ Auto |
| Price: Low to High | `meta._price.long` | ✅ Auto |
| Price: High to Low | `meta._price.long` (desc) | ✅ Auto |

#### 2.3.2 Custom Sort: Best Selling + Rating Combo

**Tambahkan di `05-canopy-es-search.php`:**

```php
/**
 * Add custom "Recommended" sorting that combines sales + rating.
 */
add_filter( 'woocommerce_catalog_orderby', function ( $options ) {
    $options = array_merge(
        array( 'recommended' => __( 'Recommended', 'canopy' ) ),
        $options
    );
    return $options;
} );

add_filter( 'ep_formatted_args', function ( $formatted_args, $args ) {
    if ( isset( $args['orderby'] ) && 'recommended' === $args['orderby'] ) {
        $formatted_args['sort'] = array(
            array(
                '_script' => array(
                    'type'   => 'number',
                    'script' => array(
                        'source' => "doc['meta.total_sales.long'].value * 0.7 + doc['meta._wc_average_rating.double'].value * 0.3",
                    ),
                    'order'  => 'desc',
                ),
            ),
        );
    }
    return $formatted_args;
}, 20, 2 );
```

---

### 2.4 Search Results Relevancy Tuning

#### 2.4.1 Boost Product Title & SKU

Produk yang match di title atau SKU harus muncul lebih tinggi dari match di description:

**Tambahkan di `05-canopy-es-search.php`:**

```php
/**
 * Boost search relevancy for product title and SKU.
 */
add_filter( 'ep_weighting_default_post_type_weights', function ( $weights, $post_type ) {
    if ( 'product' !== $post_type ) {
        return $weights;
    }

    return array(
        'post_title'   => array(
            'weight'  => 5.0,
            'enabled' => true,
        ),
        'post_content' => array(
            'weight'  => 1.0,
            'enabled' => true,
        ),
        'post_excerpt' => array(
            'weight'  => 2.0,
            'enabled' => true,
        ),
        'meta._sku.value' => array(
            'weight'  => 4.0,
            'enabled' => true,
        ),
    );
}, 10, 2 );
```

#### 2.4.2 Synonym Support

Untuk fashion e-commerce, tambahkan synonym agar search lebih pintar:

```
# Contoh synonyms yang berguna untuk fashion:
dress, gaun
skirt, rok  
top, atasan, blouse
pants, celana, trousers
midi, mid-length
maxi, long
```

Konfigurasi via ElasticPress → Features → Custom Search Results → Synonyms
Atau via Elasticsearch synonym filter (lebih advanced, bisa dikonfigurasi di Task 4).

---

### 2.5 Exclude Out-of-Stock dari Search (Optional)

```php
/**
 * Optionally exclude out-of-stock products from search results.
 * Mengikuti setting WooCommerce "Hide out of stock items from the catalog".
 */
add_filter( 'ep_formatted_args', function ( $formatted_args ) {
    if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
        $formatted_args['post_filter']['bool']['must'][] = array(
            'term' => array(
                'meta._stock_status.keyword' => 'instock',
            ),
        );
    }
    return $formatted_args;
} );
```

---

## Checklist Verifikasi

- [ ] Autosuggest berfungsi di search bar Ecomus theme
- [ ] Ketik "skirt" → muncul hasil produk secara real-time
- [ ] Filter by Size berfungsi di sidebar shop page
- [ ] Filter by Color berfungsi di sidebar shop page
- [ ] Filter by Price (range slider) berfungsi
- [ ] Sorting dropdown berfungsi (Price Low-High, High-Low, Newest, dll)
- [ ] Search results menampilkan produk yang relevan (title match > description match)
- [ ] Performa filtering lebih cepat dibanding sebelum Elasticsearch

---

## File yang Dibuat/Dimodifikasi

| File | Aksi | Deskripsi |
|------|------|-----------|
| `wp-content/mu-plugins/05-canopy-es-search.php` | **NEW** | Search enhancements, autosuggest, faceted filter, sorting |

---

> [!TIP]
> Setelah Task 2 selesai, lanjut ke **[Task 3: AJAX Product Loading](file:///c:/laragon/www/tcr-wordpress/ai-task/03-ajax-product-loading.md)** — ini adalah task paling besar dan paling impactful.
