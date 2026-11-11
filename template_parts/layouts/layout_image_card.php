<?php
/**
 * layout_image_card.php
 */

$nk_anchor = nk_anchor( get_sub_field( 'anchor' ) );
$nk_image  = get_sub_field( 'image' );
$nk_card   = nk_richtext( get_sub_field( 'card_text' ) );
?>

<section class="nk-section nk-section--dark image-card"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?>>

	<div class="nk-container">
		<?php get_template_part( 'template_parts/partials/section-head', null, array(
			'title' => get_sub_field( 'title' ),
			'text'  => get_sub_field( 'text' ),
		) ); ?>
	</div>

	<div class="image-card__stage">

		<?php if ( $nk_image ) : ?>
			<div class="image-card__media">
				<?= nk_image( $nk_image, 'full', array(
					'class'   => 'image-card__img',
					'loading' => 'lazy',
					'sizes'   => '100vw',
				) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $nk_card ) : ?>
			<div class="nk-container">
				<div class="image-card__card nk-glass nk-richtext fade-up"><?= $nk_card; ?></div>
			</div>
		<?php endif; ?>

	</div>

</section>
