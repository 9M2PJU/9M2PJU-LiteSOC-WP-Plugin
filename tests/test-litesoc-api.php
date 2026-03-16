<?php
/**
 * Test script for LiteSOC API Wrapper
 * 
 * To run: php test-litesoc-api.php
 */

define('ABSPATH', __DIR__ . '/');

// Mock WordPress functions
function get_option($option) {
    if ($option === 'litesoc_api_key') return 'test-api-key';
    return null;
}

function wp_remote_request($url, $args) {
    echo "Requesting URL: $url\n";
    echo "Method: " . $args['method'] . "\n";
    echo "Body: " . (isset($args['body']) ? $args['body'] : 'N/A') . "\n";
    
    return [
        'body' => json_encode(['success' => true]),
        'response' => ['code' => 200]
    ];
}

function wp_remote_retrieve_body($response) { return $response['body']; }
function wp_remote_retrieve_response_code($response) { return $response['response']['code']; }
function wp_json_encode($data) { return json_encode($data); }
function is_wp_error($obj) { return false; }
function current_time($type) { return date('Y-m-d H:i:s'); }
function add_query_arg($params, $url) { return $url . '?' . http_build_query($params); }

class WP_Error {}

// Load the class
require_once 'includes/class-litesoc-api.php';

$api = new LiteSOC_API();
echo "Testing track()...\n";
$api->track('test.event', ['actor' => 'test_user']);

echo "\nTesting get_events()...\n";
$api->get_events(['limit' => 10]);

echo "\ndone.\n";
