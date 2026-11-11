<?php
/**
 * wp_editor.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', function () {
	remove_theme_support( 'core-block-patterns' );
} );

add_filter( 'should_load_remote_block_patterns', '__return_false' );

add_action( 'plugins_loaded', function () {
	remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
} );

add_filter( 'allowed_block_types_all', function( $allowed_blocks, $editor_context ) {
	return array(
		'core/paragraph',
		'core/heading',
		'core/list',
		'core/list-item',
		'core/quote',
		'core/image',

		'core/buttons',
		'core/button',

		'core/spacer',
		'core/missing',
	);
}, 10, 2 );

add_action( 'after_setup_theme', function () {
	add_theme_support( 'editor-color-palette', array() );
	add_theme_support( 'disable-custom-colors' );
	add_theme_support( 'editor-gradient-presets', array() );
	add_theme_support( 'disable-custom-gradients' );
	add_theme_support( 'editor-font-sizes', array() );
	add_theme_support( 'disable-custom-font-sizes' );

	add_theme_support( 'editor-styles' );
	add_editor_style( 'dist/css/editor.min.css' );
} );

add_filter( 'register_block_type_args', function ( $args, $name ) {
	if ( 'core/heading' === $name && isset( $args['attributes'] ) ) {
		$args['attributes']['levelOptions'] = array(
			'type'    => 'array',
			'default' => array( 2, 3, 4 ),
		);
	}

	return $args;
}, 10, 2 );

add_action( 'admin_head', function () {
	echo '<style>
		.editor-inserter__patterns,
		.block-editor-block-patterns-list,
		.components-tab-panel__tabs button[id*="pattern"],
		.block-editor-inserter__panel-header button[id*="pattern"],
		.block-editor-inserter__search,
		.block-editor-inserter__search-input,
		.components-search-control {
			display: none !important;
		}
	</style>';
} );
