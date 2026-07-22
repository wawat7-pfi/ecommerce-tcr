<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews" class="woocommerce-Reviews">
	<div class="ecomus-product-rating">
		<h2 class="woocommerce-Reviews-title">
			<?php
			$count = $product->get_review_count();
			if ( $count && wc_review_ratings_enabled() ) {
				/* translators: 1: reviews count 2: product name */
				$reviews_title = sprintf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'ecomus' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
				echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ); // WPCS: XSS ok.
			} else {
				esc_html_e( 'Customer Reviews', 'ecomus' );
			}
			?>
		</h2>
	<?php
	if ( have_comments() ) :
		$rating_count   = $product->get_rating_count();
		$review_count   = $product->get_review_count();
		$average        = $product->get_average_rating();
	?>
		<div class="ecomus-product-rating__wrapper">
			<?php echo wc_get_rating_html( $average, $rating_count ); ?>
			<div class="ecomus-product-rating__count"><?php echo sprintf( _n( 'Based on %s review', 'Based on %s reviews', $review_count, 'ecomus' ), esc_html( $review_count ) ); ?></div>
		</div>
		<button class="ecomus-form-review em-button-outline" type="button" data-toggle="modal" data-target="ecomus-review-form-modal"><?php echo esc_html__( 'Write a review', 'ecomus' ); ?></button>
		<?php else : ?>
			<p class="woocommerce-noreviews"><?php esc_html_e( 'No reviews yet.', 'ecomus' ); ?></p>
		<?php endif; ?>
	</div>
	<div id="comments">
		<?php if ( have_comments() ) : ?>
			<ol class="commentlist">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
			</ol>

			<?php
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links(
					apply_filters(
						'woocommerce_comment_pagination_args',
						array(
							'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
							'next_text' => is_rtl() ? '&larr;' : '&rarr;',
							'type'      => 'list',
						)
					)
				);
				echo '</nav>';
			endif;
			?>
		<?php else : ?>
			<button class="ecomus-form-review em-button-outline" type="button" data-toggle="modal" data-target="ecomus-review-form-modal"><?php echo esc_html__( 'Write a review', 'ecomus' ); ?></button>
		<?php endif; ?>
	</div>
	<div class="clear"></div>
</div>
