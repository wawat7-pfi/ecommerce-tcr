<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$register_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$login_class = $register_enabled ? 'woocommerce-enable-register' : '';
$mode = $_GET && isset( $_GET['mode'] ) ? $_GET['mode'] : '';
$mode = $args && isset( $args['action']) ? $args['action'] : $mode;

do_action( 'woocommerce_before_customer_login_form' ); ?>
<?php if( empty( $mode ) || $mode == 'login' || $mode == 'popup' ): ?>
<div class="em-row woocommerce-customer-login active <?php echo esc_attr( $login_class ); ?>" id="customer_login">

	<div class="em-col em-xs-12 em-sm-6">

		<h2 class="woocommerce-customer-login__title"><?php esc_html_e( 'Login', 'ecomus' ); ?></h2>

		<form class="woocommerce-form woocommerce-form-login login" method="post">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username"><?php esc_html_e( 'Email address', 'ecomus' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password"><?php esc_html_e( 'Password', 'ecomus' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="woocommerce-form-row--remember">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'ecomus' ); ?></span>
				</label>
				<a class="woocommerce-lost-password" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'ecomus' ); ?></a>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--submit form-row">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'ecomus' ); ?>"><?php esc_html_e( 'Log in', 'ecomus' ); ?></button>

				<?php if( $register_enabled && $mode == 'popup' ): ?>
					<a href="#" class="em-button em-button-subtle em-font-semibold em-button-register-mode">
						<span class="ecomus-button-text"><?php esc_html_e('New customer? Create your account', 'ecomus');?></span>
						<?php echo \Ecomus\Icon::get_svg( 'arrow-top' ); ?>
					</a>
				<?php endif; ?>
			</p>

			<?php
				if( class_exists('NextendSocialLogin', false) && !class_exists('NextendSocialLoginPRO', false) ) {
					echo do_shortcode( '[nextend_social_login]' );
				}
			?>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

	</div>
	<?php if( $register_enabled && $mode != 'popup' ): ?>
	<div class="em-col em-xs-12 em-sm-6">

		<h2 class="woocommerce-customer-login__title"><?php esc_html_e( "I'm new here", 'ecomus' ); ?></h2>
		<div class="woocommerce-form-new">
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<?php esc_html_e('Sign up for early Sale access plus tailored new arrivals, trends and promotions. To opt out, click unsubscribe in our emails.', 'ecomus'); ?>
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) . '?mode=register' ) ?>" class="em-button em-button-subtle em-font-semibold">
					<span class="ecomus-button-text"><?php esc_html_e('Register', 'ecomus');?></span>
					<?php echo \Ecomus\Icon::get_svg( 'arrow-top' ); ?>
				</a>

			</p>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>
<?php if( $register_enabled && ($mode == 'register' || $mode == 'popup') ): ?>
<div class="woocommerce-customer-register">

	<h2><?php esc_html_e( 'Register', 'ecomus' ); ?></h2>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide form-row-register-desc">
		<?php esc_html_e('Sign up for early Sale access plus tailored new arrivals, trends and promotions. To opt out, click unsubscribe in our emails.', 'ecomus'); ?>
	</p>

	<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

		<?php do_action( 'woocommerce_register_form_start' ); ?>

		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_username"><?php esc_html_e( 'Username', 'ecomus' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
			</p>

		<?php endif; ?>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="reg_email"><?php esc_html_e( 'Email address', 'ecomus' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
			<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
		</p>

		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide form-row-password">
				<label for="reg_password"><?php esc_html_e( 'Password', 'ecomus' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
			</p>

		<?php else : ?>

			<p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'ecomus' ); ?></p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_register_form' ); ?>

		<p class="woocommerce-form-row form-row woocommerce-form--register-button text-center">
			<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
			<button type="submit" class="woocommerce-Button woocommerce-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'ecomus' ); ?>"><?php esc_html_e( 'Register', 'ecomus' ); ?></button>

			<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' )  . '?mode=login'  ) ?>" class="em-button em-button-subtle em-font-semibold em-button-login-mode">
				<span class="ecomus-button-text"><?php esc_html_e('Already have an account? Log in here', 'ecomus');?></span>
				<?php echo \Ecomus\Icon::get_svg( 'arrow-top' ); ?>
			</a>
		</p>

		<?php
			if( class_exists('NextendSocialLogin', false) && !class_exists('NextendSocialLoginPRO', false) ) {
				echo do_shortcode( '[nextend_social_login]' );
			}
		?>

		<?php do_action( 'woocommerce_register_form_end' ); ?>

	</form>
</div>
<?php endif; ?>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
