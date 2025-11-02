<?php
/**
 * Opt-In/Opt-Out Handler Class
 *
 * Handles customer SMS preferences
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBSMS_Opt_In {

    /**
     * Single instance of the class
     *
     * @var NBSMS_Opt_In
     */
    protected static $instance = null;

    /**
     * Get instance
     *
     * @return NBSMS_Opt_In
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
        // Add opt-in checkbox to checkout
        add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'add_checkout_opt_in_field' ) );
        
        // Save opt-in preference
        add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_checkout_opt_in' ) );
        
        // Add opt-in to My Account
        add_action( 'woocommerce_edit_account_form', array( $this, 'add_account_opt_in_field' ) );
        
        // Save My Account opt-in
        add_action( 'woocommerce_save_account_details', array( $this, 'save_account_opt_in' ) );
        
        // Add opt-in to registration
        add_action( 'woocommerce_register_form', array( $this, 'add_registration_opt_in_field' ) );
        
        // Save registration opt-in
        add_action( 'woocommerce_created_customer', array( $this, 'save_registration_opt_in' ) );
    }

    /**
     * Add opt-in checkbox to checkout
     *
     * @param WC_Checkout $checkout Checkout object
     * @since 1.0.0
     */
    public function add_checkout_opt_in_field( $checkout ) {
        $opt_in_text = get_option( 'nbsms_opt_in_text', __( 'Yes, send me SMS notifications about my order', 'nigeria-bulk-sms-for-woocommerce' ) );
        
        echo '<div id="nbsms-opt-in-checkout">';
        
        woocommerce_form_field( 'nbsms_opt_in', array(
            'type'    => 'checkbox',
            'class'   => array( 'form-row-wide' ),
            'label'   => $opt_in_text,
            'default' => 1, // Default checked
        ), $checkout->get_value( 'nbsms_opt_in' ) );
        
        echo '</div>';
    }

    /**
     * Save checkout opt-in preference
     *
     * @param int $order_id Order ID
     * @since 1.0.0
     */
    public function save_checkout_opt_in( $order_id ) {
        if ( ! empty( $_POST['nbsms_opt_in'] ) ) {
            update_post_meta( $order_id, '_nbsms_opt_in', 'yes' );
        } else {
            update_post_meta( $order_id, '_nbsms_opt_in', 'no' );
        }
    }

    /**
     * Add opt-in field to My Account
     *
     * @since 1.0.0
     */
    public function add_account_opt_in_field() {
        $customer_id = get_current_user_id();
        $opt_in = get_user_meta( $customer_id, 'nbsms_opt_in', true );
        
        if ( $opt_in === '' ) {
            $opt_in = 'yes'; // Default
        }
        
        $opt_in_text = get_option( 'nbsms_opt_in_text', __( 'Yes, send me SMS notifications about my orders', 'nigeria-bulk-sms-for-woocommerce' ) );
        ?>
        <fieldset>
            <legend><?php esc_html_e( 'SMS Notifications', 'nigeria-bulk-sms-for-woocommerce' ); ?></legend>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                    <input type="checkbox" 
                           class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" 
                           name="nbsms_opt_in" 
                           id="nbsms_opt_in" 
                           value="yes" 
                           <?php checked( $opt_in, 'yes' ); ?>>
                    <span><?php echo esc_html( $opt_in_text ); ?></span>
                </label>
            </p>
        </fieldset>
        <?php
    }

    /**
     * Save My Account opt-in preference
     *
     * @param int $customer_id Customer ID
     * @since 1.0.0
     */
    public function save_account_opt_in( $customer_id ) {
        if ( isset( $_POST['nbsms_opt_in'] ) ) {
            update_user_meta( $customer_id, 'nbsms_opt_in', 'yes' );
        } else {
            update_user_meta( $customer_id, 'nbsms_opt_in', 'no' );
        }
    }

    /**
     * Add opt-in field to registration
     *
     * @since 1.0.0
     */
    public function add_registration_opt_in_field() {
        $opt_in_text = get_option( 'nbsms_opt_in_text', __( 'Yes, send me SMS notifications', 'nigeria-bulk-sms-for-woocommerce' ) );
        ?>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input type="checkbox" 
                       class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" 
                       name="nbsms_opt_in" 
                       id="nbsms_opt_in" 
                       value="yes" 
                       checked="checked">
                <span><?php echo esc_html( $opt_in_text ); ?></span>
            </label>
        </p>
        <?php
    }

    /**
     * Save registration opt-in preference
     *
     * @param int $customer_id Customer ID
     * @since 1.0.0
     */
    public function save_registration_opt_in( $customer_id ) {
        if ( isset( $_POST['nbsms_opt_in'] ) ) {
            update_user_meta( $customer_id, 'nbsms_opt_in', 'yes' );
        } else {
            update_user_meta( $customer_id, 'nbsms_opt_in', 'no' );
        }
    }
}

// Initialize
add_action( 'plugins_loaded', array( 'NBSMS_Opt_In', 'instance' ), 20 );
