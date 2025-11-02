/**
 * Admin JavaScript
 *
 * @package WC_Nigeria_Bulk_SMS
 * @since 1.0.0
 */

(function($) {
    'use strict';

    var NBSMS_Admin = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.loadBalance();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Test connection button
            $(document).on('click', '#nbsms-test-connection', this.testConnection.bind(this));
            
            // Refresh balance button
            $(document).on('click', '#nbsms-refresh-balance', this.loadBalance.bind(this));
            
            // Settings form submission (optional AJAX save)
            // $(document).on('submit', '#nbsms-settings-form', this.saveSettings.bind(this));
            
            // Testing page events
            $(document).on('submit', '#nbsms-test-form', this.sendTestSMS.bind(this));
            $(document).on('input', '#test_message', this.updateCharacterCount.bind(this));
            $(document).on('blur', '#test_phone', this.validatePhone.bind(this));
            
            // Initialize character counter if on testing page
            if ($('#test_message').length) {
                this.updateCharacterCount();
            }
            
            console.log('Nigeria Bulk SMS Admin JS loaded');
        },

        /**
         * Test API connection
         */
        testConnection: function(e) {
            e.preventDefault();
            
            var $button = $('#nbsms-test-connection');
            var $result = $('#nbsms-connection-result');
            
            // Show loading
            $button.prop('disabled', true);
            $button.find('.dashicons').addClass('dashicons-update-spinning');
            $result.removeClass('success error').hide();
            
            // Make AJAX request
            $.ajax({
                url: nbsmsAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'nbsms_test_connection',
                    nonce: nbsmsAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $result
                            .addClass('success')
                            .html('<strong>' + nbsmsAdmin.strings.success + '</strong> ' + response.data.message)
                            .fadeIn();
                        
                        // Update balance if available
                        if (response.data.balance) {
                            $('.balance-amount').text(response.data.balance);
                        }
                    } else {
                        $result
                            .addClass('error')
                            .html('<strong>' + nbsmsAdmin.strings.error + '</strong> ' + response.data.message)
                            .fadeIn();
                    }
                },
                error: function() {
                    $result
                        .addClass('error')
                        .html('<strong>' + nbsmsAdmin.strings.error + '</strong> Connection failed. Please try again.')
                        .fadeIn();
                },
                complete: function() {
                    $button.prop('disabled', false);
                    $button.find('.dashicons').removeClass('dashicons-update-spinning');
                }
            });
        },

        /**
         * Load account balance
         */
        loadBalance: function(e) {
            if (e) {
                e.preventDefault();
            }
            
            var $button = $('#nbsms-refresh-balance');
            var $amount = $('.balance-amount');
            
            // Check if balance display exists
            if ($amount.length === 0) {
                return;
            }
            
            // Show loading
            if ($button.length) {
                $button.prop('disabled', true);
                $button.find('.dashicons').addClass('dashicons-update-spinning');
            }
            
            // Make AJAX request
            $.ajax({
                url: nbsmsAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'nbsms_get_balance',
                    nonce: nbsmsAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $amount.text(response.data.balance);
                        
                        // Show success animation
                        $amount.css('color', '#46b450').animate({
                            fontSize: '20px'
                        }, 200).animate({
                            fontSize: '18px'
                        }, 200, function() {
                            $amount.css('color', '#2271b1');
                        });
                    } else {
                        $amount.text('Error');
                        NBSMS_Admin.showNotice(response.data.message, 'error');
                    }
                },
                error: function() {
                    $amount.text('Error');
                },
                complete: function() {
                    if ($button.length) {
                        $button.prop('disabled', false);
                        $button.find('.dashicons').removeClass('dashicons-update-spinning');
                    }
                }
            });
        },

        /**
         * Save settings via AJAX (optional - form handles it by default)
         */
        saveSettings: function(e) {
            e.preventDefault();
            
            var $form = $(e.target);
            var $submitButton = $form.find('[name="nbsms_save_settings"]');
            var $loading = $('.nbsms-save-loading');
            
            // Show loading
            $submitButton.prop('disabled', true);
            $loading.show();
            
            // Serialize form data
            var formData = $form.serialize();
            formData += '&action=nbsms_save_settings';
            formData += '&nonce=' + nbsmsAdmin.nonce;
            
            // Make AJAX request
            $.ajax({
                url: nbsmsAdmin.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        NBSMS_Admin.showSuccess(response.data.message);
                    } else {
                        NBSMS_Admin.showError(response.data.message);
                    }
                },
                error: function() {
                    NBSMS_Admin.showError('An error occurred while saving settings.');
                },
                complete: function() {
                    $submitButton.prop('disabled', false);
                    $loading.hide();
                }
            });
        },

        /**
         * Show loading state
         */
        showLoading: function(button) {
            button.prop('disabled', true);
            button.find('.nbsms-loading').show();
        },

        /**
         * Hide loading state
         */
        hideLoading: function(button) {
            button.prop('disabled', false);
            button.find('.nbsms-loading').hide();
        },

        /**
         * Show success message
         */
        showSuccess: function(message) {
            this.showNotice(message, 'success');
        },

        /**
         * Show error message
         */
        showError: function(message) {
            this.showNotice(message, 'error');
        },

        /**
         * Show notice
         */
        showNotice: function(message, type) {
            var noticeClass = type === 'success' ? 'nbsms-success' : 'nbsms-error';
            var notice = $('<div class="' + noticeClass + '">' + message + '</div>');
            
            // Remove existing notices
            $('.nbsms-success, .nbsms-error').remove();
            
            // Add new notice after h1
            $('.wrap h1').after(notice);
            
            // Scroll to notice
            $('html, body').animate({
                scrollTop: notice.offset().top - 100
            }, 500);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * Confirm dialog
         */
        confirm: function(message) {
            return window.confirm(message || nbsmsAdmin.strings.confirm_delete);
        },

        /**
         * Send test SMS
         */
        sendTestSMS: function(e) {
            e.preventDefault();
            
            var $form = $(e.target);
            var $button = $('#send-test-sms-btn');
            var $loading = $('.nbsms-test-loading');
            var $result = $('#test-sms-result');
            
            // Get form data
            var formData = {
                action: 'nbsms_send_test_sms',
                nonce: nbsmsAdmin.nonce,
                phone: $('#test_phone').val(),
                message: $('#test_message').val(),
                sender_id: $('#test_sender_id').val()
            };
            
            // Validate
            if (!formData.phone || !formData.message) {
                $result
                    .removeClass('success')
                    .addClass('error')
                    .html('<strong>' + nbsmsAdmin.strings.error + '</strong> Please fill in all required fields.')
                    .fadeIn();
                return;
            }
            
            // Show loading
            $button.prop('disabled', true);
            $loading.show();
            $result.hide();
            
            // Send AJAX request
            $.ajax({
                url: nbsmsAdmin.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        var details = '';
                        if (response.data.data) {
                            details = '<div class="test-result-details">';
                            details += '<span><strong>Cost:</strong> ₦' + response.data.data.cost + '</span>';
                            details += '<span><strong>SMS Parts:</strong> ' + response.data.data.sms_parts + '</span>';
                            details += '<span><strong>Count:</strong> ' + response.data.data.count + '</span>';
                            details += '</div>';
                        }
                        
                        $result
                            .removeClass('error')
                            .addClass('success')
                            .html('<strong>' + nbsmsAdmin.strings.success + '</strong> ' + response.data.message + details)
                            .fadeIn();
                        
                        // Clear form
                        $('#test_message').val('');
                        NBSMS_Admin.updateCharacterCount();
                        
                        // Reload page after 2 seconds to update history
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $result
                            .removeClass('success')
                            .addClass('error')
                            .html('<strong>' + nbsmsAdmin.strings.error + '</strong> ' + response.data.message)
                            .fadeIn();
                    }
                },
                error: function() {
                    $result
                        .removeClass('success')
                        .addClass('error')
                        .html('<strong>' + nbsmsAdmin.strings.error + '</strong> An error occurred. Please try again.')
                        .fadeIn();
                },
                complete: function() {
                    $button.prop('disabled', false);
                    $loading.hide();
                }
            });
        },

        /**
         * Update character count
         */
        updateCharacterCount: function() {
            var $textarea = $('#test_message');
            if ($textarea.length === 0) return;
            
            var message = $textarea.val();
            var length = message.length;
            var parts = NBSMS_Admin.calculateSMSParts(length);
            var maxChars = parts === 1 ? 160 : (parts * 153);
            var percentage = (length / maxChars) * 100;
            
            // Update displays
            $('#char-count').text(length);
            $('#sms-parts').text(parts);
            
            // Update progress bar
            var $progress = $('#char-progress');
            $progress.css('width', Math.min(percentage, 100) + '%');
            
            // Change color based on usage
            $progress.removeClass('warning danger');
            if (percentage > 90) {
                $progress.addClass('danger');
            } else if (percentage > 75) {
                $progress.addClass('warning');
            }
            
            // Show estimated cost if available
            // This is a placeholder - actual cost would come from API
            var estimatedCost = parts * 4; // Assuming ₦4 per SMS
            $('#estimated-cost').text(estimatedCost);
        },

        /**
         * Calculate SMS parts
         */
        calculateSMSParts: function(length) {
            if (length === 0) return 1;
            if (length <= 160) return 1;
            return Math.ceil(length / 153);
        },

        /**
         * Validate phone number
         */
        validatePhone: function(e) {
            var $input = $(e.target);
            var phone = $input.val();
            var $result = $('#phone-validation-result');
            
            if (!phone) {
                $result.hide();
                return;
            }
            
            // Quick client-side validation
            var nigerianPattern = /^(0[789][01]\d{8}|234[789][01]\d{8}|\+234[789][01]\d{8})$/;
            
            if (nigerianPattern.test(phone.replace(/\s+/g, ''))) {
                $result
                    .removeClass('invalid')
                    .addClass('valid')
                    .html('✓ Valid phone number format')
                    .fadeIn();
            } else {
                $result
                    .removeClass('valid')
                    .addClass('invalid')
                    .html('✗ Invalid phone number format. Use: 08012345678 or +2348012345678')
                    .fadeIn();
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        NBSMS_Admin.init();
    });

    // Make NBSMS_Admin globally accessible
    window.NBSMS_Admin = NBSMS_Admin;

})(jQuery);

        /**
         * Template editor functionality
         */
        initTemplateEditor: function() {
            // Character counter for template editor
            $(document).on('input', '#template_content', function() {
                NBSMS_Admin.updateTemplateCharCount();
            });
            
            // Variable buttons
            $(document).on('click', '.variable-button', function(e) {
                e.preventDefault();
                var variable = $(this).data('variable');
                NBSMS_Admin.insertVariable(variable);
            });
            
            // Preview template
            $(document).on('click', '#preview-template-btn', function(e) {
                e.preventDefault();
                NBSMS_Admin.previewTemplate();
            });
            
            // Initialize counter if on template page
            if ($('#template_content').length) {
                NBSMS_Admin.updateTemplateCharCount();
            }
        },

        /**
         * Update template character count
         */
        updateTemplateCharCount: function() {
            var $textarea = $('#template_content');
            if ($textarea.length === 0) return;
            
            var message = $textarea.val();
            var length = message.length;
            var parts = NBSMS_Admin.calculateSMSParts(length);
            var maxChars = parts === 1 ? 160 : (parts * 153);
            var percentage = (length / maxChars) * 100;
            
            $('#template-char-count').text(length);
            $('#template-sms-parts').text(parts);
            
            var $progress = $('#template-char-progress');
            $progress.css('width', Math.min(percentage, 100) + '%');
            
            $progress.removeClass('warning danger');
            if (percentage > 90) {
                $progress.addClass('danger');
            } else if (percentage > 75) {
                $progress.addClass('warning');
            }
        },

        /**
         * Insert variable at cursor position
         */
        insertVariable: function(variable) {
            var $textarea = $('#template_content');
            if ($textarea.length === 0) return;
            
            var textarea = $textarea[0];
            var start = textarea.selectionStart;
            var end = textarea.selectionEnd;
            var text = $textarea.val();
            
            var before = text.substring(0, start);
            var after = text.substring(end, text.length);
            
            $textarea.val(before + variable + after);
            textarea.selectionStart = textarea.selectionEnd = start + variable.length;
            $textarea.focus();
            
            NBSMS_Admin.updateTemplateCharCount();
        },

        /**
         * Preview template with sample data
         */
        previewTemplate: function() {
            var $button = $('#preview-template-btn');
            var $result = $('#template-preview-result');
            var template = $('#template_content').val();
            
            if (!template) {
                alert('Please enter template content first.');
                return;
            }
            
            $button.prop('disabled', true);
            $result.hide();
            
            $.ajax({
                url: nbsmsAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'nbsms_preview_template',
                    nonce: nbsmsAdmin.nonce,
                    template_content: template
                },
                success: function(response) {
                    if (response.success) {
                        var html = '<div class="template-preview-display">';
                        html += '<strong>Preview:</strong><br>';
                        html += response.data.preview.replace(/\n/g, '<br>');
                        html += '</div>';
                        html += '<p class="description" style="margin-top:10px;">';
                        html += 'Characters: ' + response.data.char_count + ' | ';
                        html += 'SMS Parts: ' + response.data.sms_parts;
                        html += '</p>';
                        
                        $result.html(html).fadeIn();
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('Failed to generate preview. Please try again.');
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        }

    };

    // Initialize on document ready
    $(document).ready(function() {
        NBSMS_Admin.init();
        NBSMS_Admin.initTemplateEditor();
    });

    // Make NBSMS_Admin globally accessible
    window.NBSMS_Admin = NBSMS_Admin;

})(jQuery);
