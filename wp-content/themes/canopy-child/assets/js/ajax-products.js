/**
 * Canopy AJAX Products — Frontend Controller
 *
 * Handles async product loading on shop & category pages via Elasticsearch REST API.
 */
(function ($) {
    'use strict';

    const config = window.canopyProducts || {};
    if (!config.restUrl) return;

    const SELECTORS = {
        productsContainer: '.products, ul.products',
        paginationContainer: '.woocommerce-pagination, .canopy-ajax-pagination',
        productLoop: 'ul.products',
        orderbyForm: '.woocommerce-ordering',
        orderbySelect: '.woocommerce-ordering .orderby',
        resultCount: '.woocommerce-result-count',
    };

    let currentParams = {
        page: 1,
        per_page: config.perPage || 12,
        orderby: 'menu_order',
        order: 'ASC',
        category: config.category || '',
        search: '',
        min_price: '',
        max_price: '',
        attribute_pa_size: '',
        attribute_pa_color: '',
    };

    /**
     * Fetch products from REST API.
     */
    async function fetchProducts(params) {
        const url = new URL(config.restUrl);
        Object.keys(params).forEach(key => {
            if (params[key] !== '' && params[key] !== null) {
                url.searchParams.set(key, params[key]);
            }
        });

        const response = await fetch(url.toString(), {
            headers: {
                'X-WP-Nonce': config.nonce,
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return response.json();
    }

    /**
     * Render product list and pagination into DOM.
     */
    function renderProducts(data) {
        const $container = $(SELECTORS.productsContainer).closest('.shop-content-inner, .site-content, main, #main');
        const $loop = $(SELECTORS.productLoop).length ? $(SELECTORS.productLoop) : $('.canopy-initial-skeleton');

        if ($loop.length && data.html) {
            const trimmed = $.trim(data.html);
            if (trimmed.indexOf('<ul') === 0 || trimmed.indexOf('<div') === 0) {
                $loop.replaceWith(data.html);
            } else {
                $loop.html(data.html).removeClass('canopy-initial-skeleton');
            }
        }

        // Apply smooth fade-in animation
        $(SELECTORS.productLoop).find('.product').each(function (i) {
            $(this).css('animation-delay', (i * 0.04) + 's').addClass('canopy-fade-in');
        });

        // Update pagination
        const $pagination = $(SELECTORS.paginationContainer);
        if ($pagination.length && data.pagination) {
            $pagination.replaceWith(data.pagination);
        } else if (data.pagination) {
            $(SELECTORS.productLoop).after(data.pagination);
        }

        // Update result count text
        const $count = $(SELECTORS.resultCount);
        if ($count.length && data.total !== undefined) {
            const start = ((currentParams.page - 1) * currentParams.per_page) + 1;
            const end = Math.min(data.total, currentParams.page * currentParams.per_page);
            $count.text(`Showing ${start}–${end} of ${data.total} results`);
        }

        // Re-attach event listeners to pagination links
        attachPaginationHandlers();

        // Scroll smoothly to top of products on page change
        if (currentParams.page > 1) {
            const $target = $(SELECTORS.productLoop);
            if ($target.length) {
                $('html, body').animate({ scrollTop: $target.offset().top - 100 }, 300);
            }
        }

        // Trigger WooCommerce / Ecomus theme event for quick view, swatches, etc.
        $(document.body).trigger('post-load').trigger('wc_fragments_refreshed');
    }

    /**
     * Load products with loading state indicator.
     */
    async function loadProducts() {
        const $wrapper = $(SELECTORS.productsContainer).closest('.shop-content-inner, .site-content, main, #main');
        $wrapper.addClass('canopy-products-container is-loading');

        try {
            const data = await fetchProducts(currentParams);
            renderProducts(data);
        } catch (error) {
            console.error('[Canopy AJAX Products Error]:', error);
        } finally {
            $wrapper.removeClass('is-loading');
        }
    }

    /**
     * Attach pagination link click handlers.
     */
    function attachPaginationHandlers() {
        $(document).off('click.canopyPagination', '.canopy-ajax-pagination a.page-numbers, .woocommerce-pagination a.page-numbers');
        $(document).on('click.canopyPagination', '.canopy-ajax-pagination a.page-numbers, .woocommerce-pagination a.page-numbers', function (e) {
            e.preventDefault();
            const href = $(this).attr('href');
            if (!href) return;

            let page = 1;
            const matches = href.match(/paged?[\/=](\d+)/);
            if (matches && matches[1]) {
                page = parseInt(matches[1], 10);
            }

            currentParams.page = page;
            loadProducts();
        });
    }

    /**
     * Attach orderby dropdown change handler.
     */
    function attachSortingHandler() {
        $(document).on('change', SELECTORS.orderbySelect, function (e) {
            e.preventDefault();
            currentParams.orderby = $(this).val();
            currentParams.page = 1;
            loadProducts();
        });

        $(document).on('submit', SELECTORS.orderbyForm, function (e) {
            e.preventDefault();
        });
    }

    /**
     * Prefetch next page results when user scrolls near the bottom of products list.
     */
    function setupPrefetchObserver() {
        if (!('IntersectionObserver' in window)) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const nextParams = Object.assign({}, currentParams, { page: currentParams.page + 1 });
                    fetchProducts(nextParams).catch(() => {});
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: '200px' });

        const lastProduct = document.querySelector(`${SELECTORS.productLoop} .product:last-child`);
        if (lastProduct) observer.observe(lastProduct);
    }

    /**
     * Initialize on DOM Ready.
     */
    $(function () {
        if ($(SELECTORS.productsContainer).length === 0 && $('.canopy-initial-skeleton').length === 0) return;

        attachSortingHandler();
        attachPaginationHandlers();
        setupPrefetchObserver();

        // Trigger initial async product load via REST API
        loadProducts();
    });

})(jQuery);
