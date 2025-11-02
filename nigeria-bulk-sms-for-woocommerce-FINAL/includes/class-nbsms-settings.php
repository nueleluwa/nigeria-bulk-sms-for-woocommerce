<?php
/**
 * Settings Handler Class
 *
 * Manages plugin settings and options
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBSMS_Settings {

    /**
     * Settings option name
     *
     * @var string
     */
    private $option_name = 'nbsms_settings';

    /**
     * Get setting value
     *
     * @param string $key Setting key
     * @param mixed $default Default value
     * @return mixed
     * @since 1.0.0
     */
    public function get( $key, $default = '' ) {
        $value = get_option( 'nbsms_' . $key, $default );
        return apply_filters( 'nbsms_get_setting', $value, $key, $default );
    }

    /**
     * Update setting value
     *
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return bool
     * @since 1.0.0
     */
    public function update( $key, $value ) {
        $value = apply_filters( 'nbsms_update_setting', $value, $key );
        return update_option( 'nbsms_' . $key, $value );
    }

    /**
     * Delete setting
     *
     * @param string $key Setting key
     * @return bool
     * @since 1.0.0
     */
    public function delete( $key ) {
        return delete_option( 'nbsms_' . $key );
    }

    /**
     * Get all settings
     *
     * @return array
     * @since 1.0.0
     */
    public function get_all() {
        return array(
            'api_username'           => $this->get( 'api_username' ),
            'api_password'           => $this->get( 'api_password' ),
            'sender_id'              => $this->get( 'sender_id' ),
            'connection_timeout'     => $this->get( 'connection_timeout', 30 ),
            'enable_logs'            => $this->get( 'enable_logs', 'yes' ),
            'log_retention_days'     => $this->get( 'log_retention_days', 30 ),
            'notifications_enabled'  => $this->get( 'notifications_enabled', array() ),
        );
    }

    /**
     * Sanitize settings
     *
     * @param array $settings Raw settings
     * @return array Sanitized settings
     * @since 1.0.0
     */
    public function sanitize( $settings ) {
        $sanitized = array();

        if ( isset( $settings['api_username'] ) ) {
            $sanitized['api_username'] = sanitize_text_field( $settings['api_username'] );
        }

        if ( isset( $settings['api_password'] ) ) {
            $sanitized['api_password'] = sanitize_text_field( $settings['api_password'] );
        }

        if ( isset( $settings['sender_id'] ) ) {
            $sanitized['sender_id'] = sanitize_text_field( $settings['sender_id'] );
        }

        if ( isset( $settings['connection_timeout'] ) ) {
            $sanitized['connection_timeout'] = absint( $settings['connection_timeout'] );
        }

        if ( isset( $settings['enable_logs'] ) ) {
            $sanitized['enable_logs'] = $settings['enable_logs'] === 'yes' ? 'yes' : 'no';
        }

        if ( isset( $settings['log_retention_days'] ) ) {
            $sanitized['log_retention_days'] = absint( $settings['log_retention_days'] );
        }

        if ( isset( $settings['notifications_enabled'] ) ) {
            $sanitized['notifications_enabled'] = is_array( $settings['notifications_enabled'] ) 
                ? array_map( 'sanitize_text_field', $settings['notifications_enabled'] )
                : array();
        }

        return apply_filters( 'nbsms_sanitize_settings', $sanitized, $settings );
    }

    /**
     * Validate settings
     *
     * @param array $settings Settings to validate
     * @return array Validation errors
     * @since 1.0.0
     */
    public function validate( $settings ) {
        $errors = array();

        // Validate API username
        if ( empty( $settings['api_username'] ) ) {
            $errors['api_username'] = __( 'API Username is required.', 'nigeria-bulk-sms-for-woocommerce' );
        }

        // Validate API password
        if ( empty( $settings['api_password'] ) ) {
            $errors['api_password'] = __( 'API Password is required.', 'nigeria-bulk-sms-for-woocommerce' );
        }

        // Validate sender ID
        if ( ! empty( $settings['sender_id'] ) && strlen( $settings['sender_id'] ) > 11 ) {
            $errors['sender_id'] = __( 'Sender ID must not exceed 11 characters.', 'nigeria-bulk-sms-for-woocommerce' );
        }

        // Validate timeout
        if ( isset( $settings['connection_timeout'] ) ) {
            $timeout = absint( $settings['connection_timeout'] );
            if ( $timeout < 5 || $timeout > 120 ) {
                $errors['connection_timeout'] = __( 'Connection timeout must be between 5 and 120 seconds.', 'nigeria-bulk-sms-for-woocommerce' );
            }
        }

        return apply_filters( 'nbsms_validate_settings', $errors, $settings );
    }

    /**
     * Reset settings to defaults
     *
     * @return bool
     * @since 1.0.0
     */
    public function reset_to_defaults() {
        $this->update( 'api_username', '' );
        $this->update( 'api_password', '' );
        $this->update( 'sender_id', '' );
        $this->update( 'connection_timeout', 30 );
        $this->update( 'enable_logs', 'yes' );
        $this->update( 'log_retention_days', 30 );
        $this->update( 'notifications_enabled', array() );

        return true;
    }
}
