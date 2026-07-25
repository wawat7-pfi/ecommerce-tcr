# Task 4: Redis Testing, Monitoring & Production Checklist

## Objective

Benchmark performa sebelum dan sesudah Redis, setup monitoring dashboard, health check automation, dan production deployment checklist.

---

## Prerequisites

- [x] Task 1–3 selesai (Redis server + WordPress integration + WooCommerce optimization)
- [x] Redis Object Cache status "Connected" di WP Admin
- [x] Cache invalidation hooks aktif
- [ ] Baseline metrics sudah dicatat (sebelum Redis)

---

## Step-by-Step Implementation

### 4.1 Benchmark Before/After Redis

#### 4.1.1 Benchmark Script

Buat file `scratch/benchmark_redis.php`:

```php
<?php
define('WP_USE_THEMES', false);

echo "=== Redis Performance Benchmark ===\n\n";

// 1. Measure wp-load.php bootstrap time
$t0 = microtime(true);
require_once __DIR__ . '/wp-load.php';
$t_boot = round((microtime(true) - $t0) * 1000, 2);
echo "1. WP Bootstrap Time: {$t_boot} ms\n";

// 2. Measure Object Cache status
echo "\n2. Object Cache Backend: " . (wp_using_ext_object_cache() ? 'Redis (External)' : 'Database (Internal)') . "\n";

// 3. Measure wp_cache performance
$iterations = 1000;

// Write test
$t1 = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    wp_cache_set("bench_key_{$i}", "value_{$i}", 'canopy_bench', 60);
}
$t_write = round((microtime(true) - $t1) * 1000, 2);
echo "\n3. Cache Write ({$iterations} keys): {$t_write} ms (" . round($t_write / $iterations, 4) . " ms/op)\n";

// Read test
$t2 = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    wp_cache_get("bench_key_{$i}", 'canopy_bench');
}
$t_read = round((microtime(true) - $t2) * 1000, 2);
echo "4. Cache Read ({$iterations} keys): {$t_read} ms (" . round($t_read / $iterations, 4) . " ms/op)\n";

// Cleanup
for ($i = 0; $i < $iterations; $i++) {
    wp_cache_delete("bench_key_{$i}", 'canopy_bench');
}

// 4. Measure get_option() speed (heavily used by plugins)
$options_to_test = array('siteurl', 'blogname', 'active_plugins', 'woocommerce_default_country');
$t3 = microtime(true);
for ($j = 0; $j < 100; $j++) {
    foreach ($options_to_test as $opt) {
        get_option($opt);
    }
}
$t_options = round((microtime(true) - $t3) * 1000, 2);
echo "5. get_option() (400 calls): {$t_options} ms (" . round($t_options / 400, 4) . " ms/op)\n";

// 5. Measure REST API endpoint speed
$t4 = microtime(true);
$req = new WP_REST_Request('GET', '/canopy/v1/products');
$req->set_param('per_page', 12);
$req->set_param('page', 1);
$res = rest_do_request($req);
$t_rest = round((microtime(true) - $t4) * 1000, 2);
echo "6. REST API /canopy/v1/products: {$t_rest} ms (Status: " . $res->get_status() . ")\n";

// 6. Redis server stats
if (class_exists('Redis') && defined('WP_REDIS_HOST')) {
    try {
        $redis = new Redis();
        $redis->connect(WP_REDIS_HOST, WP_REDIS_PORT ?? 6379, 1);
        
        $info = $redis->info();
        $stats = $redis->info('stats');
        $mem = $redis->info('memory');
        
        $hits = $stats['keyspace_hits'] ?? 0;
        $misses = $stats['keyspace_misses'] ?? 0;
        $ratio = ($hits + $misses > 0) ? round($hits / ($hits + $misses) * 100, 1) : 0;
        
        echo "\n=== Redis Server Stats ===\n";
        echo " - Version: " . $info['redis_version'] . "\n";
        echo " - Uptime: " . round($info['uptime_in_seconds'] / 3600, 1) . " hours\n";
        echo " - Total Keys: " . $redis->dbSize() . "\n";
        echo " - Memory Used: " . round($mem['used_memory'] / 1024 / 1024, 2) . " MB\n";
        echo " - Peak Memory: " . round($mem['used_memory_peak'] / 1024 / 1024, 2) . " MB\n";
        echo " - Cache Hit Ratio: {$ratio}%\n";
        echo " - Hits: " . number_format($hits) . "\n";
        echo " - Misses: " . number_format($misses) . "\n";
        echo " - Connected Clients: " . $info['connected_clients'] . "\n";
        echo " - Evicted Keys: " . ($stats['evicted_keys'] ?? 0) . "\n";
        
        $redis->close();
    } catch (RedisException $e) {
        echo "\nRedis Error: " . $e->getMessage() . "\n";
    }
}
```

