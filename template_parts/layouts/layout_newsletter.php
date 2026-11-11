<?php
/**
 * Layout: Newsletter (FRAME 07 / #newsletter).
 *
 * Posts to admin-post.php (inc/newsletter.php). layout_newsletter.js sends
 * the same form with fetch and shows the matching message in place; without
 * JS the page reloads with ?nk-newsletter=<status> and the message is printed
 * here.
 *
 * @package naturKRAFT
 */

$nk_anchor   = nk_anchor( get_sub_field( 'anchor' ) );
$nk_messages = array(
	'success' => (string) get_sub_field( 'msg_success' ),
	'already' => (string) get_sub_field( 'msg_already' ),
	'invalid' => (string) get_sub_field( 'msg_invalid' ),
	'error'   => (string) get_sub_field( 'msg_error' ),
);
$nk_status   = nk_newsletter_status_from_request();
$nk_consent  = nk_richtext( get_sub_field( 'consent' ) );
$nk_field_id = 'nk-newsletter-email-' . get_row_index();
?>

<section class="nk-section nk-section--sage newsletter"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?>>

	<div class="nk-container">

		<?php get_template_part( 'template_parts/partials/section-head', null, array(
			'title' => get_sub_field( 'title' ),
			'text'  => get_sub_field( 'text' ),
		) ); ?>

		<form
			class="newsletter__form fade-up"
			action="<?= esc_url( admin_url( 'admin-post.php' ) ); ?>"
			method="post"
			data-nk-newsletter
			<?php foreach ( $nk_messages as $nk_key => $nk_msg ) : ?>
				data-msg-<?= esc_attr( $nk_key ); ?>="<?= esc_attr( $nk_msg ); ?>"
			<?php endforeach; ?>
		>
			<input type="hidden" name="action" value="<?= esc_attr( NK_NEWSLETTER_ACTION ); ?>">
			<input type="hidden" name="anchor" value="<?= esc_attr( $nk_anchor ); ?>">

			<div class="newsletter__row">

				<label class="screen-reader-text" for="<?= esc_attr( $nk_field_id ); ?>"><?php esc_html_e( 'E-Mail-Adresse', 'naturkraft' ); ?></label>

				<input
					class="newsletter__input"
					type="email"
					id="<?= esc_attr( $nk_field_id ); ?>"
					name="email"
					required
					maxlength="254"
					autocomplete="email"
					placeholder="<?= esc_attr( get_sub_field( 'placeholder' ) ?: 'Ihre E-Mail-Adresse' ); ?>"
					<?= in_array( $nk_status, array( 'invalid' ), true ) ? 'aria-invalid="true"' : ''; ?>
				>

				<button class="nk-btn nk-btn--solid nk-btn--lg newsletter__button" type="submit">
					<?= esc_html( get_sub_field( 'button' ) ?: 'Newsletter abonnieren' ); ?>
				</button>

			</div>

			<?php if ( $nk_consent ) : ?>
				<div class="newsletter__consent nk-richtext"><?= $nk_consent; ?></div>
			<?php endif; ?>

			<p class="newsletter__message" role="status" aria-live="polite"<?= $nk_status ? ' data-status="' . esc_attr( $nk_status ) . '"' : ''; ?>><?= $nk_status ? esc_html( $nk_messages[ $nk_status ] ) : ''; ?></p>

		</form>

	</div>

</section>
