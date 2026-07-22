<?php
/**
 * Category settings
 *
 * @package Ecomus
 */

namespace Ecomus\Admin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Category settings
 */
class Category_Settings {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Placeholder image
	 *
	 * @since 1.0.0
	 * @var $placeholder_img_src
	 */
	public $placeholder_img_src;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		// Register custom post type and custom taxonomy
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		$this->placeholder_img_src = get_template_directory_uri() . '/images/placeholder.png';

		// Add form Category
		add_action( 'category_add_form_fields', array( $this, 'add_category_fields' ), 30 );
		add_action( 'category_edit_form_fields', array( $this, 'edit_category_fields' ), 20 );

		// Save fields
		add_action( 'created_term', array( $this, 'save_category_fields' ), 20, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 20, 3 );
	}

	/**
	 * Register admin scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_admin_scripts( $hook ) {
		$screen = get_current_screen();
		if ( ( $hook == 'edit-tags.php' &&  $screen->taxonomy == 'category' ) || ( $hook == 'term.php' &&  $screen->taxonomy == 'category' ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'ecomus_category_js', get_template_directory_uri() . "/assets/js/backend/product-cat.js", array( 'jquery', 'wp-color-picker' ), '20250329', true );
			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	/**
	 * Category thumbnail fields.
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_category_fields() {
		?>
		<hr/>
		<div class="form-field">
            <label><?php esc_html_e( 'Page Header Background', 'ecomus' ); ?></label>

            <div id="em_page_header_bg" class="em-page-header-bg">
                <ul class="em-cat-page-header-bg"></ul>
                <input type="hidden" id="em_page_header_bg_id" class="em_page_header_bg_id" name="em_page_header_bg_id"/>
                <button type="button"
                        data-delete="<?php esc_attr_e( 'Delete image', 'ecomus' ); ?>"
                        data-text="<?php esc_attr_e( 'Delete', 'ecomus' ); ?>"
                        class="upload_images_button button"><?php esc_html_e( 'Upload/Add Image', 'ecomus' ); ?></button>
            </div>
            <div class="clear"></div>
        </div>
		<div class="form-field">
            <label><?php esc_html_e( 'Page Header Text Color', 'ecomus' ); ?></label>
            <input type="text" id="em_page_header_text_color" class="em_page_header_text_color" name="em_page_header_text_color"/>
        </div>
		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $term Term (category) being edited
     *
	 * @return void
	 */
	public function edit_category_fields( $term ) {
		$page_header_bg_id = get_term_meta( $term->term_id, 'em_page_header_bg_id', true );
		$page_header_text_color = get_term_meta( $term->term_id, 'em_page_header_text_color', true );
		?>
		<tr class="form-field">
            <th scope="row" valign="top"><label><?php esc_html_e( 'Page Header Background', 'ecomus' ); ?></label></th>
            <td>
                <div id="em_page_header_bg" class="em-page-header-bg">
                    <ul class="em-cat-page-header-bg">
						<?php

						if ( $page_header_bg_id ) {
							$image = wp_get_attachment_thumb_url( $page_header_bg_id );
							?>
							<li class="image" data-attachment_id="<?php echo esc_attr( $page_header_bg_id ); ?>">
								<img src="<?php echo esc_url( $image ); ?>" width="100px" height="100px"/>
								<ul class="actions">
									<li>
										<a href="#" class="delete"
											title="<?php esc_attr_e( 'Delete image', 'ecomus' ); ?>"><?php esc_html_e( 'Delete', 'ecomus' ); ?></a>
									</li>
								</ul>
							</li>
							<?php
						}
						?>
                    </ul>
                    <input type="hidden" id="em_page_header_bg_id" class="em_page_header_bg_id" name="em_page_header_bg_id"
                           value="<?php echo esc_attr( $page_header_bg_id ); ?>"/>
                    <button type="button"
                            data-delete="<?php esc_attr_e( 'Delete image', 'ecomus' ); ?>"
                            data-text="<?php esc_attr_e( 'Delete', 'ecomus' ); ?>"
                            class="upload_images_button button"><?php esc_html_e( 'Upload/Add Image', 'ecomus' ); ?></button>
                </div>
                <div class="clear"></div>
            </td>
        </tr>
		<tr class="form-field">
            <th scope="row" valign="top"><label><?php esc_html_e( 'Page Header Text Color', 'ecomus' ); ?></label></th>
            <td>
                <input type="text" id="em_page_header_text_color" class="em_page_header_text_color" name="em_page_header_text_color" value="<?php echo esc_attr( $page_header_text_color ); ?>"/>
            </td>
        </tr>
		<?php
	}

	/**
	 * Save Category fields
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param string $taxonomy
     *
	 * @return void
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( 'category' === $taxonomy && function_exists( 'update_term_meta' ) ) {
			if ( isset( $_POST['em_page_header_bg_id'] ) ) {
				update_term_meta( $term_id, 'em_page_header_bg_id', $_POST['em_page_header_bg_id'] );
			}
			if ( isset( $_POST['em_page_header_text_color'] ) ) {
				update_term_meta( $term_id, 'em_page_header_text_color', $_POST['em_page_header_text_color'] );
			}
		}

	}
}
