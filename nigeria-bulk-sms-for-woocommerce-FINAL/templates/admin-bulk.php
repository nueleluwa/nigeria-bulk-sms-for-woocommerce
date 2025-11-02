<?php
/**
 * Bulk SMS Admin Template
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.6.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap nbsms-wrap">
    <h1>
        <span class="dashicons dashicons-megaphone"></span>
        Bulk SMS Campaign
    </h1>

    <div class="nbsms-bulk-container">
        
        <!-- Step Indicator -->
        <div class="nbsms-steps">
            <div class="nbsms-step active" data-step="1">
                <span class="step-number">1</span>
                <span class="step-label">Select Recipients</span>
            </div>
            <div class="nbsms-step" data-step="2">
                <span class="step-number">2</span>
                <span class="step-label">Compose Message</span>
            </div>
            <div class="nbsms-step" data-step="3">
                <span class="step-number">3</span>
                <span class="step-label">Review & Send</span>
            </div>
        </div>

        <!-- Step 1: Select Recipients -->
        <div class="nbsms-step-content" id="step-1" style="display: block;">
            <div class="nbsms-card">
                <h2>Select Recipients</h2>
                
                <!-- Segmentation Options -->
                <div class="nbsms-segment-tabs">
                    <button class="nbsms-tab active" data-tab="segment">
                        <span class="dashicons dashicons-groups"></span>
                        Customer Segments
                    </button>
                    <button class="nbsms-tab" data-tab="csv">
                        <span class="dashicons dashicons-media-spreadsheet"></span>
                        Import CSV
                    </button>
                    <button class="nbsms-tab" data-tab="manual">
                        <span class="dashicons dashicons-edit"></span>
                        Manual Entry
                    </button>
                </div>

                <!-- Customer Segments Tab -->
                <div class="nbsms-tab-content active" id="tab-segment">
                    <div class="nbsms-form-row">
                        <label>
                            <strong>Customer Segment:</strong>
                        </label>
                        <select id="customer-segment" class="nbsms-input">
                            <option value="all">All Customers</option>
                            <option value="with_orders">Customers with Orders</option>
                            <option value="no_orders">Customers without Orders</option>
                            <option value="high_value">High-Value Customers</option>
                            <option value="recent">Recent Customers</option>
                        </select>
                    </div>

                    <!-- Filters (shown based on segment) -->
                    <div id="segment-filters">
                        <div class="nbsms-form-row" id="filter-min-orders" style="display:none;">
                            <label>
                                <strong>Minimum Orders:</strong>
                            </label>
                            <input type="number" id="min-orders" class="nbsms-input" min="0" value="1">
                            <span class="description">Customers with at least this many completed orders</span>
                        </div>

                        <div class="nbsms-form-row" id="filter-min-spent" style="display:none;">
                            <label>
                                <strong>Minimum Amount Spent (₦):</strong>
                            </label>
                            <input type="number" id="min-spent" class="nbsms-input" min="0" value="10000">
                            <span class="description">Customers who have spent at least this amount</span>
                        </div>

                        <div class="nbsms-form-row" id="filter-days-since" style="display:none;">
                            <label>
                                <strong>Active in Last (Days):</strong>
                            </label>
                            <input type="number" id="days-since-order" class="nbsms-input" min="0" value="30">
                            <span class="description">Customers who ordered within this many days</span>
                        </div>
                    </div>

                    <div class="nbsms-form-row">
                        <button type="button" id="load-customers" class="button button-primary">
                            <span class="dashicons dashicons-search"></span>
                            Load Customers
                        </button>
                    </div>

                    <!-- Customer List -->
                    <div id="customer-list" style="display:none; margin-top: 20px;">
                        <div class="nbsms-list-header">
                            <h3>
                                <span id="customer-count">0</span> Customers Found
                                <small>(<span id="opted-in-count">0</span> opted in)</small>
                            </h3>
                            <div class="nbsms-list-actions">
                                <label>
                                    <input type="checkbox" id="select-all-customers">
                                    Select All
                                </label>
                                <label style="margin-left: 15px;">
                                    <input type="checkbox" id="only-opted-in" checked>
                                    Only Opted-In Customers
                                </label>
                            </div>
                        </div>

                        <div class="nbsms-customer-table">
                            <table class="wp-list-table widefat fixed striped">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="table-select-all"></th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Orders</th>
                                        <th>Total Spent</th>
                                        <th width="80">Opted In</th>
                                    </tr>
                                </thead>
                                <tbody id="customer-tbody">
                                    <!-- Customers will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- CSV Import Tab -->
                <div class="nbsms-tab-content" id="tab-csv">
                    <div class="nbsms-upload-area">
                        <div class="upload-icon">
                            <span class="dashicons dashicons-upload"></span>
                        </div>
                        <h3>Upload CSV File</h3>
                        <p>CSV should contain: Name, Phone, Email (optional)</p>
                        <input type="file" id="csv-file" accept=".csv,.txt" style="display:none;">
                        <button type="button" id="choose-csv-file" class="button button-primary">
                            Choose File
                        </button>
                        <button type="button" id="import-csv" class="button button-secondary" style="display:none;">
                            Import CSV
                        </button>
                        <p class="description">
                            <a href="#" id="download-sample-csv">Download sample CSV</a>
                        </p>
                    </div>

                    <div id="csv-preview" style="display:none; margin-top: 20px;">
                        <h3>Import Preview</h3>
                        <div class="nbsms-import-stats">
                            <div class="stat">
                                <strong id="csv-total">0</strong>
                                <span>Total Rows</span>
                            </div>
                            <div class="stat success">
                                <strong id="csv-valid">0</strong>
                                <span>Valid</span>
                            </div>
                            <div class="stat error">
                                <strong id="csv-invalid">0</strong>
                                <span>Invalid</span>
                            </div>
                        </div>
                        <div id="csv-errors" style="display:none;">
                            <h4>Errors:</h4>
                            <ul id="csv-error-list"></ul>
                        </div>
                    </div>
                </div>

                <!-- Manual Entry Tab -->
                <div class="nbsms-tab-content" id="tab-manual">
                    <div class="nbsms-form-row">
                        <label>
                            <strong>Enter phone numbers (one per line):</strong>
                        </label>
                        <textarea id="manual-phones" class="nbsms-textarea" rows="10" placeholder="08012345678&#10;08087654321&#10;+2348012345678"></textarea>
                        <span class="description">Supported formats: 080..., +234..., 234...</span>
                    </div>
                    <div class="nbsms-form-row">
                        <button type="button" id="parse-manual-phones" class="button button-primary">
                            <span class="dashicons dashicons-yes"></span>
                            Validate Numbers
                        </button>
                    </div>
                    <div id="manual-results" style="display:none;">
                        <p>
                            <strong>Valid:</strong> <span id="manual-valid-count">0</span> |
                            <strong>Invalid:</strong> <span id="manual-invalid-count">0</span>
                        </p>
                    </div>
                </div>

                <!-- Selected Recipients Summary -->
                <div id="selected-recipients-summary" style="display:none; margin-top: 20px;">
                    <div class="nbsms-summary-box">
                        <h3>
                            <span class="dashicons dashicons-yes-alt"></span>
                            <span id="selected-count">0</span> Recipients Selected
                        </h3>
                    </div>
                </div>

                <div class="nbsms-step-actions">
                    <button type="button" class="button button-primary button-large" id="next-to-step-2" disabled>
                        Next: Compose Message
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 2: Compose Message -->
        <div class="nbsms-step-content" id="step-2" style="display: none;">
            <div class="nbsms-card">
                <h2>Compose Your Message</h2>

                <div class="nbsms-form-row">
                    <label>
                        <strong>Message Template:</strong>
                    </label>
                    <select id="bulk-template" class="nbsms-input">
                        <option value="">-- Select a template --</option>
                        <?php
                        global $wpdb;
                        $templates = $wpdb->get_results("
                            SELECT id, name, content 
                            FROM {$wpdb->prefix}nbsms_templates 
                            WHERE status = 'active' 
                            ORDER BY name
                        ");
                        foreach ($templates as $template) {
                            echo '<option value="' . esc_attr($template->id) . '">' . esc_html($template->name) . '</option>';
                        }
                        ?>
                    </select>
                    <span class="description">Or compose a new message below</span>
                </div>

                <div class="nbsms-form-row">
                    <label>
                        <strong>Message:</strong>
                    </label>
                    <textarea id="bulk-message" class="nbsms-textarea" rows="6" placeholder="Type your message here..."></textarea>
                    
                    <!-- Character Counter -->
                    <div class="nbsms-char-counter">
                        <div class="counter-bar">
                            <div class="counter-progress" id="bulk-char-progress"></div>
                        </div>
                        <div class="counter-info">
                            <span id="bulk-char-count">0</span> / <span id="bulk-char-limit">160</span> characters
                            <span class="separator">|</span>
                            <span id="bulk-sms-parts">1</span> SMS part(s)
                            <span class="separator">|</span>
                            <span id="bulk-est-cost">₦0</span> per recipient
                        </div>
                    </div>
                </div>

                <!-- Available Variables -->
                <div class="nbsms-variables">
                    <strong>Available Variables:</strong>
                    <div class="variable-tags">
                        <span class="variable-tag" data-var="{name}">{name}</span>
                        <span class="variable-tag" data-var="{customer_name}">{customer_name}</span>
                        <span class="variable-tag" data-var="{email}">{email}</span>
                        <span class="variable-tag" data-var="{phone}">{phone}</span>
                        <span class="variable-tag" data-var="{orders}">{orders}</span>
                        <span class="variable-tag" data-var="{spent}">{spent}</span>
                    </div>
                    <p class="description">Click a variable to insert it into your message</p>
                </div>

                <!-- Message Preview -->
                <div class="nbsms-preview">
                    <h4>Preview:</h4>
                    <div class="preview-box" id="bulk-message-preview">
                        Your message will appear here...
                    </div>
                </div>

                <div class="nbsms-step-actions">
                    <button type="button" class="button button-secondary" id="back-to-step-1">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        Back
                    </button>
                    <button type="button" class="button button-primary button-large" id="next-to-step-3" disabled>
                        Next: Review & Send
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 3: Review & Send -->
        <div class="nbsms-step-content" id="step-3" style="display: none;">
            <div class="nbsms-card">
                <h2>Review & Send</h2>

                <!-- Campaign Summary -->
                <div class="nbsms-campaign-summary">
                    <div class="summary-section">
                        <h3>
                            <span class="dashicons dashicons-groups"></span>
                            Recipients
                        </h3>
                        <div class="summary-value" id="summary-recipients">0</div>
                    </div>

                    <div class="summary-section">
                        <h3>
                            <span class="dashicons dashicons-email-alt"></span>
                            SMS Parts
                        </h3>
                        <div class="summary-value" id="summary-sms-parts">0</div>
                    </div>

                    <div class="summary-section">
                        <h3>
                            <span class="dashicons dashicons-cart"></span>
                            Total Cost
                        </h3>
                        <div class="summary-value highlight" id="summary-cost">₦0</div>
                    </div>
                </div>

                <!-- Message Preview -->
                <div class="nbsms-final-preview">
                    <h3>Message:</h3>
                    <div class="preview-box" id="final-message-preview"></div>
                </div>

                <!-- Sending Options -->
                <div class="nbsms-send-options">
                    <h3>Sending Options:</h3>
                    
                    <label class="nbsms-radio">
                        <input type="radio" name="send_timing" value="now" checked>
                        <span>Send Now</span>
                    </label>

                    <label class="nbsms-radio">
                        <input type="radio" name="send_timing" value="schedule">
                        <span>Schedule for Later</span>
                    </label>

                    <div id="schedule-options" style="display:none; margin-left: 30px; margin-top: 10px;">
                        <div class="nbsms-form-row">
                            <label>
                                <strong>Date & Time:</strong>
                            </label>
                            <input type="datetime-local" id="schedule-datetime" class="nbsms-input">
                        </div>
                    </div>
                </div>

                <!-- Warning Messages -->
                <div class="nbsms-warnings">
                    <div class="notice notice-warning inline">
                        <p>
                            <strong>⚠️ Important:</strong> Please verify your message and recipient list before sending. 
                            This action cannot be undone.
                        </p>
                    </div>
                </div>

                <div class="nbsms-step-actions">
                    <button type="button" class="button button-secondary" id="back-to-step-2">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        Back
                    </button>
                    <button type="button" class="button button-primary button-large" id="send-bulk-sms">
                        <span class="dashicons dashicons-megaphone"></span>
                        Send Bulk SMS
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div class="nbsms-step-content" id="step-success" style="display: none;">
            <div class="nbsms-card">
                <div class="nbsms-success-message">
                    <div class="success-icon">
                        <span class="dashicons dashicons-yes-alt"></span>
                    </div>
                    <h2>Bulk SMS Sent Successfully!</h2>
                    <div id="success-details">
                        <p>
                            <strong id="success-queued">0</strong> messages have been queued for sending.
                        </p>
                        <p>
                            Messages will be processed by the system queue and sent within the next few minutes.
                        </p>
                    </div>
                    <div class="nbsms-step-actions">
                        <button type="button" class="button button-primary" onclick="location.reload()">
                            Send Another Campaign
                        </button>
                        <a href="?page=nbsms-logs" class="button button-secondary">
                            View Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    
    let selectedRecipients = [];
    let bulkMessage = '';

    // Tab switching
    $('.nbsms-tab').on('click', function() {
        const tab = $(this).data('tab');
        $('.nbsms-tab').removeClass('active');
        $(this).addClass('active');
        $('.nbsms-tab-content').removeClass('active');
        $('#tab-' + tab).addClass('active');
    });

    // Segment change
    $('#customer-segment').on('change', function() {
        const segment = $(this).val();
        
        // Hide all filters
        $('#segment-filters .nbsms-form-row').hide();
        
        // Show relevant filters
        if (segment === 'with_orders' || segment === 'high_value') {
            $('#filter-min-orders').show();
        }
        if (segment === 'high_value') {
            $('#filter-min-spent').show();
        }
        if (segment === 'recent') {
            $('#filter-days-since').show();
        }
    });

    // Load customers
    $('#load-customers').on('click', function() {
        const button = $(this);
        button.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Loading...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nbsms_get_customers',
                nonce: '<?php echo wp_create_nonce('nbsms_ajax_nonce'); ?>',
                segment: $('#customer-segment').val(),
                min_orders: $('#min-orders').val(),
                min_spent: $('#min-spent').val(),
                days_since_order: $('#days-since-order').val()
            },
            success: function(response) {
                if (response.success) {
                    displayCustomers(response.data.customers);
                    $('#customer-count').text(response.data.count);
                    $('#customer-list').slideDown();
                } else {
                    alert('Error loading customers: ' + response.data);
                }
            },
            complete: function() {
                button.prop('disabled', false).html('<span class="dashicons dashicons-search"></span> Load Customers');
            }
        });
    });

    // Display customers in table
    function displayCustomers(customers) {
        const tbody = $('#customer-tbody');
        tbody.empty();

        let optedInCount = 0;

        customers.forEach(function(customer) {
            if (customer.opted_in) optedInCount++;

            const row = $('<tr>');
            row.append('<td><input type="checkbox" class="customer-checkbox" data-customer=\'' + JSON.stringify(customer) + '\' ' + (customer.opted_in ? 'checked' : '') + '></td>');
            row.append('<td>' + customer.name + '</td>');
            row.append('<td>' + customer.phone + '</td>');
            row.append('<td>' + customer.orders + '</td>');
            row.append('<td>₦' + customer.spent.toLocaleString() + '</td>');
            row.append('<td>' + (customer.opted_in ? '<span class="dashicons dashicons-yes" style="color:green;"></span>' : '<span class="dashicons dashicons-no" style="color:red;"></span>') + '</td>');
            tbody.append(row);
        });

        $('#opted-in-count').text(optedInCount);
        updateSelectedCount();
    }

    // Select all checkboxes
    $('#table-select-all, #select-all-customers').on('change', function() {
        const checked = $(this).prop('checked');
        $('.customer-checkbox').prop('checked', checked);
        updateSelectedCount();
    });

    // Individual checkbox
    $(document).on('change', '.customer-checkbox', function() {
        updateSelectedCount();
    });

    // Only opted-in filter
    $('#only-opted-in').on('change', function() {
        const onlyOptedIn = $(this).prop('checked');
        $('.customer-checkbox').each(function() {
            const customer = JSON.parse($(this).attr('data-customer'));
            if (onlyOptedIn && !customer.opted_in) {
                $(this).prop('checked', false);
            }
        });
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        selectedRecipients = [];
        $('.customer-checkbox:checked').each(function() {
            selectedRecipients.push(JSON.parse($(this).attr('data-customer')));
        });

        const count = selectedRecipients.length;
        $('#selected-count').text(count);
        
        if (count > 0) {
            $('#selected-recipients-summary').slideDown();
            $('#next-to-step-2').prop('disabled', false);
        } else {
            $('#selected-recipients-summary').slideUp();
            $('#next-to-step-2').prop('disabled', true);
        }
    }

    // CSV Upload
    $('#choose-csv-file').on('click', function() {
        $('#csv-file').click();
    });

    $('#csv-file').on('change', function() {
        if (this.files.length > 0) {
            $('#import-csv').show();
        }
    });

    $('#import-csv').on('click', function() {
        const fileInput = $('#csv-file')[0];
        if (fileInput.files.length === 0) {
            alert('Please select a CSV file first');
            return;
        }

        const formData = new FormData();
        formData.append('csv_file', fileInput.files[0]);
        formData.append('action', 'nbsms_import_csv');
        formData.append('nonce', '<?php echo wp_create_nonce('nbsms_ajax_nonce'); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    displayCsvResults(response.data);
                    selectedRecipients = response.data.customers;
                    updateSelectedCount();
                } else {
                    alert('Error importing CSV: ' + response.data);
                }
            }
        });
    });

    function displayCsvResults(results) {
        $('#csv-total').text(results.total);
        $('#csv-valid').text(results.imported);
        $('#csv-invalid').text(results.skipped);
        $('#csv-preview').slideDown();

        if (results.errors.length > 0) {
            const errorList = $('#csv-error-list');
            errorList.empty();
            results.errors.forEach(function(error) {
                errorList.append('<li>' + error + '</li>');
            });
            $('#csv-errors').show();
        }
    }

    // Manual phone entry
    $('#parse-manual-phones').on('click', function() {
        const phones = $('#manual-phones').val().split('\n');
        let valid = 0;
        let invalid = 0;

        selectedRecipients = [];

        phones.forEach(function(phone) {
            phone = phone.trim();
            if (phone) {
                // Simple validation (you can enhance this)
                if (/^(\+?234|0)[0-9]{10}$/.test(phone.replace(/\s/g, ''))) {
                    valid++;
                    selectedRecipients.push({
                        name: '',
                        phone: phone,
                        email: ''
                    });
                } else {
                    invalid++;
                }
            }
        });

        $('#manual-valid-count').text(valid);
        $('#manual-invalid-count').text(invalid);
        $('#manual-results').slideDown();
        updateSelectedCount();
    });

    // Step navigation
    $('#next-to-step-2').on('click', function() {
        goToStep(2);
    });

    $('#back-to-step-1').on('click', function() {
        goToStep(1);
    });

    $('#next-to-step-3').on('click', function() {
        goToStep(3);
        updateReviewSummary();
    });

    $('#back-to-step-2').on('click', function() {
        goToStep(2);
    });

    function goToStep(step) {
        $('.nbsms-step').removeClass('active');
        $('.nbsms-step[data-step="' + step + '"]').addClass('active');
        
        $('.nbsms-step-content').hide();
        $('#step-' + step).show();
    }

    // Template selection
    $('#bulk-template').on('change', function() {
        const templateId = $(this).val();
        if (templateId) {
            const selectedText = $(this).find('option:selected').text();
            // In production, you'd fetch the template content via AJAX
            // For now, just clear the message
            $('#bulk-message').val('');
        }
    });

    // Message input
    $('#bulk-message').on('input', function() {
        bulkMessage = $(this).val();
        updateCharacterCount();
        updateMessagePreview();
        
        if (bulkMessage.length > 0) {
            $('#next-to-step-3').prop('disabled', false);
        } else {
            $('#next-to-step-3').prop('disabled', true);
        }
    });

    // Variable insertion
    $('.variable-tag').on('click', function() {
        const variable = $(this).data('var');
        const textarea = $('#bulk-message');
        const cursorPos = textarea.prop('selectionStart');
        const textBefore = textarea.val().substring(0, cursorPos);
        const textAfter = textarea.val().substring(cursorPos);
        
        textarea.val(textBefore + variable + textAfter);
        textarea.trigger('input');
        textarea.focus();
    });

    // Character count and cost estimation
    function updateCharacterCount() {
        const message = $('#bulk-message').val();
        const length = message.length;
        const parts = Math.ceil(length / 160) || 1;
        const progress = (length % 160) / 160 * 100;
        
        $('#bulk-char-count').text(length);
        $('#bulk-char-limit').text(parts * 160);
        $('#bulk-sms-parts').text(parts);
        $('#bulk-char-progress').css('width', progress + '%');
        
        // Cost estimation
        const costPerSms = 4;
        const costPerRecipient = parts * costPerSms;
        $('#bulk-est-cost').text('₦' + costPerRecipient);
    }

    // Message preview
    function updateMessagePreview() {
        const message = $('#bulk-message').val();
        const preview = message || 'Your message will appear here...';
        $('#bulk-message-preview').text(preview);
    }

    // Update review summary
    function updateReviewSummary() {
        const recipients = selectedRecipients.length;
        const message = $('#bulk-message').val();
        const parts = Math.ceil(message.length / 160) || 1;
        const totalSms = recipients * parts;
        const totalCost = totalSms * 4;

        $('#summary-recipients').text(recipients);
        $('#summary-sms-parts').text(totalSms);
        $('#summary-cost').text('₦' + totalCost.toLocaleString());
        $('#final-message-preview').text(message);
    }

    // Schedule options
    $('input[name="send_timing"]').on('change', function() {
        if ($(this).val() === 'schedule') {
            $('#schedule-options').slideDown();
        } else {
            $('#schedule-options').slideUp();
        }
    });

    // Send bulk SMS
    $('#send-bulk-sms').on('click', function() {
        if (!confirm('Are you sure you want to send this bulk SMS campaign?')) {
            return;
        }

        const button = $(this);
        button.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Sending...');

        const sendTiming = $('input[name="send_timing"]:checked').val();
        const scheduleTime = sendTiming === 'schedule' ? $('#schedule-datetime').val() : '';

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nbsms_send_bulk',
                nonce: '<?php echo wp_create_nonce('nbsms_ajax_nonce'); ?>',
                recipients: JSON.stringify(selectedRecipients),
                message: $('#bulk-message').val(),
                schedule_time: scheduleTime
            },
            success: function(response) {
                if (response.success) {
                    $('#success-queued').text(response.data.queued);
                    goToStep('success');
                } else {
                    alert('Error sending bulk SMS: ' + response.data);
                    button.prop('disabled', false).html('<span class="dashicons dashicons-megaphone"></span> Send Bulk SMS');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                button.prop('disabled', false).html('<span class="dashicons dashicons-megaphone"></span> Send Bulk SMS');
            }
        });
    });

    // Download sample CSV
    $('#download-sample-csv').on('click', function(e) {
        e.preventDefault();
        const csvContent = "Name,Phone,Email\nJohn Doe,08012345678,john@example.com\nJane Smith,08087654321,jane@example.com";
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'sample-bulk-sms.csv';
        a.click();
    });

});
</script>

<style>
.nbsms-bulk-container {
    max-width: 1200px;
    margin: 20px 0;
}

.nbsms-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    position: relative;
}

.nbsms-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #ddd;
    z-index: 0;
}

.nbsms-step {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
}

.step-number {
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    border-radius: 50%;
    background: #ddd;
    color: #666;
    font-weight: bold;
    margin-bottom: 5px;
}

.nbsms-step.active .step-number {
    background: #900;
    color: #fff;
}

.step-label {
    display: block;
    font-size: 13px;
    color: #666;
}

.nbsms-step.active .step-label {
    color: #900;
    font-weight: bold;
}

.nbsms-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 30px;
}

.nbsms-card h2 {
    margin-top: 0;
    border-bottom: 2px solid #900;
    padding-bottom: 10px;
}

.nbsms-segment-tabs {
    display: flex;
    gap: 10px;
    margin: 20px 0;
    border-bottom: 2px solid #ddd;
}

.nbsms-tab {
    padding: 12px 20px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nbsms-tab:hover {
    background: #f0f0f1;
}

.nbsms-tab.active {
    border-bottom-color: #900;
    color: #900;
    font-weight: bold;
}

.nbsms-tab-content {
    display: none;
    padding: 20px 0;
}

.nbsms-tab-content.active {
    display: block;
}

.nbsms-form-row {
    margin-bottom: 20px;
}

.nbsms-form-row label {
    display: block;
    margin-bottom: 8px;
}

.nbsms-input, .nbsms-textarea {
    width: 100%;
    max-width: 500px;
}

.nbsms-customer-table {
    margin-top: 15px;
    max-height: 400px;
    overflow-y: auto;
}

.nbsms-list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.nbsms-list-header h3 {
    margin: 0;
}

.nbsms-upload-area {
    text-align: center;
    padding: 60px 20px;
    border: 2px dashed #ddd;
    border-radius: 8px;
    background: #f9f9f9;
}

.upload-icon .dashicons {
    font-size: 64px;
    color: #ddd;
}

.nbsms-import-stats {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin: 20px 0;
}

.nbsms-import-stats .stat {
    text-align: center;
    padding: 20px;
    background: #f0f0f1;
    border-radius: 8px;
    min-width: 120px;
}

.nbsms-import-stats .stat strong {
    display: block;
    font-size: 32px;
    margin-bottom: 5px;
}

.nbsms-import-stats .stat.success {
    background: #d4edda;
    color: #155724;
}

.nbsms-import-stats .stat.error {
    background: #f8d7da;
    color: #721c24;
}

.nbsms-char-counter {
    margin-top: 10px;
}

.counter-bar {
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 8px;
}

.counter-progress {
    height: 100%;
    background: #900;
    transition: width 0.3s;
}

.counter-info {
    font-size: 13px;
    color: #666;
}

.separator {
    margin: 0 10px;
    color: #ccc;
}

.nbsms-variables {
    margin: 20px 0;
    padding: 15px;
    background: #f0f0f1;
    border-radius: 4px;
}

.variable-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 10px 0;
}

.variable-tag {
    padding: 6px 12px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    font-family: monospace;
    font-size: 12px;
}

.variable-tag:hover {
    background: #900;
    color: #fff;
    border-color: #900;
}

.nbsms-preview {
    margin: 20px 0;
}

.preview-box {
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    white-space: pre-wrap;
    font-family: Arial, sans-serif;
    min-height: 80px;
}

.nbsms-campaign-summary {
    display: flex;
    gap: 20px;
    margin: 30px 0;
}

.summary-section {
    flex: 1;
    text-align: center;
    padding: 25px;
    background: #f0f0f1;
    border-radius: 8px;
}

.summary-section h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.summary-value {
    font-size: 36px;
    font-weight: bold;
    color: #333;
}

.summary-value.highlight {
    color: #900;
}

.nbsms-send-options {
    margin: 30px 0;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 4px;
}

.nbsms-radio {
    display: block;
    margin: 10px 0;
    cursor: pointer;
}

.nbsms-radio input {
    margin-right: 8px;
}

.nbsms-step-actions {
    margin-top: 30px;
    text-align: right;
    border-top: 1px solid #ddd;
    padding-top: 20px;
}

.nbsms-step-actions button {
    margin-left: 10px;
}

.nbsms-success-message {
    text-align: center;
    padding: 60px 20px;
}

.success-icon .dashicons {
    font-size: 80px;
    color: #46b450;
}

.nbsms-success-message h2 {
    color: #46b450;
    border: none;
}

.nbsms-summary-box {
    padding: 15px;
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
}

.nbsms-summary-box h3 {
    margin: 0;
    color: #155724;
    display: flex;
    align-items: center;
    gap: 10px;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spin {
    animation: spin 1s linear infinite;
}
</style>
<?php
