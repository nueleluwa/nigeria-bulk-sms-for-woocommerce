<?php
/**
 * Admin Template Form Template (Add/Edit)
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$page_title = $is_edit ? __( 'Edit Template', 'nigeria-bulk-sms-for-woocommerce' ) : __( 'Add New Template', 'nigeria-bulk-sms-for-woocommerce' );
$template_name = $is_edit && $template ? $template->template_name : '';
$template_content = $is_edit && $template ? $template->template_content : '';
$template_type = $is_edit && $template ? $template->template_type : 'custom';
$is_active = $is_edit && $template ? $template->is_active : 1;
$is_default = $is_edit && $template && $template->is_default;

require_once NBSMS_PLUGIN_DIR . 'includes/class-nbsms-template-parser.php';
$parser = new NBSMS_Template_Parser();
$variables = $parser->get_variables_with_descriptions();
?>

<div class="wrap nbsms-template-form-wrap">
    <h1><?php echo esc_html( $page_title ); ?></h1>
    <hr class="wp-header-end">

    <?php settings_errors( 'nbsms_templates' ); ?>

    <form method="post" action="" id="nbsms-template-form">
        <?php wp_nonce_field( 'nbsms_template_nonce', 'nbsms_template_nonce_field' ); ?>
        <?php if ( $is_edit && $template ) : ?>
            <input type="hidden" name="template_id" value="<?php echo esc_attr( $template->id ); ?>">
        <?php endif; ?>

        <div class="nbsms-template-editor">
            <div class="template-editor-main">
                <div class="nbsms-card">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="template_name"><?php esc_html_e( 'Template Name', 'nigeria-bulk-sms-for-woocommerce' ); ?> <span class="required">*</span></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           name="template_name" 
                                           id="template_name" 
                                           value="<?php echo esc_attr( $template_name ); ?>" 
                                           class="regular-text" 
                                           <?php echo $is_default ? 'readonly' : ''; ?>
                                           required>
                                    <p class="description">
                                        <?php esc_html_e( 'A descriptive name for this template.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="template_type"><?php esc_html_e( 'Template Type', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                                </th>
                                <td>
                                    <select name="template_type" id="template_type" <?php echo $is_default ? 'disabled' : ''; ?>>
                                        <option value="custom" <?php selected( $template_type, 'custom' ); ?>><?php esc_html_e( 'Custom', 'nigeria-bulk-sms-for-woocommerce' ); ?></option>
                                        <option value="order" <?php selected( $template_type, 'order' ); ?>><?php esc_html_e( 'Order', 'nigeria-bulk-sms-for-woocommerce' ); ?></option>
                                        <option value="customer" <?php selected( $template_type, 'customer' ); ?>><?php esc_html_e( 'Customer', 'nigeria-bulk-sms-for-woocommerce' ); ?></option>
                                        <option value="bulk" <?php selected( $template_type, 'bulk' ); ?>><?php esc_html_e( 'Bulk', 'nigeria-bulk-sms-for-woocommerce' ); ?></option>
                                    </select>
                                    <p class="description">
                                        <?php esc_html_e( 'Categorize your template for easier management.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="template_content"><?php esc_html_e( 'Message Content', 'nigeria-bulk-sms-for-woocommerce' ); ?> <span class="required">*</span></label>
                                </th>
                                <td>
                                    <textarea name="template_content" 
                                              id="template_content" 
                                              rows="6" 
                                              class="large-text code"
                                              required><?php echo esc_textarea( $template_content ); ?></textarea>
                                    
                                    <div class="character-counter">
                                        <div class="counter-info">
                                            <span class="char-count">
                                                <strong id="template-char-count">0</strong> / 160 <?php esc_html_e( 'characters', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                            </span>
                                            <span class="sms-parts">
                                                <strong id="template-sms-parts">1</strong> <?php esc_html_e( 'SMS part(s)', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                            </span>
                                        </div>
                                        <div class="counter-bar">
                                            <div class="counter-progress" id="template-char-progress"></div>
                                        </div>
                                    </div>

                                    <p class="description">
                                        <?php esc_html_e( 'Use variables like {customer_name} to personalize messages. Click on variables in the sidebar to insert them.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="is_active"><?php esc_html_e( 'Status', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active" 
                                               value="1" 
                                               <?php checked( $is_active, 1 ); ?>>
                                        <?php esc_html_e( 'Active', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e( 'Inactive templates can be saved but won\'t appear in selection lists.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="template-preview-section">
                        <h3><?php esc_html_e( 'Preview with Sample Data', 'nigeria-bulk-sms-for-woocommerce' ); ?></h3>
                        <button type="button" id="preview-template-btn" class="button">
                            <span class="dashicons dashicons-visibility"></span>
                            <?php esc_html_e( 'Preview Template', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                        </button>
                        <div id="template-preview-result" class="template-preview-box" style="display:none;"></div>
                    </div>
                </div>

                <p class="submit">
                    <button type="submit" name="nbsms_save_template" class="button button-primary button-large">
                        <span class="dashicons dashicons-yes"></span>
                        <?php echo $is_edit ? esc_html__( 'Update Template', 'nigeria-bulk-sms-for-woocommerce' ) : esc_html__( 'Create Template', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                    </button>
                    <a href="?page=nigeria-bulk-sms-templates" class="button button-large">
                        <?php esc_html_e( 'Cancel', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                    </a>
                </p>
            </div>

            <div class="template-editor-sidebar">
                <div class="nbsms-card">
                    <h3><?php esc_html_e( 'Available Variables', 'nigeria-bulk-sms-for-woocommerce' ); ?></h3>
                    <p class="description">
                        <?php esc_html_e( 'Click on a variable to insert it at cursor position.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                    </p>

                    <div class="variables-list">
                        <?php foreach ( $variables as $variable => $description ) : ?>
                            <button type="button" class="variable-button" data-variable="<?php echo esc_attr( $variable ); ?>">
                                <code><?php echo esc_html( $variable ); ?></code>
                                <span class="variable-desc"><?php echo esc_html( $description ); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="nbsms-card">
                    <h3><?php esc_html_e( 'Template Tips', 'nigeria-bulk-sms-for-woocommerce' ); ?></h3>
                    <ul class="tips-list">
                        <li><?php esc_html_e( 'Keep messages concise and clear', 'nigeria-bulk-sms-for-woocommerce' ); ?></li>
                        <li><?php esc_html_e( 'Use variables for personalization', 'nigeria-bulk-sms-for-woocommerce' ); ?></li>
                        <li><?php esc_html_e( 'Test with preview before saving', 'nigeria-bulk-sms-for-woocommerce' ); ?></li>
                        <li><?php esc_html_e( 'Aim for under 160 characters', 'nigeria-bulk-sms-for-woocommerce' ); ?></li>
                        <li><?php esc_html_e( 'Avoid special characters', 'nigeria-bulk-sms-for-woocommerce' ); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </form>

    <p>
        <a href="?page=nigeria-bulk-sms-templates" class="button">
            <span class="dashicons dashicons-arrow-left-alt"></span>
            <?php esc_html_e( 'Back to Templates', 'nigeria-bulk-sms-for-woocommerce' ); ?>
        </a>
    </p>
</div>
