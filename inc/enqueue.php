<?php
/**
 * Front-end assets.
 *
 * Everything is served from dist/ (see package.json). Layout scripts load only
 * on pages that use the layout, and Contact Form 7 only where a form is shown.
 *
 * @package naturKRAFT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nk_dist_uri( string $rel ): string {
	return NK_THEME_URI . '/dist/' . ltrim( $rel, '/' );
}

function nk_dist_path( string $rel ): string {
	return NK_THEME_PATH . '/dist/' . ltrim( $rel, '/' );
}

/**
 * filemtime cache buster, null if the file has not been built yet.
 */
function nk_dist_ver( string $rel ): ?int {
	$path = nk_dist_path( $rel );

	return file_exists( $path ) ? filemtime( $path ) : null;
}


/* Gutenberg front-end CSS is not used - the content is styled by the theme. */
add_action( 'wp_enqueue_scripts', function () {

	if ( is_admin() ) {
		return;
	}

	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}, 100 );


add_action( 'wp_enqueue_scripts', function () {

	wp_enqueue_style( 'nk-style', nk_dist_uri( 'css/style.min.css' ), array(), nk_dist_ver( 'css/style.min.css' ) );

	wp_enqueue_script( 'nk-gsap', nk_dist_uri( 'js/gsap.min.js' ), array(), nk_dist_ver( 'js/gsap.min.js' ), array(
		'in_footer' => true,
		'strategy'  => 'defer',
	) );

	wp_enqueue_script( 'nk-custom', nk_dist_uri( 'js/custom.min.js' ), array( 'nk-gsap' ), nk_dist_ver( 'js/custom.min.js' ), array(
		'in_footer' => true,
		'strategy'  => 'defer',
	) );

	$layouts = nk_detect_layouts();

	if ( in_array( 'layout_contact', $layouts, true ) ) {

		/* CF7 is told not to load everywhere (see inc/contact-form.php). */
		if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
			wpcf7_enqueue_scripts();
		}

		wp_enqueue_script( 'nk-layout-contact', nk_dist_uri( 'js/layout_contact.min.js' ), array(), nk_dist_ver( 'js/layout_contact.min.js' ), array(
			'in_footer' => true,
			'strategy'  => 'defer',
		) );
	}

	if ( in_array( 'layout_newsletter', $layouts, true ) ) {

		wp_enqueue_script( 'nk-layout-newsletter', nk_dist_uri( 'js/layout_newsletter.min.js' ), array(), nk_dist_ver( 'js/layout_newsletter.min.js' ), array(
			'in_footer' => true,
			'strategy'  => 'defer',
		) );
	}
}, 20 );
