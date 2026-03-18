<?php
/**
 * Plugin Name: 9M2PJU LiteSOC
 * Plugin URI: https://github.com/9M2PJU/9M2PJU-LiteSOC-WP-Plugin
 * Description: LiteSOC security event tracking and threat detection for WordPress.
 * Version: 1.3.5
 * Author: 9M2PJU
 * Author URI: https://hamradio.my
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: 9m2pju-litesoc
 * Requires at least: 5.0
 * Tested up to: 6.9
 * Requires PHP: 7.2
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
if ( ! defined( 'LITESOC_9M2PJU_VERSION' ) ) {
	define( 'LITESOC_9M2PJU_VERSION', '1.3.5' );
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
