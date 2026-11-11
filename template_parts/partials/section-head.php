<?php
/**
 * Section heading block: centred title (h2) and rich text.
 *
 * @var array $args {
 *     @type string $title
 *     @type string $text  WYSIWYG HTML.
 * }
 *
 * @package naturKRAFT
 */

$nk_title = trim( (string) ( $args['title'] ?? '' ) );
$nk_text  = nk_richtext( $args['text'] ?? '' );

if ( '' === $nk_title && '' === $nk_text ) {
	return;
}
?>

<div class="nk-section__head group-fade-up">

	<?php if ( '' !== $nk_title ) : ?>
		<h2 class="nk-section__title fade-up"><?= nk_lines( $nk_title ); ?></h2>
	<?php endif; ?>

	<?php if ( '' !== $nk_text ) : ?>
		<div class="nk-section__text nk-richtext fade-up"><?= $nk_text; ?></div>
	<?php endif; ?>

</div>
