<?php
/**
 * layout_collaboration.php
 */

$nk_anchor = nk_anchor( get_sub_field( 'anchor' ) );
$nk_rows   = (array) get_sub_field( 'rows' );
?>

<section class="nk-section nk-section--light collab"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?>>

	<div class="nk-container">

		<?php get_template_part( 'template_parts/partials/section-head', null, array(
			'title' => get_sub_field( 'title' ),
			'text'  => get_sub_field( 'text' ),
		) ); ?>

		<?php if ( $nk_rows ) : ?>
			<ul class="collab__list" role="list">
				<?php foreach ( $nk_rows as $nk_row ) : ?>
					<li class="collab__row fade-up">

						<h3 class="collab__title"><?= esc_html( $nk_row['title'] ?? '' ); ?></h3>

						<?php if ( ! empty( $nk_row['text'] ) ) : ?>
							<p class="collab__text"><?= nk_lines( $nk_row['text'] ); ?></p>
						<?php endif; ?>

						<div class="collab__cta">
							<?= nk_bereich_cta(
								(string) ( $nk_row['cta_label'] ?? '' ),
								(string) ( $nk_row['bereich'] ?? '' ),
								(string) ( $nk_row['cta_target'] ?? '' ),
								'nk-arrow-link',
								nk_icon( 'arrow-link' )
							); ?>
						</div>

					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>

</section>
