<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'LiteSOC_Admin' ) ) :
/**
 * LiteSOC Admin Interface
 */
class LiteSOC_Admin {
	private $api;
	private $plugin_basename;

	public function __construct( $api, $plugin_basename ) {
		$this->api             = $api;
		$this->plugin_basename = $plugin_basename;
		$this->init_hooks();
	}

	private function init_hooks() {
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
		add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'add_action_links' ) );
	}

	public function add_menu_pages() {
		add_menu_page(
			__( '9M2PJU LiteSOC WP Settings', 'litesoc' ),
			'9M2PJU LiteSOC',
			'manage_options',
			'litesoc',
			array( $this, 'render_settings_page' ),
			'dashicons-shield-alt',
			80
		);
	}

	public function register_settings() {
		register_setting( 'litesoc_settings', 'litesoc_api_key', 'sanitize_text_field' );
		
		add_settings_section(
			'litesoc_main_section',
			__( 'API Configuration', 'litesoc' ),
			null,
			'litesoc'
		);

		add_settings_field(
			'litesoc_api_key',
			__( 'LiteSOC API Key', 'litesoc' ),
			array( $this, 'render_api_key_field' ),
			'litesoc',
			'litesoc_main_section'
		);
	}

	public function render_api_key_field() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$key = get_option( 'litesoc_api_key' );
		?>
		<input type="password" name="litesoc_api_key" value="<?php echo esc_attr( $key ); ?>" class="regular-text">
		<p class="description"><?php _e( 'Enter your LiteSOC API Key from your dashboard.', 'litesoc' ); ?></p>
		<?php
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap litesoc-admin-wrap">
			<div class="litesoc-settings-header">
				<div class="litesoc-header-content">
					<img src="<?php echo esc_url( LITESOC_URL . 'logo.png' ); ?>" class="litesoc-logo-header" alt="9M2PJU LiteSOC Logo">
					<div class="litesoc-title-block">
						<h1><?php _e( '9M2PJU LiteSOC', 'litesoc' ); ?></h1>
						<p class="litesoc-subtitle"><?php _e( 'Threat Detection & Event Tracking', 'litesoc' ); ?></p>
					</div>
				</div>
			</div>
			<div class="litesoc-settings-body">
				<form action="options.php" method="post">
				<?php
				settings_fields( 'litesoc_settings' );
				do_settings_sections( 'litesoc' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function enqueue_styles( $hook ) {
		if ( 'toplevel_page_litesoc' !== $hook && 'index.php' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'litesoc-admin', LITESOC_URL . 'admin/css/litesoc-admin.css', array(), LITESOC_VERSION );
	}

	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'litesoc_events_widget',
			__( '9M2PJU LiteSOC Recent Security Events', 'litesoc' ),
			array( $this, 'render_dashboard_widget' )
		);
	}

	public function render_dashboard_widget() {
		$events = $this->api->get_events( array( 'limit' => 5 ) );

		if ( is_wp_error( $events ) ) {
			echo '<p>' . esc_html( $events->get_error_message() ) . '</p>';
			return;
		}

		if ( empty( $events['data']['data'] ) ) {
			echo '<p>' . __( 'No recent events found.', 'litesoc' ) . '</p>';
			return;
		}

		echo '<ul class="litesoc-event-list">';
		foreach ( $events['data']['data'] as $event ) {
			$severity_class = 'litesoc-severity-' . strtolower( $event['severity'] );
			echo '<li>';
			echo '<span class="litesoc-event-name">' . esc_html( $event['event_name'] ) . '</span>';
			echo '<span class="litesoc-event-meta">' . esc_html( $event['user_ip'] ) . ' - ' . esc_html( $event['actor_id'] ) . '</span>';
			echo '<span class="litesoc-severity ' . esc_attr( $severity_class ) . '">' . esc_html( $event['severity'] ) . '</span>';
			echo '</li>';
		}
		echo '</ul>';
		echo '<p><a href="' . admin_url( 'admin.php?page=litesoc' ) . '">' . __( 'View all events', 'litesoc' ) . '</a></p>';
	}

	public function add_action_links( $links ) {
		$settings_link = '<a href="admin.php?page=litesoc">' . __( 'Settings', 'litesoc' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}
endif;
