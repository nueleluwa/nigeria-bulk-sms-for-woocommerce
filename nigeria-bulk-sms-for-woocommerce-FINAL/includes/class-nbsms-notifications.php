<?php
/**
 * Notifications Handler Class
 *
 * Handles automated WooCommerce notification triggers
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBSMS_Notifications {

    /**
     * Single instance of the class
     *
     * @var NBSMS_Notifications
     */
    protected static $instance = null;

    /**
     * Available notification events
     *
     * @var array
     */
    private $notification_events = array();

    /**
     * Get instance
     *
     * @return NBSMS_Notifications
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
        $this->define_notification_events();
        $this->init_hooks();
    }

    /**
     * Define available notification events
     *
     * @since 1.0.0
     */
    private function define_notification_events() {
        $this->notification_events = array(
            'new_customer' => array(
                'title'       => __( 'New Customer Registration', 'nigeria-bulk-sms-for-woocommerce' ),
                'description' => __( 'Send welcome SMS when a new customer registers', 'nigeria-bulk-sms-for-woocommerce' ),
                'hook'        => 'woocommerce_created_customer',
                'default_template' => 'Welcome Message',
            ),
            'order_pending' => array(
                'title'       => __( 'Order Pending Payment', 'nigeria-bulk-sms-for-woocommerce' ),
                'description' => __( 'Send SMS when order is awaiting payment', 'nigeria-bulk-sms-for-woocommerce' ),
                'hook'        => 'woocommerce_order_status_pending',
                'default_template' => 'Order Confirmation',
            ),
            'order_processing' => array(
                'title'       => __( 'Order Processing', 'nigeria-bulk-sms-for-woocommerce' ),
                'description' => __( 'Send SMS when order status changes to processing', 'nigeria-bulk-sms-for-woocommerce' ),
                'hook'        => 'woocommerce_order_status_processing',
                'default_template' => 'Order Processing',
            ),
            'order_completed' => array(
                'title'       => __( 'Order Completed', 'nigeria-bulk-sms-for-woocommerce' ),
                'description' => __( 'Send SMS when order is marked as completed', 'nigeria-bulk-sms-for-woocommerce' ),
                'hook'        => 'woocommerce_order_status_completed',
                'default_template' => 'Order Completed',
            ),
            'order_cancelled' => array(
                'title'       => __( 'Order Cancelled', 'nigeria-bulk-sms-for-woocommerce' ),
                'description' => __( 'Send SMS when order is cancelled', 'nigeria-bulk-sms-for-woocommerce' ),
                'hook'        => 'woocommerce_order_status_cancelled',
                'default_template' => null,
            ),
            'order_refunded' => array(
                'title'       => __( 'Order Refunded', 'nigeria-bulk-sms-for-woocommerce' ),
                'description' => __( 'Send SMS when order is refunded', 'nigeria-bulk-sms-for-woocommerce' ),
                'hook'        => 'woocommerce_order_status_refunded',
                'default_template' => null,
            ),
            'payment_complete' => array(
                'title'       => __( 'Payment Received', 'nigeria-bulk-sms-for-woocommerce' ),
                'description' => __( 'Send SMS when payment is confirmed', 'nigeria-bulk-sms-for-woocommerce' ),
                'hook'        => 'woocommerce_payment_complete',
                'default_template' => 'Payment Received',
            ),
        );

        $this->notification_events = apply_filters( 'nbsms_notification_events', $this->notification_events );
    }

    /**
     * Initialize hooks
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        // Register WooCommerce hooks
        foreach ( $this->notification_events as $event_key => $event ) {
            if ( $this->is_notification_enabled( $event_key ) ) {
                add_action( $event['hook'], array( $this, 'handle_notification' ), 10, 2 );
            }
        }

        // Admin hooks for settings
        add_action( 'admin_init', array( $this, 'maybe_update_notification_settings' ) );
    }

    /**
     * Handle notification trigger
     *
     * @param int $order_id_or_customer_id Order or customer ID
     * @param WC_Order $order Order object (if available)
     * @since 1.0.0
     */
    public function handle_notification( $order_id_or_customer_id, $order = null ) {
        // Get the current action/hook that triggered this
        $current_hook = current_action();
        
        // Find which notification event this is
        $event_key = $this->get_event_key_by_hook( $current_hook );
        
        if ( ! $event_key ) {
            return;
        }

        // Handle different notification types
        if ( $event_key === 'new_customer' ) {
            $this->send_customer_notification( $order_id_or_customer_id, $event_key );
        } else {
            // Order-related notifications
            $order = $order ? $order : wc_get_order( $order_id_or_customer_id );
            
            if ( ! $order ) {
                return;
            }

            $this->send_order_notification( $order, $event_key );
        }
    }

    /**
     * Send customer notification
     *
     * @param int $customer_id Customer ID
     * @param string $event_key Event key
     * @since 1.0.0
     */
    private function send_customer_notification( $customer_id, $event_key ) {
        $customer = new WC_Customer( $customer_id );
        
        if ( ! $customer->get_id() ) {
            return;
        }

        $phone = $customer->get_billing_phone();
        
        if ( empty( $phone ) ) {
            return;
        }

        // Check opt-in status
        if ( ! $this->customer_opted_in( $customer_id ) ) {
            return;
        }

        // Get template
        $template_id = $this->get_notification_template( $event_key );
        
        if ( ! $template_id ) {
            return;
        }

        $template = $this->get_template_content( $template_id );
        
        if ( empty( $template ) ) {
            return;
        }

        // Parse template with customer data
        $data = array(
            'customer_name'      => $customer->get_first_name(),
            'customer_full_name' => $customer->get_first_name() . ' ' . $customer->get_last_name(),
            'customer_email'     => $customer->get_email(),
            'site_name'          => get_bloginfo( 'name' ),
            'site_url'           => home_url(),
        );

        require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-template-parser.php';
        $parser = new NBSMS_Template_Parser();
        $message = $parser->parse( $template, $data );

        // Add to queue
        $this->add_to_queue( $phone, $message, 'customer', null, $customer->get_first_name() );
    }

    /**
     * Send order notification
     *
     * @param WC_Order $order Order object
     * @param string $event_key Event key
     * @since 1.0.0
     */
    private function send_order_notification( $order, $event_key ) {
        $phone = $order->get_billing_phone();
        
        if ( empty( $phone ) ) {
            return;
        }

        // Check opt-in status
        if ( ! $this->order_customer_opted_in( $order ) ) {
            return;
        }

        // Check conditional logic
        if ( ! $this->meets_conditions( $order, $event_key ) ) {
            return;
        }

        // Get template
        $template_id = $this->get_notification_template( $event_key );
        
        if ( ! $template_id ) {
            return;
        }

        $template = $this->get_template_content( $template_id );
        
        if ( empty( $template ) ) {
            return;
        }

        // Parse template with order data
        require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-template-parser.php';
        $parser = new NBSMS_Template_Parser();
        $message = $parser->parse_with_order( $template, $order );

        // Add to queue
        $customer_name = $order->get_billing_first_name();
        $this->add_to_queue( $phone, $message, 'automated', $order->get_id(), $customer_name );
    }

    /**
     * Add message to queue
     *
     * @param string $phone Phone number
     * @param string $message Message content
     * @param string $type Message type
     * @param int $order_id Order ID
     * @param string $recipient_name Recipient name
     * @since 1.0.0
     */
    private function add_to_queue( $phone, $message, $type, $order_id = null, $recipient_name = '' ) {
        $settings = NBSMS_Core::instance()->get_settings();
        $sender_id = $settings->get( 'sender_id', '' );

        NBSMS_DB::insert_queue( array(
            'recipient_phone' => $phone,
            'recipient_name'  => $recipient_name,
            'message'         => $message,
            'sender_id'       => $sender_id,
            'message_type'    => $type,
            'order_id'        => $order_id,
            'priority'        => 5,
        ) );

        do_action( 'nbsms_message_queued', $phone, $message, $type, $order_id );
    }

    /**
     * Check if notification is enabled
     *
     * @param string $event_key Event key
     * @return bool
     * @since 1.0.0
     */
    private function is_notification_enabled( $event_key ) {
        $enabled_notifications = get_option( 'nbsms_enabled_notifications', array() );
        return in_array( $event_key, $enabled_notifications, true );
    }

    /**
     * Get notification template ID
     *
     * @param string $event_key Event key
     * @return int|null Template ID
     * @since 1.0.0
     */
    private function get_notification_template( $event_key ) {
        $templates = get_option( 'nbsms_notification_templates', array() );
        return isset( $templates[ $event_key ] ) ? absint( $templates[ $event_key ] ) : null;
    }

    /**
     * Get template content
     *
     * @param int $template_id Template ID
     * @return string Template content
     * @since 1.0.0
     */
    private function get_template_content( $template_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nbsms_templates';
        
        $template = $wpdb->get_row( $wpdb->prepare( "SELECT template_content FROM {$table} WHERE id = %d", $template_id ) );
        
        return $template ? $template->template_content : '';
    }

    /**
     * Check if customer opted in
     *
     * @param int $customer_id Customer ID
     * @return bool
     * @since 1.0.0
     */
    private function customer_opted_in( $customer_id ) {
        $opt_in = get_user_meta( $customer_id, 'nbsms_opt_in', true );
        
        // If not set, assume opted in (for existing customers)
        if ( $opt_in === '' ) {
            return true;
        }
        
        return $opt_in === 'yes';
    }

    /**
     * Check if order customer opted in
     *
     * @param WC_Order $order Order object
     * @return bool
     * @since 1.0.0
     */
    private function order_customer_opted_in( $order ) {
        // Check order meta first
        $opt_in = $order->get_meta( '_nbsms_opt_in' );
        
        if ( $opt_in !== '' ) {
            return $opt_in === 'yes';
        }

        // Check customer meta if logged in
        $customer_id = $order->get_customer_id();
        
        if ( $customer_id ) {
            return $this->customer_opted_in( $customer_id );
        }

        // Guest customers - assume opted in if they provided phone
        return true;
    }

    /**
     * Check if order meets conditions
     *
     * @param WC_Order $order Order object
     * @param string $event_key Event key
     * @return bool
     * @since 1.0.0
     */
    private function meets_conditions( $order, $event_key ) {
        $conditions = get_option( 'nbsms_notification_conditions', array() );
        
        if ( ! isset( $conditions[ $event_key ] ) ) {
            return true;
        }

        $condition = $conditions[ $event_key ];

        // Check minimum order amount
        if ( isset( $condition['min_amount'] ) && ! empty( $condition['min_amount'] ) ) {
            $min_amount = floatval( $condition['min_amount'] );
            if ( $order->get_total() < $min_amount ) {
                return false;
            }
        }

        return apply_filters( 'nbsms_notification_meets_conditions', true, $order, $event_key, $condition );
    }

    /**
     * Get event key by hook name
     *
     * @param string $hook Hook name
     * @return string|null Event key
     * @since 1.0.0
     */
    private function get_event_key_by_hook( $hook ) {
        foreach ( $this->notification_events as $key => $event ) {
            if ( $event['hook'] === $hook ) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Get all notification events
     *
     * @return array
     * @since 1.0.0
     */
    public function get_notification_events() {
        return $this->notification_events;
    }

    /**
     * Maybe update notification settings
     *
     * @since 1.0.0
     */
    public function maybe_update_notification_settings() {
        // This will be called from admin settings save
        // Implementation in admin class
    }
}

// Initialize
add_action( 'plugins_loaded', array( 'NBSMS_Notifications', 'instance' ), 20 );
