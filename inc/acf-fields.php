<?php
/**
 * acf-fields.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'acf/load_field/name=bereich', function ( $field ) {
	$field['choices'] = array();

	foreach ( nk_bereiche() as $bereich ) {
		$field['choices'][ $bereich ] = $bereich;
	}

	return $field;
} );

add_filter( 'acf/fields/wysiwyg/toolbars', function ( $toolbars ) {
	$toolbars['naturKRAFT'] = array(
		1 => array( 'formatselect', 'bold', 'italic', 'underline', 'link', 'unlink', 'bullist', 'removeformat', 'undo', 'redo' ),
	);

	return $toolbars;
} );

add_filter( 'tiny_mce_before_init', function ( $init ) {
	$init['block_formats'] = 'Absatz=p;Hervorhebung=h3';

	return $init;
} );

add_filter( 'acf/fields/flexible_content/layout_title/name=layouts', function ( $title, $field, $layout, $i ) {
	$anchor = get_sub_field( 'anchor' );

	if ( $anchor ) {
		$title .= ' <code>#' . esc_html( nk_anchor( $anchor ) ) . '</code>';
	}

	return $title;
}, 10, 4 );
