<?php
/**
 * Plugin Name: 9M2PJU LiteSOC
 * Plugin URI: https://github.com/9M2PJU/9M2PJU-LiteSOC-WP-Plugin
 * Description: LiteSOC security event tracking and threat detection for WordPress.
 * Version: 1.3.1
 * Author: 9M2PJU
 * Author URI: https://hamradio.my
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: 9m2pju-litesoc
 * Requires at least: 5.0
 * Tested up to: 6.9
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
if ( ! defined( 'LITESOC_9M2PJU_VERSION' ) ) {
	define( 'LITESOC_9M2PJU_VERSION', '1.3.1' );
}
if ( ! defined( 'LITESOC_9M2PJU_PATH' ) ) {
	define( 'LITESOC_9M2PJU_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'LITESOC_9M2PJU_URL' ) ) {
	define( 'LITESOC_9M2PJU_URL', plugin_dir_url( __FILE__ ) );
}

// Include classes (unconditional for robustness during activation)
require_once LITESOC_9M2PJU_PATH . 'includes/class-9m2pju-litesoc-api.php';
require_once LITESOC_9M2PJU_PATH . 'includes/class-9m2pju-litesoc-tracker.php';
require_once LITESOC_9M2PJU_PATH . 'admin/class-9m2pju-litesoc-admin.php';

if ( ! function_exists( 'litesoc_9m2pju_init' ) ) :
/**
 * Initialize the plugin
 */
function litesoc_9m2pju_init() {
	$api     = new LITESOC_9M2PJU_LiteSOC_API();
	$tracker = new LITESOC_9M2PJU_LiteSOC_Tracker( $api );
	
	if ( is_admin() ) {
		new LITESOC_9M2PJU_LiteSOC_Admin( $api, plugin_basename( __FILE__ ) );
	}
}
add_action( 'plugins_loaded', 'litesoc_9m2pju_init' );
endif;
