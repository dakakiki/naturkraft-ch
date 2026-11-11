<?php
/**
 * Template helpers: icons, the inquiry areas, buttons.
 *
 * @package naturKRAFT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The eight inquiry areas of the contact form.
 *
 * Single source of truth: the CF7 radio list, the ACF "Bereich" selects on
 * the call-to-action buttons and the area-specific hints all read this.
 * Order matters - the dropdown fills its two columns top to bottom.
 */
function nk_bereiche(): array {
	return array(
		'Gastronomie und Hotellerie',
		'Handel',
		'Landwirtschaft',
		'Verarbeitung und Bündelung',
		'Marken und Unternehmen',
		'Förderverein und Förderung',
		'Investition in die AG',
		'Sonstiges',
	);
}


/**
 * Inline SVG icons from the XD file, drawn with currentColor.
 */
function nk_icon( string $name, string $class = '' ): string {

	$icons = array(
		/* "arrow2" - the diagonal arrow next to the collaboration links */
		'arrow-link' => '<svg viewBox="0 0 1080 1080" width="16" height="16"><polygon fill="currentColor" points="0 974.69 825.74 148.93 350.18 148.93 350.18 0 1080 0 1080 729.82 931.07 729.82 931.07 254.26 105.31 1080 0 974.69"/></svg>',

		/* "Arrow 1" - chevron, points up; rotated in CSS where it has to point down */
		'chevron'    => '<svg viewBox="0 0 1000 1000" width="20" height="20"><polyline fill="none" stroke="currentColor" stroke-width="60" points="225.37 621.03 500 346.39 774.63 621.03"/></svg>',

		/* "correct" - the tick in the area dropdown */
		'check'      => '<svg viewBox="0 0 1000 1000" width="20" height="20"><polyline fill="none" stroke="currentColor" stroke-width="80" points="761.97 187.23 428.21 726.02 238.03 451.39"/></svg>',

		'menu'       => '<svg viewBox="0 0 40 21" width="32" height="17"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><line x1="1" y1="1" x2="39" y2="1"/><line x1="1" y1="10.5" x2="39" y2="10.5"/><line x1="1" y1="20" x2="39" y2="20"/></g></svg>',

		'close'      => '<svg viewBox="0 0 24 24" width="22" height="22"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><line x1="3" y1="3" x2="21" y2="21"/><line x1="21" y1="3" x2="3" y2="21"/></g></svg>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	$svg = $icons[ $name ];

	$class = trim( 'nk-icon nk-icon--' . $name . ' ' . $class );

	return str_replace(
		'<svg ',
		'<svg class="' . esc_attr( $class ) . '" aria-hidden="true" focusable="false" ',
		$svg
	);
}


/**
 * <a> for an ACF link field.
 *
 * @param array|null $link  ACF link array (url, title, target).
 * @param string     $class Button classes.
 * @param array      $extra Extra attributes.
 */
function nk_link( $link, string $class = '', array $extra = array() ): string {

	if ( empty( $link['url'] ) || empty( $link['title'] ) ) {
		return '';
	}

	$atts = array_merge( array(
		'class' => $class,
		'href'  => $link['url'],
	), $extra );

	if ( ! empty( $link['target'] ) && '_self' !== $link['target'] ) {
		$atts['target'] = $link['target'];
		$atts['rel']    = 'noopener';
	}

	return sprintf( '<a %s>%s</a>', nk_atts( $atts ), esc_html( $link['title'] ) );
}


/**
 * Call to action that jumps to the contact form and preselects an area.
 *
 * The area is passed in data-nk-bereich; layout_contact.js picks it up. The
 * link itself is a plain in-page anchor, so without JavaScript it still lands
 * on the form.
 */
function nk_bereich_cta( string $label, string $bereich, string $target, string $class, string $after = '' ): string {

	if ( '' === trim( $label ) ) {
		return '';
	}

	$target = '' !== trim( $target ) ? trim( $target ) : '#kontakt';

	$atts = array(
		'class' => $class,
		'href'  => $target,
	);

	if ( '' !== $bereich && in_array( $bereich, nk_bereiche(), true ) ) {
		$atts['data-nk-bereich'] = $bereich;
	}

	return sprintf(
		'<a %s><span>%s</span>%s</a>',
		nk_atts( $atts ),
		esc_html( $label ),
		$after
	);
}


/**
 * Attribute array → escaped attribute string. Empty values are dropped.
 */
function nk_atts( array $atts ): string {

	$out = array();

	foreach ( $atts as $key => $value ) {

		if ( null === $value || false === $value || '' === $value ) {
			continue;
		}

		if ( 'href' === $key || 'src' === $key ) {
			$value = esc_url( $value );
		} else {
			$value = esc_attr( $value );
		}

		$out[] = sprintf( '%s="%s"', esc_attr( $key ), $value );
	}

	return implode( ' ', $out );
}


/**
 * Rich text from an ACF WYSIWYG field.
 *
 * Empty paragraphs that editors use as spacers are dropped - the spacing is
 * in the stylesheet, and the two would add up.
 */
function nk_richtext( $html ): string {

	$html = (string) $html;
	$html = preg_replace( '#<p>(\s|&nbsp;|<br\s*/?>)*</p>#i', '', $html );

	return wp_kses_post( trim( $html ) );
}


/**
 * Plain text with editor line breaks kept (ACF textarea, "new_lines" off).
 */
function nk_lines( $text ): string {
	return nl2br( esc_html( trim( (string) $text ) ), false );
}
