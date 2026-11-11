<?php
/**
 * layout_contact.php
 */

$nk_anchor = nk_anchor( get_sub_field( 'anchor' ) );
$nk_form   = (int) get_sub_field( 'form' );
?>

<section class="nk-section nk-section--light contact"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?>>

	<div class="nk-container">

		<?php get_template_part( 'template_parts/partials/section-head', null, array(
			'title' => get_sub_field( 'title' ),
			'text'  => get_sub_field( 'text' ),
		) ); ?>

		<?php if ( $nk_form && shortcode_exists( 'contact-form-7' ) ) : ?>
			<div class="contact__form fade-up">
				<?= do_shortcode( sprintf( '[contact-form-7 id="%d" html_class="nk-form"]', $nk_form ) ); ?>
			</div>
		<?php endif; ?>

	</div>

</section>
