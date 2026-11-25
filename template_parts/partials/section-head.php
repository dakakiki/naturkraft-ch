<?php
/**
 * section-head.php – all layouts
 */

$nk_title = trim( (string) ( $args['title'] ?? '' ) );
$nk_text  = nk_richtext( $args['text'] ?? '' );
$nk_tag   = nk_row_heading();

if ( '' === $nk_title && '' === $nk_text ) {
	return;
}
?>

<div class="nk-section__head group-fade-up">

	<?php if ( '' !== $nk_title ) : ?>
		<<?= $nk_tag; ?> class="nk-section__title fade-up"><?= nk_lines( $nk_title ); ?></<?= $nk_tag; ?>>
	<?php endif; ?>

	<?php if ( '' !== $nk_text ) : ?>
		<div class="nk-section__text nk-richtext fade-up"><?= $nk_text; ?></div>
	<?php endif; ?>

</div>