#### 4.1.2 Expected Results

| Metric | Tanpa Redis | Dengan Redis | Improvement |
|--------|------------|-------------|-------------|
| WP Bootstrap | ~4.000 ms | ~500-800 ms | **5-8x faster** |
| get_option() (400 calls) | ~80 ms | ~2 ms | **40x faster** |
| Cache Write (1000 keys) | ~200 ms (DB) | ~15 ms (RAM) | **13x faster** |
| Cache Read (1000 keys) | ~150 ms (DB) | ~8 ms (RAM) | **18x faster** |
| REST API (cached) | ~5 ms | ~1 ms | **5x faster** |
| Full Page TTFB | ~5.000 ms | ~500-1.000 ms | **5-10x faster** |

---

### 4.2 Health Check & Monitoring

#### 4.2.1 Redis Health Check MU-Plugin

Tambahkan ke `08-redis-config.php` atau buat file terpisah:

```php
/**
 * Redis Health Check — runs every 5 minutes via WP Cron.
 */
add_action( 'init', function() {
    if ( ! wp_next_scheduled( 'canopy_redis_health_check' ) ) {
        wp_schedule_event( time(), 'five_minutes', 'canopy_redis_health_check' );
    }
});

// Register custom cron interval
add_filter( 'cron_schedules', function( $schedules ) {
    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => 'Every 5 Minutes',
    );
    return $schedules;
});

add_action( 'canopy_redis_health_check', function() {
    if ( ! class_exists( 'Redis' ) || ! defined( 'WP_REDIS_HOST' ) ) {
        return;
    }

    try {
        $redis = new Redis();
        $redis->connect( WP_REDIS_HOST, WP_REDIS_PORT ?? 6379, 1 );
        $pong = $redis->ping();

        if ( $pong ) {
            $mem = $redis->info( 'memory' );
            $used_mb = round( $mem['used_memory'] / 1024 / 1024, 2 );
            $max_mb  = 256; // Configured maxmemory

            // Alert if memory usage > 80%
            if ( $used_mb > ( $max_mb * 0.8 ) ) {
                error_log( "[Canopy Redis] WARNING: Memory usage {$used_mb}MB / {$max_mb}MB (>80%)" );
            }

            // Log stats
            $stats = $redis->info( 'stats' );
            $hits    = $stats['keyspace_hits'] ?? 0;
            $misses  = $stats['keyspace_misses'] ?? 0;
            $ratio   = ( $hits + $misses > 0 ) ? round( $hits / ( $hits + $misses ) * 100, 1 ) : 0;

            // Alert if hit ratio drops below 80%
            if ( $ratio < 80 && ( $hits + $misses ) > 1000 ) {
                error_log( "[Canopy Redis] WARNING: Cache hit ratio {$ratio}% (below 80% threshold)" );
            }
        }

        $redis->close();
    } catch ( \RedisException $e ) {
        error_log( "[Canopy Redis] CRITICAL: Redis connection failed - " . $e->getMessage() );
        
        // Optional: Send admin notification
        if ( function_exists( 'wp_mail' ) ) {
            wp_mail(
                get_option( 'admin_email' ),
                '[The Canopy Room] Redis Down Alert',
                'Redis server is unreachable: ' . $e->getMessage()
            );
        }
    }
});
```

#### 4.2.2 WP Admin Dashboard Widget

```php
/**
 * Redis monitoring dashboard widget.
 */
add_action( 'wp_dashboard_setup', function() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    wp_add_dashboard_widget(
        'canopy_redis_dashboard',
        '🔴 Redis Cache Monitor',
        'canopy_redis_dashboard_widget'
    );
});

function canopy_redis_dashboard_widget() {
    if ( ! class_exists( 'Redis' ) || ! defined( 'WP_REDIS_HOST' ) ) {
        echo '<p>❌ Redis not configured.</p>';
        return;
    }

    try {
        $redis = new Redis();
        $redis->connect( WP_REDIS_HOST, WP_REDIS_PORT ?? 6379, 1 );

        $info   = $redis->info();
        $stats  = $redis->info( 'stats' );
        $mem    = $redis->info( 'memory' );
        $keys   = $redis->dbSize();
        $hits   = $stats['keyspace_hits'] ?? 0;
        $misses = $stats['keyspace_misses'] ?? 0;
        $ratio  = ( $hits + $misses > 0 ) ? round( $hits / ( $hits + $misses ) * 100, 1 ) : 0;

        echo '<table style="width:100%;border-collapse:collapse;">';
        echo '<tr><td>Status</td><td><strong style="color:green;">🟢 Connected</strong></td></tr>';
        echo '<tr><td>Version</td><td>' . esc_html( $info['redis_version'] ) . '</td></tr>';
        echo '<tr><td>Uptime</td><td>' . round( $info['uptime_in_seconds'] / 3600, 1 ) . ' hours</td></tr>';
        echo '<tr><td>Memory</td><td>' . round( $mem['used_memory'] / 1024 / 1024, 2 ) . ' MB / 256 MB</td></tr>';
        echo '<tr><td>Keys</td><td>' . number_format( $keys ) . '</td></tr>';
        echo '<tr><td>Hit Ratio</td><td><strong>' . $ratio . '%</strong></td></tr>';
        echo '<tr><td>Hits / Misses</td><td>' . number_format( $hits ) . ' / ' . number_format( $misses ) . '</td></tr>';
        echo '<tr><td>Evictions</td><td>' . number_format( $stats['evicted_keys'] ?? 0 ) . '</td></tr>';
        echo '</table>';

        // Quick actions
        echo '<p style="margin-top:10px;">';
        echo '<a href="' . esc_url( admin_url( 'options-general.php?page=redis-cache' ) ) . '" class="button">Redis Settings</a> ';
        echo '</p>';

        $redis->close();
    } catch ( \RedisException $e ) {
        echo '<p style="color:red;">❌ Redis Error: ' . esc_html( $e->getMessage() ) . '</p>';
    }
}
```

