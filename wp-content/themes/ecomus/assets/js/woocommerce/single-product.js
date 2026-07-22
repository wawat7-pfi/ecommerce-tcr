(function ($) {
	'use strict';

	var ecomus = ecomus || {};
	ecomus.init = function () {
		ecomus.$body = $(document.body),
			ecomus.$window = $(window),
			ecomus.$header = $('#site-header');

		this.singleProductGallery();
		this.productImageZoom();
		ecomus.$body.on( 'ecomus_product_gallery_zoom', function(){
			ecomus.productImageZoom();
		} );

		this.productLightBox();
		this.productAffiliate();

		this.ecomusMore();

		this.productTabsDropdown();
		this.upsellsProductCarousel();
		this.relatedProductCarousel();
	};

	/**
	 * Product Gallery
	 */
	ecomus.productGallery = function ( vertical, $selector = $('.woocommerce-product-gallery') ) {
		if (typeof Swiper === 'undefined') {
			return;
		}

		var $window = $( window );
		var slider = null;
		var thumbs = null;

		function initSwiper( $el, options ) {
			if( $el.length < 1 ) {
				return;
			}

			return new Swiper( $el.get(0), options );
		}

		function enableSwiper( el ) {
			el.enable();
		}

		function disableSwiper( el ) {
			if(el){
				el.disable();
			}
		}

		function galleryOptions( $el ) {
			var options = {
				loop: false,
				autoplay: false,
				speed: 800,
				spaceBetween: 30,
				watchOverflow: true,
				autoHeight: true,
				navigation: {
					nextEl: $el.find('.swiper-button-next').get(0),
					prevEl: $el.find('.swiper-button-prev').get(0),
				},
				on: {
					init: function () {
						setTimeout(function () {
							$el.css('opacity', 1);
						}, 100 );

						ecomus.$body.trigger( 'ecomus_product_gallery_init' );
					},
					slideChange: function () {
						if( this.slides[this.realIndex].getAttribute( 'data-zoom_status' ) == 'false' ) {
							this.$el.parent().addClass( 'swiper-item-current-extra' );
						} else {
							if( this.$el.parent().hasClass( 'swiper-item-current-extra' ) ) {
								this.$el.parent().removeClass( 'swiper-item-current-extra' );
							}
						}
					},
					slideChangeTransitionEnd: function () {
						ecomus.$body.trigger( 'ecomus_product_gallery_slideChangeTransitionEnd' );
					}
				}
			};

			if( thumbs ) {
				options.thumbs = {
					swiper: thumbs,
				};
			}

			return options;
		}

		function initGallery() {
			var $gallery = $selector.find('.woocommerce-product-gallery__wrapper');

			$gallery.addClass('woocommerce-product-gallery__slider swiper');
			$gallery.wrapInner('<div class="swiper-wrapper"></div>');
			$gallery.find('.swiper-wrapper').after('<span class="ecomus-svg-icon em-button-light ecomus-swiper-button swiper-button swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.5 11L0 5.5L5.5 0L6.47625 0.97625L1.9525 5.5L6.47625 10.0238L5.5 11Z" fill="currentColor"/></svg></span>');
			$gallery.find('.swiper-wrapper').after('<span class="ecomus-svg-icon em-button-light ecomus-swiper-button swiper-button swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 11L7 5.5L1.5 0L0.52375 0.97625L5.0475 5.5L0.52375 10.0238L1.5 11Z" fill="currentColor"/></svg></span>');
			$gallery.find('.woocommerce-product-gallery__image').addClass('swiper-slide');

			return initSwiper( $gallery, galleryOptions( $gallery ) );
		}

		function thumbnailsOptions( $el ) {
			var options = {
				spaceBetween: 10,
				watchOverflow: true,
				watchSlidesProgress: true,
				autoHeight: true,
				mousewheel: {
					forceToAxis: true,
					sensitivity: 1,
				},
				on: {
					beforeInit: function () {
						var $index = 1;
						$el.find('.swiper-slide').each( function () {
							$(this).parent().find('.swiper-slide:nth-child(' + $index + ')').css('--animation-delay', ( ( $index * 3 ) / 10 ) + 's' );
							$index++;
						});
					},
					init: function () {
						setTimeout(function () {
							$el.css('opacity', 1);
						}, 100 );

						ecomus.$body.trigger( 'ecomus_product_thumbnails_init' );
					},
				},
			};

			if (vertical) {
				options.breakpoints = {
									0: {
										direction: 'horizontal',
										slidesPerView: 5,
									},
									1200: {
										direction: 'vertical',
										slidesPerView: "auto",
									}
								};
			} else {
				options.direction = 'horizontal';
				options.slidesPerView = 5;
			}

			return options;
		}

		function initThumbnails() {
			var $thumbnails = $selector.find( '.ecomus-product-gallery-thumbnails' );

			$thumbnails.addClass('swiper');
			$thumbnails.wrapInner('<div class="woocommerce-product-thumbnail__nav swiper-wrapper"></div>');
			$thumbnails.find('.woocommerce-product-gallery__image').addClass('swiper-slide');

			return initSwiper( $thumbnails, thumbnailsOptions( $thumbnails ) );
		}

		function responsiveGallery() {
			if ( $window.width() < 1200 ) {
				enableSwiper( thumbs );
				enableSwiper( slider );
			} else {
				disableSwiper( thumbs );
				disableSwiper( slider );
			}
		}

		function init() {
			$selector.imagesLoaded(function () {
				var $thumbnails = $selector.find( '.ecomus-product-gallery-thumbnails' );
					$thumbnails.appendTo( $selector );

				thumbs = initThumbnails();
				slider = initGallery();

				if ( typeof ecomusData.product_gallery_slider !== 'undefined' && ! ecomusData.product_gallery_slider ) {
					$selector.addClass( 'woocommerce-product-gallery--reponsive' );

					responsiveGallery();
					$window.on( 'resize', function () {
						responsiveGallery();
					});
				}
			});
		}

		init();
	};

	/**
	 * Single Product Gallery
	 */
	ecomus.singleProductGallery = function () {
		var $gallery = $('div.product .woocommerce-product-gallery');

		if ( ! $gallery.length ) {
			return;
		}

		if( $gallery.hasClass( 'woocommerce-product-gallery--vertical' ) ) {
			$('.woocommerce-product-gallery').on('product_thumbnails_slider_vertical wc-product-gallery-after-init', function(){
				ecomus.productGallery(true);
			});
		} else {
			ecomus.productGallery(false);
			$('.woocommerce-product-gallery').on('product_thumbnails_slider_horizontal', function(){
				ecomus.productGallery(false);
			});
		}
	};

	/**
	 * Product Image Zoom
	 */
	ecomus.productImageZoom = function () {
		if (typeof Drift === 'undefined') {
			return;
		}

		var $selector = $('.product-gallery-summary');

		if( ! $selector ) {
			return;
		}

		if( ecomusData.product_image_zoom == 'none' ) {
			return;
		}

		var $summary   = $selector.find('.entry-summary'),
		    $gallery   = $selector.find('.woocommerce-product-gallery__wrapper');

		if( ecomusData.product_image_zoom == 'bounding' ) {
			var $zoom = $( '<div class="ecomus-product-zoom-wrapper" />' );
			$summary.prepend( $zoom );
		}

		var options = {
			containInline: true,
		};

		if( ecomusData.product_image_zoom == 'bounding' ) {
			options.paneContainer = $zoom.get(0);
			options.hoverBoundingBox = true;
			options.zoomFactor = 2;
		}

		if( ecomusData.product_image_zoom == 'inner' ) {
			options.zoomFactor = 3;
		}

		if( ecomusData.product_image_zoom == 'magnifier' ) {
			options.zoomFactor = 2;
			options.inlinePane = true;
		}

		$gallery.find( '.woocommerce-product-gallery__image' ).each( function() {
			var $this = $(this),
				$image = $this.find( 'img' ),
				imageUrl = $this.find( 'a' ).attr('href');

			if( $this.hasClass('ecomus-product-video') || $this.data( 'zoom_status' ) == false ) {
				return;
			}

			if( ecomusData.product_image_zoom == 'inner' ) {
				options.paneContainer = $this.get(0);
			}

			$image.attr( 'data-zoom', imageUrl );

			if( $image.length ) {
				new Drift( $image.get(0), options );
			}

		});

		$('.single-product div.product .product-gallery-summary .variations_form').on( 'show_variation hide_variation', function () {
			var $selector = $(this).closest( '.product-gallery-summary' ),
				$gallery = $selector.find( '.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image' ).eq(0),
				imageUrl = $gallery.find( 'a' ).attr( 'href' ),
				$image = $gallery.find( 'img' );

			$image.attr( 'data-zoom', imageUrl );
		});

		ecomus.$window.on( 'resize', function () {
			if( ecomus.$window.width() < 1200 ) {
				if( ! $( '.single-product div.product .woocommerce-product-gallery' ).hasClass( 'woocommerce-product-gallery--has-zoom' ) ) {
					return;
				}

				var touch = false;

				$( '.woocommerce-product-gallery--has-zoom .woocommerce-product-gallery__image' ).on('touchstart', function() {
					touch = true;
				});

				$( '.woocommerce-product-gallery--has-zoom .woocommerce-product-gallery__image' ).on('touchmove', function() {
					touch = false;
				});

				$( '.woocommerce-product-gallery--has-zoom .woocommerce-product-gallery__image' ).on('touchend', function() {
					if ( touch ) {
						$(this).addClass( 'zoom-enable' );
					} else {
						$(this).removeClass( 'zoom-enable' );
					}
				});
			}
		});
	};

	/**
 	 * Ecomus More
 	 */
	ecomus.ecomusMore = function () {
		var $selector =  $(document).find( '.short-description__content' );

			$selector.each( function () {
				if( jQuery(this)[0].scrollHeight > jQuery(this)[0].clientHeight ) {
					jQuery(this).siblings( '.short-description__more' ).removeClass( 'hidden' );
				}
			});

		$( document.body ).on( 'click', '.short-description__more', function(e) {
			e.preventDefault();

			var $settings = $(this).data( 'settings' ),
				$more     = $settings.more,
				$less     = $settings.less;

			if( $(this).hasClass( 'less' ) ) {
				$(this).removeClass( 'less' );
				$(this).text( $more );
				$(this).siblings( '.short-description__content' ).removeAttr( 'style' );
			} else {
				$(this).addClass( 'less' );
				$(this).text( $less );
				$(this).siblings( '.short-description__content' ).css( '-webkit-line-clamp', 'inherit' );
			}
		});
	}

	/**
	 * Product Light Box
	 */
	ecomus.productLightBox = function () {
		var $selector = $('.woocommerce-product-gallery');

		$('.woocommerce-product-gallery__image').on( 'click', 'a', function (e) {
			return false;
		});

		if( ! $selector ) {
			return;
		}

		if( ! ecomusData.product_image_lightbox ) {
			return
		}
		lightBoxButton();
		ecomus.$body.on( 'ecomus_product_gallery_lightbox', function(){
			lightBoxButton();
		} );

		$(document).on( 'click', '.ecomus-button--product-lightbox', function (e) {
			e.preventDefault();

			var pswpElement = $( '.pswp' )[0],
				items       = getGalleryItems( $(this).siblings( '.woocommerce-product-gallery__wrapper' ).find( '.woocommerce-product-gallery__image' ) ),
				clicked = $(this).siblings( '.woocommerce-product-gallery__wrapper' ).find( '.swiper-slide-active' );

			var options = $.extend( {
				index: $( clicked ).index(),
				addCaptionHTMLFn: function( item, captionEl ) {
					if ( ! item.title ) {
						captionEl.children[0].textContent = '';
						return false;
					}
					captionEl.children[0].textContent = item.title;
					return true;
				}
			}, wc_single_product_params.photoswipe_options );

			// Initializes and opens PhotoSwipe.
			var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
			photoswipe.init();
		});

		function lightBoxButton() {
			$('.woocommerce-product-gallery__image').on( 'click', 'a', function (e) {
				return false;
			});

			$selector.append('<a href="#" class="ecomus-button--product-lightbox em-flex em-flex-align-center em-flex-center"><span class="ecomus-svg-icon ecomus-svg-icon--fullscreen"><svg width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.672 17.0111L17.672 12.0526L16.4091 12.0526L16.4091 15.4846L11.5285 10.604L10.6352 11.4972L15.5158 16.3778L12.082 16.3796L12.082 17.6425L17.0405 17.6425C17.3878 17.6407 17.6701 17.3584 17.672 17.0111ZM2.48608 16.3778L5.91808 16.3778L5.91808 17.6407L0.961377 17.6425C0.78679 17.6425 0.628951 17.5701 0.515665 17.4568C0.40053 17.3417 0.329985 17.1856 0.329948 17.0111L0.329912 12.0525H1.59277V15.4845L6.47335 10.6039L7.36662 11.4972L2.48604 16.3778L2.48608 16.3778ZM15.514 1.56337H12.082L12.0819 0.300476L17.0405 0.300512C17.1234 0.300625 17.2054 0.317088 17.2819 0.348957C17.3584 0.380826 17.4278 0.427474 17.4862 0.486229C17.6032 0.603249 17.6719 0.764822 17.6719 0.931941L17.6701 5.88864L16.4072 5.88864L16.4072 2.45664L11.5267 7.33722L10.6334 6.44395L15.514 1.56337ZM6.47523 7.33722L7.3685 6.44395L2.48608 1.56152L5.91993 1.56337L5.91808 0.298663L0.961377 0.300512C0.878435 0.300435 0.796292 0.316715 0.719649 0.34842C0.643005 0.380126 0.573367 0.426633 0.514718 0.485282C0.456069 0.543931 0.409562 0.613569 0.377857 0.690212C0.346151 0.766856 0.329871 0.848999 0.329948 0.931941L0.328062 5.88868L1.59277 5.89053L1.59281 2.45479L6.47523 7.33722Z"></path></svg></span></a>')
		}

		function getGalleryItems( $slides ) {
			var items = [];

			if ( $slides.length > 0 ) {
				$slides.each( function( i, el ) {
					var img = $( el ).find( 'img' );

					if ( $( el ).hasClass('ecomus-product-video') ) {
						var video = $( el ).find('.ecomus-video-wrapper').html();

						if( video.length ) {
							var item = {
								html: '<div class="pswp__video">'+ video +'</div>'
							};
							items.push( item );
						}
					} else if ( img.length ) {
						var large_image_src = img.attr( 'data-large_image' ),
							large_image_w   = img.attr( 'data-large_image_width' ),
							large_image_h   = img.attr( 'data-large_image_height' ),
							alt             = img.attr( 'alt' ),
							item            = {
								alt  : alt,
								src  : large_image_src,
								w    : large_image_w,
								h    : large_image_h,
								title: img.attr( 'data-caption' ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
							};
						items.push( item );
					}
				} );
			}

			return items;
		};
	};

	/**
	 * Product tabs dropdown
	 */
	ecomus.productAffiliate = function () {
		if( ecomusData.product_affiliate_new_tab === '' ) {
			return;
		}

		var $button = $('.ecomus-product-type-external .single_add_to_cart_button');

		if ( $button.length ) {
			$button.off('click.ecomusAffiliate').on('click.ecomusAffiliate', function (e) {
				e.preventDefault();

				var url = $(this).closest('form.cart').attr('action');
				if ( url ) {
					window.open(url, '_blank');
				}
			});
		}
	};

	/**
	 * Product tabs dropdown
	 */
	ecomus.productTabsDropdown = function () {
		var $productTabs = $( '.woocommerce-tabs--dropdown' );

		if( ! $productTabs ) {
			return;
		}

		if( ecomusData.product_tabs_layout !== 'accordion' ) {
			return;
		}

		$productTabs.on( 'click', '.woocommerce-tabs-title', function() {
			if( $(this).hasClass('active') ) {
				if( $(this).closest('.woocommerce-tabs--dropdown').hasClass('wc-tabs-first--opened') ) {
					$(this).closest('.woocommerce-tabs--dropdown').removeClass('wc-tabs-first--opened');
				}

				$(this).removeClass('active');
				$(this).siblings('.woocommerce-tabs-content').slideUp(200);
			} else {
				$(this).addClass('active');
				$(this).siblings('.woocommerce-tabs-content').slideDown(200);
			}
		});

		$( 'a.woocommerce-review-link' ).on('click', function() {
			$('#tab-reviews .woocommerce-tabs-title:not(.active)').trigger('click');
		});

		$(document).ready(function() {
			if ( window.location.href.indexOf( '#reviews' ) > -1 ) {
				$('#tab-reviews .woocommerce-tabs-title:not(.active)').trigger('click');
			}
		});
	};

	/**
	 * Upsells Product Carousel.
	 */
	ecomus.upsellsProductCarousel = function () {
		var $columns = ecomusData.upsells_products_columns;

		ecomus.productsCarousel( $('.products.upsells'), $columns );
	}

	/**
	 * Related Product Carousel.
	 */
	ecomus.relatedProductCarousel = function () {
		var $columns = ecomusData.related_products_columns;

		ecomus.productsCarousel( $('.products.related'), $columns );
	}

	/**
	 * Products Carousel.
	 */
	ecomus.productsCarousel = function ($productSection, $columns) {
		if ( !$productSection.length ) {
			return;
		}

		var $products = $productSection.find('ul.products');

		$products.wrap('<div class="products-carousel swiper ecomus-carousel--elementor" data-spacing="30"></div>');
		$products.after('<div class="swiper-pagination swiper-pagination-bullet--small"></div>');
		$products.addClass('swiper-wrapper');
		$products.find('li.product').addClass('swiper-slide');

		if ( ecomusData.product_card_hover == 'slider' ) {
			$products.parent().after('<span class="ecomus-svg-icon swiper-button-outline-dark ecomus-swiper-button swiper-button swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.5 11L0 5.5L5.5 0L6.47625 0.97625L1.9525 5.5L6.47625 10.0238L5.5 11Z" fill="currentColor"/></svg></span>');
			$products.parent().after('<span class="ecomus-svg-icon swiper-button-outline-dark ecomus-swiper-button swiper-button swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 11L7 5.5L1.5 0L0.52375 0.97625L5.0475 5.5L0.52375 10.0238L1.5 11Z" fill="currentColor"/></svg></span>');
		} else {
			$products.after('<span class="ecomus-svg-icon swiper-button-light ecomus-swiper-button swiper-button swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.5 11L0 5.5L5.5 0L6.47625 0.97625L1.9525 5.5L6.47625 10.0238L5.5 11Z" fill="currentColor"/></svg></span>');
			$products.after('<span class="ecomus-svg-icon swiper-button-light ecomus-swiper-button swiper-button swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 11L7 5.5L1.5 0L0.52375 0.97625L5.0475 5.5L0.52375 10.0238L1.5 11Z" fill="currentColor"/></svg></span>');
		}

		var $productCarousel = $products.closest('.products-carousel'),
			$productThumbnail = $products.find( '.product-thumbnail' );

		var options = {
			loop: false,
			autoplay: false,
			speed: 800,
			watchSlidesVisibility: true,
			watchOverflow: true,
			navigation: {
				nextEl: $productSection.find('.ecomus-swiper-button.swiper-button-next').get(0),
				prevEl: $productSection.find('.ecomus-swiper-button.swiper-button-prev').get(0),
			},
			pagination: {
				el: $productCarousel.find('.swiper-pagination').get(0),
				type: 'bullets',
				clickable: true,
			},
			on: {
				init: function () {
					this.$el.css('opacity', 1);
				},
				beforeInit: function () {
					var self = this;

					if( $productThumbnail.length > 0 ) {
						$productThumbnail.imagesLoaded(function () {
							var	heightThumbnails = $productThumbnail.outerHeight(),
								heading = $productSection.find('h2').first(),
								top = ( heightThumbnails / 2 );

							if ( ecomusData.product_card_hover === 'slider' ) {
								$productSection.addClass('em-product-hover--slider');

								if ( heading.length ) {
									var headingHeight = heading.outerHeight();

									top = top + headingHeight;
								}
							} else {
								top = ( ( heightThumbnails / 2 ) + 15 );
							}

							$(self.navigation.$nextEl).css({'--em-arrow-top': top + 'px'});
							$(self.navigation.$prevEl).css({'--em-arrow-top': top + 'px'});
						});
					}
				},
				resize: function () {
					var self = this;

					if( $productThumbnail.length > 0 ) {
						$productThumbnail.imagesLoaded(function () {
							var	heightThumbnails = $productThumbnail.outerHeight(),
								heading = $productSection.find('h2').first(),
								top = ( heightThumbnails / 2 );

							if ( ecomusData.product_card_hover === 'slider' ) {
								$productSection.addClass('product-hover--slider');

								if ( heading.length ) {
									var headingHeight = heading.outerHeight();

									top = top + headingHeight;
								}
							} else {
								top = ( ( heightThumbnails / 2 ) + 15 );
							}

							$(self.navigation.$nextEl).css({'--em-arrow-top': top + 'px'});
							$(self.navigation.$prevEl).css({'--em-arrow-top': top + 'px'});
						});
					}
				}
			},
			spaceBetween: $productCarousel.data('spacing'),
			breakpoints: {
				300: {
					slidesPerView: $columns.mobile,
					slidesPerGroup: 2,
					spaceBetween: 15,
				},
				768: {
					slidesPerView: $columns.tablet,
					spaceBetween: $productCarousel.data('spacing'),
				},
				1200: {
					slidesPerView: $columns.desktop,
				},
			}
		};

		new Swiper( $productCarousel.get(0), options );
	}

	/**
	 * Document ready
	 */
	ecomus.init();

})(jQuery);