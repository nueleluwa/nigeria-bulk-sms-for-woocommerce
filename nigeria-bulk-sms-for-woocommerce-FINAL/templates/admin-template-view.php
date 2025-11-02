<?php
/**
 * Admin Template View Template
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-template-parser.php';
$parser = new NBSMS_Template_Parser();
$api = new NBSMS_API();
?>

<div class="wrap nbsms-template-view-wrap">
    <h1><?php echo esc_html( $template->template_name ); ?></h1>
    <hr class="wp-header-end">

    <div class="nbsms-template-view-grid">
        <div class="template-view-main">
            <div class="nbsms-card">
                <h2><?php esc_html_e( 'Template Details', 'nigeria-bulk-sms-for-woocommerce' ); ?></h2>
                
                <table class="widefat">
                    <tbody>
                        <tr>
                            <th><?php esc_html_e( 'Name:', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <td><?php echo esc_html( $template->template_name ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Type:', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <td><?php echo esc_html( ucfirst( $template->template_type ) ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Status:', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <td>
                                <?php if ( $template->is_active ) : ?>
                                    <span class="status-badge sent"><?php esc_html_e( 'Active', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                                <?php else : ?>
                                    <span class="status-badge pending"><?php esc_html_e( 'Inactive', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                                <?php endif; ?>
                                <?php if ( $template->is_default ) : ?>
                                    <span class="status-badge sent"><?php esc_html_e( 'Default', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Characters:', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <td><?php echo esc_html( mb_strlen( $template->template_content ) ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'SMS Parts:', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <td><?php echo esc_html( $api->calculate_sms_parts( $template->template_content ) ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Created:', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $template->created_at ) ) ); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="nbsms-card">
                <h2><?php esc_html_e( 'Template Content', 'nigeria-bulk-sms-for-woocommerce' ); ?></h2>
                <div class="template-content-display">
                    <?php echo nl2br( esc_html( $template->template_content ) ); ?>
                </div>
            </div>

            <div class="nbsms-card">
                <h2><?php esc_html_e( 'Preview with Sample Data', 'nigeria-bulk-sms-for-woocommerce' ); ?></h2>
                <?php
                $sample_data = array(
                    'customer_name'      => 'John',
                    'customer_full_name' => 'John Doe',
                    'order_id'           => '12345',
                    'order_total'        => '25,000',
                    'order_status'       => 'Processing',
                    'site_name'          => get_bloginfo( 'name' ),
                    'site_url'           => home_url(),
                    'tracking_number'    => 'TRK123456789',
                );
                $preview = $parser->parse( $template->template_content, $sample_data );
                ?>
                <div class="template-preview-display">
                    <?php echo nl2br( esc_html( $preview ) ); ?>
                </div>
                <p class="description">
                    <?php esc_html_e( 'This is how the message will appear with actual customer data.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                </p>
            </div>
        </div>

        <div class="template-view-sidebar">
            <div class="nbsms-card">
                <h3><?php esc_html_e( 'Actions', 'nigeria-bulk-sms-for-woocommerce' ); ?></h3>
                <p>
                    <a href="?page=nigeria-bulk-sms-templates&action=edit&template_id=<?php echo esc_attr( $template->id ); ?>" class="button button-primary button-large" style="width:100%;">
                        <span class="dashicons dashicons-edit"></span>
                        <?php esc_html_e( 'Edit Template', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                    </a>
                </p>
                <?php if ( ! $template->is_default ) : ?>
                    <form method="post" onsubmit="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this template?', 'nigeria-bulk-sms-for-woocommerce' ); ?>');">
                        <?php wp_nonce_field( 'nbsms_delete_template_' . $template->id ); ?>
                        <input type="hidden" name="template_id" value="<?php echo esc_attr( $template->id ); ?>">
                        <button type="submit" name="nbsms_delete_template" class="button button-large" style="width:100%;">
                            <span class="dashicons dashicons-trash"></span>
                            <?php esc_html_e( 'Delete Template', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="nbsms-card">
                <h3><?php esc_html_e( 'Variables Used', 'nigeria-bulk-sms-for-woocommerce' ); ?></h3>
                <?php
                $variables_used = $parser->extract_variables( $template->template_content );
                if ( ! empty( $variables_used ) ) :
                    ?>
                    <ul class="variables-used-list">
                        <?php foreach ( $variables_used as $var ) : ?>
                            <li><code>{<?php echo esc_html( $var ); ?>}</code></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="description"><?php esc_html_e( 'No variables used in this template.', 'nigeria-bulk-sms-for-woocommerce' ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <p>
        <a href="?page=nigeria-bulk-sms-templates" class="button">
            <span class="dashicons dashicons-arrow-left-alt"></span>
            <?php esc_html_e( 'Back to Templates', 'nigeria-bulk-sms-for-woocommerce' ); ?>
        </a>
    </p>
</div>
