/**
 * Dashboard JavaScript functionality
 * 
 * Handles popup interactions and dashboard interactions using MAILPN_Popups
 */
jQuery(document).ready(function($) {
    'use strict';

    // Dashboard functionality
    const dashboard = {
        init: function() {
            this.bindEvents();
            this.initializeTooltips();
        },

        bindEvents: function() {
            // Open popups when clicking on stat cards
            $('#recent-sent-emails-card').on('click', function() {
                dashboard.openEmailsPopup('recent-sent-emails-popup');
            });

            $('#pending-scheduled-emails-card').on('click', function() {
                dashboard.openEmailsPopup('pending-scheduled-emails-popup');
            });

            // Refresh stats periodically (every 5 minutes)
            setInterval(function() {
                dashboard.refreshStats();
            }, 300000); // 5 minutes

            // Add click effects to stat cards
            $('.mailpn-stat-card').on('mousedown', function() {
                $(this).addClass('mailpn-stat-card-pressed');
            }).on('mouseup mouseleave', function() {
                $(this).removeClass('mailpn-stat-card-pressed');
            });

            // Add hover effects
            $('.mailpn-stat-card').hover(
                function() {
                    $(this).addClass('mailpn-stat-card-hover');
                },
                function() {
                    $(this).removeClass('mailpn-stat-card-hover');
                }
            );
        },

        openEmailsPopup: function(popupId) {
            // Show loading state first
            const popup = $('#' + popupId);
            if (popup.length) {
                const popupBody = popup.find('.mailpn-popup-body');
                if (popupBody.length) {
                    // Use the basic loader from MAILPN_Data
                    popupBody.html(MAILPN_Data.mailpn_loader);
                }
            }

            // Open popup with loading state
            MAILPN_Popups.open(popupId, {
                beforeShow: function() {
                    // Popup is already showing loading state
                }
            });

            // Simulate loading time and then show content
            setTimeout(function() {
                dashboard.loadPopupContent(popupId);
            }, 500);
        },

        loadPopupContent: function(popupId) {
            const popup = $('#' + popupId);
            if (popup.length) {
                const popupBody = popup.find('.mailpn-popup-body');
                if (popupBody.length) {
                    // Load the appropriate content based on popup ID
                    if (popupId === 'recent-sent-emails-popup') {
                        dashboard.loadRecentSentEmails(popupBody);
                    } else if (popupId === 'pending-scheduled-emails-popup') {
                        dashboard.loadPendingScheduledEmails(popupBody);
                    }
                }
            }
        },

        loadRecentSentEmails: function(container) {
            // Show loading state
            container.html(MAILPN_Data.mailpn_loader);
            
            // Simulate AJAX call - in real implementation, this would be an AJAX request
            setTimeout(function() {
                // For now, we'll just show the content that was already rendered
                // In a real implementation, you would make an AJAX call here
                container.html($('#recent-sent-emails-list-content').html());
                dashboard.initializeTableFunctionality(container);
            }, 1000);
        },

        loadPendingScheduledEmails: function(container) {
            // Show loading state
            container.html(MAILPN_Data.mailpn_loader);
            
            // Simulate AJAX call - in real implementation, this would be an AJAX request
            setTimeout(function() {
                // For now, we'll just show the content that was already rendered
                // In a real implementation, you would make an AJAX call here
                container.html($('#pending-scheduled-emails-list-content').html());
                dashboard.initializeTableFunctionality(container);
            }, 1000);
        },

        initializeTableFunctionality: function(container) {
            // Add search functionality
            const table = container.find('.mailpn-emails-table');
            if (table.length) {
                const searchInput = $('<input type="text" class="mailpn-search-input" placeholder="' + (mailpn_dashboard.search_placeholder || 'Search emails...') + '">');
                searchInput.insertBefore(table);
                
                searchInput.on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    dashboard.filterTable(table, searchTerm);
                });
            }

            // Add sorting functionality
            container.find('.mailpn-emails-table th').on('click', function() {
                dashboard.sortTable($(this));
            });
        },

        filterTable: function(table, searchTerm) {
            const rows = table.find('tbody tr');
            
            rows.each(function() {
                const row = $(this);
                const text = row.text().toLowerCase();
                const isVisible = text.includes(searchTerm);
                row.toggle(isVisible);
            });
        },

        sortTable: function(header) {
            const table = header.closest('table');
            const tbody = table.find('tbody');
            const rows = tbody.find('tr').toArray();
            const columnIndex = header.index();
            const isAscending = header.hasClass('mailpn-sort-asc');

            // Remove existing sort classes
            table.find('th').removeClass('mailpn-sort-asc mailpn-sort-desc');

            // Sort rows
            rows.sort(function(a, b) {
                const aValue = $(a).find('td').eq(columnIndex).text().trim();
                const bValue = $(b).find('td').eq(columnIndex).text().trim();
                
                // Try to parse as date first
                const aDate = new Date(aValue);
                const bDate = new Date(bValue);
                
                if (!isNaN(aDate) && !isNaN(bDate)) {
                    return isAscending ? bDate - aDate : aDate - bDate;
                }
                
                // Fall back to string comparison
                if (isAscending) {
                    return bValue.localeCompare(aValue);
                } else {
                    return aValue.localeCompare(bValue);
                }
            });

            // Reorder rows
            tbody.empty().append(rows);

            // Add sort class
            header.addClass(isAscending ? 'mailpn-sort-desc' : 'mailpn-sort-asc');
        },

        refreshStats: function() {
            // This could be used to refresh stats via AJAX if needed
        },

        initializeTooltips: function() {
            // Add tooltips to stat cards
            $('.mailpn-stat-card').each(function() {
                const card = $(this);
                const title = card.find('h3').text();
                const number = card.find('.mailpn-stat-number').text();
                const description = card.find('p').text();
                
                card.attr('title', `${title}: ${number} - ${description}`);
            });
        },

        // Utility function to format numbers
        formatNumber: function(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toString();
        },

        // Utility function to animate number changes
        animateNumber: function(element, start, end, duration) {
            const startTime = performance.now();
            const difference = end - start;
            
            function updateNumber(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function (ease-out)
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = start + (difference * easeOut);
                
                element.text(Math.floor(current));
                
                if (progress < 1) {
                    requestAnimationFrame(updateNumber);
                }
            }
            
            requestAnimationFrame(updateNumber);
        }
    };

    // Initialize dashboard
    dashboard.init();

    // Expose functions globally for potential AJAX calls
    window.MAILPN_Dashboard = {
        dashboard: dashboard
    };

    // Add CSS for additional interactions
    const additionalCSS = `
        .mailpn-stat-card-pressed {
            transform: translateY(-2px) scale(0.98) !important;
        }
        
        .mailpn-stat-card-hover {
            transform: translateY(-3px) !important;
        }
        
        .mailpn-search-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .mailpn-search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
        }
        
        .mailpn-emails-table th {
            cursor: pointer;
            user-select: none;
            position: relative;
        }
        
        .mailpn-emails-table th:hover {
            background-color: #e9ecef;
        }
        
        .mailpn-emails-table th::after {
            content: '↕';
            position: absolute;
            right: 8px;
            opacity: 0.3;
        }
        
        .mailpn-emails-table th.mailpn-sort-asc::after {
            content: '↑';
            opacity: 1;
        }
        
        .mailpn-emails-table th.mailpn-sort-desc::after {
            content: '↓';
            opacity: 1;
        }
    `;

    // Inject CSS
    const style = document.createElement('style');
    style.textContent = additionalCSS;
    document.head.appendChild(style);
}); 