<?php
/**
 * Newsletter sign-up via Mailchimp (double opt-in).
 *
 * Credentials live in wp-config.php, never in the database or the repo:
 *
 *   define( 'NK_MAILCHIMP_API_KEY', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-us21' );
 *   define( 'NK_MAILCHIMP_AUDIENCE_ID', 'a1b2c3d4e5' );
 *
 * New addresses are added with status "pending", so Mailchimp sends its
 * confirmation e-mail and only a confirmed address is subscribed. The text of
 * that e-mail is set in Mailchimp (Audience → Signup forms → Opt-in
 * confirmation email).
 *
 * One handler serves both paths: the JS form posts with fetch and gets JSON,
 * without JS the browser posts normally and is redirected back to the section
 * with ?nk-newsletter=<status>.
 *
 * Statuses: success, already, invalid, error.
 *
 * @package naturKRAFT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NK_NEWSLETTER_ACTION = 'nk_newsletter';


function nk_newsletter_configured(): bool {
	return defined( 'NK_MAILCHIMP_API_KEY' ) && NK_MAILCHIMP_API_KEY
		&& defined( 'NK_MAILCHIMP_AUDIENCE_ID' ) && NK_MAILCHIMP_AUDIENCE_ID;
}


/**
 * One Mailchimp API call.
 *
 * @return array{code:int, body:array}
 */
function nk_mailchimp_request( string $method, string $path, array $body = array() ): array {

	$key = (string) NK_MAILCHIMP_API_KEY;
	$dc  = substr( strrchr( $key, '-' ) ?: '', 1 );

	if ( '' === $dc ) {
		return array( 'code' => 0, 'body' => array( 'title' => 'API key without data center suffix' ) );
	}

	$args = array(
		'method'  => $method,
		'timeout' => 10,
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( 'naturkraft:' . $key ),
			'Content-Type'  => 'application/json',
		),
	);

	if ( $body ) {
		$args['body'] = wp_json_encode( $body );
	}

	$response = wp_remote_request( 'https://' . $dc . '.api.mailchimp.com/3.0/' . ltrim( $path, '/' ), $args );

	if ( is_wp_error( $response ) ) {
		return array( 'code' => 0, 'body' => array( 'title' => $response->get_error_message() ) );
	}

	return array(
		'code' => (int) wp_remote_retrieve_response_code( $response ),
		'body' => (array) json_decode( wp_remote_retrieve_body( $response ), true ),
	);
}


/**
 * Subscribes an address with double opt-in.
 */
function nk_newsletter_subscribe( string $email ): string {

	$email = sanitize_email( $email );

	if ( '' === $email || ! is_email( $email ) ) {
		return 'invalid';
	}

	if ( ! nk_newsletter_configured() ) {
		error_log( '[naturKRAFT newsletter] Mailchimp is not configured (NK_MAILCHIMP_API_KEY / NK_MAILCHIMP_AUDIENCE_ID).' );
		return 'error';
	}

	$member = 'lists/' . rawurlencode( (string) NK_MAILCHIMP_AUDIENCE_ID ) . '/members/' . md5( strtolower( $email ) );

	/*
	 * PUT creates the member as "pending" (Mailchimp sends the confirmation
	 * mail) or returns the existing member untouched.
	 */
	$result = nk_mailchimp_request( 'PUT', $member, array(
		'email_address' => $email,
		'status_if_new' => 'pending',
		'language'      => 'de',
	) );

	if ( 200 === $result['code'] ) {

		$status = $result['body']['status'] ?? '';

		if ( 'subscribed' === $status ) {
			return 'already';
		}

		if ( 'pending' === $status ) {
			return 'success';
		}

		/* Unsubscribed or archived earlier: a new opt-in is required. */
		$again = nk_mailchimp_request( 'PATCH', $member, array( 'status' => 'pending' ) );

		if ( 200 === $again['code'] ) {
			return 'success';
		}

		$result = $again;
	}

	$title = (string) ( $result['body']['title'] ?? '' );

	/* Mailchimp rejects fake or role addresses as an invalid resource. */
	if ( 400 === $result['code'] && 'Invalid Resource' === $title ) {
		return 'invalid';
	}

	error_log( sprintf( '[naturKRAFT newsletter] Mailchimp %d: %s', $result['code'], $title ) );

	return 'error';
}


/**
 * Form handler for admin-post.php (logged in and out).
 */
function nk_newsletter_handle(): void {

	$email  = isset( $_POST['email'] ) ? wp_unslash( (string) $_POST['email'] ) : '';
	$status = nk_newsletter_subscribe( $email );

	$wants_json = ! empty( $_POST['ajax'] ) || false !== strpos( (string) ( $_SERVER['HTTP_ACCEPT'] ?? '' ), 'application/json' );

	if ( $wants_json ) {
		wp_send_json( array( 'status' => $status ), 'error' === $status ? 502 : 200 );
	}

	$anchor = isset( $_POST['anchor'] ) ? sanitize_title( wp_unslash( (string) $_POST['anchor'] ) ) : '';

	$back = wp_get_referer() ?: home_url( '/' );
	$back = strtok( $back, '#' );
	$back = remove_query_arg( 'nk-newsletter', $back );

	wp_safe_redirect( add_query_arg( 'nk-newsletter', $status, $back ) . ( $anchor ? '#' . $anchor : '' ) );
	exit;
}

add_action( 'admin_post_nopriv_' . NK_NEWSLETTER_ACTION, 'nk_newsletter_handle' );
add_action( 'admin_post_' . NK_NEWSLETTER_ACTION, 'nk_newsletter_handle' );


/**
 * Status passed back after a non-JS submit.
 */
function nk_newsletter_status_from_request(): string {

	$status = isset( $_GET['nk-newsletter'] ) ? sanitize_key( wp_unslash( $_GET['nk-newsletter'] ) ) : '';

	return in_array( $status, array( 'success', 'already', 'invalid', 'error' ), true ) ? $status : '';
}
