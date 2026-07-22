<?php
/**
 * Hooks of Language.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Wishlist template.
 */
class Language {
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
	 * Return boolean language switcher
	 *
	 * @return void
	 */
	public static function language_status() {
		return apply_filters( 'wpml_active_languages', array() );
	}

	/**
	 * Print HTML of language switcher
	 * It requires plugin WPML installed
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function language_switcher( $display = 'list' ) {
		$languages = self::language_status();
		$lang_list = array();
		$current   = '';

		if ( empty( $languages ) ) {
			return;
		}

		\Ecomus\Theme::set_prop( 'popovers', 'language' );

		if( $display == 'list' ) {
			foreach ( (array) $languages as $code => $language ) {
				if ( ! $language['active'] ) {
					$lang_list[] = sprintf(
						'<li class="ecomus-language__menu-item %s"><a href="%s">%s</a></li>',
						esc_attr( $code ),
						esc_url( $language['url'] ),
						esc_html( $language['native_name'] )
					);
				} else {
					$current = $language;
					array_unshift( $lang_list, sprintf(
						'<li class="ecomus-language__menu-item %s active"><a href="%s">%s</a></li>',
						esc_attr( $code ),
						esc_url( $language['url'] ),
						esc_html( $language['native_name'] )
					) );
				}
			}

			if( $current && isset( $current['native_name'] ) ) {
				echo sprintf(
					'<div class="current em-font-medium">%s %s</div>',
					$current['native_name'],
					\Ecomus\Icon::get_svg('arrow-bottom')
				);
			}

			echo '<div class="currency-dropdown">';
			echo '<ul class="preferences-menu__item-child">';
				echo implode( "\n\t", $lang_list );
			echo '</ul>';
			echo '</div>';
		} else {
			?>
			<label><?php esc_html_e( 'Language', 'ecomus' ); ?></label>
			<select name="language" id="ecomus_language" class="language_select preferences_select">
				<?php
				foreach ( (array) $languages as $key => $language ) {
					$current_language = ! empty( $language['active'] ) ? esc_attr( $key ) : '';
					echo '<option value="' . esc_url( $language['url'] ) . '"' . selected( $current_language, esc_attr( $key ), false ) . '>' . esc_html( $language['native_name'] ) . '</option>';
				}
				?>
			</select>
			<?php
		}
	}
}
