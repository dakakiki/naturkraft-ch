<?php
/**
 * Theme setup: supports, menus, head output and front-end cleanup.
 *
 * @package naturKRAFT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', function () {

	load_theme_textdomain( 'naturkraft', NK_THEME_PATH . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array(
		'search-form',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );

	register_nav_menus( array(
		'menu-main'   => __( 'Hauptnavigation', 'naturkraft' ),
		'menu-footer' => __( 'Footer', 'naturkraft' ),
	) );
} );


/**
 * Page title: "naturKRAFT | Aus gesunder Landschaft." on the front page,
 * "Impressum | naturKRAFT" elsewhere.
 */
add_filter( 'document_title_separator', function () {
	return '|';
} );


/**
 * Meta description from Theme Settings.
 *
 * There is no SEO plugin in this build, so the theme prints the one
 * description the brief defines. A page can override it with its own excerpt.
 */
add_action( 'wp_head', function () {

	$description = '';

	if ( is_singular() && has_excerpt() ) {
		$description = get_the_excerpt();
	}

	if ( '' === $description ) {
		$description = (string) nk_option( 'meta_description' );
	}

	$description = trim( wp_strip_all_tags( $description ) );

	if ( '' !== $description ) {
		printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $description ) );
	}
}, 2 );

/* Pages get an excerpt box so the description can be set per page. */
add_action( 'init', function () {
	add_post_type_support( 'page', 'excerpt' );
} );


/**
 * No third-party requests at launch.
 *
 * The brief rules out external font fetches and unnecessary embeds, so the
 * emoji script (which pulls images from s.w.org), the matching DNS prefetch
 * and the oEmbed discovery links are removed.
 */
add_action( 'init', function () {

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
} );

add_filter( 'emoji_svg_url', '__return_false' );

add_filter( 'wp_resource_hints', function ( $urls, $relation_type ) {

	if ( 'dns-prefetch' !== $relation_type ) {
		return $urls;
	}

	return array_filter( $urls, function ( $url ) {
		$href = is_array( $url ) ? ( $url['href'] ?? '' ) : $url;

		return false === strpos( (string) $href, 's.w.org' );
	} );
}, 10, 2 );


/**
 * Body classes the stylesheet hangs off.
 */
add_filter( 'body_class', function ( $classes ) {

	if ( is_singular() && nk_page_has_layouts( get_queried_object_id() ) ) {
		$classes[] = 'has-layouts';
	}

	return $classes;
} );
