<?php
/**
 * layout_text_list.php
 */

$nk_anchor = nk_anchor( get_sub_field( 'anchor' ) );
$nk_items  = (array) get_sub_field( 'items' );
?>

<section class="nk-section nk-section--light text-list"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?>>

	<div class="nk-container">

		<?php get_template_part( 'template_parts/partials/section-head', null, array(
			'title' => get_sub_field( 'title' ),
			'text'  => get_sub_field( 'text' ),
		) ); ?>

		<?php if ( $nk_items ) : ?>
			<ul class="text-list__list" role="list">
				<?php foreach ( $nk_items as $nk_item ) : ?>
					<li class="text-list__item fade-up">

						<h3 class="text-list__title"><?= esc_html( $nk_item['title'] ?? '' ); ?></h3>

						<?php if ( ! empty( $nk_item['text'] ) ) : ?>
							<p class="text-list__text"><?= nk_lines( $nk_item['text'] ); ?></p>
						<?php endif; ?>

					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>

</section>
