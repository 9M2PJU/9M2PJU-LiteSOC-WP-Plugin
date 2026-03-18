<?php
/**
 * LiteSOC Brute-force Simulation for hamradio.my
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

$users = ['unknown_admin', 'hacker1', 'webmaster', 'test_user', 'anonymous'];
$ips = ['103.1.2.3', '110.5.6.7', '124.8.9.10', '202.4.5.6', '210.7.8.9']; // Malaysian-looking IPs

echo "Starting 5-event Brute-force simulation targeting hamradio.my...\n";
echo "Constraint: All attempts will be random failures.\n\n";

for ($i = 0; $i < 5; $i++) {
    $user = $users[$i]; // Use each random user once
    $ip = $ips[array_rand($ips)];
    $event_name = 'auth.login_failed';
    
    echo "Attempt " . ($i + 1) . ": User '$user' from IP '$ip' - Result: FAILURE\n";
    
    $result = $api->track($event_name, [
        'actor' => $user,
        'user_ip' => $ip,
        'metadata' => [
            'method' => 'brute_force_sim',
            'site' => 'hamradio.my',
            'source' => 'hamradio.my',
            'environment' => 'production'
        ]
    ]);
    
    if (is_wp_error($result)) {
        echo "  Error: " . $result->get_error_message() . "\n";
    } else {
        echo "  Sent: " . json_encode($result) . "\n";
    }
    
    usleep(300000); // 0.3s delay between attempts
}

echo "\nSimulation complete. 5 failure events sent to LiteSOC.\n";
