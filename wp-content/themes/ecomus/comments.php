<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Ecomus
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

?>
<div id="comments" class="comments-area ">
	<div class="comments-content">
	<?php
	// You can start editing here -- including this comment!
		if ( have_comments() ) :
			$comments_number = get_comments_number();
			$comments_class = $comments_number ? 'has-comments' : '';
		?>
			<h2 class="comments-title <?php echo esc_attr( $comments_class ); ?>">
				<?php
				printf( // WPCS: XSS OK.
					esc_html( _nx( 'Comment (%1$s)', 'Comments (%1$s)', $comments_number, 'comments title', 'ecomus' ) ),
					number_format_i18n( $comments_number )
				);
				?>
			</h2><!-- .comments-title -->

			<ol class="comment-list <?php echo esc_attr( $comments_class ); ?>">
				<?php
				wp_list_comments( array(
					'avatar_size' => 44,
					'short_ping'  => true,
					'style'       => 'ol'
				) );
				?>
			</ol><!-- .comment-list -->
			<?php
				paginate_comments_links();

		endif; // Check for have_comments().

		if ( ! comments_open() ) {
			echo '<p class="no-comments">' . esc_html__( 'Comments are closed.', 'ecomus' ) . '</p>';
		} else {
			$comment_field = '<p class="comment-form-comment"><span>' . esc_attr__( 'Comment', 'ecomus' ) . '</span><textarea required id="comment" name="comment" cols="45" rows="7" aria-required="true"></textarea></p>';
			$submit_button = '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>';
			comment_form(
				array(
					'format'        => 'xhtml',
					'comment_field' => $comment_field,
					'submit_button' => $submit_button
				)
			);
		}
	?>
	</div>
</div>
