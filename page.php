<?php
/**
 * Page template.
 *
 * A page built from sections (ACF "Seiteninhalte") renders those sections; a
 * plain page - Impressum, Datenschutz - renders its title and editor content.
 *
 * @package naturKRAFT
 */

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php if ( function_exists( 'have_rows' ) && have_rows( 'layouts' ) ) : ?>

			<?php
			while ( have_rows( 'layouts' ) ) :
				the_row();

				get_template_part( 'template_parts/layouts/' . sanitize_key( get_row_layout() ) );
			endwhile;
			?>

		<?php else : ?>

			<?php get_template_part( 'template_parts/content/page' ); ?>

		<?php endif; ?>

	<?php endwhile; ?>

</main>

<?php
get_footer();
