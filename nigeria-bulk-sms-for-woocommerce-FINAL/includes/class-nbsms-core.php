<?php
/**
 * Core Class
 *
 * Main plugin initialization and coordination
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBSMS_Core {

    /**
     * Single instance of the class
     *
     * @var NBSMS_Core
     */
    protected static $instance = null;

    /**
     * Settings instance
     *
     * @var NBSMS_Settings
     */
    public $settings;

    /**
     * API instance
     *
     * @var NBSMS_API
     */
    public $api;

    /**
     * Get instance
     *
     * @return NBSMS_Core
     * @since 1.0.0
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->init_hooks();
        $this->init_classes();
    }

    /**
     * Initialize hooks
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        // Load plugin text domain
        add_action( 'init', array( $this, 'load_textdomain' ) );

        // Register WP Cron schedules
        add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) );

        // Process queue via cron
        add_action( 'nbsms_process_queue', array( $this, 'process_queue' ) );

        // Clean old logs
        add_action( 'nbsms_clean_old_logs', array( $this, 'clean_old_logs' ) );

        // Add custom WooCommerce order statuses if needed
        add_action( 'init', array( $this, 'register_custom_order_statuses' ) );
    }

    /**
     * Initialize plugin classes
     *
     * @since 1.0.0
     */
    private function init_classes() {
        $this->settings = new NBSMS_Settings();
        $this->api      = new NBSMS_API();
    }

    /**
     * Load plugin text domain for translations
     *
     * @since 1.0.0
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'nigeria-bulk-sms-for-woocommerce',
            false,
            dirname( NBSMS_PLUGIN_BASENAME ) . '/languages/'
        );
    }

    /**
     * Add custom cron schedules
     *
     * @param array $schedules Existing schedules
     * @return array Modified schedules
     * @since 1.0.0
     */
    public function add_cron_schedules( $schedules ) {
        // Add 5-minute schedule for queue processing
        $schedules['five_minutes'] = array(
            'interval' => 300,
            'display'  => __( 'Every 5 Minutes', 'nigeria-bulk-sms-for-woocommerce' ),
        );

        // Add daily schedule for log cleanup
        if ( ! isset( $schedules['daily'] ) ) {
            $schedules['daily'] = array(
                'interval' => 86400,
                'display'  => __( 'Once Daily', 'nigeria-bulk-sms-for-woocommerce' ),
            );
        }

        return $schedules;
    }

    /**
     * Process SMS queue
     *
     * @since 1.0.0
     */
    public function process_queue() {
        // Get pending queue items
        $queue_items = NBSMS_DB::get_queue( array(
            'status' => 'pending',
            'limit'  => 50, // Process 50 items at a time
        ) );

        if ( empty( $queue_items ) ) {
            return;
        }

        foreach ( $queue_items as $item ) {
            // Check if max retries reached
            if ( $item->retry_count >= $item->max_retries ) {
                NBSMS_DB::update_queue( $item->id, array(
                    'status'        => 'failed',
                    'error_message' => __( 'Max retries reached', 'nigeria-bulk-sms-for-woocommerce' ),
                ) );
                continue;
            }

            // Send SMS
            $response = $this->api->send_sms(
                $item->recipient_phone,
                $item->message,
                $item->sender_id
            );

            if ( $response['success'] ) {
                // Log success
                $log_id = NBSMS_DB::insert_log( array(
                    'recipient_phone' => $item->recipient_phone,
                    'recipient_name'  => $item->recipient_name,
                    'message'         => $item->message,
                    'sender_id'       => $item->sender_id,
                    'status'          => 'sent',
                    'response_data'   => maybe_serialize( $response['data'] ),
                    'message_type'    => $item->message_type,
                    'order_id'        => $item->order_id,
                    'cost'            => isset( $response['data']['price'] ) ? $response['data']['price'] : 0,
                    'sms_count'       => $this->api->calculate_sms_parts( $item->message ),
                ) );

                // Remove from queue
                NBSMS_DB::delete_queue( $item->id );

                // Log action
                do_action( 'nbsms_sms_sent_successfully', $log_id, $item, $response );
            } else {
                // Update retry count
                NBSMS_DB::update_queue( $item->id, array(
                    'retry_count'   => $item->retry_count + 1,
                    'error_message' => $response['message'],
                ) );

                // Log action
                do_action( 'nbsms_sms_send_failed', $item, $response );
            }

            // Small delay to avoid overwhelming the API
            usleep( 100000 ); // 0.1 second delay
        }
    }

    /**
     * Clean old logs based on retention setting
     *
     * @since 1.0.0
     */
    public function clean_old_logs() {
        if ( $this->settings->get( 'enable_logs' ) !== 'yes' ) {
            return;
        }

        $retention_days = $this->settings->get( 'log_retention_days', 30 );
        $deleted = NBSMS_DB::delete_old_logs( $retention_days );

        if ( $deleted > 0 ) {
            do_action( 'nbsms_logs_cleaned', $deleted );
        }
    }

    /**
     * Register custom order statuses if needed
     *
     * @since 1.0.0
     */
    public function register_custom_order_statuses() {
        // This function is a placeholder for future custom order statuses
        // Can be used to add custom WooCommerce order statuses
        do_action( 'nbsms_register_order_statuses' );
    }

    /**
     * Get plugin version
     *
     * @return string
     * @since 1.0.0
     */
    public function get_version() {
        return NBSMS_VERSION;
    }

    /**
     * Get settings instance
     *
     * @return NBSMS_Settings
     * @since 1.0.0
     */
    public function get_settings() {
        return $this->settings;
    }

    /**
     * Get API instance
     *
     * @return NBSMS_API
     * @since 1.0.0
     */
    public function get_api() {
        return $this->api;
    }
}
