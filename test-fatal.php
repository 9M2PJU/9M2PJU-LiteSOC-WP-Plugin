<?php
/**
 * Mock WordPress Environment for testing the plugin locally
 */

define( 'ABSPATH', __DIR__ . '/' );

function get_option($name) { return ''; }
function register_setting() {}
function add_settings_section() {}
function add_settings_field() {}
function __($text, $domain) { return $text; }
function _e($text, $domain) { echo $text; }
function add_action($tag, $callback, $priority = 10, $accepted_args = 1) {}
function plugin_dir_path($file) { return __DIR__ . '/'; }
function plugin_dir_url($file) { return 'http://example.com/wp-content/plugins/'; }
function is_admin() { return true; }
function wp_get_current_user() { 
    $user = new stdClass();
    $user->ID = 1;
    $user->user_email = 'test@example.com';
    return $user;
}
function get_userdata($id) { return wp_get_current_user(); }
function wp_enqueue_style() {}
function wp_add_dashboard_widget() {}
function is_wp_error($obj) { return false; }
function esc_html($text) { return $text; }
function esc_attr($text) { return $text; }
function admin_url($path) { return 'http://example.com/wp-admin/' . $path; }
function wp_remote_request() { return array(); }
function wp_remote_retrieve_body() { return '{}'; }
function wp_remote_retrieve_response_code() { return 200; }
function add_query_arg($args, $url) { return $url; }
function wp_json_encode($data) { return json_encode($data); }

// Load the plugin
echo "Starting plugin load test...\n";
require_once 'litesoc.php';
echo "Plugin file loaded successfully.\n";

// Trigger initialization
echo "Calling litesoc_init()...\n";
litesoc_init();
echo "litesoc_init() called successfully.\n";

echo "TEST PASSED: No fatal errors detected.\n";
