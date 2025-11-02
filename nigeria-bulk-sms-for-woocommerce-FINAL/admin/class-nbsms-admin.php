<?php
/**
 * Admin Class
 *
 * Handles all admin-side functionality
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBSMS_Admin {

    /**
     * Single instance of the class
     *
     * @var NBSMS_Admin
     */
    protected static $instance = null;

    /**
     * Get instance
     *
     * @return NBSMS_Admin
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
    }

    /**
     * Initialize hooks
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        // Add admin menu
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

        // Enqueue admin scripts and styles
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Add settings link on plugins page
        add_filter( 'plugin_action_links_' . NBSMS_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );

        // AJAX handlers
        add_action( 'wp_ajax_nbsms_test_connection', array( $this, 'ajax_test_connection' ) );
        add_action( 'wp_ajax_nbsms_get_balance', array( $this, 'ajax_get_balance' ) );
        add_action( 'wp_ajax_nbsms_save_settings', array( $this, 'ajax_save_settings' ) );
        add_action( 'wp_ajax_nbsms_send_test_sms', array( $this, 'ajax_send_test_sms' ) );
        add_action( 'wp_ajax_nbsms_validate_phone', array( $this, 'ajax_validate_phone' ) );
        add_action( 'wp_ajax_nbsms_preview_template', array( $this, 'ajax_preview_template' ) );
    }

    /**
     * Add admin menu
     *
     * @since 1.0.0
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __( 'Nigeria Bulk SMS', 'nigeria-bulk-sms-for-woocommerce' ),
            __( 'Nigeria Bulk SMS', 'nigeria-bulk-sms-for-woocommerce' ),
            'manage_woocommerce',
            'nigeria-bulk-sms',
            array( $this, 'render_dashboard_page' ),
            'dashicons-email',
            56
        );

        // Dashboard submenu
        add_submenu_page(
            'nigeria-bulk-sms',
            __( 'Dashboard', 'nigeria-bulk-sms-for-woocommerce' ),
            __( 'Dashboard', 'nigeria-bulk-sms-for-woocommerce' ),
            'manage_woocommerce',
            'nigeria-bulk-sms',
            array( $this, 'render_dashboard_page' )
        );

        // Settings submenu
        add_submenu_page(
            'nigeria-bulk-sms',
            __( 'Settings', 'nigeria-bulk-sms-for-woocommerce' ),
            __( 'Settings', 'nigeria-bulk-sms-for-woocommerce' ),
            'manage_woocommerce',
            'nigeria-bulk-sms-settings',
            array( $this, 'render_settings_page' )
        );

        // Templates submenu
        add_submenu_page(
            'nigeria-bulk-sms',
            __( 'Templates', 'nigeria-bulk-sms-for-woocommerce' ),
            __( 'Templates', 'nigeria-bulk-sms-for-woocommerce' ),
            'manage_woocommerce',
            'nigeria-bulk-sms-templates',
            array( $this, 'render_templates_page' )
        );

        // Send SMS submenu
        add_submenu_page(
            'nigeria-bulk-sms',
            __( 'Send SMS', 'nigeria-bulk-sms-for-woocommerce' ),
            __( 'Send SMS', 'nigeria-bulk-sms-for-woocommerce' ),
            'manage_woocommerce',
            'nigeria-bulk-sms-send',
            array( $this, 'render_send_sms_page' )
        );

        // Testing submenu
        add_submenu_page(
            'nigeria-bulk-sms',
            __( 'Testing', 'nigeria-bulk-sms-for-woocommerce' ),
            __( 'Testing', 'nigeria-bulk-sms-for-woocommerce' ),
            'manage_woocommerce',
            'nigeria-bulk-sms-testing',
            array( $this, 'render_testing_page' )
        );

        // Logs submenu
        add_submenu_page(
            'nigeria-bulk-sms',
            __( 'Logs', 'nigeria-bulk-sms-for-woocommerce' ),
            __( 'Logs', 'nigeria-bulk-sms-for-woocommerce' ),
            'manage_woocommerce',
            'nigeria-bulk-sms-logs',
            array( $this, 'render_logs_page' )
        );

        // Reports submenu
        add_submenu_page(
            'nigeria-bulk-sms',
            __( 'Reports', 'nigeria-bulk-sms-for-woocommerce' ),
            __( 'Reports', 'nigeria-bulk-sms-for-woocommerce' ),
            'manage_woocommerce',
            'nigeria-bulk-sms-reports',
            array( $this, 'render_reports_page' )
        );
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook
     * @since 1.0.0
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on our plugin pages
        if ( strpos( $hook, 'nigeria-bulk-sms' ) === false ) {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'nbsms-admin-css',
            NBSMS_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            NBSMS_VERSION
        );

        // Enqueue Chart.js library (using WordPress bundled version or fallback)
        wp_enqueue_script(
            'nbsms-chartjs',
            NBSMS_PLUGIN_URL . 'admin/js/chart.min.js',
            array(),
            '4.4.0',
            true
        );

        // Enqueue JS
        wp_enqueue_script(
            'nbsms-admin-js',
            NBSMS_PLUGIN_URL . 'admin/js/admin.js',
            array( 'jquery', 'nbsms-chartjs' ),
            NBSMS_VERSION,
            true
        );

        // Localize script
        wp_localize_script(
            'nbsms-admin-js',
            'nbsmsAdmin',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'nbsms_admin_nonce' ),
                'strings'  => array(
                    'confirm_delete' => __( 'Are you sure you want to delete this item?', 'nigeria-bulk-sms-for-woocommerce' ),
                    'sending'        => __( 'Sending...', 'nigeria-bulk-sms-for-woocommerce' ),
                    'success'        => __( 'Success!', 'nigeria-bulk-sms-for-woocommerce' ),
                    'error'          => __( 'Error!', 'nigeria-bulk-sms-for-woocommerce' ),
                ),
            )
        );
    }

    /**
     * Add plugin action links
     *
     * @param array $links Existing links
     * @return array Modified links
     * @since 1.0.0
     */
    public function add_plugin_action_links( $links ) {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            admin_url( 'admin.php?page=nigeria-bulk-sms-settings' ),
            __( 'Settings', 'nigeria-bulk-sms-for-woocommerce' )
        );

        array_unshift( $links, $settings_link );

        return $links;
    }

    /**
     * Render dashboard page
     *
     * @since 1.0.0
     */
    public function render_dashboard_page() {
        // Redirect to logs page which has the analytics dashboard
        wp_redirect( admin_url( 'admin.php?page=nigeria-bulk-sms-logs' ) );
        exit;
    }

    /**
     * Render settings page
     *
     * @since 1.0.0
     */
    public function render_settings_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'nigeria-bulk-sms-for-woocommerce' ) );
        }

        // Handle form submission
        if ( isset( $_POST['nbsms_save_settings'] ) && check_admin_referer( 'nbsms_settings_nonce', 'nbsms_settings_nonce_field' ) ) {
            $this->save_settings();
        }

        // Get current settings
        $settings = NBSMS_Core::instance()->get_settings();
        $api = NBSMS_Core::instance()->get_api();

        // Get active tab
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'api';

        include NBSMS_PLUGIN_DIR . 'templates/admin-settings.php';
    }

    /**
     * Save settings
     *
     * @since 1.0.0
     */
    private function save_settings() {
        $settings = NBSMS_Core::instance()->get_settings();

        // Sanitize and validate settings
        $data = array(
            'api_username'       => isset( $_POST['api_username'] ) ? sanitize_text_field( wp_unslash( $_POST['api_username'] ) ) : '',
            'api_password'       => isset( $_POST['api_password'] ) ? sanitize_text_field( wp_unslash( $_POST['api_password'] ) ) : '',
            'sender_id'          => isset( $_POST['sender_id'] ) ? sanitize_text_field( wp_unslash( $_POST['sender_id'] ) ) : '',
            'connection_timeout' => isset( $_POST['connection_timeout'] ) ? absint( wp_unslash( $_POST['connection_timeout'] ) ) : 30,
            'enable_logs'        => isset( $_POST['enable_logs'] ) ? 'yes' : 'no',
            'log_retention_days' => isset( $_POST['log_retention_days'] ) ? absint( wp_unslash( $_POST['log_retention_days'] ) ) : 30,
        );

        // Handle notifications settings
        if ( isset( $_POST['save_notifications'] ) ) {
            $enabled_notifications = isset( $_POST['enabled_notifications'] ) && is_array( $_POST['enabled_notifications'] ) 
                ? array_map( 'sanitize_text_field', wp_unslash( $_POST['enabled_notifications'] ) ) 
                : array();
            
            $notification_templates = isset( $_POST['notification_templates'] ) && is_array( $_POST['notification_templates'] )
                ? array_map( 'absint', wp_unslash( $_POST['notification_templates'] ) )
                : array();
            
            $notification_conditions = isset( $_POST['notification_conditions'] ) && is_array( $_POST['notification_conditions'] )
                ? $this->sanitize_conditions( wp_unslash( $_POST['notification_conditions'] ) )
                : array();

            update_option( 'nbsms_enabled_notifications', $enabled_notifications );
            update_option( 'nbsms_notification_templates', $notification_templates );
            update_option( 'nbsms_notification_conditions', $notification_conditions );
        }

        // Validate
        $errors = $settings->validate( $data );

        if ( ! empty( $errors ) ) {
            add_settings_error(
                'nbsms_settings',
                'nbsms_validation_error',
                implode( '<br>', $errors ),
                'error'
            );
            return;
        }

        // Update settings
        foreach ( $data as $key => $value ) {
            $settings->update( $key, $value );
        }

        add_settings_error(
            'nbsms_settings',
            'nbsms_settings_saved',
            __( 'Settings saved successfully!', 'nigeria-bulk-sms-for-woocommerce' ),
            'success'
        );
    }

    /**
     * Sanitize notification conditions
     *
     * @param array $conditions Raw conditions
     * @return array Sanitized conditions
     * @since 1.0.0
     */
    private function sanitize_conditions( $conditions ) {
        $sanitized = array();
        
        foreach ( $conditions as $event_key => $condition ) {
            $sanitized[ sanitize_text_field( $event_key ) ] = array(
                'min_amount' => isset( $condition['min_amount'] ) ? floatval( $condition['min_amount'] ) : 0,
            );
        }
        
        return $sanitized;
    }

    /**
     * AJAX: Test API connection
     *
     * @since 1.0.0
     */
    public function ajax_test_connection() {
        check_ajax_referer( 'nbsms_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nigeria-bulk-sms-for-woocommerce' ) ) );
        }

        $api = new NBSMS_API();
        $result = $api->test_connection();

        if ( $result['success'] ) {
            wp_send_json_success( array(
                'message' => $result['message'],
                'balance' => isset( $result['data']['balance'] ) ? $result['data']['balance'] : null,
            ) );
        } else {
            wp_send_json_error( array( 'message' => $result['message'] ) );
        }
    }

    /**
     * AJAX: Get account balance
     *
     * @since 1.0.0
     */
    public function ajax_get_balance() {
        check_ajax_referer( 'nbsms_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nigeria-bulk-sms-for-woocommerce' ) ) );
        }

        $api = new NBSMS_API();
        $result = $api->get_balance();

        if ( $result['success'] ) {
            wp_send_json_success( array(
                'balance' => isset( $result['data']['balance'] ) ? $result['data']['balance'] : 'N/A',
                'data'    => $result['data'],
            ) );
        } else {
            wp_send_json_error( array( 'message' => $result['message'] ) );
        }
    }

    /**
     * AJAX: Save settings
     *
     * @since 1.0.0
     */
    public function ajax_save_settings() {
        check_ajax_referer( 'nbsms_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nigeria-bulk-sms-for-woocommerce' ) ) );
        }

        $settings = NBSMS_Core::instance()->get_settings();

        // Sanitize and validate settings
        $data = array(
            'api_username'       => isset( $_POST['api_username'] ) ? sanitize_text_field( wp_unslash( $_POST['api_username'] ) ) : '',
            'api_password'       => isset( $_POST['api_password'] ) ? sanitize_text_field( wp_unslash( $_POST['api_password'] ) ) : '',
            'sender_id'          => isset( $_POST['sender_id'] ) ? sanitize_text_field( wp_unslash( $_POST['sender_id'] ) ) : '',
            'connection_timeout' => isset( $_POST['connection_timeout'] ) ? absint( wp_unslash( $_POST['connection_timeout'] ) ) : 30,
            'enable_logs'        => isset( $_POST['enable_logs'] ) && $_POST['enable_logs'] === 'yes' ? 'yes' : 'no',
            'log_retention_days' => isset( $_POST['log_retention_days'] ) ? absint( wp_unslash( $_POST['log_retention_days'] ) ) : 30,
        );

        // Validate
        $errors = $settings->validate( $data );

        if ( ! empty( $errors ) ) {
            wp_send_json_error( array( 'message' => implode( '<br>', $errors ) ) );
        }

        // Update settings
        foreach ( $data as $key => $value ) {
            $settings->update( $key, $value );
        }

        wp_send_json_success( array( 'message' => __( 'Settings saved successfully!', 'nigeria-bulk-sms-for-woocommerce' ) ) );
    }

    /**
     * AJAX: Send test SMS
     *
     * @since 1.0.0
     */
    public function ajax_send_test_sms() {
        check_ajax_referer( 'nbsms_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nigeria-bulk-sms-for-woocommerce' ) ) );
        }

        // Get form data
        $phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';
        $sender_id = isset( $_POST['sender_id'] ) ? sanitize_text_field( wp_unslash( $_POST['sender_id'] ) ) : '';

        // Validate inputs
        if ( empty( $phone ) ) {
            wp_send_json_error( array( 'message' => __( 'Phone number is required.', 'nigeria-bulk-sms-for-woocommerce' ) ) );
        }

        if ( empty( $message ) ) {
            wp_send_json_error( array( 'message' => __( 'Message is required.', 'nigeria-bulk-sms-for-woocommerce' ) ) );
        }

        // Validate phone number
        $api = new NBSMS_API();
        if ( ! $api->validate_phone_number( $phone ) ) {
            wp_send_json_error( array( 
                'message' => __( 'Invalid phone number format. Use Nigerian format (e.g., 08012345678) or international format (e.g., +2348012345678).', 'nigeria-bulk-sms-for-woocommerce' ) 
            ) );
        }

        // Send SMS
        $response = $api->send_sms( $phone, $message, $sender_id );

        if ( $response['success'] ) {
            // Log the test SMS
            $log_data = array(
                'recipient_phone' => $api->format_phone_number( $phone ),
                'message'         => $message,
                'sender_id'       => $sender_id,
                'status'          => 'sent',
                'response_data'   => maybe_serialize( $response['data'] ),
                'message_type'    => 'test',
                'cost'            => isset( $response['data']['price'] ) ? $response['data']['price'] : 0,
                'sms_count'       => $api->calculate_sms_parts( $message ),
            );

            NBSMS_DB::insert_log( $log_data );

            wp_send_json_success( array(
                'message' => __( 'Test SMS sent successfully!', 'nigeria-bulk-sms-for-woocommerce' ),
                'data'    => array(
                    'cost'      => isset( $response['data']['price'] ) ? $response['data']['price'] : 0,
                    'count'     => isset( $response['data']['count'] ) ? $response['data']['count'] : 1,
                    'sms_parts' => $api->calculate_sms_parts( $message ),
                ),
            ) );
        } else {
            // Log failed attempt
            $log_data = array(
                'recipient_phone' => $api->format_phone_number( $phone ),
                'message'         => $message,
                'sender_id'       => $sender_id,
                'status'          => 'failed',
                'message_type'    => 'test',
                'error_message'   => $response['message'],
            );

            NBSMS_DB::insert_log( $log_data );

            wp_send_json_error( array( 'message' => $response['message'] ) );
        }
    }

    /**
     * AJAX: Validate phone number
     *
     * @since 1.0.0
     */
    public function ajax_validate_phone() {
        check_ajax_referer( 'nbsms_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nigeria-bulk-sms-for-woocommerce' ) ) );
        }

        $phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

        if ( empty( $phone ) ) {
            wp_send_json_error( array( 'message' => __( 'Phone number is required.', 'nigeria-bulk-sms-for-woocommerce' ) ) );
        }

        $api = new NBSMS_API();
        $is_valid = $api->validate_phone_number( $phone );

        if ( $is_valid ) {
            $formatted = $api->format_phone_number( $phone );
            wp_send_json_success( array(
                'message'   => __( 'Valid phone number', 'nigeria-bulk-sms-for-woocommerce' ),
                'formatted' => $formatted,
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'Invalid phone number format. Use Nigerian format (e.g., 08012345678) or international format (e.g., +2348012345678).', 'nigeria-bulk-sms-for-woocommerce' ),
            ) );
        }
    }

    /**
     * Render templates page
     *
     * @since 1.0.0
     */
    public function render_templates_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'nigeria-bulk-sms-for-woocommerce' ) );
        }

        // Handle actions
        $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : 'list';
        $template_id = isset( $_GET['template_id'] ) ? absint( $_GET['template_id'] ) : 0;

        // Handle form submissions
        if ( isset( $_POST['nbsms_save_template'] ) && check_admin_referer( 'nbsms_template_nonce', 'nbsms_template_nonce_field' ) ) {
            $this->save_template();
            $action = 'list';
        }

        if ( isset( $_POST['nbsms_delete_template'] ) && check_admin_referer( 'nbsms_delete_template_' . $template_id ) ) {
            $this->delete_template( $template_id );
            $action = 'list';
        }

        // Route to appropriate view
        switch ( $action ) {
            case 'add':
                $this->render_template_form();
                break;
            case 'edit':
                $this->render_template_form( $template_id );
                break;
            case 'view':
                $this->render_template_view( $template_id );
                break;
            default:
                $this->render_templates_list();
                break;
        }
    }

    /**
     * Render templates list
     *
     * @since 1.0.0
     */
    private function render_templates_list() {
        $templates = $this->get_all_templates();
        include NBSMS_PLUGIN_DIR . 'templates/admin-templates-list.php';
    }

    /**
     * Render template form (add/edit)
     *
     * @param int $template_id Template ID for editing
     * @since 1.0.0
     */
    private function render_template_form( $template_id = 0 ) {
        $template = null;
        $is_edit = false;

        if ( $template_id > 0 ) {
            $template = $this->get_template( $template_id );
            $is_edit = true;
        }

        include NBSMS_PLUGIN_DIR . 'templates/admin-template-form.php';
    }

    /**
     * Render template view
     *
     * @param int $template_id Template ID
     * @since 1.0.0
     */
    private function render_template_view( $template_id ) {
        $template = $this->get_template( $template_id );
        
        if ( ! $template ) {
            wp_die( esc_html__( 'Template not found.', 'nigeria-bulk-sms-for-woocommerce' ) );
        }

        include NBSMS_PLUGIN_DIR . 'templates/admin-template-view.php';
    }

    /**
     * Get all templates
     *
     * @return array
     * @since 1.0.0
     */
    private function get_all_templates() {
        global $wpdb;
        $table = $wpdb->prefix . 'nbsms_templates';
        
        return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY is_default DESC, template_name ASC" );
    }

    /**
     * Get single template
     *
     * @param int $template_id Template ID
     * @return object|null
     * @since 1.0.0
     */
    private function get_template( $template_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nbsms_templates';
        
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $template_id ) );
    }

    /**
     * Save template (add or update)
     *
     * @since 1.0.0
     */
    private function save_template() {
        global $wpdb;
        $table = $wpdb->prefix . 'nbsms_templates';

        $template_id = isset( $_POST['template_id'] ) ? absint( wp_unslash( $_POST['template_id'] ) ) : 0;
        $template_name = isset( $_POST['template_name'] ) ? sanitize_text_field( wp_unslash( $_POST['template_name'] ) ) : '';
        $template_content = isset( $_POST['template_content'] ) ? sanitize_textarea_field( $_POST['template_content'] ) : '';
        $template_type = isset( $_POST['template_type'] ) ? sanitize_text_field( wp_unslash( $_POST['template_type'] ) ) : 'custom';
        $is_active = isset( $_POST['is_active'] ) ? 1 : 0;

        // Validate
        if ( empty( $template_name ) || empty( $template_content ) ) {
            add_settings_error(
                'nbsms_templates',
                'nbsms_template_error',
                __( 'Template name and content are required.', 'nigeria-bulk-sms-for-woocommerce' ),
                'error'
            );
            return;
        }

        $data = array(
            'template_name'    => $template_name,
            'template_content' => $template_content,
            'template_type'    => $template_type,
            'is_active'        => $is_active,
        );

        if ( $template_id > 0 ) {
            // Update existing template
            $wpdb->update( $table, $data, array( 'id' => $template_id ) );
            $message = __( 'Template updated successfully!', 'nigeria-bulk-sms-for-woocommerce' );
        } else {
            // Insert new template
            $wpdb->insert( $table, $data );
            $message = __( 'Template created successfully!', 'nigeria-bulk-sms-for-woocommerce' );
        }

        add_settings_error(
            'nbsms_templates',
            'nbsms_template_saved',
            $message,
            'success'
        );
    }

    /**
     * Delete template
     *
     * @param int $template_id Template ID
     * @since 1.0.0
     */
    private function delete_template( $template_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nbsms_templates';

        // Check if it's a default template
        $template = $this->get_template( $template_id );
        if ( $template && $template->is_default ) {
            add_settings_error(
                'nbsms_templates',
                'nbsms_template_error',
                __( 'Cannot delete default templates.', 'nigeria-bulk-sms-for-woocommerce' ),
                'error'
            );
            return;
        }

        $wpdb->delete( $table, array( 'id' => $template_id ) );

        add_settings_error(
            'nbsms_templates',
            'nbsms_template_deleted',
            __( 'Template deleted successfully!', 'nigeria-bulk-sms-for-woocommerce' ),
            'success'
        );
    }

    /**
     * AJAX: Preview template with sample data
     *
     * @since 1.0.0
     */
    public function ajax_preview_template() {
        check_ajax_referer( 'nbsms_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nigeria-bulk-sms-for-woocommerce' ) ) );
        }

        $template_content = isset( $_POST['template_content'] ) ? sanitize_textarea_field( $_POST['template_content'] ) : '';

        if ( empty( $template_content ) ) {
            wp_send_json_error( array( 'message' => __( 'Template content is required.', 'nigeria-bulk-sms-for-woocommerce' ) ) );
        }

        // Sample data for preview
        $sample_data = array(
            'customer_name'      => 'John',
            'customer_full_name' => 'John Doe',
            'order_id'           => '12345',
            'order_total'        => '25,000',
            'order_status'       => 'Processing',
            'site_name'          => get_bloginfo( 'name' ),
            'site_url'           => home_url(),
            'tracking_number'    => 'TRK123456789',
            'product_name'       => 'Sample Product',
            'quantity'           => '2',
        );

        // Parse template
        require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-template-parser.php';
        $parser = new NBSMS_Template_Parser();
        $parsed = $parser->parse( $template_content, $sample_data );

        // Calculate SMS parts
        $api = new NBSMS_API();
        $sms_parts = $api->calculate_sms_parts( $parsed );
        $char_count = mb_strlen( $parsed );

        wp_send_json_success( array(
            'preview'    => $parsed,
            'char_count' => $char_count,
            'sms_parts'  => $sms_parts,
        ) );
    }

    /**
     * Render send SMS page
     *
     * @since 1.0.0
     */
    public function render_send_sms_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'nigeria-bulk-sms-for-woocommerce' ) );
        }

        // Load the fully-implemented bulk SMS template
        include NBSMS_PLUGIN_DIR . 'templates/admin-bulk.php';
    }

    /**
     * Render testing page
     *
     * @since 1.0.0
     */
    public function render_testing_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'nigeria-bulk-sms-for-woocommerce' ) );
        }

        // Check if API credentials are configured
        $api = NBSMS_Core::instance()->get_api();
        $settings = NBSMS_Core::instance()->get_settings();
        
        // Get test history
        $test_history = $this->get_test_history( 10 );

        include NBSMS_PLUGIN_DIR . 'templates/admin-testing.php';
    }

    /**
     * Get test SMS history
     *
     * @param int $limit Number of records to retrieve
     * @return array
     * @since 1.0.0
     */
    private function get_test_history( $limit = 10 ) {
        global $wpdb;
        $logs_table = $wpdb->prefix . 'nbsms_logs';

        $query = $wpdb->prepare(
            "SELECT * FROM {$logs_table} 
            WHERE message_type = 'test' 
            ORDER BY created_at DESC 
            LIMIT %d",
            $limit
        );

        return $wpdb->get_results( $query );
    }

    /**
     * Render logs page
     *
     * @since 1.0.0
     */
    public function render_logs_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'nigeria-bulk-sms-for-woocommerce' ) );
        }

        // Load the fully-implemented logs template with analytics
        include NBSMS_PLUGIN_DIR . 'templates/admin-logs.php';
    }

    /**
     * Render reports page
     *
     * @since 1.0.0
     */
    public function render_reports_page() {
        // Redirect to logs analytics tab which has reporting functionality
        wp_redirect( admin_url( 'admin.php?page=nigeria-bulk-sms-logs&tab=analytics' ) );
        exit;
    }
}
