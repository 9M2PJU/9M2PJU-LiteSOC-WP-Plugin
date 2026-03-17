<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'LITESOC_9M2PJU_LiteSOC_API' ) ) :
/**
 * LiteSOC API Wrapper
 */
class LITESOC_9M2PJU_LiteSOC_API {
	private $api_key;
	private $source;
	private $environment;
	private $base_url = 'https://api.litesoc.io';

	public function __construct() {
		$this->api_key     = get_option( 'litesoc_9m2pju_api_key' );
		$this->source      = get_option( 'litesoc_9m2pju_source', 'wordpress' );
		$this->environment = get_option( 'litesoc_9m2pju_environment', 'production' );
	}

	public function set_api_key( $key ) {
		$this->api_key = $key;
	}

	/**
	 * Track an event
	 */
	public function track( $event_name, $options = array() ) {
		if ( ! $this->api_key ) {
			return false;
		}

		// Normalize actor
		$actor = array( 'id' => 'system', 'email' => null );
		if ( isset( $options['actor'] ) ) {
			if ( is_array( $options['actor'] ) ) {
				$actor = array(
					'id'    => isset( $options['actor']['id'] ) ? (string) $options['actor']['id'] : 'unknown',
					'email' => isset( $options['actor']['email'] ) ? $options['actor']['email'] : null,
				);
			} else {
				$actor = array(
					'id'    => (string) $options['actor'],
					'email' => isset( $options['actorEmail'] ) ? $options['actorEmail'] : null,
				);
			}
		}

		$payload = array(
			'events' => array(
				array(
					'event'     => $event_name,
					'actor'     => $actor,
					'user_ip'   => isset( $options['user_ip'] ) ? $options['user_ip'] : $this->get_user_ip(),
					'metadata'  => array_merge(
						array(
							'source'      => $this->source,
							'environment' => $this->environment,
						),
						isset( $options['metadata'] ) ? (array) $options['metadata'] : array()
					),
					'timestamp' => isset( $options['timestamp'] ) ? $options['timestamp'] : gmdate( 'c' ), // ISO 8601
				),
			),
		);

		return $this->request( '/collect', $payload, 'POST' );
	}

	/**
	 * Get events
	 */
	public function get_events( $filters = array() ) {
		return $this->request( '/events', $filters, 'GET' );
	}

	/**
	 * Get alerts
	 */
	public function get_alerts( $filters = array() ) {
		return $this->request( '/alerts', $filters, 'GET' );
	}

	/**
	 * Verify API Key
	 */
	public function verify_key() {
		return $this->request( '/events', array( 'limit' => 1 ), 'GET' );
	}

	/**
	 * Internal request handler
	 */
	private function request( $endpoint, $params = array(), $method = 'POST' ) {
		if ( ! $this->api_key ) {
			return new WP_Error( 'no_api_key', esc_html__( 'LiteSOC API Key is missing.', '9m2pju-litesoc' ) );
		}

		$url     = $this->base_url . $endpoint;
		$args    = array(
			'method'  => $method,
			'headers' => array(
				'X-API-Key'    => $this->api_key,
				'Content-Type' => 'application/json',
				'User-Agent'   => '9m2pju-litesoc-wordpress-plugin/' . LITESOC_9M2PJU_VERSION,
			),
			'timeout' => 15,
		);

		if ( $method === 'POST' ) {
			$args['body'] = wp_json_encode( $params );
		} else {
			$url = add_query_arg( $params, $url );
		}

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( ! in_array( $status_code, array( 200, 202 ) ) ) {
			return new WP_Error( 'api_error', isset( $data['message'] ) ? $data['message'] : 'Unknown error' );
		}

		return $data;
	}

	private function get_user_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
		
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// Get the first IP in the list
			$forwarded = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
			$ips       = explode( ',', $forwarded );
			$ip        = trim( $ips[0] );
		}

		// Validate IP
		if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return $ip;
		}

		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
	}
}
endif;
