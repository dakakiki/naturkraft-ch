<?php
/**
 * ACF field behaviour that cannot live in the JSON.
 *
 * @package naturKRAFT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The "Bereich" selects always offer exactly the areas of the contact form.
 */
add_filter( 'acf/load_field/name=bereich', function ( $field ) {

	$field['choices'] = array();

	foreach ( nk_bereiche() as $bereich ) {
		$field['choices'][ $bereich ] = $bereich;
	}

	return $field;
} );


/**
 * A reduced WYSIWYG toolbar for section texts.
 *
 * "Hervorhebung" (h3) is the bold 24px line the design uses under or between
 * paragraphs; everything else is plain running text.
 */
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


/**
 * Collapsed flexible content rows show their title, not just "Hero", "Text".
 */
add_filter( 'acf/fields/flexible_content/layout_title/name=layouts', function ( $title, $field, $layout, $i ) {

	$heading = get_sub_field( 'title' );

	if ( $heading ) {
		$title .= ' – <em>' . esc_html( wp_strip_all_tags( $heading ) ) . '</em>';
	}

	$anchor = get_sub_field( 'anchor' );

	if ( $anchor ) {
		$title .= ' <code>#' . esc_html( nk_anchor( $anchor ) ) . '</code>';
	}

	return $title;
}, 10, 4 );
