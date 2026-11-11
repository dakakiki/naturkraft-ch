<?php
/**
 * fonts.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', function () {
	$fonts = array(
		'SourceSansPro-Regular.woff2',
		'SourceSansPro-Black.woff2',
	);

	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( NK_THEME_URI . '/assets/fonts/SourceSansPro/' . $font )
		);
	}
}, 1 );
