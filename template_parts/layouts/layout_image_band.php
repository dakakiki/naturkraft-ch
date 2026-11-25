<?php
/**
 * layout_image_band.php
 */

$nk_anchor = nk_anchor( get_sub_field( 'anchor' ) );
$nk_title  = trim( (string) get_sub_field( 'title' ) );
$nk_image  = get_sub_field( 'image' );
$nk_text   = nk_richtext( get_sub_field( 'text' ) );
$nk_tag    = nk_row_heading();
?>

<section class="nk-section nk-section--dark image-band"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?><?= 'h1' === $nk_tag ? ' data-animate-now' : ''; ?>>

	<div class="image-band__stage">

		<?php if ( $nk_image ) : ?>
			<div class="image-band__media">
				<?= nk_image( $nk_image, 'full', array(
					'class'   => 'image-band__img',
					'loading' => 'h1' === $nk_tag ? 'eager' : 'lazy',
					'sizes'   => '100vw',
				) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $nk_title ) : ?>
			<div class="nk-container">
				<<?= $nk_tag; ?> class="image-band__title fade-up"><?= nk_lines( $nk_title ); ?></<?= $nk_tag; ?>>
			</div>
		<?php endif; ?>

	</div>

	<?php if ( '' !== $nk_text ) : ?>
		<div class="nk-container">
			<div class="image-band__text nk-richtext fade-up"><?= $nk_text; ?></div>
		</div>
	<?php endif; ?>

</section>
