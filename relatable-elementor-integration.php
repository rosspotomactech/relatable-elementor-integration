<?php
/**
 * Plugin Name: Relatable CRM Integration for Elementor Pro
 * Description: Integrates Elementor Pro Forms with Relatable CRM to automatically create or update contacts upon form submission.
 * Version: 1.0.0
 * Tested up to: 7.0.2
 * Requires PHP: 7.4
 * Author: Potomac Technologies, LLC
 * Author URI: https://potomactech.net
 * Text Domain: relatable-elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// -------------------------------------------------------------------
// Plugin Update Checker Integration
// -------------------------------------------------------------------
$puc_file = __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

if ( file_exists( $puc_file ) ) {
	require_once $puc_file;

	// Use the v5 PucFactory to build the GitHub update checker
	$relatableUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		'https://github.com/rosspotomactech/relatable-elementor-integration/',
		__FILE__,
		'relatable-elementor-integration'
	);

	// Optional: Set the branch that contains the stable release. 
	// Uncomment and modify the line below if you use a specific branch like 'main' or 'master'
	// $relatableUpdateChecker->setBranch('main');
}

// -------------------------------------------------------------------
// Main Plugin Class
// -------------------------------------------------------------------
class Relatable_Elementor_Integration {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// Register Admin Settings Page
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );

		// Register Elementor Action
		add_action( 'elementor_pro/forms/actions/register', [ $this, 'register_elementor_form_action' ] );
	}

	/**
	 * Add Settings Menu under WordPress Settings
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Relatable CRM Integration', 'relatable-elementor' ),
			__( 'Relatable CRM', 'relatable-elementor' ),
			'manage_options',
			'relatable-crm-settings',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Register Settings API options with sanitization
	 */
	public function register_settings() {
		register_setting(
			'relatable_settings_group',
			'relatable_api_key',
			[
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			]
		);

		add_settings_section(
			'relatable_main_section',
			__( 'API Configuration', 'relatable-elementor' ),
			null,
			'relatable-crm-settings'
		);

		add_settings_field(
			'relatable_api_key',
			__( 'API Key', 'relatable-elementor' ),
			[ $this, 'render_api_key_field' ],
			'relatable-crm-settings',
			'relatable_main_section'
		);
	}

	/**
	 * Render API Key input field
	 */
	public function render_api_key_field() {
		$api_key = get_option( 'relatable_api_key', '' );
		printf(
			'<input type="password" name="relatable_api_key" value="%s" class="regular-text" required />',
			esc_attr( $api_key )
		);
	}

	/**
	 * Render Admin Settings HTML
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'relatable_settings_group' );
				do_settings_sections( 'relatable-crm-settings' );
				submit_button( __( 'Save Settings', 'relatable-elementor' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register Custom Elementor Form Action Handler
	 *
	 * @param \ElementorPro\Modules\Forms\Registrar $form_actions_registrar
	 */
	public function register_elementor_form_action( $form_actions_registrar ) {
		require_once __DIR__ . '/includes/class-relatable-action.php';
		$form_actions_registrar->register( new Relatable_Elementor_Form_Action() );
	}
}

// Initialize Plugin
add_action( 'plugins_loaded', function() {
	Relatable_Elementor_Integration::get_instance();
} );