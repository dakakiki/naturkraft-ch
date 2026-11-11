<?php
/**
 * acf.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nk_option( string $name, $default = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $name, 'option' );

	return ( null === $value || '' === $value || false === $value ) ? $default : $value;
}

function nk_detect_layouts( $post_id = null ): array {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	if ( null === $post_id ) {
		if ( ! is_singular() ) {
			return array();
		}

		$post_id = get_queried_object_id();
	}

	if ( empty( $post_id ) ) {
		return array();
	}

	$rows = get_field( 'layouts', $post_id, false );

	if ( empty( $rows ) || ! is_array( $rows ) ) {
		return array();
	}

	$layouts = array();

	foreach ( $rows as $row ) {
		if ( ! empty( $row['acf_fc_layout'] ) ) {
			$layouts[] = $row['acf_fc_layout'];
		}
	}

	return array_values( array_unique( $layouts ) );
}

function nk_page_has_layouts( $post_id ): bool {
	return ! empty( nk_detect_layouts( $post_id ) );
}

function nk_image( $image, string $size = 'large', array $attrs = array() ): string {
	$id = is_array( $image ) ? (int) ( $image['ID'] ?? 0 ) : (int) $image;

	if ( ! $id ) {
		return '';
	}

	if ( 'image/svg+xml' === get_post_mime_type( $id ) ) {
		$url = wp_get_attachment_url( $id );
		$alt = $attrs['alt'] ?? get_post_meta( $id, '_wp_attachment_image_alt', true );

		unset( $attrs['alt'] );

		$out = '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '"';

		foreach ( $attrs as $key => $value ) {
			$out .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}

		return $out . '>';
	}

	return wp_get_attachment_image( $id, $size, false, $attrs );
}

function nk_anchor( $value ): string {
	return sanitize_title( (string) $value );
}
