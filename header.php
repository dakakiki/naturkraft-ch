<?php
/**
 * Site header.
 *
 * One navigation in the DOM: inline on desktop, an overlay panel below the
 * lg breakpoint. The burger button stays on top of the panel and turns into
 * its close button.
 *
 * @package naturKRAFT
 */

$nk_logo   = nk_option( 'header_logo' );
$nk_button = nk_option( 'header_button' );
$nk_home   = is_front_page() ? '#start' : home_url( '/' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<script>
		/*
		 * Scroll reveals start hidden only while JS is confirmed alive. If the
		 * animation bundle never finishes, the flag is dropped and all content
		 * is shown.
		 */
		(function () {
			var root = document.documentElement;
			root.classList.add('js');
			window.setTimeout(function () {
				if (!window.__animationsReady) {
					root.classList.remove('js');
				}
			}, 3000);
		})();
	</script>

	<?php wp_head(); ?>
</head>

<body id="top" <?php body_class(); ?>>

<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Zum Inhalt springen', 'naturkraft' ); ?></a>

<header class="site-header">

	<div class="site-header__inner">

		<a class="site-header__logo" href="<?= esc_url( $nk_home ); ?>">
			<?php if ( $nk_logo ) : ?>
				<?= nk_image( $nk_logo, 'medium_large', array(
					'class'         => 'site-header__logo-img',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'alt'           => get_bloginfo( 'name' ) . ' – ' . get_bloginfo( 'description' ),
					'sizes'         => '(min-width: 1024px) 294px, 180px',
				) ); ?>
			<?php else : ?>
				<span class="site-header__logo-text"><?php bloginfo( 'name' ); ?></span>
			<?php endif; ?>
		</a>

		<button
			type="button"
			class="site-header__toggle"
			aria-controls="site-nav"
			aria-expanded="false"
			data-label-open="<?php esc_attr_e( 'Menü öffnen', 'naturkraft' ); ?>"
			data-label-close="<?php esc_attr_e( 'Menü schliessen', 'naturkraft' ); ?>"
			aria-label="<?php esc_attr_e( 'Menü öffnen', 'naturkraft' ); ?>"
		>
			<?= nk_icon( 'menu', 'site-header__icon-open' ); ?>
			<?= nk_icon( 'close', 'site-header__icon-close' ); ?>
		</button>

		<div class="site-nav" id="site-nav">

			<nav class="site-nav__menu" aria-label="<?php esc_attr_e( 'Hauptnavigation', 'naturkraft' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'menu-main',
					'container'      => false,
					'menu_class'     => 'site-nav__list',
					'depth'          => 1,
					'fallback_cb'    => false,
				) );
				?>
			</nav>

			<?php if ( ! empty( $nk_button['url'] ) ) : ?>
				<div class="site-nav__cta">
					<?= nk_link( $nk_button, 'nk-btn nk-btn--solid site-nav__button' ); ?>
				</div>
			<?php endif; ?>

		</div>

	</div>

</header>
