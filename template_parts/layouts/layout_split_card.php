<?php
/**
 * layout_split_card.php
 */

$nk_anchor     = nk_anchor( get_sub_field( 'anchor' ) );
$nk_title      = trim( (string) get_sub_field( 'title' ) );
$nk_text       = (string) get_sub_field( 'text' );
$nk_kicker     = trim( (string) get_sub_field( 'kicker' ) );
$nk_card_title = trim( (string) get_sub_field( 'card_title' ) );
$nk_card_text  = nk_richtext( get_sub_field( 'card_text' ) );
$nk_button     = nk_link( get_sub_field( 'button' ), 'nk-btn' );
$nk_image      = get_sub_field( 'image' );
$nk_has_head   = '' !== $nk_title || '' !== nk_richtext( $nk_text );
$nk_tag        = $nk_has_head ? 'h3' : nk_row_heading();
?>

<section class="nk-section nk-section--light split-card<?= $nk_has_head ? '' : ' split-card--solo'; ?>"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?><?= 'h1' === $nk_tag ? ' data-animate-now' : ''; ?>>

	<div class="nk-container">

		<?php if ( $nk_has_head ) : ?>
			<?php get_template_part( 'template_parts/partials/section-head', null, array(
				'title' => $nk_title,
				'text'  => $nk_text,
			) ); ?>
		<?php endif; ?>

		<div class="split-card__grid">

			<div class="split-card__card group-fade-up">

				<?php if ( '' !== $nk_kicker ) : ?>
					<p class="split-card__kicker fade-up"><?= esc_html( $nk_kicker ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== $nk_card_title ) : ?>
					<<?= $nk_tag; ?> class="split-card__title<?= 'h3' === $nk_tag ? '' : ' split-card__title--lg'; ?> fade-up"><?= nk_lines( $nk_card_title ); ?></<?= $nk_tag; ?>>
				<?php endif; ?>

				<?php if ( '' !== $nk_card_text ) : ?>
					<div class="split-card__text nk-richtext fade-up"><?= $nk_card_text; ?></div>
				<?php endif; ?>

				<?php if ( $nk_button ) : ?>
					<div class="split-card__cta fade-up"><?= $nk_button; ?></div>
				<?php endif; ?>

			</div>

			<?php if ( $nk_image ) : ?>
				<div class="split-card__media">
					<?= nk_image( $nk_image, 'large', array(
						'class'   => 'split-card__img',
						'loading' => 'h1' === $nk_tag ? 'eager' : 'lazy',
						'sizes'   => '(min-width: 1240px) 580px, (min-width: 768px) 50vw, 100vw',
					) ); ?>
				</div>
			<?php endif; ?>

		</div>

	</div>

</section>
