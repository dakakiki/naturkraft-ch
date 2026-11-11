<?php
/**
 * layout_text_images.php
 */

$nk_anchor = nk_anchor( get_sub_field( 'anchor' ) );
$nk_images = (array) get_sub_field( 'images' );
$nk_images = array_slice( array_filter( $nk_images ), 0, 3 );
?>

<section class="nk-section nk-section--light text-images"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?>>

	<div class="nk-container">
		<?php get_template_part( 'template_parts/partials/section-head', null, array(
			'title' => get_sub_field( 'title' ),
			'text'  => get_sub_field( 'text' ),
		) ); ?>
	</div>

	<?php if ( $nk_images ) : ?>
		<div class="text-images__gallery" data-count="<?= count( $nk_images ); ?>" data-gsap-cards>
			<?php foreach ( $nk_images as $nk_img ) : ?>
				<figure class="text-images__item">
					<?= nk_image( $nk_img, 'large', array(
						'class'   => 'text-images__img',
						'loading' => 'lazy',
						'sizes'   => '(min-width: 568px) 34vw, 100vw',
					) ); ?>
				</figure>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</section>
