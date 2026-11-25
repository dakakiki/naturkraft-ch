<?php
/**
 * layout_text.php
 */

$nk_anchor     = nk_anchor( get_sub_field( 'anchor' ) );
$nk_background = (string) get_sub_field( 'background' );
$nk_background = in_array( $nk_background, array( 'light', 'dark', 'sage' ), true ) ? $nk_background : 'light';
$nk_link       = nk_link( get_sub_field( 'link' ) );
?>

<section class="nk-section nk-section--<?= esc_attr( $nk_background ); ?> text-section"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?>>

	<div class="nk-container">

		<?php get_template_part( 'template_parts/partials/section-head', null, array(
			'title' => get_sub_field( 'title' ),
			'text'  => get_sub_field( 'text' ),
		) ); ?>

		<?php if ( $nk_link ) : ?>
			<p class="text-section__link fade-up"><?= $nk_link; ?></p>
		<?php endif; ?>

	</div>

</section>
