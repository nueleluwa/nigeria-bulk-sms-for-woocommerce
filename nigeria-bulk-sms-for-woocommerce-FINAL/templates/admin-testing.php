<?php
/**
 * Admin Testing Template
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$has_credentials = $api->has_credentials();
$sender_id = $settings->get( 'sender_id', '' );
?>

<div class="wrap nbsms-testing-wrap">
    <h1><?php esc_html_e( 'SMS Testing', 'nigeria-bulk-sms-for-woocommerce' ); ?></h1>
    <p class="description">
        <?php esc_html_e( 'Send test SMS messages to verify your integration and test message formatting.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
    </p>

    <?php if ( ! $has_credentials ) : ?>
        <div class="nbsms-notice nbsms-warning">
            <p>
                <span class="dashicons dashicons-warning"></span>
                <strong><?php esc_html_e( 'API credentials not configured.', 'nigeria-bulk-sms-for-woocommerce' ); ?></strong>
                <?php
                printf(
                    /* translators: %s: URL to settings page */
                    esc_html__( 'Please %sconfigure your API credentials%s before sending test messages.', 'nigeria-bulk-sms-for-woocommerce' ),
                    '<a href="' . esc_url( admin_url( 'admin.php?page=nigeria-bulk-sms-settings' ) ) . '">',
                    '</a>'
                );
                ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="nbsms-testing-content">
        <div class="nbsms-testing-grid">
            <!-- Test SMS Form -->
            <div class="nbsms-card nbsms-test-form-card">
                <h2><?php esc_html_e( 'Send Test SMS', 'nigeria-bulk-sms-for-woocommerce' ); ?></h2>

                <form id="nbsms-test-form" class="nbsms-test-form">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="test_phone"><?php esc_html_e( 'Phone Number', 'nigeria-bulk-sms-for-woocommerce' ); ?> <span class="required">*</span></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           name="phone" 
                                           id="test_phone" 
                                           class="regular-text" 
                                           placeholder="e.g., 08012345678 or +2348012345678"
                                           <?php echo ! $has_credentials ? 'disabled' : ''; ?>
                                           required>
                                    <div id="phone-validation-result" class="validation-result"></div>
                                    <p class="description">
                                        <?php esc_html_e( 'Enter a Nigerian phone number. Supports multiple formats.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="test_message"><?php esc_html_e( 'Message', 'nigeria-bulk-sms-for-woocommerce' ); ?> <span class="required">*</span></label>
                                </th>
                                <td>
                                    <textarea name="message" 
                                              id="test_message" 
                                              rows="5" 
                                              class="large-text"
                                              placeholder="<?php esc_attr_e( 'Enter your test message here...', 'nigeria-bulk-sms-for-woocommerce' ); ?>"
                                              <?php echo ! $has_credentials ? 'disabled' : ''; ?>
                                              required></textarea>
                                    
                                    <div class="character-counter">
                                        <div class="counter-info">
                                            <span class="char-count">
                                                <strong id="char-count">0</strong> / 160 <?php esc_html_e( 'characters', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                            </span>
                                            <span class="sms-parts">
                                                <strong id="sms-parts">1</strong> <?php esc_html_e( 'SMS part(s)', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                            </span>
                                            <span class="estimated-cost" id="estimated-cost-container" style="display:none;">
                                                <?php esc_html_e( 'Est. Cost:', 'nigeria-bulk-sms-for-woocommerce' ); ?> ₦<span id="estimated-cost">0</span>
                                            </span>
                                        </div>
                                        <div class="counter-bar">
                                            <div class="counter-progress" id="char-progress"></div>
                                        </div>
                                    </div>

                                    <p class="description">
                                        <?php esc_html_e( 'Single SMS: 160 characters. Multi-part: 153 characters per part.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="test_sender_id"><?php esc_html_e( 'Sender ID', 'nigeria-bulk-sms-for-woocommerce' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           name="sender_id" 
                                           id="test_sender_id" 
                                           value="<?php echo esc_attr( $sender_id ); ?>" 
                                           class="regular-text" 
                                           maxlength="11"
                                           placeholder="<?php esc_attr_e( 'Default sender ID', 'nigeria-bulk-sms-for-woocommerce' ); ?>"
                                           <?php echo ! $has_credentials ? 'disabled' : ''; ?>>
                                    <p class="description">
                                        <?php esc_html_e( 'Leave empty to use default sender ID from settings.', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <button type="submit" 
                                class="button button-primary button-large" 
                                id="send-test-sms-btn"
                                <?php echo ! $has_credentials ? 'disabled' : ''; ?>>
                            <span class="dashicons dashicons-email"></span>
                            <?php esc_html_e( 'Send Test SMS', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                        </button>
                        <span class="nbsms-test-loading" style="display:none;">
                            <span class="spinner is-active" style="float:none;"></span>
                        </span>
                    </p>

                    <div id="test-sms-result" class="test-result"></div>
                </form>
            </div>

            <!-- SMS Format Guide -->
            <div class="nbsms-card nbsms-format-guide">
                <h3><?php esc_html_e( 'Phone Number Formats', 'nigeria-bulk-sms-for-woocommerce' ); ?></h3>
                <ul class="format-list">
                    <li>
                        <span class="format-example">08012345678</span>
                        <span class="format-label"><?php esc_html_e( 'Nigerian format', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                    </li>
                    <li>
                        <span class="format-example">+2348012345678</span>
                        <span class="format-label"><?php esc_html_e( 'International format', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                    </li>
                    <li>
                        <span class="format-example">2348012345678</span>
                        <span class="format-label"><?php esc_html_e( 'Without + prefix', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                    </li>
                </ul>

                <h3><?php esc_html_e( 'Character Limits', 'nigeria-bulk-sms-for-woocommerce' ); ?></h3>
                <ul class="format-list">
                    <li>
                        <span class="format-example">160</span>
                        <span class="format-label"><?php esc_html_e( 'Single SMS', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                    </li>
                    <li>
                        <span class="format-example">153</span>
                        <span class="format-label"><?php esc_html_e( 'Per multi-part SMS', 'nigeria-bulk-sms-for-woocommerce' ); ?></span>
                    </li>
                </ul>

                <h3><?php esc_html_e( 'Tips', 'nigeria-bulk-sms-for-woocommerce' ); ?></h3>
                <ul class="tips-list">
                    <li><?php esc_html_e( 'Test with your own number first', 'nigeria-bulk-sms-for-woocommerce' ); ?></li>
                    <li><?php esc_html_e( 'Keep messages clear and concise', 'nigeria-bulk-sms-for-woocommerce' ); ?></li>
                    <li><?php esc_html_e( 'Avoid special characters when possible', 'nigeria-bulk-sms-for-woocommerce' ); ?></li>
                    <li><?php esc_html_e( 'Messages over 160 chars are charged per part', 'nigeria-bulk-sms-for-woocommerce' ); ?></li>
                </ul>
            </div>
        </div>

        <!-- Test History -->
        <?php if ( ! empty( $test_history ) ) : ?>
        <div class="nbsms-card nbsms-test-history">
            <h2><?php esc_html_e( 'Recent Test Messages', 'nigeria-bulk-sms-for-woocommerce' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Last 10 test messages sent', 'nigeria-bulk-sms-for-woocommerce' ); ?></p>

            <div class="nbsms-table-wrap">
                <table class="nbsms-table wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Date/Time', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Phone Number', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Message', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Cost', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                            <th><?php esc_html_e( 'Parts', 'nigeria-bulk-sms-for-woocommerce' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $test_history as $log ) : ?>
                        <tr>
                            <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $log->created_at ) ) ); ?></td>
                            <td><?php echo esc_html( $log->recipient_phone ); ?></td>
                            <td>
                                <div class="message-preview" title="<?php echo esc_attr( $log->message ); ?>">
                                    <?php echo esc_html( wp_trim_words( $log->message, 10 ) ); ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $status_class = $log->status === 'sent' ? 'sent' : ( $log->status === 'failed' ? 'failed' : 'pending' );
                                $status_text = ucfirst( $log->status );
                                ?>
                                <span class="status-badge <?php echo esc_attr( $status_class ); ?>">
                                    <?php echo esc_html( $status_text ); ?>
                                </span>
                            </td>
                            <td>₦<?php echo esc_html( number_format( $log->cost, 2 ) ); ?></td>
                            <td><?php echo esc_html( $log->sms_count ); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="view-all-logs">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=nigeria-bulk-sms-logs' ) ); ?>" class="button">
                    <?php esc_html_e( 'View All Logs', 'nigeria-bulk-sms-for-woocommerce' ); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>
