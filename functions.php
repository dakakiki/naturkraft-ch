<?php
/**
 * naturKRAFT theme bootstrap.
 *
 * Everything lives in inc/ - this file only defines the constants and loads
 * the modules in dependency order.
 *
 * @package naturKRAFT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NK_THEME_VERSION', wp_get_theme( get_template() )->get( 'Version' ) );
define( 'NK_THEME_PATH', get_template_directory() );
define( 'NK_THEME_URI', get_template_directory_uri() );

/* Helpers first - the modules below call them. */
require_once NK_THEME_PATH . '/inc/helpers/acf.php';
require_once NK_THEME_PATH . '/inc/helpers/template.php';
require_once NK_THEME_PATH . '/inc/helpers/fonts.php';
require_once NK_THEME_PATH . '/inc/helpers/svg.php';
require_once NK_THEME_PATH . '/inc/helpers/wp_editor.php';

require_once NK_THEME_PATH . '/inc/setup.php';
require_once NK_THEME_PATH . '/inc/enqueue.php';
require_once NK_THEME_PATH . '/inc/options-page.php';
require_once NK_THEME_PATH . '/inc/acf-fields.php';
require_once NK_THEME_PATH . '/inc/contact-form.php';
require_once NK_THEME_PATH . '/inc/newsletter.php';
