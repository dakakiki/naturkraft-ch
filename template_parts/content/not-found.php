<?php
/**
 * "Diese Seite wurde nicht gefunden." - 404 and empty archives.
 *
 * @package naturKRAFT
 */

$nk_title  = nk_option( 'error_title', 'Diese Seite wurde nicht gefunden.' );
$nk_button = nk_option( 'error_button', 'Zur Startseite' );
?>

<section class="page-plain page-plain--404">

	<div class="page-plain__inner">

		<h1 class="page-plain__title"><?= esc_html( $nk_title ); ?></h1>

		<p class="page-plain__actions">
			<a class="nk-btn nk-btn--solid nk-btn--lg" href="<?= esc_url( home_url( '/' ) ); ?>"><?= esc_html( $nk_button ); ?></a>
		</p>

	</div>

</section>
