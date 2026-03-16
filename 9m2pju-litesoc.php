<?php
/**
 * Plugin Name: 9M2PJU LiteSOC
 * Plugin URI: https://github.com/9M2PJU/9M2PJU-LiteSOC-WP-Plugin
 * Description: LiteSOC security event tracking and threat detection for WordPress.
 * Version: 1.1.2
 * Author: 9M2PJU
 * Author URI: https://hamradio.my
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: 9m2pju-litesoc
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
if ( ! defined( 'LITESOC_VERSION' ) ) {
	define( 'LITESOC_VERSION', '1.1.2' );
}
if ( ! defined( 'LITESOC_PATH' ) ) {
	define( 'LITESOC_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'LITESOC_URL' ) ) {
	define( 'LITESOC_URL', plugin_dir_url( __FILE__ ) );
}

// Include classes (unconditional for robustness during activation)
require_once LITESOC_PATH . 'includes/class-litesoc-api.php';
require_once LITESOC_PATH . 'includes/class-litesoc-tracker.php';
require_once LITESOC_PATH . 'admin/class-litesoc-admin.php';

if ( ! function_exists( 'litesoc_init' ) ) :
/**
 * Initialize the plugin
 */
function litesoc_init() {
	$api     = new LiteSOC_API();
	$tracker = new LiteSOC_Tracker( $api );
	
	if ( is_admin() ) {
		new LiteSOC_Admin( $api, plugin_basename( __FILE__ ) );
	}
}
add_action( 'plugins_loaded', 'litesoc_init' );
endif;
