<?php
/**
 * Plugin Name: 9M2PJU LiteSOC
 * Plugin URI: https://github.com/9M2PJU/9M2PJU-LiteSOC-WP-Plugin
 * Description: LiteSOC security event tracking and threat detection for WordPress.
 * Version: 1.1.3
 * Author: 9M2PJU
 * Author URI: https://hamradio.my
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: 9m2pju-litesoc-wp
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
if ( ! defined( '_9M2PJU_LITESOC_WP_VERSION' ) ) {
	define( '_9M2PJU_LITESOC_WP_VERSION', '1.1.3' );
}
if ( ! defined( '_9M2PJU_LITESOC_WP_PATH' ) ) {
	define( '_9M2PJU_LITESOC_WP_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( '_9M2PJU_LITESOC_WP_URL' ) ) {
	define( '_9M2PJU_LITESOC_WP_URL', plugin_dir_url( __FILE__ ) );
}

// Include classes (unconditional for robustness during activation)
require_once _9M2PJU_LITESOC_WP_PATH . 'includes/class-9m2pju-litesoc-wp-api.php';
require_once _9M2PJU_LITESOC_WP_PATH . 'includes/class-9m2pju-litesoc-wp-tracker.php';
require_once _9M2PJU_LITESOC_WP_PATH . 'admin/class-9m2pju-litesoc-wp-admin.php';

if ( ! function_exists( '_9m2pju_litesoc_wp_init' ) ) :
/**
 * Initialize the plugin
 */
function _9m2pju_litesoc_wp_init() {
	$api     = new _9M2PJU_LiteSOC_WP_API();
	$tracker = new _9M2PJU_LiteSOC_WP_Tracker( $api );
	
	if ( is_admin() ) {
		new _9M2PJU_LiteSOC_WP_Admin( $api, plugin_basename( __FILE__ ) );
	}
}
add_action( 'plugins_loaded', '_9m2pju_litesoc_wp_init' );
endif;
