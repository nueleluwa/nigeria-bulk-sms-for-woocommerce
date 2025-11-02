<?php
/**
 * Logs & Analytics Admin Template
 *
 * @package Nigeria_Bulk_SMS_For_WooCommerce
 * @since 1.7.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap nbsms-wrap">
    <h1>
        <span class="dashicons dashicons-chart-bar"></span>
        SMS Logs & Analytics
    </h1>

    <!-- Tab Navigation -->
    <div class="nbsms-tabs">
        <button class="nbsms-tab-btn active" data-tab="analytics">
            <span class="dashicons dashicons-chart-line"></span>
            Analytics Dashboard
        </button>
        <button class="nbsms-tab-btn" data-tab="logs">
            <span class="dashicons dashicons-list-view"></span>
            SMS Logs
        </button>
    </div>

    <!-- Analytics Tab -->
    <div class="nbsms-tab-content active" id="tab-analytics">
        
        <!-- Date Range Filter -->
        <div class="nbsms-card">
            <div class="date-filter-header">
                <h3>Date Range</h3>
                <div class="date-inputs">
                    <input type="date" id="stats-date-from" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                    <span class="date-separator">to</span>
                    <input type="date" id="stats-date-to" value="<?php echo date('Y-m-d'); ?>">
                    <button type="button" class="button" id="refresh-stats">
                        <span class="dashicons dashicons-update"></span>
                        Refresh
                    </button>
                </div>
                <div class="quick-filters">
                    <button class="button" data-days="7">Last 7 Days</button>
                    <button class="button" data-days="30">Last 30 Days</button>
                    <button class="button" data-days="90">Last 90 Days</button>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="nbsms-stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #00a0d2;">
                    <span class="dashicons dashicons-email-alt"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="stat-total">-</div>
                    <div class="stat-label">Total SMS Sent</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #46b450;">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="stat-successful">-</div>
                    <div class="stat-label">Successful</div>
                    <div class="stat-meta" id="stat-success-rate">-</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #dc3232;">
                    <span class="dashicons dashicons-dismiss"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="stat-failed">-</div>
                    <div class="stat-label">Failed</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #ffb900;">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="stat-pending">-</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #826eb4;">
                    <span class="dashicons dashicons-chart-area"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="stat-sms-parts">-</div>
                    <div class="stat-label">SMS Parts</div>
                </div>
            </div>

            <div class="stat-card highlight">
                <div class="stat-icon" style="background: #900;">
                    <span class="dashicons dashicons-money-alt"></span>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="stat-total-cost">₦-</div>
                    <div class="stat-label">Total Cost</div>
                    <div class="stat-meta" id="stat-avg-cost">-</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="nbsms-charts-row">
            <div class="chart-card">
                <h3>SMS Activity (Last 7 Days)</h3>
                <canvas id="activity-chart"></canvas>
            </div>

            <div class="chart-card">
                <h3>SMS by Type</h3>
                <canvas id="type-chart"></canvas>
            </div>
        </div>

        <!-- Top Recipients Table -->
        <div class="nbsms-card">
            <h3>Top 10 Recipients</h3>
            <div class="top-recipients-table">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Recipient</th>
                            <th>Phone</th>
                            <th>SMS Count</th>
                            <th>Total Cost</th>
                        </tr>
                    </thead>
                    <tbody id="top-recipients-tbody">
                        <tr><td colspan="4" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Logs Tab -->
    <div class="nbsms-tab-content" id="tab-logs">
        
        <!-- Filters -->
        <div class="nbsms-card">
            <div class="logs-filters">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Status:</label>
                        <select id="filter-status" class="nbsms-input">
                            <option value="">All Status</option>
                            <option value="sent">Sent</option>
                            <option value="failed">Failed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Type:</label>
                        <select id="filter-type" class="nbsms-input">
                            <option value="">All Types</option>
                            <option value="automated">Automated</option>
                            <option value="bulk">Bulk</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>From:</label>
                        <input type="date" id="filter-date-from" class="nbsms-input">
                    </div>

                    <div class="filter-group">
                        <label>To:</label>
                        <input type="date" id="filter-date-to" class="nbsms-input">
                    </div>

                    <div class="filter-group">
                        <label>Search:</label>
                        <input type="text" id="filter-search" class="nbsms-input" placeholder="Phone, name, or message...">
                    </div>

                    <div class="filter-actions">
                        <button type="button" class="button button-primary" id="apply-filters">
                            <span class="dashicons dashicons-filter"></span>
                            Apply Filters
                        </button>
                        <button type="button" class="button" id="clear-filters">
                            Clear
                        </button>
                    </div>
                </div>

                <div class="filter-row">
                    <div class="bulk-actions">
                        <select id="bulk-action">
                            <option value="">Bulk Actions</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="button" class="button" id="apply-bulk-action">Apply</button>
                    </div>

                    <div class="export-actions">
                        <button type="button" class="button" id="export-logs">
                            <span class="dashicons dashicons-download"></span>
                            Export to CSV
                        </button>
                    </div>

                    <div class="per-page">
                        <label>Show:</label>
                        <select id="per-page">
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>per page</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="nbsms-card">
            <div class="logs-table-container">
                <table class="wp-list-table widefat fixed striped" id="logs-table">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="select-all-logs"></th>
                            <th width="60">ID</th>
                            <th width="150">Date/Time</th>
                            <th>Recipient</th>
                            <th>Message</th>
                            <th width="80">Status</th>
                            <th width="80">Type</th>
                            <th width="60">Parts</th>
                            <th width="70">Cost</th>
                            <th width="80">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="logs-tbody">
                        <tr><td colspan="10" class="text-center">Loading logs...</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="logs-pagination" id="logs-pagination">
                <div class="pagination-info">
                    Showing <span id="showing-from">0</span>-<span id="showing-to">0</span> of <span id="total-records">0</span> records
                </div>
                <div class="pagination-controls" id="pagination-controls">
                    <!-- Pagination buttons will be inserted here -->
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Log Detail Modal -->
<div id="log-detail-modal" class="nbsms-modal" style="display:none;">
    <div class="nbsms-modal-content">
        <div class="nbsms-modal-header">
            <h2>SMS Log Details</h2>
            <span class="nbsms-modal-close">&times;</span>
        </div>
        <div class="nbsms-modal-body" id="log-detail-content">
            <!-- Details will be loaded here -->
        </div>
    </div>
</div>

<!-- Chart.js will be enqueued via WordPress -->
<!-- Load Chart.js from CDN -->

<script type="text/javascript">
jQuery(document).ready(function($) {
    
    let currentPage = 1;
    let activityChart = null;
    let typeChart = null;

    // Tab switching
    $('.nbsms-tab-btn').on('click', function() {
        const tab = $(this).data('tab');
        $('.nbsms-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.nbsms-tab-content').removeClass('active');
        $('#tab-' + tab).addClass('active');

        if (tab === 'analytics') {
            loadStatistics();
        } else if (tab === 'logs') {
            loadLogs();
        }
    });

    // Load statistics on page load
    loadStatistics();

    // Quick date filters
    $('.quick-filters button').on('click', function() {
        const days = $(this).data('days');
        const today = new Date();
        const fromDate = new Date(today.setDate(today.getDate() - days));
        
        $('#stats-date-from').val(fromDate.toISOString().split('T')[0]);
        $('#stats-date-to').val(new Date().toISOString().split('T')[0]);
        
        loadStatistics();
    });

    // Refresh stats
    $('#refresh-stats').on('click', function() {
        loadStatistics();
    });

    // Load statistics
    function loadStatistics() {
        const button = $('#refresh-stats');
        button.prop('disabled', true).find('.dashicons').addClass('spin');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nbsms_get_log_stats',
                nonce: '<?php echo wp_create_nonce('nbsms_ajax_nonce'); ?>',
                date_from: $('#stats-date-from').val(),
                date_to: $('#stats-date-to').val()
            },
            success: function(response) {
                if (response.success) {
                    displayStatistics(response.data);
                } else {
                    alert('Error loading statistics');
                }
            },
            complete: function() {
                button.prop('disabled', false').find('.dashicons').removeClass('spin');
            }
        });
    }

    // Display statistics
    function displayStatistics(stats) {
        // Update stat cards
        $('#stat-total').text(stats.total_sent || 0);
        $('#stat-successful').text(stats.successful || 0);
        $('#stat-failed').text(stats.failed || 0);
        $('#stat-pending').text(stats.pending || 0);
        $('#stat-sms-parts').text(stats.total_sms_parts || 0);
        $('#stat-total-cost').text('₦' + (stats.total_cost || 0).toLocaleString());
        
        $('#stat-success-rate').text(stats.success_rate + '% success rate');
        $('#stat-avg-cost').text('₦' + stats.avg_cost + ' per SMS');

        // Update activity chart
        updateActivityChart(stats.daily || []);

        // Update type chart
        updateTypeChart(stats.by_type || []);

        // Update top recipients
        updateTopRecipients(stats.top_recipients || []);
    }

    // Update activity chart
    function updateActivityChart(dailyData) {
        const ctx = document.getElementById('activity-chart');
        
        if (activityChart) {
            activityChart.destroy();
        }

        const labels = dailyData.map(d => d.date);
        const sentData = dailyData.map(d => parseInt(d.sent));
        const failedData = dailyData.map(d => parseInt(d.failed));

        activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Sent',
                        data: sentData,
                        borderColor: '#46b450',
                        backgroundColor: 'rgba(70, 180, 80, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Failed',
                        data: failedData,
                        borderColor: '#dc3232',
                        backgroundColor: 'rgba(220, 50, 50, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Update type chart
    function updateTypeChart(typeData) {
        const ctx = document.getElementById('type-chart');
        
        if (typeChart) {
            typeChart.destroy();
        }

        const labels = typeData.map(d => d.message_type);
        const counts = typeData.map(d => parseInt(d.count));
        const colors = ['#00a0d2', '#826eb4', '#ffb900'];

        typeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Update top recipients
    function updateTopRecipients(recipients) {
        const tbody = $('#top-recipients-tbody');
        tbody.empty();

        if (recipients.length === 0) {
            tbody.append('<tr><td colspan="4" class="text-center">No data available</td></tr>');
            return;
        }

        recipients.forEach(function(recipient) {
            const row = $('<tr>');
            row.append('<td>' + (recipient.recipient_name || '-') + '</td>');
            row.append('<td>' + recipient.recipient_phone + '</td>');
            row.append('<td>' + recipient.sms_count + '</td>');
            row.append('<td>₦' + parseFloat(recipient.total_cost).toLocaleString() + '</td>');
            tbody.append(row);
        });
    }

    // Load logs
    function loadLogs(page = 1) {
        currentPage = page;
        const tbody = $('#logs-tbody');
        tbody.html('<tr><td colspan="10" class="text-center">Loading logs...</td></tr>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nbsms_get_logs',
                nonce: '<?php echo wp_create_nonce('nbsms_ajax_nonce'); ?>',
                status: $('#filter-status').val(),
                type: $('#filter-type').val(),
                date_from: $('#filter-date-from').val(),
                date_to: $('#filter-date-to').val(),
                search: $('#filter-search').val(),
                per_page: $('#per-page').val(),
                page: page
            },
            success: function(response) {
                if (response.success) {
                    displayLogs(response.data);
                } else {
                    alert('Error loading logs');
                }
            }
        });
    }

    // Display logs
    function displayLogs(data) {
        const tbody = $('#logs-tbody');
        tbody.empty();

        if (data.logs.length === 0) {
            tbody.append('<tr><td colspan="10" class="text-center">No logs found</td></tr>');
            return;
        }

        data.logs.forEach(function(log) {
            const row = $('<tr>');
            
            // Checkbox
            row.append('<td><input type="checkbox" class="log-checkbox" value="' + log.id + '"></td>');
            
            // ID
            row.append('<td>' + log.id + '</td>');
            
            // Date
            const date = new Date(log.created_at);
            row.append('<td>' + date.toLocaleString() + '</td>');
            
            // Recipient
            let recipient = log.recipient_name || 'N/A';
            recipient += '<br><small>' + log.recipient_phone + '</small>';
            row.append('<td>' + recipient + '</td>');
            
            // Message
            const message = log.message.length > 50 ? log.message.substring(0, 50) + '...' : log.message;
            row.append('<td>' + message + '</td>');
            
            // Status
            let statusClass = '';
            if (log.status === 'sent') statusClass = 'status-sent';
            else if (log.status === 'failed') statusClass = 'status-failed';
            else if (log.status === 'pending') statusClass = 'status-pending';
            
            row.append('<td><span class="status-badge ' + statusClass + '">' + log.status + '</span></td>');
            
            // Type
            row.append('<td>' + log.message_type + '</td>');
            
            // Parts
            row.append('<td class="text-center">' + log.sms_count + '</td>');
            
            // Cost
            row.append('<td>₦' + parseFloat(log.cost).toFixed(2) + '</td>');
            
            // Actions
            row.append('<td><button class="button button-small view-log" data-id="' + log.id + '">View</button> <button class="button button-small delete-log" data-id="' + log.id + '">Delete</button></td>');
            
            tbody.append(row);
        });

        // Update pagination
        updatePagination(data);
    }

    // Update pagination
    function updatePagination(data) {
        const from = ((data.current_page - 1) * data.per_page) + 1;
        const to = Math.min(data.current_page * data.per_page, data.total_records);
        
        $('#showing-from').text(from);
        $('#showing-to').text(to);
        $('#total-records').text(data.total_records);

        const controls = $('#pagination-controls');
        controls.empty();

        if (data.total_pages <= 1) return;

        // Previous button
        if (data.current_page > 1) {
            controls.append('<button class="button page-btn" data-page="' + (data.current_page - 1) + '">« Previous</button>');
        }

        // Page numbers
        for (let i = 1; i <= data.total_pages; i++) {
            if (i === 1 || i === data.total_pages || (i >= data.current_page - 2 && i <= data.current_page + 2)) {
                const active = i === data.current_page ? 'active' : '';
                controls.append('<button class="button page-btn ' + active + '" data-page="' + i + '">' + i + '</button>');
            } else if (i === data.current_page - 3 || i === data.current_page + 3) {
                controls.append('<span class="page-dots">...</span>');
            }
        }

        // Next button
        if (data.current_page < data.total_pages) {
            controls.append('<button class="button page-btn" data-page="' + (data.current_page + 1) + '">Next »</button>');
        }
    }

    // Apply filters
    $('#apply-filters').on('click', function() {
        loadLogs(1);
    });

    // Clear filters
    $('#clear-filters').on('click', function() {
        $('#filter-status').val('');
        $('#filter-type').val('');
        $('#filter-date-from').val('');
        $('#filter-date-to').val('');
        $('#filter-search').val('');
        loadLogs(1);
    });

    // Per page change
    $('#per-page').on('change', function() {
        loadLogs(1);
    });

    // Pagination click
    $(document).on('click', '.page-btn', function() {
        const page = $(this).data('page');
        loadLogs(page);
    });

    // Select all
    $('#select-all-logs').on('change', function() {
        $('.log-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Bulk actions
    $('#apply-bulk-action').on('click', function() {
        const action = $('#bulk-action').val();
        const selected = $('.log-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (!action) {
            alert('Please select an action');
            return;
        }

        if (selected.length === 0) {
            alert('Please select logs');
            return;
        }

        if (action === 'delete') {
            if (!confirm('Are you sure you want to delete ' + selected.length + ' log(s)?')) {
                return;
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nbsms_bulk_delete_logs',
                    nonce: '<?php echo wp_create_nonce('nbsms_ajax_nonce'); ?>',
                    log_ids: selected
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        loadLogs(currentPage);
                    } else {
                        alert('Error: ' + response.data);
                    }
                }
            });
        }
    });

    // Export logs
    $('#export-logs').on('click', function() {
        const button = $(this);
        button.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Exporting...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nbsms_export_logs',
                nonce: '<?php echo wp_create_nonce('nbsms_ajax_nonce'); ?>',
                status: $('#filter-status').val(),
                type: $('#filter-type').val(),
                date_from: $('#filter-date-from').val(),
                date_to: $('#filter-date-to').val(),
                search: $('#filter-search').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Exported ' + response.data.count + ' logs successfully!');
                    window.location.href = response.data.url;
                } else {
                    alert('Error exporting logs');
                }
            },
            complete: function() {
                button.prop('disabled', false).html('<span class="dashicons dashicons-download"></span> Export to CSV');
            }
        });
    });

    // View log detail (placeholder)
    $(document).on('click', '.view-log', function() {
        const logId = $(this).data('id');
        alert('Log detail view - ID: ' + logId);
        // Implement modal with full log details
    });

    // Delete single log
    $(document).on('click', '.delete-log', function() {
        if (!confirm('Are you sure you want to delete this log?')) {
            return;
        }

        const logId = $(this).data('id');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nbsms_delete_log',
                nonce: '<?php echo wp_create_nonce('nbsms_ajax_nonce'); ?>',
                log_id: logId
            },
            success: function(response) {
                if (response.success) {
                    loadLogs(currentPage);
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Load logs when switching to logs tab
    if ($('.nbsms-tab-btn[data-tab="logs"]').hasClass('active')) {
        loadLogs(1);
    }

});
</script>

<style>
/* Add styles from previous implementation */
.nbsms-tabs {
    display: flex;
    gap: 10px;
    margin: 20px 0;
    border-bottom: 2px solid #ddd;
}

