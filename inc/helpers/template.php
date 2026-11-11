<?php
/**
 * template.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

function nk_icon( string $name, string $class = '' ): string {
	$icons = array(

		'arrow-link' => '<svg viewBox="0 0 1080 1080" width="16" height="16"><polygon fill="currentColor" points="0 974.69 825.74 148.93 350.18 148.93 350.18 0 1080 0 1080 729.82 931.07 729.82 931.07 254.26 105.31 1080 0 974.69"/></svg>',

		'chevron'    => '<svg viewBox="0 0 1000 1000" width="20" height="20"><polyline fill="none" stroke="currentColor" stroke-width="60" points="225.37 621.03 500 346.39 774.63 621.03"/></svg>',

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

function nk_richtext( $html ): string {
	$html = (string) $html;
	$html = preg_replace( '#<p>(\s|&nbsp;|<br\s*/?>)*</p>#i', '', $html );

	return wp_kses_post( trim( $html ) );
}

function nk_lines( $text ): string {
	return nl2br( esc_html( trim( (string) $text ) ), false );
}
