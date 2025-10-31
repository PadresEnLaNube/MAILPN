(function($) {
    'use strict';

    window.MAILPN_Statistics = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $(document).on('click', '#mailpn-statistics-button', function(e) {
                e.preventDefault();
                MAILPN_Statistics.loadStatistics();
            });
        },

        loadStatistics: function() {
            
            // Check if MAILPN_Popups is available
            if (typeof MAILPN_Popups === 'undefined') {
                console.error('MAILPN_Statistics: MAILPN_Popups is not defined');
                return;
            }
            
            // Get current filters from URL
            var urlParams = new URLSearchParams(window.location.search);
            var filters = {
                recipient_filter: urlParams.get('mailpn_recipient_filter') || '',
                type_filter: urlParams.get('mailpn_type_filter') || '',
                template_filter: urlParams.get('mailpn_template_filter') || ''
            };

            // Show popup with loading state
            MAILPN_Popups.open('mailpn-statistics-popup');

            // Make AJAX request
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'mailpn_get_statistics',
                    nonce: mailpn_statistics_ajax.nonce,
                    recipient_filter: filters.recipient_filter,
                    type_filter: filters.type_filter,
                    template_filter: filters.template_filter
                },
                success: function(response) {
                    if (response.success) {
                        MAILPN_Statistics.renderStatistics(response.data);
                    } else {
                        MAILPN_Statistics.showError('Error loading statistics');
                    }
                },
                error: function() {
                    MAILPN_Statistics.showError('Error loading statistics');
                }
            });
        },

        renderStatistics: function(data) {
            var content = $('#mailpn-statistics-content');
            
            // Create statistics HTML
            var html = '<div class="mailpn-statistics-grid">';
            
            // Summary cards
            html += '<div class="mailpn-statistics-summary">';
            html += '<div class="mailpn-stat-card">';
            html += '<div class="mailpn-stat-card-icon"><i class="material-icons-outlined">email</i></div>';
            html += '<div class="mailpn-stat-card-content">';
            html += '<div class="mailpn-stat-card-number">' + data.total_emails + '</div>';
            html += '<div class="mailpn-stat-card-label">' + mailpn_statistics_ajax.i18n.total_emails + '</div>';
            html += '</div></div>';
            
            html += '<div class="mailpn-stat-card">';
            html += '<div class="mailpn-stat-card-icon"><i class="material-icons-outlined">visibility</i></div>';
            html += '<div class="mailpn-stat-card-content">';
            html += '<div class="mailpn-stat-card-number">' + data.opened_emails + '</div>';
            html += '<div class="mailpn-stat-card-label">' + mailpn_statistics_ajax.i18n.opened_emails + '</div>';
            html += '</div></div>';
            
            html += '<div class="mailpn-stat-card">';
            html += '<div class="mailpn-stat-card-icon"><i class="material-icons-outlined">trending_up</i></div>';
            html += '<div class="mailpn-stat-card-content">';
            html += '<div class="mailpn-stat-card-number">' + data.open_rate + '%</div>';
            html += '<div class="mailpn-stat-card-label">' + mailpn_statistics_ajax.i18n.open_rate + '</div>';
            html += '</div></div>';
            
            html += '<div class="mailpn-stat-card">';
            html += '<div class="mailpn-stat-card-icon"><i class="material-icons-outlined">link</i></div>';
            html += '<div class="mailpn-stat-card-content">';
            html += '<div class="mailpn-stat-card-number">' + data.total_clicks + '</div>';
            html += '<div class="mailpn-stat-card-label">' + mailpn_statistics_ajax.i18n.total_clicks + '</div>';
            html += '</div></div>';
            html += '</div>';
            
            // Charts section
            html += '<div class="mailpn-statistics-charts">';
            
            // Sent vs Opened chart
            if (Object.keys(data.sent_by_date).length > 0) {
                html += '<div class="mailpn-chart-container">';
                html += '<h4>' + mailpn_statistics_ajax.i18n.sent_vs_opened + '</h4>';
                html += '<canvas id="mailpn-sent-opened-chart" width="400" height="200"></canvas>';
                html += '</div>';
            }
            
            // Clicks by URL chart
            if (Object.keys(data.clicks_by_url).length > 0) {
                html += '<div class="mailpn-chart-container">';
                html += '<h4>' + mailpn_statistics_ajax.i18n.clicks_by_url + '</h4>';
                html += '<canvas id="mailpn-clicks-chart" width="400" height="200"></canvas>';
                html += '</div>';
            }
            
            html += '</div>';
            
            // Clicks table
            if (Object.keys(data.clicks_by_url).length > 0) {
                html += '<div class="mailpn-clicks-table">';
                html += '<h4>' + mailpn_statistics_ajax.i18n.clicks_details + '</h4>';
                html += '<table class="widefat">';
                html += '<thead><tr><th>' + mailpn_statistics_ajax.i18n.url + '</th><th>' + mailpn_statistics_ajax.i18n.clicks + '</th></tr></thead>';
                html += '<tbody>';
                
                var sortedUrls = Object.keys(data.clicks_by_url).sort(function(a, b) {
                    return data.clicks_by_url[b] - data.clicks_by_url[a];
                });
                
                sortedUrls.forEach(function(url) {
                    html += '<tr>';
                    html += '<td>' + (url.length > 50 ? url.substring(0, 50) + '...' : url) + '</td>';
                    html += '<td>' + data.clicks_by_url[url] + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                html += '</div>';
            }
            
            html += '</div>';
            
            content.html(html);
            
            // Render charts
            this.renderCharts(data);
        },

        renderCharts: function(data) {
            // Sent vs Opened chart
            if (document.getElementById('mailpn-sent-opened-chart')) {
                var sentOpenedCtx = document.getElementById('mailpn-sent-opened-chart').getContext('2d');
                
                var sentData = [];
                var openedData = [];
                var labels = [];
                
                // Combine sent and opened dates
                var allDates = new Set([
                    ...Object.keys(data.sent_by_date),
                    ...Object.keys(data.opened_by_date)
                ]);
                
                allDates.forEach(function(date) {
                    labels.push(date);
                    sentData.push(data.sent_by_date[date] || 0);
                    openedData.push(data.opened_by_date[date] || 0);
                });
                
                new Chart(sentOpenedCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: mailpn_statistics_ajax.i18n.sent_emails,
                            data: sentData,
                            borderColor: '#0073aa',
                            backgroundColor: 'rgba(0, 115, 170, 0.1)',
                            tension: 0.1
                        }, {
                            label: mailpn_statistics_ajax.i18n.opened_emails,
                            data: openedData,
                            borderColor: '#46b450',
                            backgroundColor: 'rgba(70, 180, 80, 0.1)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
            
            // Clicks by URL chart
            if (document.getElementById('mailpn-clicks-chart')) {
                var clicksCtx = document.getElementById('mailpn-clicks-chart').getContext('2d');
                
                var urls = Object.keys(data.clicks_by_url);
                var clickCounts = urls.map(function(url) {
                    return data.clicks_by_url[url];
                });
                
                // Truncate long URLs for display
                var displayUrls = urls.map(function(url) {
                    return url.length > 30 ? url.substring(0, 30) + '...' : url;
                });
                
                new Chart(clicksCtx, {
                    type: 'bar',
                    data: {
                        labels: displayUrls,
                        datasets: [{
                            label: mailpn_statistics_ajax.i18n.clicks,
                            data: clickCounts,
                            backgroundColor: '#0073aa',
                            borderColor: '#005a87',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        },

        showError: function(message) {
            var content = $('#mailpn-statistics-content');
            content.html('<div class="mailpn-error">' + message + '</div>');
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        MAILPN_Statistics.init();
    });

})(jQuery);
