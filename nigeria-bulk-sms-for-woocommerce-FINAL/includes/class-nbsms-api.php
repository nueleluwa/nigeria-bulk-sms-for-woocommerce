<?php
/**
 * API Wrapper Class
 *
 * Handles all interactions with Nigeria Bulk SMS API
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBSMS_API {

    /**
     * API endpoint base URL
     */
    const API_ENDPOINT = 'https://portal.nigeriabulksms.com/api/';

    /**
     * API username
     *
     * @var string
     */
    private $username;

    /**
     * API password
     *
     * @var string
     */
    private $password;

    /**
     * Connection timeout
     *
     * @var int
     */
    private $timeout;

    /**
     * Last error message
     *
     * @var string
     */
    private $last_error;

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->username = get_option( 'nbsms_api_username', '' );
        $this->password = get_option( 'nbsms_api_password', '' );
        $this->timeout  = get_option( 'nbsms_connection_timeout', 30 );
    }

    /**
     * Send SMS message
     *
     * @param string|array $mobiles Phone number(s)
     * @param string $message Message content
     * @param string $sender_id Sender ID
     * @return array Response data
     * @since 1.0.0
     */
    public function send_sms( $mobiles, $message, $sender_id = '' ) {
        // Validate inputs
        if ( empty( $mobiles ) || empty( $message ) ) {
            return $this->format_error( 'Missing required parameters: mobiles and message are required.' );
        }

        // Use default sender ID if not provided
        if ( empty( $sender_id ) ) {
            $sender_id = get_option( 'nbsms_sender_id', '' );
        }

        // Format phone numbers
        if ( is_array( $mobiles ) ) {
            $mobiles = implode( ',', array_map( array( $this, 'format_phone_number' ), $mobiles ) );
        } else {
            $mobiles = $this->format_phone_number( $mobiles );
        }

        // Build API request parameters
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'message'  => $message,
            'sender'   => $sender_id,
            'mobiles'  => $mobiles,
        );

        // Make API request
        $response = $this->make_request( $params );

        return $response;
    }

    /**
     * Get account balance
     *
     * @return array Response data
     * @since 1.0.0
     */
    public function get_balance() {
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'action'   => 'balance',
        );

        return $this->make_request( $params );
    }

    /**
     * Test API connection
     *
     * @return array Response with connection status
     * @since 1.0.0
     */
    public function test_connection() {
        // Validate credentials
        if ( empty( $this->username ) || empty( $this->password ) ) {
            return array(
                'success' => false,
                'message' => __( 'API credentials are not configured.', 'nigeria-bulk-sms-for-woocommerce' ),
            );
        }

        // Try to get balance to test connection
        $response = $this->get_balance();

        if ( $response['success'] ) {
            return array(
                'success' => true,
                'message' => __( 'Connection successful!', 'nigeria-bulk-sms-for-woocommerce' ),
                'data'    => $response['data'],
            );
        } else {
            return array(
                'success' => false,
                'message' => $response['message'],
            );
        }
    }

    /**
     * Get message delivery report
     *
     * @param string $reference Message reference
     * @return array Response data
     * @since 1.0.0
     */
    public function get_delivery_report( $reference = '' ) {
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'action'   => 'reports',
        );

        return $this->make_request( $params );
    }

    /**
     * Make HTTP request to API
     *
     * @param array $params Request parameters
     * @return array Formatted response
     * @since 1.0.0
     */
    private function make_request( $params ) {
        // Make HTTP POST request for better security
        $response = wp_remote_post( self::API_ENDPOINT, array(
            'timeout' => $this->timeout,
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body'    => $params,
        ) );

        // Check for WP_Error
        if ( is_wp_error( $response ) ) {
            $this->last_error = $response->get_error_message();
            return $this->format_error( $this->last_error );
        }

        // Get response body
        $body = wp_remote_retrieve_body( $response );
        $http_code = wp_remote_retrieve_response_code( $response );

        // Parse JSON response
        $data = json_decode( $body, true );

        // Check if response is valid JSON
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            $this->last_error = 'Invalid JSON response from API';
            return $this->format_error( $this->last_error );
        }

        // Check for API errors
        if ( isset( $data['error'] ) && ! empty( $data['error'] ) ) {
            $error_code = isset( $data['errno'] ) ? $data['errno'] : '';
            $error_message = $this->get_error_message( $error_code, $data['error'] );
            $this->last_error = $error_message;
            
            return $this->format_error( $error_message, $error_code );
        }

        // Success response
        return $this->format_success( $data );
    }

    /**
     * Format success response
     *
     * @param array $data Response data
     * @return array
     * @since 1.0.0
     */
    private function format_success( $data ) {
        return array(
            'success' => true,
            'data'    => $data,
            'message' => isset( $data['status'] ) ? $data['status'] : 'Success',
        );
    }

    /**
     * Format error response
     *
     * @param string $message Error message
     * @param string $code Error code
     * @return array
     * @since 1.0.0
     */
    private function format_error( $message, $code = '' ) {
        return array(
            'success' => false,
            'message' => $message,
            'code'    => $code,
            'data'    => null,
        );
    }

    /**
     * Get user-friendly error message
     *
     * @param string $error_code Error code
     * @param string $default_message Default message
     * @return string
     * @since 1.0.0
     */
    private function get_error_message( $error_code, $default_message ) {
        $error_messages = array(
            '100' => __( 'Incomplete request parameters', 'nigeria-bulk-sms-for-woocommerce' ),
            '101' => __( 'Request denied', 'nigeria-bulk-sms-for-woocommerce' ),
            '103' => __( 'Login denied. Please check your API credentials.', 'nigeria-bulk-sms-for-woocommerce' ),
            '110' => __( 'Login status failed', 'nigeria-bulk-sms-for-woocommerce' ),
            '111' => __( 'Login status denied', 'nigeria-bulk-sms-for-woocommerce' ),
            '120' => __( 'Message limit reached', 'nigeria-bulk-sms-for-woocommerce' ),
            '121' => __( 'Mobile limit reached', 'nigeria-bulk-sms-for-woocommerce' ),
            '122' => __( 'Sender limit reached', 'nigeria-bulk-sms-for-woocommerce' ),
            '130' => __( 'Sender prohibited', 'nigeria-bulk-sms-for-woocommerce' ),
            '131' => __( 'Message prohibited', 'nigeria-bulk-sms-for-woocommerce' ),
            '140' => __( 'Invalid price setup', 'nigeria-bulk-sms-for-woocommerce' ),
            '141' => __( 'Invalid route setup', 'nigeria-bulk-sms-for-woocommerce' ),
            '142' => __( 'Invalid schedule date', 'nigeria-bulk-sms-for-woocommerce' ),
            '150' => __( 'Insufficient funds. Please recharge your account.', 'nigeria-bulk-sms-for-woocommerce' ),
            '151' => __( 'Gateway denied access', 'nigeria-bulk-sms-for-woocommerce' ),
            '152' => __( 'Service denied access', 'nigeria-bulk-sms-for-woocommerce' ),
            '160' => __( 'File upload error', 'nigeria-bulk-sms-for-woocommerce' ),
            '161' => __( 'File upload limit exceeded', 'nigeria-bulk-sms-for-woocommerce' ),
            '162' => __( 'File restricted', 'nigeria-bulk-sms-for-woocommerce' ),
            '190' => __( 'Maintenance in progress', 'nigeria-bulk-sms-for-woocommerce' ),
            '191' => __( 'Internal error', 'nigeria-bulk-sms-for-woocommerce' ),
        );

        return isset( $error_messages[ $error_code ] ) ? $error_messages[ $error_code ] : $default_message;
    }

    /**
     * Format phone number to API requirements
     *
     * @param string $phone Phone number
     * @return string Formatted phone number
     * @since 1.0.0
     */
    public function format_phone_number( $phone ) {
        // Remove all non-numeric characters
        $phone = preg_replace( '/[^0-9]/', '', $phone );

        // If number starts with 0, replace with 234
        if ( substr( $phone, 0, 1 ) === '0' ) {
            $phone = '234' . substr( $phone, 1 );
        }

        // If number doesn't start with 234, add it
        if ( substr( $phone, 0, 3 ) !== '234' ) {
            $phone = '234' . $phone;
        }

        return $phone;
    }

    /**
     * Validate phone number format
     *
     * @param string $phone Phone number
     * @return bool
     * @since 1.0.0
     */
    public function validate_phone_number( $phone ) {
        // Remove all non-numeric characters
        $phone = preg_replace( '/[^0-9]/', '', $phone );

        // Nigerian phone numbers should be 11 digits starting with 0
        // or 13 digits starting with 234
        // or 10 digits (without leading 0)
        if ( preg_match( '/^0[7-9][0-1]\d{8}$/', $phone ) ) {
            return true; // Nigerian format: 080XXXXXXXX
        }

        if ( preg_match( '/^234[7-9][0-1]\d{8}$/', $phone ) ) {
            return true; // International format: 2348XXXXXXXXX
        }

        if ( preg_match( '/^[7-9][0-1]\d{8}$/', $phone ) ) {
            return true; // Without leading 0: 80XXXXXXXX
        }

        return false;
    }

    /**
     * Calculate SMS parts
     *
     * @param string $message Message content
     * @return int Number of SMS parts
     * @since 1.0.0
     */
    public function calculate_sms_parts( $message ) {
        $length = mb_strlen( $message, 'UTF-8' );

        if ( $length <= 160 ) {
            return 1;
        }

        // Messages longer than 160 chars are split into 153-char parts
        return (int) ceil( $length / 153 );
    }

    /**
     * Get last error message
     *
     * @return string
     * @since 1.0.0
     */
    public function get_last_error() {
        return $this->last_error;
    }

    /**
     * Check if credentials are configured
     *
     * @return bool
     * @since 1.0.0
     */
    public function has_credentials() {
        return ! empty( $this->username ) && ! empty( $this->password );
    }
}
