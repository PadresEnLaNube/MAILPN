/**
 * QR Scanner Web Application JavaScript for MAILPN Plugin
 *
 * @package MAILPN
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // QR Scanner Application
    var MAILPNScanner = {
        video: null,
        canvas: null,
        ctx: null,
        stream: null,
        isScanning: false,
        currentFacingMode: 'environment',
        scanInterval: null,
        currentOrderId: null,
        usePopup: false,

        init: function() {
            this.video = document.getElementById('mailpn-camera-video');
            this.canvas = document.createElement('canvas');
            this.ctx = this.canvas.getContext('2d');
            
            this.bindEvents();
            this.loadQRCodeLibrary();
            this.loadStats();
            this.loadHistory();
        },

        bindEvents: function() {
            // Camera controls
            $('#start-camera').on('click', function(e) {
                e.preventDefault();
                MAILPNScanner.startCamera();
            });

            $('#stop-camera').on('click', function(e) {
                e.preventDefault();
                MAILPNScanner.stopCamera();
            });

            $('#switch-camera').on('click', function(e) {
                e.preventDefault();
                MAILPNScanner.switchCamera();
            });

            // Manual validation
            $('#validate-manual').on('click', function(e) {
                e.preventDefault();
                MAILPNScanner.validateManual();
            });

            // Enter key for manual input
            $('#manual-qr-input').on('keypress', function(e) {
                if (e.key === 'Enter' && e.ctrlKey) {
                    e.preventDefault();
                    MAILPNScanner.validateManual();
                }
            });

            // Mark as delivered
            $(document).on('click', '.mailpn-mark-delivered-btn', function(e) {
                e.preventDefault();
                var orderId = $(this).data('order-id');
                MAILPNScanner.markAsDelivered(orderId);
            });

            // New scan button
            $(document).on('click', '.mailpn-new-scan-btn', function(e) {
                e.preventDefault();
                MAILPNScanner.resetScanner();
            });

            // Window resize
            $(window).on('resize', function() {
                MAILPNScanner.handleResize();
            });
        },

        loadQRCodeLibrary: function() {
            // Load jsQR library if not already loaded
            if (typeof jsQR === 'undefined') {
                var script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
                script.onload = function() {
                    console.log('jsQR library loaded');
                };
                document.head.appendChild(script);
            }
        },

        startCamera: function() {
            var self = this;
            
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                this.showError(mailpn_scanner_ajax.strings.camera_error);
                return;
            }

            var constraints = {
                video: {
                    facingMode: this.currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(stream) {
                    self.stream = stream;
                    self.video.srcObject = stream;
                    self.isScanning = true;
                    
                    $('#start-camera').hide();
                    $('#stop-camera').show();
                    
                    // Start scanning
                    self.startScanning();
                })
                .catch(function(error) {
                    console.error('Camera access error:', error);
                    self.showError(mailpn_scanner_ajax.strings.camera_error);
                });
        },

        stopCamera: function() {
            if (this.stream) {
                this.stream.getTracks().forEach(function(track) {
                    track.stop();
                });
                this.stream = null;
            }
            
            this.isScanning = false;
            this.video.srcObject = null;
            
            if (this.scanInterval) {
                clearInterval(this.scanInterval);
                this.scanInterval = null;
            }
            
            $('#start-camera').show();
            $('#stop-camera').hide();
        },

        switchCamera: function() {
            this.currentFacingMode = this.currentFacingMode === 'environment' ? 'user' : 'environment';
            
            if (this.isScanning) {
                this.stopCamera();
                setTimeout(function() {
                    MAILPNScanner.startCamera();
                }, 500);
            }
        },

        startScanning: function() {
            var self = this;
            
            this.scanInterval = setInterval(function() {
                if (self.video.readyState === self.video.HAVE_ENOUGH_DATA) {
                    self.canvas.height = self.video.videoHeight;
                    self.canvas.width = self.video.videoWidth;
                    self.ctx.drawImage(self.video, 0, 0, self.canvas.width, self.canvas.height);
                    
                    var imageData = self.ctx.getImageData(0, 0, self.canvas.width, self.canvas.height);
                    
                    if (typeof jsQR !== 'undefined') {
                        var code = jsQR(imageData.data, imageData.width, imageData.height);
                        
                        if (code) {
                            self.stopCamera();
                            self.validateQRCode(code.data);
                        }
                    }
                }
            }, 100);
        },

        validateManual: function() {
            var qrData = $('#manual-qr-input').val().trim();
            
            if (!qrData) {
                this.showError('Please enter QR code data');
                return;
            }
            
            this.validateQRCode(qrData);
        },

        validateQRCode: function(qrData) {
            var self = this;
            
            this.showLoading();
            
            $.ajax({
                url: mailpn_scanner_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailpn_scanner_validate_qr',
                    qr_data: qrData,
                    nonce: mailpn_scanner_ajax.nonce
                },
                success: function(response) {
                    self.hideLoading();
                    
                    if (response.success) {
                        self.displayResult(response.data);
                        self.addToHistory(response.data);
                        self.loadStats();
                    } else {
                        self.showError(response.data.message || mailpn_scanner_ajax.strings.validation_error);
                    }
                },
                error: function() {
                    self.hideLoading();
                    self.showError(mailpn_scanner_ajax.strings.network_error);
                }
            });
        },

        displayResult: function(data) {
            var resultContainer = $('#mailpn-scanner-results');
            var resultTitle = $('#result-title');
            var resultStatus = $('#result-status');
            var resultContent = $('#result-content');
            var resultActions = $('#result-actions');
            
            var orderInfo = data.order_info;
            this.currentOrderId = orderInfo.order_id;
            
            // Get translated strings
            var qrValidatedText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.qr_validated_successfully || 'QR Code Validated Successfully' : 'QR Code Validated Successfully';
            var orderInfoText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.order_information || 'Order Information' : 'Order Information';
            var validationStatusText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.validation_status || 'Validation Status' : 'Validation Status';
            var deliveryStatusText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.delivery_status || 'Delivery Status' : 'Delivery Status';
            var orderIdText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.order_id || 'Order ID' : 'Order ID';
            var customerText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.customer || 'Customer' : 'Customer';
            var totalText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.total || 'Total' : 'Total';
            var orderDateText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.order_date || 'Order Date' : 'Order Date';
            var statusText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.status || 'Status' : 'Status';
            var validatedText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.validated || 'Validated' : 'Validated';
            var deliveredText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.delivered || 'Delivered' : 'Delivered';
            var notDeliveredText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.not_delivered || 'Not Delivered' : 'Not Delivered';
            var newScanText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.new_scan || 'New Scan' : 'New Scan';
            var markDeliveredText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.mark_as_delivered || 'Mark as Delivered' : 'Mark as Delivered';
            
            // Set title and status
            resultTitle.text(qrValidatedText);
            resultStatus.text('Valid').removeClass('invalid').addClass('valid');
            
            // Build content
            var contentHtml = `
                <div class="mailpn-result-info">
                    <div>
                        <h4>${orderInfoText}</h4>
                        <p><strong>${orderIdText}:</strong> #${orderInfo.order_id}</p>
                        <p><strong>${customerText}:</strong> ${orderInfo.customer_name}</p>
                        <p><strong>${totalText}:</strong> ${orderInfo.order_total}</p>
                        <p><strong>${orderDateText}:</strong> ${orderInfo.order_date}</p>
                    </div>
                    <div>
                        <h4>${validationStatusText}</h4>
                        <p><strong>${statusText}:</strong> <span class="mailpn-result-status valid">${validatedText}</span></p>
                        <p><strong>${validatedText}:</strong> ${orderInfo.validation_date}</p>
                        <h4>${deliveryStatusText}</h4>
                        <p><strong>${statusText}:</strong> 
                            <span class="mailpn-result-status ${orderInfo.ticket_delivered === '1' ? 'valid' : 'invalid'}">
                                ${orderInfo.ticket_delivered === '1' ? deliveredText : notDeliveredText}
                            </span>
                        </p>
                        ${orderInfo.ticket_delivered === '1' ? `<p><strong>${deliveredText}:</strong> ${orderInfo.ticket_delivery_date}</p>` : ''}
                    </div>
                </div>
            `;
            
            resultContent.html(contentHtml);
            
            // Build actions
            var actionsHtml = `
                <button type="button" class="mailpn-btn mailpn-btn-secondary mailpn-new-scan-btn">
                    ${newScanText}
                </button>
            `;
            
            if (orderInfo.ticket_delivered !== '1') {
                actionsHtml += `
                    <button type="button" class="mailpn-btn mailpn-btn-success mailpn-mark-delivered-btn" data-order-id="${orderInfo.order_id}">
                        <span class="mailpn-btn-icon">✅</span>
                        ${markDeliveredText}
                    </button>
                `;
            }
            
            resultActions.html(actionsHtml);
            
            // Show results
            resultContainer.show();
            resultContainer[0].scrollIntoView({ behavior: 'smooth' });
        },

        markAsDelivered: function(orderId) {
            var self = this;
            
            this.showLoading();
            
            $.ajax({
                url: mailpn_scanner_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailpn_scanner_mark_delivered',
                    order_id: orderId,
                    nonce: mailpn_scanner_ajax.nonce
                },
                success: function(response) {
                    self.hideLoading();
                    
                    if (response.success) {
                        self.showSuccess(mailpn_scanner_ajax.strings.delivery_success);
                        self.loadStats();
                        self.loadHistory();
                        
                        // Update the result display
                        setTimeout(function() {
                            self.resetScanner();
                        }, 2000);
                    } else {
                        self.showError(mailpn_scanner_ajax.strings.delivery_error);
                    }
                },
                error: function() {
                    self.hideLoading();
                    self.showError(mailpn_scanner_ajax.strings.network_error);
                }
            });
        },

        loadStats: function() {
            var self = this;
            
            $.ajax({
                url: mailpn_scanner_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailpn_scanner_get_stats',
                    nonce: mailpn_scanner_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.updateStats(response.data);
                    }
                },
                error: function() {
                    console.error('Error loading stats');
                }
            });
        },

        updateStats: function(stats) {
            $('#total-qr-codes').text(stats.total_qr_codes);
            $('#validated-qr-codes').text(stats.validated_qr_codes);
            $('#delivered-tickets').text(stats.delivered_tickets);
            $('#pending-validation').text(stats.pending_validation);
        },

        loadHistory: function() {
            var self = this;
            
            $.ajax({
                url: mailpn_scanner_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailpn_scanner_get_history',
                    nonce: mailpn_scanner_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.updateHistory(response.data);
                    }
                },
                error: function() {
                    console.error('Error loading history');
                }
            });
        },

        updateHistory: function(history) {
            var historyList = $('#mailpn-history-list');
            var historyHtml = '';
            
            if (history.length === 0) {
                historyHtml = '<p style="text-align: center; color: #666; padding: 20px;">No recent scans found.</p>';
            } else {
                history.forEach(function(scan) {
                    var validationStatus = scan.validated ? 'valid' : 'pending';
                    var deliveryStatus = scan.ticket_delivered ? 'delivered' : 'not-delivered';
                    var deliveryText = scan.ticket_delivered ? 'Delivered' : (mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.not_delivered || 'Not Delivered' : 'Not Delivered');
                    var pendingText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.pending || 'Pending' : 'Pending';
                    var validatedText = mailpn_scanner_ajax && mailpn_scanner_ajax.strings ? mailpn_scanner_ajax.strings.validated || 'Validated' : 'Validated';
                    
                    historyHtml += `
                        <div class="mailpn-history-item mailpn-width-100-percent mailpn-display-table">
                            <div class="mailpn-history-info mailpn-display-table-cell mailpn-width-50-percent mailpn-tablet-width-100-percent">
                                <span class="mailpn-history-order">Order #${scan.order_id}</span>
                                <span class="mailpn-history-customer">${scan.customer_name}</span>
                                <span class="mailpn-history-date">${scan.order_date}</span>
                            </div>
                            <div class="mailpn-history-status mailpn-display-table-cell mailpn-width-50-percent mailpn-tablet-width-100-percent">
                                <span class="mailpn-history-validated ${validationStatus}">
                                    ${scan.validated ? validatedText : pendingText}
                                </span>
                                <span class="mailpn-history-delivered ${deliveryStatus}">
                                    ${deliveryText}
                                </span>
                            </div>
                        </div>
                    `;
                });
            }
            
            historyList.html(historyHtml);
        },

        addToHistory: function(data) {
            // This would typically be handled server-side
            // For now, we just reload the history
            this.loadHistory();
        },

        resetScanner: function() {
            $('#mailpn-scanner-results').hide();
            $('#manual-qr-input').val('');
            this.currentOrderId = null;
            
            // Scroll to top
            $('html, body').animate({ scrollTop: 0 }, 300);
        },

        showLoading: function() {
            $('#mailpn-loading-overlay').show();
        },

        hideLoading: function() {
            $('#mailpn-loading-overlay').hide();
        },

        showError: function(message) {
            // Use the existing MAILPN message system
            if (typeof mailpn_get_main_message !== 'undefined') {
                mailpn_get_main_message(message);
            } else {
                // Fallback to original method if mailpn_get_main_message is not available
                var errorHtml = `
                    <div class="mailpn-error-message">
                        ${message}
                    </div>
                `;
                
                $('.mailpn-qr-scanner-app').prepend(errorHtml);
                
                setTimeout(function() {
                    $('.mailpn-error-message').fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        },

        showSuccess: function(message) {
            // Use the existing MAILPN message system
            if (typeof mailpn_get_main_message !== 'undefined') {
                mailpn_get_main_message(message);
            } else {
                // Fallback to original method if mailpn_get_main_message is not available
                var successHtml = `
                    <div class="mailpn-success-message">
                        ${message}
                    </div>
                `;
                
                $('.mailpn-qr-scanner-app').prepend(successHtml);
                
                setTimeout(function() {
                    $('.mailpn-success-message').fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        },

        handleResize: function() {
            // Handle responsive layout changes
            if (this.video && this.video.videoWidth) {
                var aspectRatio = this.video.videoWidth / this.video.videoHeight;
                var containerWidth = $('.mailpn-camera-container').width();
                var newHeight = containerWidth / aspectRatio;
                
                $('.mailpn-camera-container').height(newHeight);
            }
        },

        // Utility functions
        formatDate: function(dateString) {
            var date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        },

        formatCurrency: function(amount) {
            return parseFloat(amount).toFixed(2);
        },

        // Popup integration functions
        showQRCodePopup: function(qrData, orderInfo) {
            var popupHtml = `
                <div id="mailpn-qr-code-popup" class="mailpn-popup mailpn-scanner-popup" style="display: none;">
                    <div class="mailpn-popup-content">
                        <div class="mailpn-qr-code-display">
                            <h3>QR Code Details</h3>
                            <div class="mailpn-result-info">
                                <div>
                                    <h4>Order Information</h4>
                                    <p><strong>Order ID:</strong> #${orderInfo.order_id}</p>
                                    <p><strong>Customer:</strong> ${orderInfo.customer_name}</p>
                                    <p><strong>Total:</strong> ${orderInfo.order_total}</p>
                                    <p><strong>Order Date:</strong> ${orderInfo.order_date}</p>
                                </div>
                                <div>
                                    <h4>QR Code Data</h4>
                                    <p><strong>Status:</strong> <span class="mailpn-result-status valid">Valid</span></p>
                                    <p><strong>Validated:</strong> ${orderInfo.validation_date || 'Not validated'}</p>
                                    <p><strong>Delivery:</strong> 
                                        <span class="mailpn-result-status ${orderInfo.ticket_delivered === '1' ? 'valid' : 'invalid'}">
                                            ${orderInfo.ticket_delivered === '1' ? 'Delivered' : 'Not Delivered'}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="mailpn-qr-code-actions">
                                <button type="button" class="mailpn-btn mailpn-btn-primary" onclick="window.print()">
                                    <span class="mailpn-btn-icon">🖨️</span>
                                    Print
                                </button>
                                ${orderInfo.ticket_delivered !== '1' ? `
                                    <button type="button" class="mailpn-btn mailpn-btn-success mailpn-mark-delivered-btn" data-order-id="${orderInfo.order_id}">
                                        <span class="mailpn-btn-icon">✅</span>
                                        Mark as Delivered
                                    </button>
                                ` : ''}
                                <button type="button" class="mailpn-btn mailpn-btn-secondary" onclick="MAILPNScanner.closeQRCodePopup()">
                                    <span class="mailpn-btn-icon">✕</span>
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing popup if any
            $('#mailpn-qr-code-popup').remove();
            
            // Add popup to body
            $('body').append(popupHtml);
            
            // Show popup using MAILPN popup system
            if (typeof MAILPN_Popups !== 'undefined') {
                MAILPN_Popups.open('#mailpn-qr-code-popup');
            } else {
                // Fallback if MAILPN popup system is not available
                $('#mailpn-qr-code-popup').fadeIn();
            }
        },

        closeQRCodePopup: function() {
            if (typeof MAILPN_Popups !== 'undefined') {
                MAILPN_Popups.close();
            } else {
                $('#mailpn-qr-code-popup').fadeOut();
            }
        },

        // Enhanced display result with popup option
        displayResultWithPopup: function(data) {
            // Show in results section
            this.displayResult(data);
            
            // Also show in popup
            this.showQRCodePopup(data.qr_data, data.order_info);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        MAILPNScanner.init();
    });

    // Make MAILPNScanner globally available
    window.MAILPNScanner = MAILPNScanner;

})(jQuery); 