---

### 4.3 Cache Warm-up Script

Setelah Redis restart atau cache flush, jalankan warm-up script untuk pre-populate cache:

```php
<?php
/**
 * Cache Warm-up Script
 * Run after Redis restart or full cache flush.
 * 
 * Usage: php scratch/warmup_redis_cache.php
 */
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

echo "=== Redis Cache Warm-up ===\n\n";

$t0 = microtime(true);

// 1. Warm WordPress core options
echo "1. Warming WordPress core options...\n";
$core_options = array(
    'siteurl', 'home', 'blogname', 'blogdescription',
    'active_plugins', 'template', 'stylesheet',
    'woocommerce_default_country', 'woocommerce_currency',
    'woocommerce_shop_page_id', 'woocommerce_cart_page_id',
    'woocommerce_checkout_page_id', 'woocommerce_myaccount_page_id',
);
foreach ($core_options as $opt) {
    get_option($opt);
}
echo "   Done: " . count($core_options) . " options cached.\n";

// 2. Warm WooCommerce product counts
echo "2. Warming product counts...\n";
$counts = wp_count_posts('product');
echo "   Done: {$counts->publish} published products.\n";

// 3. Warm product categories
echo "3. Warming product categories...\n";
$cats = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
));
echo "   Done: " . count($cats) . " categories cached.\n";

// 4. Warm product attributes
echo "4. Warming product attributes...\n";
$attrs = wc_get_attribute_taxonomies();
echo "   Done: " . count($attrs) . " attributes cached.\n";

// 5. Warm first page of REST API products
echo "5. Warming REST API /canopy/v1/products (page 1)...\n";
$req = new WP_REST_Request('GET', '/canopy/v1/products');
$req->set_param('per_page', 12);
$req->set_param('page', 1);
$res = rest_do_request($req);
echo "   Done: Status " . $res->get_status() . "\n";

// 6. Warm navigation menus
echo "6. Warming navigation menus...\n";
$locations = get_nav_menu_locations();
foreach ($locations as $location => $menu_id) {
    wp_get_nav_menu_items($menu_id);
}
echo "   Done: " . count($locations) . " menus cached.\n";

$total = round((microtime(true) - $t0) * 1000, 2);
echo "\n=== Warm-up Complete: {$total} ms ===\n";

// Show Redis stats
if (class_exists('Redis') && defined('WP_REDIS_HOST')) {
    $redis = new Redis();
    $redis->connect(WP_REDIS_HOST, WP_REDIS_PORT ?? 6379, 1);
    echo "Redis Keys After Warm-up: " . $redis->dbSize() . "\n";
    $redis->close();
}
```

---

### 4.4 Redis Configuration for Production

#### 4.4.1 Production Redis Config (`redis.conf`)

Buat file `docker/redis.conf` untuk production:

```conf
# === The Canopy Room — Production Redis Configuration ===

# Network
bind 0.0.0.0
port 6379
tcp-keepalive 60
timeout 300

# Memory
maxmemory 256mb
maxmemory-policy allkeys-lru

# Persistence (RDB Snapshots)
save 900 1       # Save after 900s if at least 1 key changed
save 300 10      # Save after 300s if at least 10 keys changed
save 60 10000    # Save after 60s if at least 10000 keys changed
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir /data

# Append Only File (AOF) — more durable
appendonly yes
appendfsync everysec
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb

# Logging
loglevel warning
logfile ""

# Security (set password for production)
# requirepass YOUR_STRONG_PASSWORD_HERE

# Slow log
slowlog-log-slower-than 10000
slowlog-max-len 128

# Clients
maxclients 1000
```

#### 4.4.2 Update docker-compose.yml untuk Production

