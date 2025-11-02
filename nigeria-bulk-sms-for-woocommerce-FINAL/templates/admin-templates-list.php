<?php
/**
 * Admin Templates List Template
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap nbsms-templates-wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Message Templates', 'nigeria-bulk-sms-for-woocommerce' ); ?></h1>
    <a href="?page=nigeria-bulk-sms-templates&action=add" class="page-title-action">
        <?php esc_html_e( 'Add New Template', 'nigeria-bulk-sms-for-woocommerce' ); ?>
    </a>
    <hr class="wp-header-end">

    <?php settings_errors( 'nbsms_templates' ); ?>

    <p class="description">
        <?php esc_html_e( 'Create and manage SMS message templates with dynamic variables for personalized messages.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
    </p>

    <?php if ( empty( $templates ) ) : ?>
        <div class="nbsms-notice nbsms-info">
            <p>
                <span class="dashicons dashicons-info"></span>
                <?php esc_html_e( 'No templates found. Create your first template to get started!', 'nigeria-bulk-sms-for-woocommerce' ); ?>
            </p>
        </div>
    <?php else : ?>
        <div class="nbsms-templates-grid">
            <?php foreach ( $templates as $template ) : ?>
                <div class="nbsms-template-card <?php echo $template->is_default ? 'is-default' : ''; ?> <?php echo ! $template->is_active ? 'is-inactive' : ''; ?>">
                    <div class="template-card-header">
                        <h3 class="template-name">
                            <?php echo esc_html( $template->template_name ); ?>
                            <?php if ( $template->is_default ) : ?>
                                <span class="badge badge-default"><?php esc_html_e( 'Default', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                            <?php endif; ?>
                            <?php if ( ! $template->is_active ) : ?>
                                <span class="badge badge-inactive"><?php esc_html_e( 'Inactive', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                            <?php endif; ?>
                        </h3>
                        <span class="template-type"><?php echo esc_html( ucfirst( $template->template_type ) ); ?></span>
                    </div>

                    <div class="template-card-content">
                        <div class="template-preview">
                            <?php echo esc_html( wp_trim_words( $template->template_content, 20 ) ); ?>
                        </div>

                        <div class="template-meta">
                            <span class="template-length">
                                <span class="dashicons dashicons-editor-alignleft"></span>
                                <?php echo esc_html( mb_strlen( $template->template_content ) ); ?> <?php esc_html_e( 'chars', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                            </span>
                            <?php
                            $api = new NBSMS_API();
                            $parts = $api->calculate_sms_parts( $template->template_content );
                            ?>
                            <span class="template-parts">
                                <span class="dashicons dashicons-email"></span>
                                <?php echo esc_html( $parts ); ?> <?php esc_html_e( 'SMS', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                            </span>
                        </div>
                    </div>

                    <div class="template-card-actions">
                        <a href="?page=nigeria-bulk-sms-templates&action=view&template_id=<?php echo esc_attr( $template->id ); ?>" class="button">
                            <span class="dashicons dashicons-visibility"></span>
                            <?php esc_html_e( 'View', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                        </a>
                        <a href="?page=nigeria-bulk-sms-templates&action=edit&template_id=<?php echo esc_attr( $template->id ); ?>" class="button">
                            <span class="dashicons dashicons-edit"></span>
                            <?php esc_html_e( 'Edit', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                        </a>
                        <?php if ( ! $template->is_default ) : ?>
                            <form method="post" style="display:inline;" onsubmit="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this template?', 'nigeria-bulk-sms-for-woocommerce' ); ?>');">
                                <?php wp_nonce_field( 'nbsms_delete_template_' . $template->id ); ?>
                                <input type="hidden" name="template_id" value="<?php echo esc_attr( $template->id ); ?>">
                                <button type="submit" name="nbsms_delete_template" class="button button-link-delete">
                                    <span class="dashicons dashicons-trash"></span>
                                    <?php esc_html_e( 'Delete', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Variables Reference -->
    <div class="nbsms-card nbsms-variables-reference" style="margin-top: 30px;">
        <h2><?php esc_html_e( 'Available Variables', 'nigeria-bulk-sms-for-woocommerce' ); ?></h2>
        <p class="description">
            <?php esc_html_e( 'Use these variables in your templates. They will be replaced with actual data when sending messages.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
        </p>

        <?php
        require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-template-parser.php';
        $parser = new NBSMS_Template_Parser();
        $variables = $parser->get_variables_with_descriptions();
        ?>

        <div class="variables-grid">
            <?php foreach ( $variables as $variable => $description ) : ?>
                <div class="variable-item">
                    <code class="variable-code"><?php echo esc_html( $variable ); ?></code>
                    <span class="variable-description"><?php echo esc_html( $description ); ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="variables-example">
            <h4><?php esc_html_e( 'Example Usage:', 'nigeria-bulk-sms-for-woocommerce' ); ?></h4>
            <div class="example-box">
                <code>Hi {customer_name}, your order #{order_id} for ₦{order_total} has been confirmed!</code>
            </div>
            <p class="description">
                <?php esc_html_e( 'This will be transformed to:', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                <br><em>Hi John, your order #12345 for ₦25,000 has been confirmed!</em>
            </p>
        </div>
    </div>
</div>
