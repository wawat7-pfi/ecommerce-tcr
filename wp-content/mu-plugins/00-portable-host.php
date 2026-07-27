<?php
/**
 * Portable host + old-domain rewrite (for the migration demo).
 * Renders correctly on the IP (http) or tunnel URL (https), and rewrites any leftover
 * origin (new-site IP OR the old thecanopy-room.com domain) to the CURRENT request host
 * so links/images stay on THIS site instead of redirecting to the old WordPress.com site.
 */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}
if ( ! empty( $_SERVER['HTTP_HOST'] ) ) {
	$host   = $_SERVER['HTTP_HOST'];
	$scheme = ( ! empty( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) ? 'https' : 'http';
	$dyn    = $scheme . '://' . $host;

	add_filter( 'option_home',    function () use ( $dyn ) { return $dyn; } );
	add_filter( 'option_siteurl', function () use ( $dyn ) { return $dyn; } );

	// Only apply output buffering rewrite on frontend requests (skip admin, AJAX, REST API, CLI)
	if ( ! is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) && ! ( defined( 'DOING_CRON' ) && DOING_CRON ) && 'cli' !== php_sapi_name() ) {
		ob_start( function ( $html ) use ( $host, $scheme ) {
			$origins = array( '127.0.0.1', 'localhost', 'tcr-wordpress.test', '206.189.88.126', 'thecanopy-room.com' );
			$target  = $scheme . '://' . $host;
			foreach ( $origins as $o ) {
				if ( strpos( $html, $o ) === false ) { continue; }
				$quoted      = preg_quote( $o, '#' );
				$html        = preg_replace( '#(https?:)?//' . $quoted . '(?::\d+)*#i', $target, $html );
				$json_target = str_replace( '/', '\\/', $target );
				$html        = preg_replace( '#(https?:)?\\\\/\\\\/' . $quoted . '(?::\d+)*#i', $json_target, $html );
			}
			return $html;
		} );
	}
}
