<?php
/**
 * enqueue.php
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

function nk_dist_ver( string $rel ): ?int {
	$path = nk_dist_path( $rel );

	return file_exists( $path ) ? filemtime( $path ) : null;
}

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

	if ( array_intersect( array( 'layout_contact', 'layout_newsletter' ), $layouts ) && function_exists( 'wpcf7_enqueue_scripts' ) ) {
		wpcf7_enqueue_scripts();
	}

	if ( in_array( 'layout_contact', $layouts, true ) ) {
		wp_enqueue_script( 'nk-layout-contact', nk_dist_uri( 'js/layout_contact.min.js' ), array(), nk_dist_ver( 'js/layout_contact.min.js' ), array(
			'in_footer' => true,
			'strategy'  => 'defer',
		) );
	}
}, 20 );
