<?php
/**
 * options-page.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page( array(
		'page_title' => __( 'Theme Settings', 'naturkraft' ),
		'menu_title' => __( 'Theme Settings', 'naturkraft' ),
		'menu_slug'  => 'theme-settings',
		'capability' => 'edit_pages',
		'position'   => 59,
		'icon_url'   => 'dashicons-admin-generic',
		'redirect'   => false,
		'autoload'   => true,
	) );
} );