```yaml
  redis:
    image: redis:7-alpine
    container_name: wp_redis
    restart: unless-stopped
    command: redis-server /usr/local/etc/redis/redis.conf
    volumes:
      - redis_data:/data
      - ./redis.conf:/usr/local/etc/redis/redis.conf:ro
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

---

### 4.5 Production Deployment Checklist

#### Pre-Deployment

- [ ] Redis 7 server provisioned (managed service recommended: AWS ElastiCache, DigitalOcean Managed Redis)
- [ ] PHP Redis extension installed on production server
- [ ] `wp-config.php` Redis constants configured for production host
- [ ] `object-cache.php` drop-in included in deployment
- [ ] Redis password set (`requirepass` + `WP_REDIS_PASSWORD` constant)
- [ ] Redis max memory configured (`maxmemory 256mb` or higher based on traffic)
- [ ] Firewall rules: Only WordPress server can access Redis port 6379
- [ ] Redis **not** exposed to public internet

#### Post-Deployment

- [ ] WP Admin → Settings → Redis shows "Connected"
- [ ] Run cache warm-up script
- [ ] Verify response times < 500ms for `/shop/`
- [ ] Verify cache hit ratio > 80% after warm-up
- [ ] Monitor Redis memory usage (should stabilize < 80% of maxmemory)
- [ ] Test cache invalidation: Update a product → verify cache cleared
- [ ] Test Redis failover: Stop Redis → verify WordPress still works (fallback to DB)
- [ ] Setup monitoring alerts (Uptime Robot, New Relic, or WP Cron-based)

#### Security Checklist

- [ ] Redis password configured in production
- [ ] `WP_REDIS_PASSWORD` set in `wp-config.php`
- [ ] Redis bound to private network interface only
- [ ] No public access to port 6379
- [ ] TLS enabled for Redis connections (if over network)

---

### 4.6 Rollback Plan

Jika Redis menyebabkan masalah di production:

#### Quick Disable (< 1 minute)

```php
// Di wp-config.php, tambahkan:
define( 'WP_REDIS_DISABLED', true );
```

#### Full Rollback

1. Hapus `wp-content/object-cache.php`
2. Set `WP_CACHE` ke `false` di `wp-config.php`
3. Hapus/comment Redis constants di `wp-config.php`
4. Deactivate Redis Object Cache plugin

WordPress akan otomatis fallback ke internal object cache (database-backed).

---

## Verifikasi Checklist

- [ ] Benchmark sebelum Redis dijalankan dan hasil dicatat
- [ ] Benchmark sesudah Redis menunjukkan improvement signifikan
- [ ] Health check cron berjalan setiap 5 menit
- [ ] Dashboard widget menampilkan Redis stats
- [ ] Cache warm-up script berfungsi
- [ ] Production redis.conf sudah disiapkan
- [ ] Production deployment checklist diikuti
- [ ] Rollback plan sudah dites

---

## Output Task Ini

Setelah task ini selesai, kita akan memiliki:

1. ✅ Benchmark data before/after Redis
2. ✅ Automated health check monitoring
3. ✅ WP Admin dashboard widget untuk Redis stats
4. ✅ Cache warm-up script
5. ✅ Production-ready Redis configuration
6. ✅ Deployment & security checklist
7. ✅ Rollback plan yang sudah dites

---

## Summary: Complete Redis Stack

```
┌────────────────────────────────────────────────────────────┐
│                  The Canopy Room — Complete Stack           │
├────────────────────────────────────────────────────────────┤
│                                                            │
│  Browser ──→ Apache/Nginx                                  │
│               ├── Static Assets (Browser Cache)            │
│               └── PHP 8.3                                  │
│                    ├── object-cache.php (Drop-in)           │
│                    │    └── Redis 7 (Object Cache) ◄─ NEW  │
│                    ├── MU-Plugins                           │
│                    │    ├── 04-elasticpress-config.php      │
│                    │    ├── 05-canopy-es-search.php         │
│                    │    ├── 06-canopy-ajax-products.php     │
│                    │    ├── 07-canopy-es-tuning.php         │
│                    │    ├── 08-redis-config.php     ◄─ NEW │
│                    │    └── 09-canopy-wc-cache.php  ◄─ NEW │
│                    ├── Elasticsearch 8.17 (Product Search)  │
│                    └── MariaDB 11 (Source of Truth)         │
│                                                            │
│  Response Time Target:                                     │
│    Before Redis: ~5.000 ms                                 │
│    After Redis:  ~500 ms (10x improvement)                 │
│                                                            │
└────────────────────────────────────────────────────────────┘
```

---

> [!TIP]
> Setelah semua 4 task selesai, jalankan benchmark final dan bandingkan dengan baseline. Target: **TTFB < 500ms** pada halaman `/shop/` di environment lokal.
