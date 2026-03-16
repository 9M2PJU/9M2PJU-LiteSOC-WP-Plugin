<?php
/**
 * Plugin Name: LiteSOC Security
 * Plugin URI: https://github.com/LiteSOC/litesoc-node
 * Description: LiteSOC security event tracking and threat detection for WordPress.
 * Version: 1.0.0
 * Author: LiteSOC
 * Author URI: https://litesoc.io
 * License: MIT
 * Text Domain: litesoc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'LITESOC_VERSION', '1.0.0' );
define( 'LITESOC_PATH', plugin_dir_path( __FILE__ ) );
define( 'LITESOC_URL', plugin_dir_url( __FILE__ ) );

// Include classes
require_once LITESOC_PATH . 'includes/class-litesoc-api.php';
require_once LITESOC_PATH . 'includes/class-litesoc-tracker.php';

if ( is_admin() ) {
	require_once LITESOC_PATH . 'admin/class-litesoc-admin.php';
}

/**
 * Initialize the plugin
 */
function litesoc_init() {
	$api     = new LiteSOC_API();
	$tracker = new LiteSOC_Tracker( $api );
	
	if ( is_admin() ) {
		new LiteSOC_Admin( $api );
	}
}
add_action( 'plugins_loaded', 'litesoc_init' );
