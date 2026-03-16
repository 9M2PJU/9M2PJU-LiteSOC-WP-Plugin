<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'LiteSOC_API' ) ) :
/**
 * LiteSOC API Wrapper
 */
class LiteSOC_API {
	private $api_key;
	private $base_url = 'https://api.litesoc.io';

	public function __construct() {
		$this->api_key = get_option( 'litesoc_api_key' );
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
					'id'    => isset( $options['actor']['id'] ) ? $options['actor']['id'] : 'unknown',
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
					'metadata'  => isset( $options['metadata'] ) ? $options['metadata'] : new stdClass(),
					'timestamp' => isset( $options['timestamp'] ) ? $options['timestamp'] : date( 'c' ), // ISO 8601
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
	 * Internal request handler
	 */
	private function request( $endpoint, $params = array(), $method = 'POST' ) {
		if ( ! $this->api_key ) {
			return new WP_Error( 'no_api_key', __( 'LiteSOC API Key is missing.', 'litesoc' ) );
		}

		$url     = $this->base_url . $endpoint;
		$args    = array(
			'method'  => $method,
			'headers' => array(
				'X-API-Key'    => $this->api_key,
				'Content-Type' => 'application/json',
				'User-Agent'   => '9m2pju-litesoc-wordpress-plugin/' . LITESOC_VERSION,
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
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}
}
endif;
