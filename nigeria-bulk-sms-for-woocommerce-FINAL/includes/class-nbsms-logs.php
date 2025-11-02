<?php
/**
 * Logs Handler
 * 
 * Handles SMS logs viewing, filtering, searching, and exporting.
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.7.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class NBSMS_Logs {

    /**
     * Initialize logs handler
     */
    public function __construct() {
        // Add admin menu for logs
        add_action('admin_menu', array($this, 'register_logs_menu'), 15);
        
        // AJAX handlers
        add_action('wp_ajax_nbsms_get_logs', array($this, 'ajax_get_logs'));
        add_action('wp_ajax_nbsms_delete_log', array($this, 'ajax_delete_log'));
        add_action('wp_ajax_nbsms_bulk_delete_logs', array($this, 'ajax_bulk_delete_logs'));
        add_action('wp_ajax_nbsms_export_logs', array($this, 'ajax_export_logs'));
        add_action('wp_ajax_nbsms_get_log_stats', array($this, 'ajax_get_log_stats'));
    }

    /**
     * Register logs menu
     */
    public function register_logs_menu() {
        add_submenu_page(
            'nigeria-bulk-sms-for-woocommerce',
            'SMS Logs',
            'Logs',
            'manage_options',
            'nbsms-logs',
            array($this, 'render_logs_page')
        );
    }

    /**
     * Render logs page
     */
    public function render_logs_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        include plugin_dir_path(dirname(__FILE__)) . 'templates/admin-logs.php';
    }

    /**
     * Get logs with filters and pagination
     *
     * @param array $args Query arguments
     * @return array Logs and pagination data
     */
    public function get_logs($args = array()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbsms_logs';

        $defaults = array(
            'status'       => '',           // sent, failed, pending
            'type'         => '',           // automated, bulk, manual
            'date_from'    => '',
            'date_to'      => '',
            'search'       => '',
            'order_by'     => 'created_at',
            'order'        => 'DESC',
            'per_page'     => 20,
            'page'         => 1,
        );

        $args = wp_parse_args($args, $defaults);

        // Build WHERE clause
        $where = array('1=1');
        $where_values = array();

        // Status filter
        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }

        // Type filter
        if (!empty($args['type'])) {
            $where[] = 'message_type = %s';
            $where_values[] = $args['type'];
        }

        // Date range filter
        if (!empty($args['date_from'])) {
            $where[] = 'created_at >= %s';
            $where_values[] = $args['date_from'] . ' 00:00:00';
        }

        if (!empty($args['date_to'])) {
            $where[] = 'created_at <= %s';
            $where_values[] = $args['date_to'] . ' 23:59:59';
        }

        // Search filter
        if (!empty($args['search'])) {
            $where[] = '(recipient_phone LIKE %s OR recipient_name LIKE %s OR message LIKE %s)';
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }

        $where_clause = implode(' AND ', $where);

        // Count total records
        $count_query = "SELECT COUNT(*) FROM $table_name WHERE $where_clause";
        if (!empty($where_values)) {
            $count_query = $wpdb->prepare($count_query, $where_values);
        }
        $total_records = $wpdb->get_var($count_query);

        // Calculate pagination
        $total_pages = ceil($total_records / $args['per_page']);
        $offset = ($args['page'] - 1) * $args['per_page'];

        // Get logs
        $query = "SELECT * FROM $table_name WHERE $where_clause 
                  ORDER BY {$args['order_by']} {$args['order']} 
                  LIMIT %d OFFSET %d";
        
        $query_values = array_merge($where_values, array($args['per_page'], $offset));
        $query = $wpdb->prepare($query, $query_values);
        
        $logs = $wpdb->get_results($query);

        return array(
            'logs'          => $logs,
            'total_records' => $total_records,
            'total_pages'   => $total_pages,
            'current_page'  => $args['page'],
            'per_page'      => $args['per_page'],
        );
    }

    /**
     * Get log statistics
     *
     * @param array $args Date range and filters
     * @return array Statistics data
     */
    public function get_statistics($args = array()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbsms_logs';

        $defaults = array(
            'date_from' => date('Y-m-d', strtotime('-30 days')),
            'date_to'   => date('Y-m-d'),
        );

        $args = wp_parse_args($args, $defaults);

        // Base WHERE clause
        $where = "created_at >= %s AND created_at <= %s";
        $where_values = array(
            $args['date_from'] . ' 00:00:00',
            $args['date_to'] . ' 23:59:59',
        );

        // Total stats
        $total_query = $wpdb->prepare(
            "SELECT 
                COUNT(*) as total_sent,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as successful,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(sms_count) as total_sms_parts,
                SUM(cost) as total_cost
            FROM $table_name 
            WHERE $where",
            $where_values
        );

        $stats = $wpdb->get_row($total_query, ARRAY_A);

        // Calculate success rate
        $stats['success_rate'] = $stats['total_sent'] > 0 
            ? round(($stats['successful'] / $stats['total_sent']) * 100, 2) 
            : 0;

        // Average cost per SMS
        $stats['avg_cost'] = $stats['total_sent'] > 0 
            ? round($stats['total_cost'] / $stats['total_sent'], 2) 
            : 0;

        // Stats by type
        $type_query = $wpdb->prepare(
            "SELECT 
                message_type,
                COUNT(*) as count,
                SUM(cost) as cost
            FROM $table_name 
            WHERE $where
            GROUP BY message_type",
            $where_values
        );

        $stats['by_type'] = $wpdb->get_results($type_query, ARRAY_A);

        // Daily stats (last 7 days for chart)
        $daily_query = $wpdb->prepare(
            "SELECT 
                DATE(created_at) as date,
                COUNT(*) as count,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(cost) as cost
            FROM $table_name 
            WHERE created_at >= %s
            GROUP BY DATE(created_at)
            ORDER BY date ASC",
            date('Y-m-d', strtotime('-7 days')) . ' 00:00:00'
        );

        $stats['daily'] = $wpdb->get_results($daily_query, ARRAY_A);

        // Top recipients
        $top_recipients_query = $wpdb->prepare(
            "SELECT 
                recipient_phone,
                recipient_name,
                COUNT(*) as sms_count,
                SUM(cost) as total_cost
            FROM $table_name 
            WHERE $where
            GROUP BY recipient_phone
            ORDER BY sms_count DESC
            LIMIT 10",
            $where_values
        );

        $stats['top_recipients'] = $wpdb->get_results($top_recipients_query, ARRAY_A);

        return $stats;
    }

    /**
     * Delete log entry
     *
     * @param int $log_id Log ID
     * @return bool Success status
     */
    public function delete_log($log_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbsms_logs';

        $deleted = $wpdb->delete(
            $table_name,
            array('id' => $log_id),
            array('%d')
        );

        return $deleted !== false;
    }

    /**
     * Bulk delete logs
     *
     * @param array $log_ids Array of log IDs
     * @return int Number of deleted records
     */
    public function bulk_delete_logs($log_ids) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbsms_logs';

        if (empty($log_ids)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($log_ids), '%d'));
        $query = "DELETE FROM $table_name WHERE id IN ($placeholders)";
        
        $deleted = $wpdb->query($wpdb->prepare($query, $log_ids));

        return $deleted;
    }

    /**
     * Delete old logs based on retention settings
     *
     * @return int Number of deleted records
     */
    public function cleanup_old_logs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbsms_logs';

        $retention_days = get_option('nbsms_log_retention_days', 30);
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$retention_days} days"));

        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE created_at < %s",
            $cutoff_date
        ));

        return $deleted;
    }

    /**
     * Export logs to CSV
     *
     * @param array $args Filter arguments
     * @return string CSV file path
     */
    public function export_logs($args = array()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbsms_logs';

        // Remove pagination for export
        $args['per_page'] = 999999;
        $args['page'] = 1;

        $result = $this->get_logs($args);
        $logs = $result['logs'];

        // Create CSV file
        $upload_dir = wp_upload_dir();
        $export_dir = $upload_dir['basedir'] . '/nbsms-exports/';
        
        if (!file_exists($export_dir)) {
            wp_mkdir_p($export_dir);
        }

        $filename = 'sms-logs-' . date('Y-m-d-His') . '.csv';
        $filepath = $export_dir . $filename;

        $handle = fopen($filepath, 'w');

        // Add BOM for Excel UTF-8 support
        fwrite($handle, "\xEF\xBB\xBF");

        // Write header
        fputcsv($handle, array(
            'ID',
            'Date/Time',
            'Recipient Name',
            'Phone Number',
            'Message',
            'Status',
            'Type',
            'SMS Parts',
            'Cost (₦)',
            'Error Message',
        ));

        // Write data
        foreach ($logs as $log) {
            fputcsv($handle, array(
                $log->id,
                $log->created_at,
                $log->recipient_name,
                $log->recipient_phone,
                $log->message,
                $log->status,
                $log->message_type,
                $log->sms_count,
                $log->cost,
                $log->error_message,
            ));
        }

        fclose($handle);

        return array(
            'filepath' => $filepath,
            'filename' => $filename,
            'url'      => $upload_dir['baseurl'] . '/nbsms-exports/' . $filename,
            'count'    => count($logs),
        );
    }

    /**
     * Get single log details
     *
     * @param int $log_id Log ID
     * @return object|null Log data
     */
    public function get_log($log_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbsms_logs';

        $log = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $log_id
        ));

        return $log;
    }

    /**
     * AJAX: Get logs
     */
    public function ajax_get_logs() {
        check_ajax_referer('nbsms_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $args = array(
            'status'    => isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '',
            'type'      => isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '',
            'date_from' => isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : '',
            'date_to'   => isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : '',
            'search'    => isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '',
            'order_by'  => isset($_POST['order_by']) ? sanitize_text_field($_POST['order_by']) : 'created_at',
            'order'     => isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC',
            'per_page'  => isset($_POST['per_page']) ? intval($_POST['per_page']) : 20,
            'page'      => isset($_POST['page']) ? intval($_POST['page']) : 1,
        );

        $result = $this->get_logs($args);

        wp_send_json_success($result);
    }

    /**
     * AJAX: Delete log
     */
    public function ajax_delete_log() {
        check_ajax_referer('nbsms_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $log_id = isset($_POST['log_id']) ? intval($_POST['log_id']) : 0;

        if (!$log_id) {
            wp_send_json_error('Invalid log ID');
        }

        $deleted = $this->delete_log($log_id);

        if ($deleted) {
            wp_send_json_success('Log deleted successfully');
        } else {
            wp_send_json_error('Failed to delete log');
        }
    }

    /**
     * AJAX: Bulk delete logs
     */
    public function ajax_bulk_delete_logs() {
        check_ajax_referer('nbsms_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $log_ids = isset($_POST['log_ids']) ? array_map('intval', $_POST['log_ids']) : array();

        if (empty($log_ids)) {
            wp_send_json_error('No logs selected');
        }

        $deleted = $this->bulk_delete_logs($log_ids);

        wp_send_json_success(array(
            'deleted' => $deleted,
            'message' => sprintf('%d log(s) deleted successfully', $deleted),
        ));
    }

    /**
     * AJAX: Export logs
     */
    public function ajax_export_logs() {
        check_ajax_referer('nbsms_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $args = array(
            'status'    => isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '',
            'type'      => isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '',
            'date_from' => isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : '',
            'date_to'   => isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : '',
            'search'    => isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '',
        );

        $export = $this->export_logs($args);

        wp_send_json_success($export);
    }

    /**
     * AJAX: Get log statistics
     */
    public function ajax_get_log_stats() {
        check_ajax_referer('nbsms_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $args = array(
            'date_from' => isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : date('Y-m-d', strtotime('-30 days')),
            'date_to'   => isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : date('Y-m-d'),
        );

        $stats = $this->get_statistics($args);

        wp_send_json_success($stats);
    }
}

// Initialize
new NBSMS_Logs();
