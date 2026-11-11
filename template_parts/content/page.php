<?php
/**
 * page.php – plain pages (page.php)
 */
?>

<article <?php post_class( 'page-plain' ); ?>>

	<div class="page-plain__inner">

		<h1 class="page-plain__title"><?php the_title(); ?></h1>

		<div class="page-plain__content">
			<?php the_content(); ?>
		</div>

	</div>

</article>
