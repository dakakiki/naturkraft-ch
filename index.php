<?php
/**
 * index.php
 */

get_header();
?>

<main id="main" class="site-main" tabindex="-1">

	<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'template_parts/content/page' ); ?>
		<?php endwhile; ?>

	<?php else : ?>

		<?php get_template_part( 'template_parts/content/not-found' ); ?>

	<?php endif; ?>

</main>

<?php
get_footer();
