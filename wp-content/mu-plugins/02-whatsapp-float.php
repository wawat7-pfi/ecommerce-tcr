<?php
/** Floating WhatsApp button (like official). Number editable via option 'canopy_wa_number'. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
add_action( 'wp_footer', function () {
	$num = preg_replace( '/\D/', '', get_option( 'canopy_wa_number', '6285123343460' ) );
	if ( ! $num ) { return; }
	$url = 'https://wa.me/' . $num;
	echo '<a class="canopy-wa" href="' . esc_url( $url ) . '" target="_blank" rel="noopener" aria-label="Contact us on WhatsApp">'
	   . '<svg viewBox="0 0 32 32" width="26" height="26" fill="#fff"><path d="M16 3C9.4 3 4 8.4 4 15c0 2.1.6 4.2 1.6 6L4 29l8.2-1.6c1.7.9 3.7 1.4 5.8 1.4 6.6 0 12-5.4 12-12S22.6 3 16 3zm0 21.8c-1.8 0-3.6-.5-5.1-1.4l-.4-.2-4.9 1 1-4.8-.2-.4C5.5 18.4 5 16.7 5 15 5 8.9 9.9 4 16 4s11 4.9 11 11-4.9 10.8-11 9.8zm6-8.2c-.3-.2-1.9-.9-2.2-1-.3-.1-.5-.2-.7.2s-.8 1-.9 1.2c-.2.2-.3.2-.6.1-1.8-.9-3-1.6-4.2-3.6-.3-.5.3-.5.8-1.6.1-.2 0-.4 0-.5s-.7-1.7-1-2.3c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-1 1-1.3 2.3-.7 3.9.9 2.4 2.6 4 4.9 5.2 3.3 1.7 3.3.9 3.9.9.7 0 1.9-.8 2.2-1.5.3-.7.3-1.3.2-1.5-.1-.1-.3-.2-.6-.3z"/></svg>'
	   . '<span class="canopy-wa__txt">Contact us</span></a>';
}, 99 );
add_action( 'wp_head', function () {
	echo '<style>.canopy-wa{position:fixed;left:20px;bottom:20px;z-index:9998;display:flex;align-items:center;gap:8px;background:#25d366;color:#fff;padding:10px 16px 10px 12px;border-radius:40px;box-shadow:0 4px 14px rgba(0,0,0,.2);font-size:14px;text-decoration:none;line-height:1}.canopy-wa:hover{background:#20bd5a;color:#fff}.canopy-wa__txt{font-weight:500}@media(max-width:600px){.canopy-wa__txt{display:none}.canopy-wa{padding:12px;border-radius:50%}}</style>';
}, 99 );
