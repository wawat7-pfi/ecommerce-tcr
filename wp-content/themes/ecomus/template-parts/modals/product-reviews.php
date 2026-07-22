<?php
/**
 * Template part for displaying the product reviews
 *
 * @package Ecomus
 */

?>

<div id="ecomus-review-form-modal" class="ecomus-review-form-wrapper modal">
	<div class="modal__backdrop"></div>
	<div class="ecomus-review-form__content modal__container">
		<div class="modal__wrapper">
			<span class="ecomus-review-form-wrapper__close modal__button-close"><?php echo \Ecomus\Icon::get_svg( 'close' ); ?></span>
			<?php
			global $product;
			$review_product_id = $product instanceof WC_Product ? $product->get_id() : get_the_ID();
			$can_leave_review  = get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $review_product_id );
			?>
			<?php if ( $can_leave_review ) : ?>
				<div id="review_form_wrapper" class="modal__content">
					<div id="review_form">
						<?php
						$commenter    = wp_get_current_commenter();
						$comment_form = array(
							/* translators: %s is product title */
							'title_reply'         => have_comments() ? esc_html__( 'Write a product review', 'ecomus' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'ecomus' ), get_the_title() ),
							/* translators: %s is product title */
							'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'ecomus' ),
							'title_reply_before'  => '<span id="reply-title" class="comment-reply-title">',
							'title_reply_after'   => '</span>',
							'comment_notes_after' => '',
							'label_submit'        => esc_html__( 'Submit Review', 'ecomus' ),
							'class_submit'        => esc_attr( 'submit ecomus-button ecomus-button--color-black' ),
							'logged_in_as'        => '',
							'comment_field'       => '',
						);

						$name_email_required = (bool) get_option( 'require_name_email', 1 );
						$fields              = array(
							'author' => array(
								'label'    => __( 'Name', 'ecomus' ),
								'type'     => 'text',
								'value'    => $commenter['comment_author'],
								'required' => $name_email_required,
								'autocomplete' => 'name',
							),
							'email'  => array(
								'label'    => __( 'Email', 'ecomus' ),
								'type'     => 'email',
								'value'    => $commenter['comment_author_email'],
								'required' => $name_email_required,
								'autocomplete' => 'email',
							),
						);

						$comment_form['fields'] = array();

						foreach ( $fields as $key => $field ) {
							$field_html  = '<p class="comment-form-' . esc_attr( $key ) . '">';
							$field_html .= '<label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] );

							if ( $field['required'] ) {
								$field_html .= '&nbsp;<span class="required">*</span>';
							}

							$field_html .= '</label><input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" autocomplete="' . esc_attr( $field['autocomplete'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';

							$comment_form['fields'][ $key ] = $field_html;
						}

						$account_page_url = wc_get_page_permalink( 'myaccount' );
						if ( $account_page_url ) {
							/* translators: %s opening and closing link tags respectively */
							$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %slogged in%s to post a review.', 'ecomus' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
						}

						if ( wc_review_ratings_enabled() ) {
							$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'ecomus' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
								<option value="">' . esc_html__( 'Rate&hellip;', 'ecomus' ) . '</option>
								<option value="5">' . esc_html__( 'Perfect', 'ecomus' ) . '</option>
								<option value="4">' . esc_html__( 'Good', 'ecomus' ) . '</option>
								<option value="3">' . esc_html__( 'Average', 'ecomus' ) . '</option>
								<option value="2">' . esc_html__( 'Not that bad', 'ecomus' ) . '</option>
								<option value="1">' . esc_html__( 'Very poor', 'ecomus' ) . '</option>
							</select></div>';
						}

						$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'ecomus' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';
						$comment_form['format'] = 'xhtml';
						comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
						?>
					</div>
				</div>
			<?php else : ?>
				<p class="woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'ecomus' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>