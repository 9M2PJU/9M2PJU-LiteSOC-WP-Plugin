<?php
/**
 * LiteSOC Brute-Force Simulation Script (test1-test5)
 */

define('ABSPATH', dirname(__DIR__) . '/');
define('LITESOC_9M2PJU_VERSION', '1.2.4');

// Mock WordPress functions
function get_option($option, $default = false) {
    if ($option === 'litesoc_9m2pju_api_key') return 'lsoc_live_98540dd48f70b2edaa247c629bf9a4b0';
    if ($option === 'litesoc_9m2pju_source' && $default === 'wordpress') return 'wordpress';
    if ($option === 'litesoc_9m2pju_environment' && $default === 'production') return 'production';
    return $default;
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
        echo "  [DEBUG] Payload: " . $args['body'] . "\n";
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

$usernames = ['test1', 'test2', 'test3', 'test4', 'test5'];
$fake_ip = '103.252.202.' . rand(1, 254);

echo "Starting targeted brute-force simulation for users test1-test5 from IP $fake_ip...\n\n";

foreach ($usernames as $user) {
    echo "--- Simulating brute-force sequence for user: $user ---\n";
    
    for ($i = 1; $i <= 3; $i++) {
        echo "Attempt $i: Sending 'auth.login_failed' for '$user'...\n";
        
        $result = $api->track('auth.login_failed', [
            'actor' => $user,
            'user_ip' => $fake_ip,
            'metadata' => [
                'reason' => 'invalid_password',
                'attempt' => $i,
                'method' => 'targeted_brute_sim',
                'browser' => 'Mozilla/5.0 (Python Simulation)'
            ]
        ]);
        
        if (is_wp_error($result)) {
            echo "  Error: " . $result->get_error_message() . "\n";
        } else {
            echo "  Sent: " . json_encode($result) . "\n";
        }
        
        usleep(300000); // 300ms delay between attempts
    }
    echo "\n";
}

echo "Simulation complete. 15 events sent to LiteSOC (3 failed attempts per user).\n";
