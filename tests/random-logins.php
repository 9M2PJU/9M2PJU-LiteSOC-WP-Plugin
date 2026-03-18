<?php
/**
 * LiteSOC Random Login Simulation Script
 */

define('ABSPATH', dirname(__DIR__) . '/');
define('LITESOC_9M2PJU_VERSION', '1.3.5');

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

$users = ['admin', 'piju', 'john_doe', 'guest', 'root'];
$ips = ['1.2.3.4', '111.222.333.444', '8.8.8.8', '192.168.1.100', '45.45.45.45'];

echo "Starting 5 random login simulation to hamradio.my...\n";

for ($i = 0; $i < 5; $i++) {
    $user = $users[array_rand($users)];
    $ip = $ips[array_rand($ips)];
    $is_success = (rand(0, 1) === 1);
    $event_name = $is_success ? 'auth.login_success' : 'auth.login_failed';
    
    echo "Attempt " . ($i + 1) . ": User '$user' from IP '$ip' - Result: " . ($is_success ? 'SUCCESS' : 'FAILURE') . "\n";
    
    $result = $api->track($event_name, [
        'actor' => $user,
        'user_ip' => $ip,
        'metadata' => [
            'method' => 'manual_sim',
            'site' => 'hamradio.my',
            'browser' => 'Mozilla/5.0 Simulation'
        ]
    ]);
    
    if (is_wp_error($result)) {
        echo "  Error: " . $result->get_error_message() . "\n";
    } else {
        echo "  Sent: " . json_encode($result) . "\n";
    }
    
    usleep(500000); 
}

echo "\nSimulation complete. 5 events sent to LiteSOC.\n";
