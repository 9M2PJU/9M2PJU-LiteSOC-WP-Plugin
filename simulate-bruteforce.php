<?php
/**
 * LiteSOC Brute-Force Simulation Script
 */

define('ABSPATH', __DIR__ . '/');
define('LITESOC_VERSION', '1.0.0');

// Mock WordPress functions
function get_option($option) {
    if ($option === 'litesoc_api_key') return 'lsoc_live_98540dd48f70b2edaa247c629bf9a4b0';
    return null;
}

// REAL wp_remote_request simulation using curl (since we are in CLI)
function wp_remote_request($url, $args) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $args['method']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-API-Key: ' . $args['headers']['X-API-Key'],
        'Content-Type: application/json',
        'User-Agent: ' . $args['headers']['User-Agent']
    ]);
    if (isset($args['body'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args['body']);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, $args['timeout']);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'body' => $result,
        'response' => ['code' => $http_code]
    ];
}

function wp_remote_retrieve_body($response) { return $response['body']; }
function wp_remote_retrieve_response_code($response) { return $response['response']['code']; }
function wp_json_encode($data) { return json_encode($data); }
function is_wp_error($obj) { return false; }
function current_time($type) { return date('Y-m-d H:i:s'); }
function add_query_arg($params, $url) { return $url . '?' . http_build_query($params); }

class WP_Error {
    public $message;
    public function __construct($code, $message) { $this->message = $message; }
    public function get_error_message() { return $this->message; }
}

// Load the class
require_once 'includes/class-litesoc-api.php';

$api = new LiteSOC_API();
$target_user = 'admin';
$fake_ip = '192.168.1.' . rand(1, 254);

echo "Starting brute-force simulation for user '$target_user' from IP '$fake_ip'...\n";

$reasons = [
    'invalid_password',
    'invalid_username',
    'account_locked',
    'missing_mfa',
    'invalid_mfa',
    'expired_session',
    'rate_limited',
    'blocked_ip',
    'unauthorized_device',
    'geo_fenced'
];

for ($i = 0; $i < 10; $i++) {
    $reason = $reasons[$i];
    echo "Sending failed login attempt " . ($i + 1) . "/10 with reason '$reason'...\n";
    $result = $api->track('auth.login_failed', [
        'actor' => $target_user,
        'user_ip' => $fake_ip,
        'metadata' => [
            'reason' => $reason,
            'attempt_number' => $i + 1,
            'browser' => 'Mozilla/5.0 (Kali Linux; rv:109.0) Gecko/20100101 Firefox/115.0'
        ]
    ]);
    
    if (is_wp_error($result)) {
        echo "Error: " . $result->get_error_message() . "\n";
    } else {
        echo "Success: " . json_encode($result) . "\n";
    }
    
    // Minimal sleep to avoid rate limiting but keep it fast
    usleep(200000); 
}

echo "Sending successful login for 'admin'...\n";
$litesoc->track('auth.login_success', [
    'actor'   => 'admin',
    'user_ip' => '1.1.1.1',
    'metadata' => ['device' => 'Trusted Laptop']
]);

echo "\nSimulation complete. Please check your LiteSOC dashboard.\n";
