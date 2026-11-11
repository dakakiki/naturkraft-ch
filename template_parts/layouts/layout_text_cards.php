<?php
/**
 * Layout: Text + Karten auf Bild (FRAME 05 / #ueber-naturkraft, #traegerschaft).
 *
 * XD (1440): dark band with the heading text; two 572x447 glass cards (beige
 * and sage) that start 96 above a full-width photo (473 tall) and end 122
 * above its bottom edge. The card row is the "Trägerschaft" target.
 *
 * @package naturKRAFT
 */

$nk_anchor       = nk_anchor( get_sub_field( 'anchor' ) );
$nk_cards_anchor = nk_anchor( get_sub_field( 'cards_anchor' ) );
$nk_image        = get_sub_field( 'image' );
$nk_cards        = array_slice( (array) get_sub_field( 'cards' ), 0, 2 );
?>

<section class="nk-section nk-section--dark text-cards"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?>>

	<div class="nk-container">
		<?php get_template_part( 'template_parts/partials/section-head', null, array(
			'title' => get_sub_field( 'title' ),
			'text'  => get_sub_field( 'text' ),
		) ); ?>
	</div>

	<div class="text-cards__stage"<?= $nk_cards_anchor ? ' id="' . esc_attr( $nk_cards_anchor ) . '"' : ''; ?>>

		<?php if ( $nk_image ) : ?>
			<div class="text-cards__media">
				<?= nk_image( $nk_image, 'full', array(
					'class'   => 'text-cards__img',
					'loading' => 'lazy',
					'sizes'   => '100vw',
				) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $nk_cards ) : ?>
			<div class="nk-container">
				<div class="text-cards__grid group-fade-up">
					<?php foreach ( $nk_cards as $nk_card ) : ?>
						<?php $nk_variant = 'sage' === ( $nk_card['variant'] ?? '' ) ? 'sage' : 'beige'; ?>
						<article class="text-cards__card nk-glass nk-glass--<?= esc_attr( $nk_variant ); ?> fade-up">

							<h3 class="text-cards__title"><?= esc_html( $nk_card['title'] ?? '' ); ?></h3>

							<?php if ( ! empty( $nk_card['text'] ) ) : ?>
								<p class="text-cards__text"><?= nk_lines( $nk_card['text'] ); ?></p>
							<?php endif; ?>

							<div class="text-cards__cta">
								<?= nk_bereich_cta(
									(string) ( $nk_card['cta_label'] ?? '' ),
									(string) ( $nk_card['bereich'] ?? '' ),
									(string) ( $nk_card['cta_target'] ?? '' ),
									'nk-btn nk-btn--solid'
								); ?>
							</div>

						</article>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

	</div>

</section>
