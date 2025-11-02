<?php
/**
 * Database Operations Class
 *
 * Handles all database table creation and operations
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBSMS_DB {

    /**
     * Create plugin database tables
     *
     * @since 1.0.0
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Table for SMS logs
        $logs_table = $wpdb->prefix . 'nbsms_logs';
        $sql_logs = "CREATE TABLE IF NOT EXISTS {$logs_table} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            recipient_phone varchar(20) NOT NULL,
            recipient_name varchar(255) DEFAULT NULL,
            message text NOT NULL,
            sender_id varchar(11) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            response_data text DEFAULT NULL,
            message_type varchar(50) DEFAULT 'manual',
            order_id bigint(20) DEFAULT NULL,
            cost decimal(10,2) DEFAULT 0.00,
            sms_count int(11) DEFAULT 1,
            error_message text DEFAULT NULL,
            delivery_status varchar(20) DEFAULT NULL,
            delivery_time datetime DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY recipient_phone (recipient_phone),
            KEY status (status),
            KEY message_type (message_type),
            KEY order_id (order_id),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Table for message templates
        $templates_table = $wpdb->prefix . 'nbsms_templates';
        $sql_templates = "CREATE TABLE IF NOT EXISTS {$templates_table} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            template_name varchar(255) NOT NULL,
            template_content text NOT NULL,
            template_type varchar(50) NOT NULL DEFAULT 'custom',
            variables text DEFAULT NULL,
            is_default tinyint(1) NOT NULL DEFAULT 0,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY template_type (template_type),
            KEY is_active (is_active)
        ) $charset_collate;";

        // Table for message queue
        $queue_table = $wpdb->prefix . 'nbsms_queue';
        $sql_queue = "CREATE TABLE IF NOT EXISTS {$queue_table} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            recipient_phone varchar(20) NOT NULL,
            recipient_name varchar(255) DEFAULT NULL,
            message text NOT NULL,
            sender_id varchar(11) DEFAULT NULL,
            message_type varchar(50) DEFAULT 'automated',
            order_id bigint(20) DEFAULT NULL,
            priority int(11) DEFAULT 5,
            retry_count int(11) DEFAULT 0,
            max_retries int(11) DEFAULT 3,
            scheduled_time datetime DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            error_message text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY scheduled_time (scheduled_time),
            KEY priority (priority),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Execute table creation queries
        dbDelta( $sql_logs );
        dbDelta( $sql_templates );
        dbDelta( $sql_queue );

        // Insert default templates
        self::insert_default_templates();

        // Update plugin version
        update_option( 'nbsms_version', NBSMS_VERSION );
    }

    /**
     * Insert default message templates
     *
     * @since 1.0.0
     */
    private static function insert_default_templates() {
        global $wpdb;

        $templates_table = $wpdb->prefix . 'nbsms_templates';

        // Check if default templates already exist
        // Check cache first
        $cache_key = 'nbsms_default_templates_count';
        $count = wp_cache_get( $cache_key, 'nbsms' );
        
        if ( false === $count ) {
            $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$templates_table} WHERE is_default = %d", 1 ) );
            wp_cache_set( $cache_key, $count, 'nbsms', 3600 );
        }
        
        if ( $count > 0 ) {
            return; // Default templates already exist
        }

        $default_templates = array(
            array(
                'template_name' => 'Welcome Message',
                'template_content' => 'Welcome to {site_name}! Thank you for registering, {customer_name}. We\'re excited to have you!',
                'template_type' => 'customer',
                'variables' => 'customer_name,site_name',
                'is_default' => 1,
            ),
            array(
                'template_name' => 'Order Confirmation',
                'template_content' => 'Hi {customer_name}, your order #{order_id} has been received. Total: ₦{order_total}. Thank you for shopping with {site_name}!',
                'template_type' => 'order',
                'variables' => 'customer_name,order_id,order_total,site_name',
                'is_default' => 1,
            ),
            array(
                'template_name' => 'Order Processing',
                'template_content' => 'Hi {customer_name}, your order #{order_id} is now being processed. We\'ll notify you when it ships.',
                'template_type' => 'order',
                'variables' => 'customer_name,order_id',
                'is_default' => 1,
            ),
            array(
                'template_name' => 'Order Completed',
                'template_content' => 'Hi {customer_name}, your order #{order_id} has been completed. Thank you for choosing {site_name}!',
                'template_type' => 'order',
                'variables' => 'customer_name,order_id,site_name',
                'is_default' => 1,
            ),
            array(
                'template_name' => 'Order Shipped',
                'template_content' => 'Good news {customer_name}! Your order #{order_id} has been shipped. Tracking: {tracking_number}',
                'template_type' => 'order',
                'variables' => 'customer_name,order_id,tracking_number',
                'is_default' => 1,
            ),
            array(
                'template_name' => 'Payment Received',
                'template_content' => 'Payment confirmed for order #{order_id}. Amount: ₦{order_total}. Thank you {customer_name}!',
                'template_type' => 'order',
                'variables' => 'customer_name,order_id,order_total',
                'is_default' => 1,
            ),
        );

        foreach ( $default_templates as $template ) {
            $wpdb->insert( $templates_table, $template );
        }
    }

    /**
     * Get log entries with pagination
     *
     * @param array $args Query arguments
     * @return array
     * @since 1.0.0
     */
    public static function get_logs( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'per_page' => 20,
            'page' => 1,
            'status' => '',
            'message_type' => '',
            'date_from' => '',
            'date_to' => '',
            'search' => '',
            'orderby' => 'created_at',
            'order' => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );
        $logs_table = $wpdb->prefix . 'nbsms_logs';

        // Build WHERE clause
        $where = array( '1=1' );

        if ( ! empty( $args['status'] ) ) {
            $where[] = $wpdb->prepare( 'status = %s', $args['status'] );
        }

        if ( ! empty( $args['message_type'] ) ) {
            $where[] = $wpdb->prepare( 'message_type = %s', $args['message_type'] );
        }

        if ( ! empty( $args['date_from'] ) ) {
            $where[] = $wpdb->prepare( 'created_at >= %s', $args['date_from'] );
        }

        if ( ! empty( $args['date_to'] ) ) {
            $where[] = $wpdb->prepare( 'created_at <= %s', $args['date_to'] );
        }

        if ( ! empty( $args['search'] ) ) {
            $search = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where[] = $wpdb->prepare( '(recipient_phone LIKE %s OR recipient_name LIKE %s OR message LIKE %s)', $search, $search, $search );
        }

        $where_clause = implode( ' AND ', $where );

        // Get total count
        // Check cache first
        $cache_key = 'nbsms_logs_count_' . md5( serialize( $args ) );
        $total = wp_cache_get( $cache_key, 'nbsms' );
        
        if ( false === $total ) {
            $total = $wpdb->get_var( "SELECT COUNT(*) FROM {$logs_table} WHERE {$where_clause}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            wp_cache_set( $cache_key, $total, 'nbsms', 300 );
        }

        // Build main query
        $offset = ( $args['page'] - 1 ) * $args['per_page'];
        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );

        $query = "SELECT * FROM {$logs_table} WHERE {$where_clause} ORDER BY {$orderby} LIMIT %d OFFSET %d";
        $logs = $wpdb->get_results( $wpdb->prepare( $query, $args['per_page'], $offset ) );

        return array(
            'logs' => $logs,
            'total' => $total,
            'pages' => ceil( $total / $args['per_page'] ),
        );
    }

    /**
     * Insert a new log entry
     *
     * @param array $data Log data
     * @return int|false Insert ID or false on failure
     * @since 1.0.0
     */
    public static function insert_log( $data ) {
        global $wpdb;

        $logs_table = $wpdb->prefix . 'nbsms_logs';

        $defaults = array(
            'recipient_phone' => '',
            'recipient_name' => null,
            'message' => '',
            'sender_id' => null,
            'status' => 'pending',
            'response_data' => null,
            'message_type' => 'manual',
            'order_id' => null,
            'cost' => 0.00,
            'sms_count' => 1,
            'error_message' => null,
        );

        $data = wp_parse_args( $data, $defaults );

        $result = $wpdb->insert( $logs_table, $data );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Update log entry
     *
     * @param int $id Log ID
     * @param array $data Data to update
     * @return bool
     * @since 1.0.0
     */
    public static function update_log( $id, $data ) {
        global $wpdb;

        $logs_table = $wpdb->prefix . 'nbsms_logs';

        return $wpdb->update( $logs_table, $data, array( 'id' => $id ) );
    }

    /**
     * Delete old log entries
     *
     * @param int $days Number of days to retain
     * @return int Number of rows deleted
     * @since 1.0.0
     */
    public static function delete_old_logs( $days = 30 ) {
        global $wpdb;

        $logs_table = $wpdb->prefix . 'nbsms_logs';
        $date = date( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

        return $wpdb->query( $wpdb->prepare( "DELETE FROM {$logs_table} WHERE created_at < %s", $date ) );
    }

    /**
     * Get queue entries
     *
     * @param array $args Query arguments
     * @return array
     * @since 1.0.0
     */
    public static function get_queue( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'status' => 'pending',
            'limit' => 50,
        );

        $args = wp_parse_args( $args, $defaults );
        $queue_table = $wpdb->prefix . 'nbsms_queue';

        $query = $wpdb->prepare(
            "SELECT * FROM {$queue_table} 
            WHERE status = %s 
            AND (scheduled_time IS NULL OR scheduled_time <= NOW())
            ORDER BY priority DESC, created_at ASC
            LIMIT %d",
            $args['status'],
            $args['limit']
        );

        return $wpdb->get_results( $query );
    }

    /**
     * Insert entry into queue
     *
     * @param array $data Queue data
     * @return int|false
     * @since 1.0.0
     */
    public static function insert_queue( $data ) {
        global $wpdb;

        $queue_table = $wpdb->prefix . 'nbsms_queue';

        $defaults = array(
            'recipient_phone' => '',
            'recipient_name' => null,
            'message' => '',
            'sender_id' => null,
            'message_type' => 'automated',
            'order_id' => null,
            'priority' => 5,
            'scheduled_time' => null,
        );

        $data = wp_parse_args( $data, $defaults );

        $result = $wpdb->insert( $queue_table, $data );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Update queue entry
     *
     * @param int $id Queue ID
     * @param array $data Data to update
     * @return bool
     * @since 1.0.0
     */
    public static function update_queue( $id, $data ) {
        global $wpdb;

        $queue_table = $wpdb->prefix . 'nbsms_queue';

        return $wpdb->update( $queue_table, $data, array( 'id' => $id ) );
    }

    /**
     * Delete queue entry
     *
     * @param int $id Queue ID
     * @return bool
     * @since 1.0.0
     */
    public static function delete_queue( $id ) {
        global $wpdb;

        $queue_table = $wpdb->prefix . 'nbsms_queue';

        return $wpdb->delete( $queue_table, array( 'id' => $id ) );
    }
}
