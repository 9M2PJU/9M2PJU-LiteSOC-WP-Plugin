<?php
/**
 * LiteSOC Impossible Travel Simulation for hamradio.my
 */

define('ABSPATH', dirname(__DIR__) . '/');
define('LITESOC_9M2PJU_VERSION', '1.2.9');

// Mock WordPress functions
function get_option($option) {
    if ($option === 'litesoc_9m2pju_api_key') return 'lsoc_live_98540dd48f70b2edaa247c629bf9a4b0';
    return null;
}

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
function sanitize_text_field($str) { return htmlspecialchars($str); }
function wp_unslash($str) { return stripslashes($str); }

if ( ! class_exists( 'WP_Error' ) ) {
    class WP_Error {
        public $code;
        public $message;
        public function __construct($code = '', $message = '') {
            $this->code    = $code;
            $this->message = $message;
        }
        public function get_error_message() { return $this->message; }
    }
}

// Load the class
require_once dirname(__DIR__) . '/includes/class-9m2pju-litesoc-api.php';

$api = new LITESOC_9M2PJU_LiteSOC_API();

$user = 'traveler_9m2pju';

echo "Starting Impossible Travel simulation for user '$user'...\n";

// 1. Malaysia login
$ip_my = '161.142.123.45'; // Malaysia IP
echo "Step 1: Successful login from Malaysia (IP: $ip_my)\n";
$result1 = $api->track('auth.login_success', [
    'actor' => $user,
    'user_ip' => $ip_my,
    'metadata' => [
        'method' => 'impossible_travel_sim',
        'site' => 'hamradio.my',
        'source' => 'hamradio.my',
        'environment' => 'production'
    ]
]);
echo "  Result: " . json_encode($result1) . "\n\n";

echo "Waiting 5 seconds for travel simulation...\n";
sleep(5);

// 2. USA login
$ip_us = '8.8.8.8'; // USA (Google Public DNS)
echo "Step 2: Successful login from USA (IP: $ip_us)\n";
$result2 = $api->track('auth.login_success', [
    'actor' => $user,
    'user_ip' => $ip_us,
    'metadata' => [
        'method' => 'impossible_travel_sim',
        'site' => 'hamradio.my',
        'source' => 'hamradio.my',
        'environment' => 'production'
    ]
]);
echo "  Result: " . json_encode($result2) . "\n\n";

echo "Simulation complete. Check LiteSOC dashboard for 'Geographic Anomaly' or 'Impossible Travel' alerts.\n";
