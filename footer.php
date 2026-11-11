<?php
/**
 * Site footer.
 *
 * @package naturKRAFT
 */

$nk_footer_logo = nk_option( 'footer_logo' ) ?: nk_option( 'header_logo' );
$nk_home        = is_front_page() ? '#start' : home_url( '/' );
?>

<footer class="site-footer">

	<div class="site-footer__inner">

		<a class="site-footer__logo" href="<?= esc_url( $nk_home ); ?>">
			<?php if ( $nk_footer_logo ) : ?>
				<?= nk_image( $nk_footer_logo, 'medium_large', array(
					'class'   => 'site-footer__logo-img',
					'loading' => 'lazy',
					'alt'     => get_bloginfo( 'name' ) . ' – ' . get_bloginfo( 'description' ),
					'sizes'   => '(min-width: 1024px) 294px, 180px',
				) ); ?>
			<?php else : ?>
				<span class="site-footer__logo-text"><?php bloginfo( 'name' ); ?></span>
			<?php endif; ?>
		</a>

		<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'naturkraft' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'menu-footer',
				'container'      => false,
				'menu_class'     => 'site-footer__list',
				'depth'          => 1,
				'fallback_cb'    => false,
			) );
			?>
		</nav>

	</div>

</footer>

<a class="to-top" href="#top" aria-label="<?php esc_attr_e( 'Nach oben', 'naturkraft' ); ?>">
	<?= nk_icon( 'chevron' ); ?>
</a>

<?php wp_footer(); ?>

</body>
</html>
