<?php
/**
 * Final Live Test for LiteSOC v1.3.5
 * 
 * To run: php final-live-test.php
 */

define('ABSPATH', __DIR__ . '/');
define('LITESOC_VERSION', '1.3.5');

// Mock Server Environment
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Mock WordPress environment
function get_option($option) {
    // Current live key from previous work
    if ($option === 'litesoc_api_key') return 'lsoc_live_98540dd48f70b2edaa247c629bf9a4b0';
    return null;
}

function wp_remote_request($url, $args) {
    echo ">> Sending to LiteSOC: " . $url . "\n";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $args['method']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-API-Key: ' . $args['headers']['X-API-Key'],
        'Content-Type: application/json',
        'User-Agent: ' . $args['headers']['User-Agent']
    ));
    
    if (isset($args['body'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args['body']);
    }
    
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo ">> HTTP Status: $http_code\n";
    echo ">> Response Body: $result\n";
    curl_close($ch);
    
    return array(
        'body' => $result,
        'response' => array('code' => $http_code)
    );
}

function wp_remote_retrieve_body($response) { return $response['body']; }
function wp_remote_retrieve_response_code($response) { return $response['response']['code']; }
function wp_json_encode($data) { return json_encode($data); }
function is_wp_error($obj) { return false; }
function add_query_arg($params, $url) { return $url . '?' . http_build_query($params); }

class WP_Error {
    public $message;
    public function __construct($code, $message) { $this->message = $message; }
    public function get_error_message() { return $this->message; }
}

// Load Plugin Classes
require_once 'includes/class-litesoc-api.php';

$api = new LiteSOC_API();

echo "--- 🚀 STARTING LIVE TEST v1.3.5 ---\n";

// 1. Test Login Success
echo "1. Tracking auth.login_success for '9M2PJU'...\n";
$res1 = $api->track('auth.login_success', array(
    'actor' => array('id' => 1, 'email' => '9m2pju@hamradio.my'),
    'metadata' => array('source' => 'Final Verification Test', 'session' => uniqid())
));
echo "Result: " . json_encode($res1) . "\n\n";

// 2. Test Admin Action
echo "2. Tracking admin.plugin_activated for '9m2pju-litesoc'...\n";
$res2 = $api->track('admin.plugin_activated', array(
    'actor' => array('id' => 1, 'email' => '9m2pju@hamradio.my'),
    'metadata' => array('plugin' => '9m2pju-litesoc/litesoc.php')
));
echo "Result: " . json_encode($res2) . "\n\n";

// 3. Brute Force Simulation (5 attempts)
echo "3. Simulating Brute Force (5 rapid failures)...\n";
$reasons = ['incorrect_password', 'invalid_username', 'mfa_failure', 'geofence_block', 'rate_limited'];
for ($i = 0; $i < 5; $i++) {
    echo "Attempt " . ($i+1) . ": ";
    $api->track('auth.login_failed', array(
        'actor' => 'attacker_bot',
        'user_ip' => '103.1.2.' . rand(1,255),
        'metadata' => array('reason' => $reasons[$i], 'attempt' => $i+1)
    ));
    echo "Done.\n";
    usleep(100000);
}

echo "\n--- ✅ TEST COMPLETE ---\n";
echo "Check your LiteSOC dashboard for events with actor '9m2pju@hamradio.my' and 'attacker_bot'.\n";
