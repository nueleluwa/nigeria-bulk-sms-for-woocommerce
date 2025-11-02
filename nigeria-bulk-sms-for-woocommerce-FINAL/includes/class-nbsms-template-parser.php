<?php
/**
 * Template Parser Class
 *
 * Handles template variable/shortcode parsing and replacement
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBSMS_Template_Parser {

    /**
     * Available variables/shortcodes
     *
     * @var array
     */
    private $available_variables = array(
        'customer_name',
        'customer_full_name',
        'customer_email',
        'customer_phone',
        'order_id',
        'order_total',
        'order_status',
        'order_date',
        'site_name',
        'site_url',
        'tracking_number',
        'product_name',
        'product_names',
        'quantity',
        'shipping_address',
        'billing_address',
        'payment_method',
    );

    /**
     * Parse template with data
     *
     * @param string $template Template content
     * @param array $data Data to replace variables
     * @return string Parsed template
     * @since 1.0.0
     */
    public function parse( $template, $data = array() ) {
        // Replace all variables
        foreach ( $this->available_variables as $variable ) {
            $placeholder = '{' . $variable . '}';
            $value = isset( $data[ $variable ] ) ? $data[ $variable ] : '';
            $template = str_replace( $placeholder, $value, $template );
        }

        // Remove any remaining unreplaced variables
        $template = preg_replace( '/\{[^}]+\}/', '', $template );

        return $template;
    }

    /**
     * Parse template with WooCommerce order data
     *
     * @param string $template Template content
     * @param WC_Order $order WooCommerce order object
     * @return string Parsed template
     * @since 1.0.0
     */
    public function parse_with_order( $template, $order ) {
        if ( ! $order instanceof WC_Order ) {
            return $template;
        }

        // Prepare order data
        $data = $this->get_order_data( $order );

        return $this->parse( $template, $data );
    }

    /**
     * Get order data for template parsing
     *
     * @param WC_Order $order WooCommerce order object
     * @return array Order data
     * @since 1.0.0
     */
    private function get_order_data( $order ) {
        // Get customer name
        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();
        $full_name = trim( $first_name . ' ' . $last_name );

        // Get product names
        $items = $order->get_items();
        $product_names = array();
        $total_quantity = 0;

        foreach ( $items as $item ) {
            $product_names[] = $item->get_name();
            $total_quantity += $item->get_quantity();
        }

        $product_names_str = implode( ', ', $product_names );
        $first_product = ! empty( $product_names ) ? $product_names[0] : '';

        // Get tracking number (from common shipping plugins)
        $tracking_number = '';
        if ( metadata_exists( 'post', $order->get_id(), '_tracking_number' ) ) {
            $tracking_number = get_post_meta( $order->get_id(), '_tracking_number', true );
        } elseif ( metadata_exists( 'post', $order->get_id(), '_wc_shipment_tracking_items' ) ) {
            $tracking_items = get_post_meta( $order->get_id(), '_wc_shipment_tracking_items', true );
            if ( is_array( $tracking_items ) && ! empty( $tracking_items ) ) {
                $tracking_number = $tracking_items[0]['tracking_number'] ?? '';
            }
        }

        // Prepare data array
        $data = array(
            'customer_name'      => $first_name,
            'customer_full_name' => $full_name,
            'customer_email'     => $order->get_billing_email(),
            'customer_phone'     => $order->get_billing_phone(),
            'order_id'           => $order->get_order_number(),
            'order_total'        => number_format( $order->get_total(), 2 ),
            'order_status'       => wc_get_order_status_name( $order->get_status() ),
            'order_date'         => $order->get_date_created()->date_i18n( get_option( 'date_format' ) ),
            'site_name'          => get_bloginfo( 'name' ),
            'site_url'           => home_url(),
            'tracking_number'    => $tracking_number,
            'product_name'       => $first_product,
            'product_names'      => $product_names_str,
            'quantity'           => $total_quantity,
            'shipping_address'   => $this->format_address( $order->get_address( 'shipping' ) ),
            'billing_address'    => $this->format_address( $order->get_address( 'billing' ) ),
            'payment_method'     => $order->get_payment_method_title(),
        );

        return apply_filters( 'nbsms_template_order_data', $data, $order );
    }

    /**
     * Format address for template
     *
     * @param array $address Address array
     * @return string Formatted address
     * @since 1.0.0
     */
    private function format_address( $address ) {
        $parts = array();

        if ( ! empty( $address['address_1'] ) ) {
            $parts[] = $address['address_1'];
        }

        if ( ! empty( $address['city'] ) ) {
            $parts[] = $address['city'];
        }

        if ( ! empty( $address['state'] ) ) {
            $parts[] = $address['state'];
        }

        return implode( ', ', $parts );
    }

    /**
     * Get available variables
     *
     * @return array Available variables
     * @since 1.0.0
     */
    public function get_available_variables() {
        return $this->available_variables;
    }

    /**
     * Get variables with descriptions
     *
     * @return array Variables with descriptions
     * @since 1.0.0
     */
    public function get_variables_with_descriptions() {
        return array(
            '{customer_name}'      => __( "Customer's first name", 'nigeria-bulk-sms-for-woocommerce' ),
            '{customer_full_name}' => __( "Customer's full name", 'nigeria-bulk-sms-for-woocommerce' ),
            '{customer_email}'     => __( "Customer's email address", 'nigeria-bulk-sms-for-woocommerce' ),
            '{customer_phone}'     => __( "Customer's phone number", 'nigeria-bulk-sms-for-woocommerce' ),
            '{order_id}'           => __( 'Order number', 'nigeria-bulk-sms-for-woocommerce' ),
            '{order_total}'        => __( 'Order total amount', 'nigeria-bulk-sms-for-woocommerce' ),
            '{order_status}'       => __( 'Order status', 'nigeria-bulk-sms-for-woocommerce' ),
            '{order_date}'         => __( 'Order date', 'nigeria-bulk-sms-for-woocommerce' ),
            '{site_name}'          => __( 'Store name', 'nigeria-bulk-sms-for-woocommerce' ),
            '{site_url}'           => __( 'Store URL', 'nigeria-bulk-sms-for-woocommerce' ),
            '{tracking_number}'    => __( 'Shipment tracking number', 'nigeria-bulk-sms-for-woocommerce' ),
            '{product_name}'       => __( 'First product name', 'nigeria-bulk-sms-for-woocommerce' ),
            '{product_names}'      => __( 'All product names', 'nigeria-bulk-sms-for-woocommerce' ),
            '{quantity}'           => __( 'Total quantity', 'nigeria-bulk-sms-for-woocommerce' ),
            '{shipping_address}'   => __( 'Shipping address', 'nigeria-bulk-sms-for-woocommerce' ),
            '{billing_address}'    => __( 'Billing address', 'nigeria-bulk-sms-for-woocommerce' ),
            '{payment_method}'     => __( 'Payment method', 'nigeria-bulk-sms-for-woocommerce' ),
        );
    }

    /**
     * Validate template
     *
     * @param string $template Template content
     * @return array Validation result with 'valid' boolean and 'errors' array
     * @since 1.0.0
     */
    public function validate_template( $template ) {
        $errors = array();

        // Check if template is empty
        if ( empty( trim( $template ) ) ) {
            $errors[] = __( 'Template content cannot be empty.', 'nigeria-bulk-sms-for-woocommerce' );
        }

        // Check template length (max SMS length reasonable check)
        if ( mb_strlen( $template ) > 1000 ) {
            $errors[] = __( 'Template is too long. Consider keeping it under 1000 characters.', 'nigeria-bulk-sms-for-woocommerce' );
        }

        // Check for invalid variables
        preg_match_all( '/\{([^}]+)\}/', $template, $matches );
        if ( ! empty( $matches[1] ) ) {
            foreach ( $matches[1] as $variable ) {
                if ( ! in_array( $variable, $this->available_variables, true ) ) {
                    $errors[] = sprintf( 
                        /* translators: %s: variable name */
                        __( 'Unknown variable: {%s}', 'nigeria-bulk-sms-for-woocommerce' ), 
                        $variable 
                    );
                }
            }
        }

        return array(
            'valid'  => empty( $errors ),
            'errors' => $errors,
        );
    }

    /**
     * Extract variables from template
     *
     * @param string $template Template content
     * @return array Variables found in template
     * @since 1.0.0
     */
    public function extract_variables( $template ) {
        preg_match_all( '/\{([^}]+)\}/', $template, $matches );
        return ! empty( $matches[1] ) ? array_unique( $matches[1] ) : array();
    }
}
