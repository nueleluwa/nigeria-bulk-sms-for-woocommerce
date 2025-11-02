<?php
/**
 * Plugin Name: Nigeria Bulk SMS for WooCommerce
 * Plugin URI: https://github.com/nueleluwa/nigeria-bulk-sms-for-woocommerce
 * Description: Send automated & bulk SMS notifications to WooCommerce customers via Nigeria Bulk SMS API. Features include automated order notifications, customer segmentation, bulk campaigns, scheduled sending, analytics dashboard, and comprehensive logging.
 * Version: 1.1.0
 * Author: Emmanuel Eluwa
 * Author URI: https://github.com/nueleluwa
 * Text Domain: nigeria-bulk-sms-for-woocommerce
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 9.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @author Emmanuel Eluwa
 * @version 1.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'NBSMS_VERSION', '1.1.0' );
define( 'NBSMS_PLUGIN_FILE', __FILE__ );
define( 'NBSMS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NBSMS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'NBSMS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Check if WooCommerce is active
 *
 * @return bool
 */
function nbsms_is_woocommerce_active() {
	return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );
}

/**
 * Display admin notice if WooCommerce is not active
 */
function nbsms_woocommerce_missing_notice() {
	?>
	<div class="error">
		<p><?php echo esc_html__( 'Nigeria Bulk SMS for WooCommerce requires WooCommerce to be installed and activated.', 'nigeria-bulk-sms-for-woocommerce' ); ?></p>
	</div>
	<?php
}

/**
 * Declare HPOS compatibility
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * Initialize the plugin only if WooCommerce is active
 */
function nbsms_init() {
	if ( ! nbsms_is_woocommerce_active() ) {
		add_action( 'admin_notices', 'nbsms_woocommerce_missing_notice' );
		return;
	}

	// Load core files
	require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-api.php';
	require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-db.php';
	require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-settings.php';
	require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-core.php';
	require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-template-parser.php';
	require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-notifications.php';
	require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-opt-in.php';
	require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-bulk.php';
	require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-logs.php';

	// Initialize admin if in admin area
	if ( is_admin() ) {
		require_once NBSMS_PLUGIN_DIR . 'admin/class-nbsms-admin.php';
		NBSMS_Admin::instance();
	}

	// Initialize the core class
	NBSMS_Core::instance();
}
add_action( 'plugins_loaded', 'nbsms_init' );

/**
 * Plugin activation hook
 */
function nbsms_activate() {
	// Check WordPress version
	if ( version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ) {
		deactivate_plugins( NBSMS_PLUGIN_BASENAME );
		wp_die( esc_html__( 'This plugin requires WordPress 5.0 or higher.', 'nigeria-bulk-sms-for-woocommerce' ) );
	}

	// Check PHP version
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		deactivate_plugins( NBSMS_PLUGIN_BASENAME );
		wp_die( esc_html__( 'This plugin requires PHP 7.4 or higher.', 'nigeria-bulk-sms-for-woocommerce' ) );
	}

	// Check if WooCommerce is active
	if ( ! nbsms_is_woocommerce_active() ) {
		deactivate_plugins( NBSMS_PLUGIN_BASENAME );
		wp_die( esc_html__( 'This plugin requires WooCommerce to be installed and activated.', 'nigeria-bulk-sms-for-woocommerce' ) );
	}

	// Create database tables
	require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-db.php';
	NBSMS_DB::create_tables();

	// Set default options
	nbsms_set_default_options();

	// Schedule cron events
	if ( ! wp_next_scheduled( 'nbsms_process_queue' ) ) {
		wp_schedule_event( time(), 'hourly', 'nbsms_process_queue' );
	}

	// Flush rewrite rules
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'nbsms_activate' );

/**
 * Plugin deactivation hook
 */
function nbsms_deactivate() {
	// Clear scheduled cron events
	$timestamp = wp_next_scheduled( 'nbsms_process_queue' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'nbsms_process_queue' );
	}

	// Flush rewrite rules
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'nbsms_deactivate' );

/**
 * Set default plugin options
 */
function nbsms_set_default_options() {
	$defaults = array(
		'nbsms_api_username'          => '',
		'nbsms_api_password'          => '',
		'nbsms_sender_id'             => '',
		'nbsms_connection_timeout'    => 30,
		'nbsms_enable_logs'           => 'yes',
		'nbsms_log_retention_days'    => 30,
		'nbsms_notifications_enabled' => array(),
	);

	foreach ( $defaults as $key => $value ) {
		if ( false === get_option( $key ) ) {
			add_option( $key, $value );
		}
	}
}
