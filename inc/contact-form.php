<?php
/**
 * Contact Form 7 integration.
 *
 * - CF7 assets load only on pages with the contact layout (inc/enqueue.php),
 *   and the theme brings its own styles.
 * - [nk_hint] and [nk_privacy] form-tags print texts from Theme Settings, so
 *   the editor never has to touch the form markup to change them.
 * - A repeated submission of the same inquiry is acknowledged without a
 *   second e-mail (protection against double sending).
 *
 * The form markup itself is in inc/cf7/kontaktformular.html - the reference
 * copy of what is stored in the CF7 form.
 *
 * @package naturKRAFT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );

/* The form markup is written block by block - no automatic <p>/<br>. */
add_filter( 'wpcf7_autop_or_not', '__return_false' );


/**
 * Default hints, used until Theme Settings has its own.
 */
function nk_form_hints(): array {

	$default = nk_option( 'form_hint_default', 'Was suchen Sie, was bieten Sie an oder was möchten Sie entwickeln? Angaben zu Mengen, Region und Zeitraum helfen uns bei der Einordnung.' );

	return array(
		'default'                    => $default,
		'Förderverein und Förderung' => nk_option( 'form_hint_foerderung', 'Wie möchten Sie den Förderverein unterstützen?' ),
		'Investition in die AG'      => nk_option( 'form_hint_investition', 'Bitte beschreiben Sie kurz Ihr Interesse. Beteiligungsform und Konditionen besprechen wir persönlich.' ),
	);
}


add_action( 'wpcf7_init', function () {

	/**
	 * [nk_hint for:anliegen] - the help text under the message field.
	 *
	 * Carries every area-specific variant as data; layout_contact.js swaps
	 * the visible one when the area changes and wires aria-describedby.
	 */
	wpcf7_add_form_tag( 'nk_hint', function ( $tag ) {

		$hints   = nk_form_hints();
		$default = $hints['default'];

		unset( $hints['default'] );

		/* After a failed non-JS submit the chosen area is known already. */
		$posted  = function_exists( 'wpcf7_get_hangover' ) ? (string) wpcf7_get_hangover( 'bereich', '' ) : '';
		$current = $hints[ $posted ] ?? $default;

		return sprintf(
			'<p class="nk-form__hint" id="nk-form-hint" data-default="%1$s" data-hints="%2$s" aria-live="polite">%3$s</p>',
			esc_attr( $default ),
			esc_attr( wp_json_encode( $hints ) ),
			esc_html( $current )
		);
	} );

	/**
	 * [nk_privacy] - the note next to the submit button.
	 */
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


/**
 * Double sending.
 *
 * CF7 disables nothing on its own, so a double click or a reload can send the
 * same inquiry twice. After a successful send a fingerprint of the inquiry is
 * kept for ten minutes; an identical submission in that window is answered
 * with the normal success message but no second e-mail goes out.
 */
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
