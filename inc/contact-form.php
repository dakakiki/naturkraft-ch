<?php
/**
 * contact-form.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );

add_filter( 'wpcf7_autop_or_not', '__return_false' );

function nk_form_hints(): array {
	$default = nk_option( 'form_hint_default', 'Was suchen Sie, was bieten Sie an oder was möchten Sie entwickeln? Angaben zu Mengen, Region und Zeitraum helfen uns bei der Einordnung.' );

	return array(
		'default'                    => $default,
		'Förderverein und Förderung' => nk_option( 'form_hint_foerderung', 'Wie möchten Sie den Förderverein unterstützen?' ),
		'Investition in die AG'      => nk_option( 'form_hint_investition', 'Bitte beschreiben Sie kurz Ihr Interesse. Beteiligungsform und Konditionen besprechen wir persönlich.' ),
	);
}

function nk_form_hint_for( array $areas ): string {
	$hints   = nk_form_hints();
	$default = $hints['default'];
	$texts   = array();

	foreach ( nk_bereiche() as $area ) {
		if ( in_array( $area, $areas, true ) ) {
			$texts[] = $hints[ $area ] ?? $default;
		}
	}

	$texts = array_unique( $texts );

	return $texts ? implode( ' ', $texts ) : $default;
}

add_action( 'wpcf7_init', function () {
	wpcf7_add_form_tag( 'nk_hint', function ( $tag ) {
		$hints   = nk_form_hints();
		$default = $hints['default'];

		unset( $hints['default'] );

		$posted  = function_exists( 'wpcf7_get_hangover' ) ? wpcf7_get_hangover( 'bereich', array() ) : array();
		$current = nk_form_hint_for( array_map( 'strval', (array) $posted ) );

		return sprintf(
			'<p class="nk-form__hint" id="nk-form-hint" data-default="%1$s" data-hints="%2$s" aria-live="polite">%3$s</p>',
			esc_attr( $default ),
			esc_attr( wp_json_encode( $hints ) ),
			esc_html( $current )
		);
	} );

	wpcf7_add_form_tag( 'nk_privacy', function ( $tag ) {
		$note = nk_option( 'form_privacy_note' );

		if ( ! $note ) {
			$privacy_url = get_privacy_policy_url();

			$note = 'Wir verwenden Ihre Angaben zur Bearbeitung Ihrer Anfrage.<br>Weitere Informationen finden Sie in der '
				. ( $privacy_url ? '<a href="' . esc_url( $privacy_url ) . '">Datenschutzerklärung</a>' : 'Datenschutzerklärung' )
				. '.';
		}

		return '<div class="nk-form__privacy">' . wp_kses_post( $note ) . '</div>';
	} );
} );

function nk_cf7_fingerprint(): string {
	$submission = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null;

	if ( ! $submission ) {
		return '';
	}

	$data = $submission->get_posted_data();

	$parts = array(
		strtolower( trim( (string) ( $data['email'] ?? '' ) ) ),
		(string) ( is_array( $data['bereich'] ?? '' ) ? implode( ',', $data['bereich'] ) : ( $data['bereich'] ?? '' ) ),
		trim( (string) ( $data['anliegen'] ?? '' ) ),
	);

	return 'nk_cf7_' . md5( implode( '|', $parts ) );
}

add_filter( 'wpcf7_skip_mail', function ( $skip ) {
	$key = nk_cf7_fingerprint();

	if ( $key && get_transient( $key ) ) {
		return true;
	}

	return $skip;
} );

add_action( 'wpcf7_mail_sent', function () {
	$key = nk_cf7_fingerprint();

	if ( $key ) {
		set_transient( $key, 1, 10 * MINUTE_IN_SECONDS );
	}
} );
