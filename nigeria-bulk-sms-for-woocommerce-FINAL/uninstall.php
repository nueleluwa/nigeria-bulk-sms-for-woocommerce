<?php
/**
 * Uninstall Script
 *
 * Fired when the plugin is uninstalled.
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.1.0
 */

// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options
delete_option( 'nbsms_api_username' );
delete_option( 'nbsms_api_password' );
delete_option( 'nbsms_sender_id' );
delete_option( 'nbsms_connection_timeout' );
delete_option( 'nbsms_enable_logs' );
delete_option( 'nbsms_log_retention_days' );
delete_option( 'nbsms_notifications_enabled' );
delete_option( 'nbsms_notification_templates' );
delete_option( 'nbsms_notification_conditions' );
delete_option( 'nbsms_version' );

// Delete all transients
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_nbsms_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_nbsms_%'" );

// Clear scheduled cron events
wp_clear_scheduled_hook( 'nbsms_process_queue' );
wp_clear_scheduled_hook( 'nbsms_cleanup_old_logs' );

// Optional: Drop database tables
// Uncomment the following lines if you want to delete all data when the plugin is uninstalled
// Note: This will permanently delete all SMS logs, templates, and queue data

/*
global $wpdb;

$tables = array(
	$wpdb->prefix . 'nbsms_logs',
	$wpdb->prefix . 'nbsms_templates',
	$wpdb->prefix . 'nbsms_queue',
);

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}
*/

// Clear all caches
wp_cache_flush();
