<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '_9M2PJU_LiteSOC_WP_Admin' ) ) :
/**
 * LiteSOC Admin Interface
 */
class _9M2PJU_LiteSOC_WP_Admin {
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
			esc_html__( '9M2PJU LiteSOC WP Settings', '9m2pju-litesoc-wp' ),
			'9M2PJU LiteSOC',
			'manage_options',
			'9m2pju-litesoc-wp',
			array( $this, 'render_settings_page' ),
			'dashicons-shield-alt',
			80
		);
	}

	public function register_settings() {
		register_setting( '9m2pju_litesoc_wp_settings', '9m2pju_litesoc_wp_api_key', 'sanitize_text_field' );
		
		add_settings_section(
			'9m2pju_litesoc_wp_main_section',
			esc_html__( 'API Configuration', '9m2pju-litesoc-wp' ),
			null,
			'9m2pju-litesoc-wp'
		);

		add_settings_field(
			'9m2pju_litesoc_wp_api_key',
			esc_html__( 'LiteSOC API Key', '9m2pju-litesoc-wp' ),
			array( $this, 'render_api_key_field' ),
			'9m2pju-litesoc-wp',
			'9m2pju_litesoc_wp_main_section'
		);
	}

	public function render_api_key_field() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$key = get_option( '9m2pju_litesoc_wp_api_key' );
		?>
		<input type="password" name="9m2pju_litesoc_wp_api_key" value="<?php echo esc_attr( $key ); ?>" class="regular-text">
		<p class="description"><?php esc_html_e( 'Enter your LiteSOC API Key from your dashboard.', '9m2pju-litesoc-wp' ); ?></p>
		<?php
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap _9m2pju-litesoc-wp-admin-wrap">
			<div class="_9m2pju-litesoc-wp-settings-header">
				<div class="_9m2pju-litesoc-wp-header-content">
					<img src="<?php echo esc_url( _9M2PJU_LITESOC_WP_URL . 'logo.png' ); ?>" class="_9m2pju-litesoc-wp-logo-header" alt="9M2PJU LiteSOC Logo">
					<div class="_9m2pju-litesoc-wp-title-block">
						<h1><?php esc_html_e( '9M2PJU LiteSOC WP Plugin', '9m2pju-litesoc-wp' ); ?></h1>
						<p class="_9m2pju-litesoc-wp-subtitle"><?php esc_html_e( 'Threat Detection & Event Tracking', '9m2pju-litesoc-wp' ); ?></p>
					</div>
				</div>
			</div>
			<div class="_9m2pju-litesoc-wp-settings-body">
				<form action="options.php" method="post">
				<?php
				settings_fields( '9m2pju_litesoc_wp_settings' );
				do_settings_sections( '9m2pju-litesoc-wp' );
				submit_button();
				?>
				</form>
				
				<div class="_9m2pju-litesoc-wp-internal-footer">
					<p>
						<?php 
						printf(
							/* translators: 1: author name, 2: author URL, 3: version */
							esc_html__( 'By %1$s | Visit %2$s | Version %3$s', '9m2pju-litesoc-wp' ),
							'<a href="https://hamradio.my" target="_blank"><strong>9M2PJU</strong></a>',
							'<a href="https://github.com/9M2PJU/9M2PJU-LiteSOC-WP-Plugin" target="_blank">' . esc_html__( 'plugin site', '9m2pju-litesoc-wp' ) . '</a>',
							'<strong>' . esc_html( _9M2PJU_LITESOC_WP_VERSION ) . '</strong>'
						);
						?>
					</p>
					<div class="_9m2pju-litesoc-wp-donation-wrap">
						<a href="https://www.buymeacoffee.com/9m2pju" target="_blank" class="_9m2pju-litesoc-wp-bmc-button">
							<img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 40px !important;width: 144px !important;" >
						</a>
					</div>
				</div>
			</div> <!-- ._9m2pju-litesoc-wp-settings-body -->
		</div> <!-- ._9m2pju-litesoc-wp-admin-wrap -->
		<?php
	}

	public function enqueue_styles( $hook ) {
		if ( 'toplevel_page_9m2pju-litesoc-wp' !== $hook && 'index.php' !== $hook ) {
			return;
		}
		wp_enqueue_style( '9m2pju-litesoc-wp-admin', _9M2PJU_LITESOC_WP_URL . 'admin/css/9m2pju-litesoc-wp-admin.css', array(), _9M2PJU_LITESOC_WP_VERSION );
	}

	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'9m2pju_litesoc_wp_events_widget',
			esc_html__( '9M2PJU LiteSOC Recent Security Events', '9m2pju-litesoc-wp' ),
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
			echo '<p>' . esc_html__( 'No recent events found.', '9m2pju-litesoc-wp' ) . '</p>';
			return;
		}

		echo '<ul class="_9m2pju-litesoc-wp-event-list">';
		foreach ( $events['data']['data'] as $event ) {
			$severity_class = '_9m2pju-litesoc-wp-severity-' . strtolower( $event['severity'] );
			echo '<li>';
			echo '<span class="_9m2pju-litesoc-wp-event-name">' . esc_html( $event['event_name'] ) . '</span>';
			echo '<span class="_9m2pju-litesoc-wp-event-meta">' . esc_html( $event['user_ip'] ) . ' - ' . esc_html( $event['actor_id'] ) . '</span>';
			echo '<span class="_9m2pju-litesoc-wp-severity ' . esc_attr( $severity_class ) . '">' . esc_html( $event['severity'] ) . '</span>';
			echo '</li>';
		}
		echo '</ul>';
		echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=9m2pju-litesoc-wp' ) ) . '">' . esc_html__( 'View all events', '9m2pju-litesoc-wp' ) . '</a></p>';
	}

	public function add_action_links( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=9m2pju-litesoc-wp' ) ) . '">' . esc_html__( 'Settings', '9m2pju-litesoc-wp' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}
endif;
