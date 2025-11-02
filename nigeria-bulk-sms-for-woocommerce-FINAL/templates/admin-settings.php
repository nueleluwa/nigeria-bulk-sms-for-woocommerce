<?php
/**
 * Admin Settings Template
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$api_username = $settings->get( 'api_username', '' );
$api_password = $settings->get( 'api_password', '' );
$sender_id = $settings->get( 'sender_id', '' );
$connection_timeout = $settings->get( 'connection_timeout', 30 );
$enable_logs = $settings->get( 'enable_logs', 'yes' );
$log_retention_days = $settings->get( 'log_retention_days', 30 );
$has_credentials = $api->has_credentials();
?>

<div class="wrap nbsms-settings-wrap">
    <h1><?php esc_html_e( 'Nigeria Bulk SMS Settings', 'nigeria-bulk-sms-for-woocommerce' ); ?></h1>

    <?php settings_errors( 'nbsms_settings' ); ?>

    <nav class="nav-tab-wrapper">
        <a href="?page=nigeria-bulk-sms-settings&tab=api" class="nav-tab <?php echo esc_attr( $active_tab === 'api' ? 'nav-tab-active' : '' ); ?>">
            <?php esc_html_e( 'API Connection', 'nigeria-bulk-sms-for-woocommerce' ); ?>
        </a>
        <a href="?page=nigeria-bulk-sms-settings&tab=general" class="nav-tab <?php echo esc_attr( $active_tab === 'general' ? 'nav-tab-active' : '' ); ?>">
            <?php esc_html_e( 'General Settings', 'nigeria-bulk-sms-for-woocommerce' ); ?>
        </a>
        <a href="?page=nigeria-bulk-sms-settings&tab=notifications" class="nav-tab <?php echo esc_attr( $active_tab === 'notifications' ? 'nav-tab-active' : '' ); ?>">
            <?php esc_html_e( 'Notifications', 'nigeria-bulk-sms-for-woocommerce' ); ?>
        </a>
    </nav>

    <form method="post" action="" class="nbsms-settings-form" id="nbsms-settings-form">
        <?php wp_nonce_field( 'nbsms_settings_nonce', 'nbsms_settings_nonce_field' ); ?>

        <?php if ( $active_tab === 'api' ) : ?>
            <!-- API Connection Tab -->
            <div class="nbsms-tab-content">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="api_username"><?php esc_html_e( 'API Username', 'nigeria-bulk-sms-for-woocommerce' ); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="text" name="api_username" id="api_username" value="<?php echo esc_attr( $api_username ); ?>" class="regular-text" required>
                                <p class="description">
                                    <?php esc_html_e( 'Your Nigeria Bulk SMS API username.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                    <a href="https://portal.nigeriabulksms.com" target="_blank"><?php esc_html_e( 'Get your credentials', 'nigeria-bulk-sms-for-woocommerce' ); ?></a>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="api_password"><?php esc_html_e( 'API Password', 'nigeria-bulk-sms-for-woocommerce' ); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="password" name="api_password" id="api_password" value="<?php echo esc_attr( $api_password ); ?>" class="regular-text" required>
                                <p class="description">
                                    <?php esc_html_e( 'Your Nigeria Bulk SMS API password.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="sender_id"><?php esc_html_e( 'Default Sender ID', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="sender_id" id="sender_id" value="<?php echo esc_attr( $sender_id ); ?>" class="regular-text" maxlength="11">
                                <p class="description">
                                    <?php esc_html_e( 'The sender name that will appear on SMS messages (max 11 characters).', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="connection_timeout"><?php esc_html_e( 'Connection Timeout', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="connection_timeout" id="connection_timeout" value="<?php echo esc_attr( $connection_timeout ); ?>" min="5" max="120" class="small-text"> <?php esc_html_e( 'seconds', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                <p class="description">
                                    <?php esc_html_e( 'API request timeout in seconds (5-120).', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </p>
                            </td>
                        </tr>

                        <?php if ( $has_credentials ) : ?>
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Connection Test', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                            </th>
                            <td>
                                <button type="button" id="nbsms-test-connection" class="button button-secondary">
                                    <span class="dashicons dashicons-update"></span>
                                    <?php esc_html_e( 'Test Connection', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </button>
                                <span class="nbsms-loading" style="display:none;">
                                    <span class="spinner is-active" style="float:none;"></span>
                                </span>
                                <div id="nbsms-connection-result" style="margin-top: 10px;"></div>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Account Balance', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                            </th>
                            <td>
                                <div id="nbsms-balance-display" class="nbsms-balance-box">
                                    <span class="balance-amount">--</span>
                                    <button type="button" id="nbsms-refresh-balance" class="button button-small">
                                        <span class="dashicons dashicons-update"></span>
                                    </button>
                                </div>
                                <p class="description">
                                    <?php esc_html_e( 'Your current SMS credit balance.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ( $active_tab === 'general' ) : ?>
            <!-- General Settings Tab -->
            <div class="nbsms-tab-content">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="enable_logs"><?php esc_html_e( 'Enable Logging', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="enable_logs" id="enable_logs" value="yes" <?php checked( $enable_logs, 'yes' ); ?>>
                                    <?php esc_html_e( 'Keep logs of all SMS sent', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </label>
                                <p class="description">
                                    <?php esc_html_e( 'Store detailed logs of all SMS messages sent through the plugin.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="log_retention_days"><?php esc_html_e( 'Log Retention Period', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="log_retention_days" id="log_retention_days" value="<?php echo esc_attr( $log_retention_days ); ?>" min="1" max="365" class="small-text"> <?php esc_html_e( 'days', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                <p class="description">
                                    <?php esc_html_e( 'Automatically delete logs older than this many days. Helps keep database size manageable.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Phone Number Format', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                            </th>
                            <td>
                                <p class="description">
                                    <?php esc_html_e( 'Supported formats:', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </p>
                                <ul style="margin-top: 5px; margin-left: 20px;">
                                    <li>080XXXXXXXX (Nigerian format)</li>
                                    <li>+2348XXXXXXXXX (International format)</li>
                                    <li>2348XXXXXXXXX (Without + prefix)</li>
                                </ul>
                                <p class="description" style="margin-top: 10px;">
                                    <?php esc_html_e( 'Phone numbers are automatically formatted to match API requirements.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'SMS Character Limit', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                            </th>
                            <td>
                                <p class="description">
                                    <?php esc_html_e( 'Single SMS: 160 characters', 'nigeria-bulk-sms-for-woocommerce' ); ?><br>
                                    <?php esc_html_e( 'Multi-part SMS: 153 characters per part', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        <?php elseif ( $active_tab === 'notifications' ) : ?>
            <!-- Notifications Tab -->
            <div class="nbsms-tab-content">
                <?php
                $notifications = NBSMS_Notifications::instance();
                $events = $notifications->get_notification_events();
                $enabled_notifications = get_option( 'nbsms_enabled_notifications', array() );
                $notification_templates = get_option( 'nbsms_notification_templates', array() );
                $notification_conditions = get_option( 'nbsms_notification_conditions', array() );
                
                // Get all templates
                global $wpdb;
                $templates_table = $wpdb->prefix . 'nbsms_templates';
                $all_templates = $wpdb->get_results( "SELECT id, template_name, template_type FROM {$templates_table} WHERE is_active = 1 ORDER BY template_name ASC" );
                ?>

                <p class="description">
                    <?php esc_html_e( 'Configure automated SMS notifications for WooCommerce events. Select which events should trigger SMS and choose templates for each.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                </p>

                <table class="form-table nbsms-notifications-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><?php esc_html_e( 'Enable', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Event', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <th style="width: 250px;"><?php esc_html_e( 'Template', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <th style="width: 200px;"><?php esc_html_e( 'Conditions', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $events as $event_key => $event ) : 
                            $is_enabled = in_array( $event_key, $enabled_notifications, true );
                            $selected_template = isset( $notification_templates[ $event_key ] ) ? $notification_templates[ $event_key ] : '';
                            $condition = isset( $notification_conditions[ $event_key ] ) ? $notification_conditions[ $event_key ] : array();
                        ?>
                        <tr class="notification-row">
                            <td>
                                <input type="checkbox" 
                                       name="enabled_notifications[]" 
                                       value="<?php echo esc_attr( $event_key ); ?>" 
                                       id="enable_<?php echo esc_attr( $event_key ); ?>"
                                       <?php checked( $is_enabled ); ?>>
                            </td>
                            <td>
                                <label for="enable_<?php echo esc_attr( $event_key ); ?>">
                                    <strong><?php echo esc_html( $event['title'] ); ?></strong>
                                    <p class="description" style="margin: 5px 0 0;"><?php echo esc_html( $event['description'] ); ?></p>
                                </label>
                            </td>
                            <td>
                                <select name="notification_templates[<?php echo esc_attr( $event_key ); ?>]" 
                                        class="regular-text"
                                        <?php echo ! $is_enabled ? 'disabled' : ''; ?>>
                                    <option value=""><?php esc_html_e( '-- Select Template --', 'nigeria-bulk-sms-for-woocommerce' ); ?></option>
                                    <?php foreach ( $all_templates as $template ) : ?>
                                        <option value="<?php echo esc_attr( $template->id ); ?>" 
                                                <?php selected( $selected_template, $template->id ); ?>>
                                            <?php echo esc_html( $template->template_name ); ?>
                                            <?php if ( $template->template_type !== 'custom' ) : ?>
                                                (<?php echo esc_html( ucfirst( $template->template_type ) ); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <?php if ( strpos( $event_key, 'order_' ) === 0 || $event_key === 'payment_complete' ) : ?>
                                    <input type="number" 
                                           name="notification_conditions[<?php echo esc_attr( $event_key ); ?>][min_amount]" 
                                           placeholder="<?php esc_attr_e( 'Min amount', 'nigeria-bulk-sms-for-woocommerce' ); ?>"
                                           value="<?php echo isset( $condition['min_amount'] ) ? esc_attr( $condition['min_amount'] ) : ''; ?>"
                                           step="0.01"
                                           min="0"
                                           class="small-text"
                                           <?php echo ! $is_enabled ? 'disabled' : ''; ?>>
                                    <p class="description"><?php esc_html_e( 'Minimum order amount', 'nigeria-bulk-sms-for-woocommerce' ); ?></p>
                                <?php else : ?>
                                    <span class="description"><?php esc_html_e( 'No conditions', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <input type="hidden" name="save_notifications" value="1">
            </div>

        <?php endif; ?>

        <p class="submit">
            <button type="submit" name="nbsms_save_settings" class="button button-primary button-large">
                <span class="dashicons dashicons-yes"></span>
                <?php esc_html_e( 'Save Settings', 'nigeria-bulk-sms-for-woocommerce' ); ?>
            </button>
            <span class="nbsms-save-loading" style="display:none;">
                <span class="spinner is-active" style="float:none;"></span>
            </span>
        </p>
    </form>
</div>
