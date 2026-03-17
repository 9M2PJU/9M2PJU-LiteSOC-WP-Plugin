<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'LITESOC_9M2PJU_LiteSOC_Admin' ) ) :
/**
 * LiteSOC Admin Interface
 */
class LITESOC_9M2PJU_LiteSOC_Admin {
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
			esc_html__( '9M2PJU LiteSOC Settings', '9m2pju-litesoc' ),
			'9M2PJU LiteSOC',
			'manage_options',
			'9m2pju-litesoc',
			array( $this, 'render_settings_page' ),
			'dashicons-shield-alt',
			80
		);
	}

	public function register_settings() {
		register_setting( 'litesoc_9m2pju_options', 'litesoc_9m2pju_api_key', 'sanitize_text_field' );
		register_setting( 'litesoc_9m2pju_options', 'litesoc_9m2pju_source', 'sanitize_text_field' );
		register_setting( 'litesoc_9m2pju_options', 'litesoc_9m2pju_environment', 'sanitize_text_field' );
		
		add_settings_section(
			'litesoc_9m2pju_main_section',
			esc_html__( 'API Configuration', '9m2pju-litesoc' ),
			null,
			'9m2pju-litesoc'
		);

		add_settings_field(
			'litesoc_9m2pju_api_key',
			esc_html__( 'LiteSOC API Key', '9m2pju-litesoc' ),
			array( $this, 'render_api_key_field' ),
			'9m2pju-litesoc',
			'litesoc_9m2pju_main_section'
		);
		
		add_settings_field(
			'litesoc_9m2pju_source',
			esc_html__( 'Source', '9m2pju-litesoc' ),
			array( $this, 'render_source_field' ),
			'9m2pju-litesoc',
			'litesoc_9m2pju_main_section'
		);

		add_settings_field(
			'litesoc_9m2pju_environment',
			esc_html__( 'Environment', '9m2pju-litesoc' ),
			array( $this, 'render_environment_field' ),
			'9m2pju-litesoc',
			'litesoc_9m2pju_main_section'
		);
	}

	public function render_source_field() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$source = get_option( 'litesoc_9m2pju_source' );
		?>
		<input type="text" name="litesoc_9m2pju_source" value="<?php echo esc_attr( $source ); ?>" class="regular-text" placeholder="e.g. e-commerce-site">
		<p class="description"><?php esc_html_e( 'The name of the application or site where the events are originating from (e.g. "my-online-store", "marketing-blog").', '9m2pju-litesoc' ); ?></p>
		<?php
	}

	public function render_environment_field() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$env = get_option( 'litesoc_9m2pju_environment' );
		?>
		<input type="text" name="litesoc_9m2pju_environment" value="<?php echo esc_attr( $env ); ?>" class="regular-text" placeholder="e.g. production">
		<p class="description"><?php esc_html_e( 'The deployment environment (e.g. "production", "staging", "development").', '9m2pju-litesoc' ); ?></p>
		<?php
	}

	public function render_api_key_field() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$key = get_option( 'litesoc_9m2pju_api_key' );
		?>
		<input type="password" name="litesoc_9m2pju_api_key" value="<?php echo esc_attr( $key ); ?>" class="regular-text">
		<?php
		echo '<p class="description">' . sprintf(
			esc_html__( 'To get your API Key, please register at %1$s. Once logged in, you can find your key under Settings > My API Key in the LiteSOC dashboard.', '9m2pju-litesoc' ),
			'<a href="https://litesoc.io" target="_blank">litesoc.io</a>'
		) . '</p>';
		?>
		<?php
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap _9m2pju-litesoc-admin-wrap">
			<div class="_9m2pju-litesoc-settings-header">
				<div class="_9m2pju-litesoc-header-content">
					<img src="<?php echo esc_url( LITESOC_9M2PJU_URL . 'logo.png' ); ?>" class="_9m2pju-litesoc-logo-header" alt="9M2PJU LiteSOC Logo">
					<div class="_9m2pju-litesoc-title-block">
						<h1><?php esc_html_e( '9M2PJU LiteSOC', '9m2pju-litesoc' ); ?></h1>
						<p class="_9m2pju-litesoc-subtitle"><?php esc_html_e( 'Threat Detection & Event Tracking', '9m2pju-litesoc' ); ?></p>
					</div>
				</div>
			</div>
			<div class="_9m2pju-litesoc-settings-body">
				<form action="options.php" method="post">
				<?php
				settings_fields( 'litesoc_9m2pju_options' );
				do_settings_sections( '9m2pju-litesoc' );
				submit_button();
				?>
				</form>
				
				<div class="_9m2pju-litesoc-internal-footer">
					<p>
						<?php 
						printf(
							/* translators: 1: author name, 2: author URL, 3: version */
							esc_html__( 'By %1$s | Visit %2$s | Version %3$s', '9m2pju-litesoc' ),
							'<a href="https://hamradio.my" target="_blank"><strong>9M2PJU</strong></a>',
							'<a href="https://github.com/9M2PJU/9M2PJU-LiteSOC-WP-Plugin" target="_blank">' . esc_html__( 'plugin site', '9m2pju-litesoc' ) . '</a>',
							'<strong>' . esc_html( LITESOC_9M2PJU_VERSION ) . '</strong>'
						);
						?>
					</p>
					<div class="_9m2pju-litesoc-donation-wrap">
						<a href="https://www.buymeacoffee.com/9m2pju" target="_blank" class="_9m2pju-litesoc-bmc-button">
							<img src="<?php echo esc_url( LITESOC_9M2PJU_URL . 'assets/bmc-button.png' ); ?>" alt="Buy Me A Coffee" style="height: 40px !important;width: auto !important;" >
						</a>
					</div>
				</div>
			</div> <!-- ._9m2pju-litesoc-settings-body -->
		</div> <!-- ._9m2pju-litesoc-admin-wrap -->
		<?php
	}

	public function enqueue_styles( $hook ) {
		if ( 'toplevel_page_9m2pju-litesoc' !== $hook && 'index.php' !== $hook ) {
			return;
		}
		wp_enqueue_style( '9m2pju-litesoc-admin', LITESOC_9M2PJU_URL . 'admin/css/9m2pju-litesoc-admin.css', array(), LITESOC_9M2PJU_VERSION );
	}

	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'litesoc_9m2pju_events_widget',
			esc_html__( '9M2PJU LiteSOC Recent Security Events', '9m2pju-litesoc' ),
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
			echo '<p>' . esc_html__( 'No recent events found.', '9m2pju-litesoc' ) . '</p>';
			return;
		}

		echo '<ul class="_9m2pju-litesoc-event-list">';
		foreach ( $events['data']['data'] as $event ) {
			$severity_class = '_9m2pju-litesoc-severity-' . strtolower( $event['severity'] );
			echo '<li>';
			echo '<span class="_9m2pju-litesoc-event-name">' . esc_html( $event['event_name'] ) . '</span>';
			echo '<span class="_9m2pju-litesoc-event-meta">' . esc_html( $event['user_ip'] ) . ' - ' . esc_html( $event['actor_id'] ) . '</span>';
			echo '<span class="_9m2pju-litesoc-severity ' . esc_attr( $severity_class ) . '">' . esc_html( $event['severity'] ) . '</span>';
			echo '</li>';
		}
		echo '</ul>';
		echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=9m2pju-litesoc' ) ) . '">' . esc_html__( 'View all events', '9m2pju-litesoc' ) . '</a></p>';
	}

	public function add_action_links( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=9m2pju-litesoc' ) ) . '">' . esc_html__( 'Settings', '9m2pju-litesoc' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}
endif;