.nbsms-tab-btn {
    padding: 12px 24px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nbsms-tab-btn:hover {
    background: #f0f0f1;
}

.nbsms-tab-btn.active {
    border-bottom-color: #900;
    color: #900;
    font-weight: bold;
}

.nbsms-tab-content {
    display: none;
}

.nbsms-tab-content.active {
    display: block;
}

.nbsms-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px;
    margin-bottom: 20px;
}

.nbsms-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    gap: 15px;
    align-items: center;
}

.stat-card.highlight {
    border: 2px solid #900;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon .dashicons {
    font-size: 32px;
    color: #fff;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #333;
    line-height: 1;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #666;
}

.stat-meta {
    font-size: 12px;
    color: #999;
    margin-top: 5px;
}

.date-filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.date-inputs {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quick-filters {
    display: flex;
    gap: 5px;
}

.nbsms-charts-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin: 20px 0;
}

.chart-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 20px;
}

.chart-card h3 {
    margin-top: 0;
}

.chart-card canvas {
    max-height: 300px;
}

.logs-filters {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-group label {
    font-weight: 500;
    white-space: nowrap;
}

.filter-actions {
    margin-left: auto;
}

.logs-table-container {
    overflow-x: auto;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-sent {
    background: #d4edda;
    color: #155724;
}

.status-failed {
    background: #f8d7da;
    color: #721c24;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.logs-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}

.pagination-controls {
    display: flex;
    gap: 5px;
}

.page-btn {
    min-width: 40px;
}

.page-btn.active {
    background: #900;
    color: #fff;
    border-color: #900;
}

.page-dots {
    padding: 0 10px;
    line-height: 28px;
}

.text-center {
    text-center;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spin {
    animation: spin 1s linear infinite;
}

@media (max-width: 782px) {
    .nbsms-charts-row {
        grid-template-columns: 1fr;
    }
    
    .filter-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
<?php
