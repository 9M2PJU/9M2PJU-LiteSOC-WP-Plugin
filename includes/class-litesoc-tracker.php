<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'LiteSOC_Tracker' ) ) :
/**
 * LiteSOC Event Tracker
 */
class LiteSOC_Tracker {
	private $api;

	public function __construct( $api ) {
		$this->api = $api;
		$this->init_hooks();
	}

	private function init_hooks() {
		// Authentication Events
		add_action( 'wp_login', array( $this, 'track_login_success' ), 10, 2 );
		add_action( 'wp_login_failed', array( $this, 'track_login_failed' ), 10, 2 );
		add_action( 'wp_logout', array( $this, 'track_logout' ) );

		// User Events
		add_action( 'user_register', array( $this, 'track_user_registration' ) );
		add_action( 'delete_user', array( $this, 'track_user_deletion' ) );
		add_action( 'profile_update', array( $this, 'track_profile_update' ), 10, 2 );

		// Admin Events
		add_action( 'activated_plugin', array( $this, 'track_plugin_activation' ) );
		add_action( 'deactivated_plugin', array( $this, 'track_plugin_deactivation' ) );
	}

	public function track_login_success( $user_login, $user ) {
		$this->api->track( 'auth.login_success', array(
			'actor'    => array(
				'id'    => $user->ID,
				'email' => $user->user_email,
			),
			'metadata' => array(
				'login' => $user_login,
			),
		) );
	}

	public function track_login_failed( $username, $error ) {
		$this->api->track( 'auth.login_failed', array(
			'actor'    => $username,
			'metadata' => array(
				'error' => $error->get_error_message(),
			),
		) );
	}

	public function track_logout() {
		$user = wp_get_current_user();
		if ( $user->ID ) {
			$this->api->track( 'auth.logout', array(
				'actor' => array(
					'id'    => $user->ID,
					'email' => $user->user_email,
				),
			) );
		}
	}

	public function track_user_registration( $user_id ) {
		$user = get_userdata( $user_id );
		$this->api->track( 'user.created', array(
			'actor' => array(
				'id'    => $user->ID,
				'email' => $user->user_email,
			),
		) );
	}

	public function track_user_deletion( $user_id ) {
		$deleted_user = get_userdata( $user_id );
		$current_user = wp_get_current_user();
		$this->api->track( 'user.deleted', array(
			'actor'    => array(
				'id'    => $current_user->ID,
				'email' => $current_user->user_email,
			),
			'metadata' => array(
				'target_user_id'    => $user_id,
				'target_user_email' => $deleted_user ? $deleted_user->user_email : 'unknown',
			),
		) );
	}

	public function track_profile_update( $user_id, $old_user_data ) {
		$user = get_userdata( $user_id );
		$this->api->track( 'user.updated', array(
			'actor' => array(
				'id'    => $user->ID,
				'email' => $user->user_email,
			),
		) );
	}

	public function track_plugin_activation( $plugin ) {
		$user = wp_get_current_user();
		$this->api->track( 'admin.plugin_activated', array(
			'actor'    => array(
				'id'    => $user->ID,
				'email' => $user->user_email,
			),
			'metadata' => array(
				'plugin' => $plugin,
			),
		) );
	}

	public function track_plugin_deactivation( $plugin ) {
		$user = wp_get_current_user();
		$this->api->track( 'admin.plugin_deactivated', array(
			'actor'    => array(
				'id'    => $user->ID,
				'email' => $user->user_email,
			),
			'metadata' => array(
				'plugin' => $plugin,
			),
		) );
	}
}
endif;
