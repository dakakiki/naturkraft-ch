<?php
/**
 * Plain page: title + editor content (Impressum, Datenschutz).
 *
 * XD: a 25px dark band under the header, the text on beige, a 25px sage band
 * above the footer.
 *
 * @package naturKRAFT
 */
?>

<article <?php post_class( 'page-plain' ); ?>>

	<div class="page-plain__inner">

		<h1 class="page-plain__title"><?php the_title(); ?></h1>

		<div class="page-plain__content nk-richtext">
			<?php the_content(); ?>
		</div>

	</div>

</article>
