<?php
/**
 * layout_text_list.php
 */

$nk_anchor = nk_anchor( get_sub_field( 'anchor' ) );
$nk_items  = (array) get_sub_field( 'items' );
$nk_uid    = 'text-list-' . ( function_exists( 'get_row_index' ) ? (int) get_row_index() : wp_rand() );
?>

<section class="nk-section nk-section--light text-list"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?>>

	<div class="nk-container">

		<?php get_template_part( 'template_parts/partials/section-head', null, array(
			'title' => get_sub_field( 'title' ),
			'text'  => get_sub_field( 'text' ),
		) ); ?>

		<?php if ( $nk_items ) : ?>
			<ul class="text-list__list" role="list" data-nk-accordion>
				<?php foreach ( $nk_items as $nk_i => $nk_item ) :
					$nk_title  = (string) ( $nk_item['title'] ?? '' );
					$nk_text   = trim( (string) ( $nk_item['text'] ?? '' ) );
					$nk_toggle = $nk_uid . '-toggle-' . $nk_i;
					$nk_panel  = $nk_uid . '-panel-' . $nk_i;
					?>
					<li class="text-list__item fade-up">

						<?php if ( '' !== $nk_text ) : ?>
							<h3 class="text-list__title">
								<button type="button" class="text-list__toggle" id="<?= esc_attr( $nk_toggle ); ?>" aria-expanded="false" aria-controls="<?= esc_attr( $nk_panel ); ?>">
									<span class="text-list__label"><?= esc_html( $nk_title ); ?></span>
									<span class="text-list__icon" aria-hidden="true"></span>
								</button>
							</h3>

							<div class="text-list__panel" id="<?= esc_attr( $nk_panel ); ?>" role="region" aria-labelledby="<?= esc_attr( $nk_toggle ); ?>">
								<div class="text-list__panel-inner">
									<p class="text-list__text"><?= nk_lines( $nk_text ); ?></p>
								</div>
							</div>
						<?php else : ?>
							<h3 class="text-list__title"><?= esc_html( $nk_title ); ?></h3>
						<?php endif; ?>

					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>

</section>
