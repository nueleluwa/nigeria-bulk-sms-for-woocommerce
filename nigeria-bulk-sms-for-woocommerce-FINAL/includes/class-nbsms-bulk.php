<?php
/**
 * Bulk SMS Handler
 * 
 * Handles bulk SMS operations including customer segmentation,
 * CSV import, batch processing, and scheduled sending.
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.6.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class NBSMS_Bulk {

    /**
     * Initialize bulk SMS handler
     */
    public function __construct() {
        // Add admin menu for bulk SMS
        add_action('admin_menu', array($this, 'register_bulk_menu'), 20);
        
        // AJAX handlers
        add_action('wp_ajax_nbsms_get_customers', array($this, 'ajax_get_customers'));
        add_action('wp_ajax_nbsms_estimate_cost', array($this, 'ajax_estimate_cost'));
        add_action('wp_ajax_nbsms_send_bulk', array($this, 'ajax_send_bulk'));
        add_action('wp_ajax_nbsms_import_csv', array($this, 'ajax_import_csv'));
        add_action('wp_ajax_nbsms_schedule_bulk', array($this, 'ajax_schedule_bulk'));
        
        // Cron hook for scheduled messages
        add_action('nbsms_process_scheduled_bulk', array($this, 'process_scheduled_bulk'));
    }

    /**
     * Register bulk SMS menu
     */
    public function register_bulk_menu() {
        add_submenu_page(
            'nigeria-bulk-sms-for-woocommerce',
            'Bulk SMS',
            'Bulk SMS',
            'manage_options',
            'nbsms-bulk',
            array($this, 'render_bulk_page')
        );
    }

    /**
     * Render bulk SMS page
     */
    public function render_bulk_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        include plugin_dir_path(dirname(__FILE__)) . 'templates/admin-bulk.php';
    }

    /**
     * Get WooCommerce customers based on filters
     *
     * @param array $filters Segmentation filters
     * @return array Customer data
     */
    public function get_customers($filters = array()) {
        global $wpdb;

        $defaults = array(
            'segment'           => 'all',           // all, with_orders, no_orders, high_value, recent
            'min_orders'        => 0,
            'min_spent'         => 0,
            'days_since_order'  => 0,
            'limit'             => 1000,
            'offset'            => 0,
        );

        $filters = wp_parse_args($filters, $defaults);

        $customers = array();

        switch ($filters['segment']) {
            case 'all':
                $customers = $this->get_all_customers($filters);
                break;

            case 'with_orders':
                $customers = $this->get_customers_with_orders($filters);
                break;

            case 'no_orders':
                $customers = $this->get_customers_without_orders($filters);
                break;

            case 'high_value':
                $customers = $this->get_high_value_customers($filters);
                break;

            case 'recent':
                $customers = $this->get_recent_customers($filters);
                break;

            default:
                $customers = $this->get_all_customers($filters);
        }

        // Filter out customers without phone numbers
        $customers = array_filter($customers, function($customer) {
            return !empty($customer['phone']);
        });

        // Check opt-in status
        foreach ($customers as $key => $customer) {
            $opted_in = get_user_meta($customer['id'], 'nbsms_opt_in', true);
            $customers[$key]['opted_in'] = ($opted_in !== 'no');
        }

        return $customers;
    }

    /**
     * Get all customers
     */
    private function get_all_customers($filters) {
        $users = get_users(array(
            'role'    => 'customer',
            'number'  => $filters['limit'],
            'offset'  => $filters['offset'],
            'orderby' => 'registered',
            'order'   => 'DESC',
        ));

        $customers = array();
        foreach ($users as $user) {
            $phone = $this->get_user_phone($user->ID);
            if ($phone) {
                $customers[] = array(
                    'id'       => $user->ID,
                    'name'     => $user->display_name,
                    'email'    => $user->user_email,
                    'phone'    => $phone,
                    'orders'   => $this->get_user_order_count($user->ID),
                    'spent'    => $this->get_user_total_spent($user->ID),
                );
            }
        }

        return $customers;
    }

    /**
     * Get customers with orders
     */
    private function get_customers_with_orders($filters) {
        global $wpdb;

        $query = "
            SELECT DISTINCT pm.meta_value as customer_id
            FROM {$wpdb->prefix}postmeta pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_customer_user'
            AND pm.meta_value > 0
            AND p.post_type = 'shop_order'
            AND p.post_status IN ('wc-completed', 'wc-processing')
        ";

        if ($filters['min_orders'] > 0) {
            $query .= $wpdb->prepare("
                GROUP BY pm.meta_value
                HAVING COUNT(*) >= %d
            ", $filters['min_orders']);
        }

        $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $filters['limit'], $filters['offset']);

        $customer_ids = $wpdb->get_col($query);

        $customers = array();
        foreach ($customer_ids as $customer_id) {
            $user = get_userdata($customer_id);
            if ($user) {
                $phone = $this->get_user_phone($customer_id);
                if ($phone) {
                    $customers[] = array(
                        'id'       => $customer_id,
                        'name'     => $user->display_name,
                        'email'    => $user->user_email,
                        'phone'    => $phone,
                        'orders'   => $this->get_user_order_count($customer_id),
                        'spent'    => $this->get_user_total_spent($customer_id),
                    );
                }
            }
        }

        return $customers;
    }

    /**
     * Get customers without orders
     */
    private function get_customers_without_orders($filters) {
        $all_customers = $this->get_all_customers($filters);
        
        $customers_without_orders = array_filter($all_customers, function($customer) {
            return $customer['orders'] == 0;
        });

        return array_values($customers_without_orders);
    }

    /**
     * Get high value customers
     */
    private function get_high_value_customers($filters) {
        $customers = $this->get_customers_with_orders($filters);

        // Filter by minimum spent
        if ($filters['min_spent'] > 0) {
            $customers = array_filter($customers, function($customer) use ($filters) {
                return $customer['spent'] >= $filters['min_spent'];
            });
        }

        // Sort by spent (highest first)
        usort($customers, function($a, $b) {
            return $b['spent'] - $a['spent'];
        });

        return array_values($customers);
    }

    /**
     * Get recent customers
     */
    private function get_recent_customers($filters) {
        if ($filters['days_since_order'] > 0) {
            global $wpdb;

            $date_threshold = date('Y-m-d H:i:s', strtotime("-{$filters['days_since_order']} days"));

            $query = $wpdb->prepare("
                SELECT DISTINCT pm.meta_value as customer_id
                FROM {$wpdb->prefix}postmeta pm
                INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                WHERE pm.meta_key = '_customer_user'
                AND pm.meta_value > 0
                AND p.post_type = 'shop_order'
                AND p.post_status IN ('wc-completed', 'wc-processing')
                AND p.post_date >= %s
                ORDER BY p.post_date DESC
                LIMIT %d OFFSET %d
            ", $date_threshold, $filters['limit'], $filters['offset']);

            $customer_ids = $wpdb->get_col($query);

            $customers = array();
            foreach ($customer_ids as $customer_id) {
                $user = get_userdata($customer_id);
                if ($user) {
                    $phone = $this->get_user_phone($customer_id);
                    if ($phone) {
                        $customers[] = array(
                            'id'       => $customer_id,
                            'name'     => $user->display_name,
                            'email'    => $user->user_email,
                            'phone'    => $phone,
                            'orders'   => $this->get_user_order_count($customer_id),
                            'spent'    => $this->get_user_total_spent($customer_id),
                        );
                    }
                }
            }

            return $customers;
        } else {
            return $this->get_all_customers($filters);
        }
    }

    /**
     * Get user phone number
     */
    private function get_user_phone($user_id) {
        $phone = get_user_meta($user_id, 'billing_phone', true);
        if (empty($phone)) {
            // Try to get from last order
            $orders = wc_get_orders(array(
                'customer_id' => $user_id,
                'limit'       => 1,
                'orderby'     => 'date',
                'order'       => 'DESC',
            ));

            if (!empty($orders)) {
                $order = $orders[0];
                $phone = $order->get_billing_phone();
            }
        }

        return NBSMS_API::format_phone($phone);
    }

    /**
     * Get user order count
     */
    private function get_user_order_count($user_id) {
        $customer = new WC_Customer($user_id);
        return $customer->get_order_count();
    }

    /**
     * Get user total spent
     */
    private function get_user_total_spent($user_id) {
        $customer = new WC_Customer($user_id);
        return $customer->get_total_spent();
    }

    /**
     * Import customers from CSV
     *
     * @param string $file_path Path to CSV file
     * @return array Import results
     */
    public function import_from_csv($file_path) {
        if (!file_exists($file_path)) {
            return array(
                'success' => false,
                'message' => 'File not found',
            );
        }

        $results = array(
            'success'   => true,
            'total'     => 0,
            'imported'  => 0,
            'skipped'   => 0,
            'customers' => array(),
            'errors'    => array(),
        );

        $handle = fopen($file_path, 'r');
        if ($handle === false) {
            return array(
                'success' => false,
                'message' => 'Could not open file',
            );
        }

        // Skip header row
        $header = fgetcsv($handle);
        
        // Expected columns: name, phone, email (optional)
        while (($row = fgetcsv($handle)) !== false) {
            $results['total']++;

            $name  = isset($row[0]) ? trim($row[0]) : '';
            $phone = isset($row[1]) ? trim($row[1]) : '';
            $email = isset($row[2]) ? trim($row[2]) : '';

            // Validate phone
            $formatted_phone = NBSMS_API::format_phone($phone);
            if (empty($formatted_phone)) {
                $results['skipped']++;
                $results['errors'][] = "Row {$results['total']}: Invalid phone number - {$phone}";
                continue;
            }

            // Add to results
            $results['customers'][] = array(
                'name'  => $name,
                'phone' => $formatted_phone,
                'email' => $email,
            );
            $results['imported']++;
        }

        fclose($handle);

        return $results;
    }

    /**
     * Estimate bulk SMS cost
     *
     * @param string $message Message content
     * @param int $recipient_count Number of recipients
     * @return array Cost estimation
     */
    public function estimate_cost($message, $recipient_count) {
        $sms_parts = NBSMS_API::calculate_sms_parts($message);
        $cost_per_sms = 4; // ₦4 per SMS part
        
        $total_sms = $sms_parts * $recipient_count;
        $total_cost = $total_sms * $cost_per_sms;

        return array(
            'message_length' => strlen($message),
            'sms_parts'      => $sms_parts,
            'recipients'     => $recipient_count,
            'total_sms'      => $total_sms,
            'cost_per_sms'   => $cost_per_sms,
            'total_cost'     => $total_cost,
        );
    }

    /**
     * Send bulk SMS
     *
     * @param array $recipients Array of recipients
     * @param string $message Message content
     * @param array $options Sending options
     * @return array Sending results
     */
    public function send_bulk($recipients, $message, $options = array()) {
        $defaults = array(
            'sender_id'     => get_option('nbsms_sender_id', 'Store'),
            'schedule_time' => null,
            'batch_size'    => 50,
            'priority'      => 5,
        );

        $options = wp_parse_args($options, $defaults);

        // If scheduled, save to scheduled table
        if (!empty($options['schedule_time'])) {
            return $this->schedule_bulk($recipients, $message, $options);
        }

        $results = array(
            'success'   => true,
            'total'     => count($recipients),
            'queued'    => 0,
            'skipped'   => 0,
            'errors'    => array(),
        );

        foreach ($recipients as $recipient) {
            // Skip if no phone
            if (empty($recipient['phone'])) {
                $results['skipped']++;
                continue;
            }

            // Skip if opted out
            if (isset($recipient['opted_in']) && !$recipient['opted_in']) {
                $results['skipped']++;
                continue;
            }

            // Parse message with customer data
            $parsed_message = $this->parse_bulk_message($message, $recipient);

            // Add to queue
            $queued = NBSMS_DB::insert_queue(array(
                'recipient_phone' => $recipient['phone'],
                'recipient_name'  => isset($recipient['name']) ? $recipient['name'] : '',
                'message'         => $parsed_message,
                'sender_id'       => $options['sender_id'],
                'message_type'    => 'bulk',
                'priority'        => $options['priority'],
                'meta_data'       => json_encode(array(
                    'customer_id' => isset($recipient['id']) ? $recipient['id'] : 0,
                )),
            ));

            if ($queued) {
                $results['queued']++;
            } else {
                $results['skipped']++;
                $results['errors'][] = "Failed to queue message to {$recipient['phone']}";
            }
        }

        return $results;
    }

    /**
     * Schedule bulk SMS for later sending
     *
     * @param array $recipients Recipients list
     * @param string $message Message content
     * @param array $options Sending options
     * @return array Schedule results
     */
    public function schedule_bulk($recipients, $message, $options) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'nbsms_scheduled';

        // Create scheduled table if not exists
        $this->create_scheduled_table();

        $schedule_time = strtotime($options['schedule_time']);
        if ($schedule_time === false || $schedule_time < time()) {
            return array(
                'success' => false,
                'message' => 'Invalid schedule time',
            );
        }

        // Save scheduled batch
        $batch_data = array(
            'recipients'    => json_encode($recipients),
            'message'       => $message,
            'sender_id'     => $options['sender_id'],
            'schedule_time' => date('Y-m-d H:i:s', $schedule_time),
            'status'        => 'pending',
            'total_count'   => count($recipients),
            'created_at'    => current_time('mysql'),
        );

        $inserted = $wpdb->insert($table_name, $batch_data);

        if ($inserted) {
            // Schedule cron event
            $this->schedule_cron_event($wpdb->insert_id, $schedule_time);

            return array(
                'success'      => true,
                'batch_id'     => $wpdb->insert_id,
                'schedule_time' => date('Y-m-d H:i:s', $schedule_time),
                'recipients'   => count($recipients),
            );
        }

        return array(
            'success' => false,
            'message' => 'Failed to schedule bulk SMS',
        );
    }

    /**
     * Create scheduled table
     */
    private function create_scheduled_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'nbsms_scheduled';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            recipients longtext NOT NULL,
            message text NOT NULL,
            sender_id varchar(11) NOT NULL,
            schedule_time datetime NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            total_count int(11) NOT NULL DEFAULT 0,
            sent_count int(11) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL,
            processed_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY status (status),
            KEY schedule_time (schedule_time)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Schedule cron event for bulk sending
     */
    private function schedule_cron_event($batch_id, $schedule_time) {
        wp_schedule_single_event($schedule_time, 'nbsms_process_scheduled_bulk', array($batch_id));
    }

    /**
     * Process scheduled bulk SMS
     *
     * @param int $batch_id Scheduled batch ID
     */
    public function process_scheduled_bulk($batch_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'nbsms_scheduled';

        $batch = $wpdb->get_row($wpdb->prepare("
            SELECT * FROM $table_name WHERE id = %d
        ", $batch_id));

        if (!$batch || $batch->status !== 'pending') {
            return;
        }

        // Update status to processing
        $wpdb->update(
            $table_name,
            array('status' => 'processing'),
            array('id' => $batch_id)
        );

        // Decode recipients
        $recipients = json_decode($batch->recipients, true);

        // Send bulk
        $results = $this->send_bulk($recipients, $batch->message, array(
            'sender_id' => $batch->sender_id,
        ));

        // Update batch status
        $wpdb->update(
            $table_name,
            array(
                'status'       => 'completed',
                'sent_count'   => $results['queued'],
                'processed_at' => current_time('mysql'),
            ),
            array('id' => $batch_id)
        );
    }

    /**
     * Parse bulk message with customer data
     *
     * @param string $message Message template
     * @param array $customer Customer data
     * @return string Parsed message
     */
    private function parse_bulk_message($message, $customer) {
        $replacements = array(
            '{name}'         => isset($customer['name']) ? $customer['name'] : '',
            '{customer_name}' => isset($customer['name']) ? $customer['name'] : '',
            '{email}'        => isset($customer['email']) ? $customer['email'] : '',
            '{phone}'        => isset($customer['phone']) ? $customer['phone'] : '',
            '{orders}'       => isset($customer['orders']) ? $customer['orders'] : 0,
            '{spent}'        => isset($customer['spent']) ? wc_price($customer['spent']) : wc_price(0),
        );

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    /**
     * AJAX: Get customers
     */
    public function ajax_get_customers() {
        check_ajax_referer('nbsms_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $filters = array(
            'segment'          => isset($_POST['segment']) ? sanitize_text_field($_POST['segment']) : 'all',
            'min_orders'       => isset($_POST['min_orders']) ? intval($_POST['min_orders']) : 0,
            'min_spent'        => isset($_POST['min_spent']) ? floatval($_POST['min_spent']) : 0,
            'days_since_order' => isset($_POST['days_since_order']) ? intval($_POST['days_since_order']) : 0,
            'limit'            => 1000,
        );

        $customers = $this->get_customers($filters);

        wp_send_json_success(array(
            'customers' => $customers,
            'count'     => count($customers),
        ));
    }

    /**
     * AJAX: Estimate cost
     */
    public function ajax_estimate_cost() {
        check_ajax_referer('nbsms_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
        $recipient_count = isset($_POST['recipient_count']) ? intval($_POST['recipient_count']) : 0;

        $estimate = $this->estimate_cost($message, $recipient_count);

        wp_send_json_success($estimate);
    }

    /**
     * AJAX: Send bulk SMS
     */
    public function ajax_send_bulk() {
        check_ajax_referer('nbsms_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $recipients = isset($_POST['recipients']) ? json_decode(stripslashes($_POST['recipients']), true) : array();
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
        $schedule_time = isset($_POST['schedule_time']) ? sanitize_text_field($_POST['schedule_time']) : '';

        if (empty($recipients) || empty($message)) {
            wp_send_json_error('Missing required fields');
        }

        $options = array(
            'schedule_time' => $schedule_time,
        );

        $results = $this->send_bulk($recipients, $message, $options);

        wp_send_json_success($results);
    }

    /**
     * AJAX: Import CSV
     */
    public function ajax_import_csv() {
        check_ajax_referer('nbsms_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        if (!isset($_FILES['csv_file'])) {
            wp_send_json_error('No file uploaded');
        }

        $file = $_FILES['csv_file'];

        // Validate file type
        $allowed_types = array('text/csv', 'text/plain', 'application/csv');
        if (!in_array($file['type'], $allowed_types)) {
            wp_send_json_error('Invalid file type. Please upload a CSV file.');
        }

        // Import
        $results = $this->import_from_csv($file['tmp_name']);

        wp_send_json_success($results);
    }

    /**
     * AJAX: Schedule bulk SMS
     */
    public function ajax_schedule_bulk() {
        // Same as send_bulk but with schedule_time
        $this->ajax_send_bulk();
    }
}

// Initialize
new NBSMS_Bulk();
