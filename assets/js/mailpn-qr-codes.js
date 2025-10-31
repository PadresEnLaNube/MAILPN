/**
 * QR Code JavaScript for MAILPN Plugin
 *
 * @package MAILPN
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // QR Code Library (using qrcode.js)
    var MAILPNQR = {
        init: function() {
            this.bindEvents();
            this.loadQRCodeLibrary();
        },

        bindEvents: function() {
            // QR code button click
            $(document).on('click', '.mailpn-qr-code-btn, .view-qr-code', function(e) {
                e.preventDefault();
                var orderId = $(this).data('order-id');
                var qrData = $(this).data('qr-data');
                MAILPNQR.showQRCode(orderId, qrData);
            });

            // Validate QR code
            $(document).on('click', '.mailpn-validate-qr-btn', function() {
                var qrData = $(this).data('qr-data');
                MAILPNQR.validateQRCode(qrData);
            });

            // Mark ticket as delivered
            $(document).on('click', '.mailpn-mark-delivered-btn', function() {
                var orderId = $(this).data('order-id');
                MAILPNQR.markTicketDelivered(orderId);
            });
        },

        loadQRCodeLibrary: function() {
            // Load QR code library if not already loaded
            if (typeof QRCode === 'undefined') {
                var script = document.createElement('script');
                script.src = mailpn_qr_ajax.qrcode_js_url || '/assets/js/qrcode.min.js';
                script.onload = function() {
                };
                document.head.appendChild(script);
            }
        },

        generateQRCode: function(data, elementId) {
            if (typeof QRCode !== 'undefined') {
                // Clear existing QR code
                $('#' + elementId).empty();
                
                // Generate new QR code
                new QRCode(document.getElementById(elementId), {
                    text: data,
                    width: 200,
                    height: 200,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
            } else {
                // Fallback if QRCode library is not loaded
                $('#' + elementId).html('<p style="text-align: center; color: #666;">QR Code library not loaded</p>');
            }
        },

        showQRCode: function(orderId, qrData) {
            // Show loading
            this.showLoading();

            // Get QR code data via AJAX
            $.ajax({
                url: mailpn_qr_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailpn_get_qr_code',
                    order_id: orderId,
                    nonce: mailpn_qr_ajax.nonce
                },
                success: function(response) {
                    MAILPNQR.hideLoading();
                    if (response.success) {
                        MAILPNQR.displayQRPopup(response.data);
                    } else {
                        MAILPN_Popups.showErrorPopup('Error loading QR code: ' + response.data);
                    }
                },
                error: function() {
                    MAILPNQR.hideLoading();
                    MAILPN_Popups.showErrorPopup('Network error while loading QR code.');
                }
            });
        },

        displayQRPopup: function(data) {
            var popupId = 'mailpn-qr-popup';
            var popupHtml = '<div id="' + popupId + '" class="mailpn-popup mailpn-qr-popup">' +
                '<div class="mailpn-popup-content">' +
                    '<div class="mailpn-popup-header">' +
                        '<h3>' + mailpn_qr_ajax.strings.qr_code_title + '</h3>' +
                    '</div>' +
                    '<div class="mailpn-popup-body">' +
                        '<div class="mailpn-qr-code-container">' +
                            '<div id="mailpn-qr-code-display" class="mailpn-qr-code"></div>' +
                        '</div>' +
                        '<div class="mailpn-qr-info">' +
                            '<div class="mailpn-qr-info-item">' +
                                '<strong>' + mailpn_qr_ajax.strings.order_id + ':</strong> #' + data.order_info.order_id +
                            '</div>' +
                            '<div class="mailpn-qr-info-item">' +
                                '<strong>' + mailpn_qr_ajax.strings.customer_name + ':</strong> ' + data.order_info.customer_name +
                            '</div>' +
                            '<div class="mailpn-qr-info-item">' +
                                '<strong>' + mailpn_qr_ajax.strings.order_total + ':</strong> ' + data.order_info.order_total +
                            '</div>' +
                            '<div class="mailpn-qr-info-item">' +
                                '<strong>' + mailpn_qr_ajax.strings.order_date + ':</strong> ' + data.order_info.order_date +
                            '</div>' +
                            '<div class="mailpn-qr-info-item">' +
                                '<strong>' + mailpn_qr_ajax.strings.generated_date + ':</strong> ' + data.order_info.generated_date +
                            '</div>' +
                            '<div class="mailpn-qr-info-item">' +
                                '<strong>' + mailpn_qr_ajax.strings.validation_status + ':</strong> ' +
                                (data.order_info.validated === '1' ? 
                                    '<span class="mailpn-status-validated">' + mailpn_qr_ajax.strings.validated + '</span>' : 
                                    '<span class="mailpn-status-pending">' + mailpn_qr_ajax.strings.pending + '</span>'
                                ) +
                            '</div>' +
                            '<div class="mailpn-qr-info-item">' +
                                '<strong>' + mailpn_qr_ajax.strings.delivery_status + ':</strong> ' +
                                (data.order_info.ticket_delivered === '1' ? 
                                    '<span class="mailpn-status-delivered">' + mailpn_qr_ajax.strings.delivered + '</span>' : 
                                    '<span class="mailpn-status-not-delivered">' + mailpn_qr_ajax.strings.not_delivered + '</span>'
                                ) +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="mailpn-popup-footer">' +
                        '<button type="button" class="mailpn-validate-qr-btn" data-qr-data="' + data.qr_data + '">' +
                            mailpn_qr_ajax.strings.validate_qr +
                        '</button>' +
                        (data.order_info.ticket_delivered === '1' ? '' :
                            '<button type="button" class="mailpn-mark-delivered-btn" data-order-id="' + data.order_info.order_id + '">' +
                                mailpn_qr_ajax.strings.mark_delivered +
                            '</button>'
                        ) +
                    '</div>' +
                '</div>' +
            '</div>';

            // Remove existing popup if any
            $('#' + popupId).remove();
            
            // Add popup to body
            $('body').append(popupHtml);
            
            // Open popup using MAILPN_Popups
            MAILPN_Popups.open(popupId, {
                beforeShow: function() {
                    MAILPNQR.generateQRCode(data.qr_data, 'mailpn-qr-code-display');
                }
            });
        },

        hideQRModal: function() {
            $('.mailpn-qr-modal-overlay').remove();
        },

        showLoading: function() {
            var loadingHtml = '<div class="mailpn-qr-loading-overlay">' +
                '<div class="mailpn-qr-loading">' +
                    '<div class="mailpn-qr-loading-spinner"></div>' +
                    '<p>' + mailpn_qr_ajax.strings.loading + '</p>' +
                '</div>' +
            '</div>';
            $('body').append(loadingHtml);
        },

        hideLoading: function() {
            $('.mailpn-qr-loading-overlay').remove();
        },

        markTicketDelivered: function(orderId) {
            var self = this;
            
            $.ajax({
                url: mailpn_qr_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailpn_mark_ticket_delivered',
                    order_id: orderId,
                    nonce: mailpn_qr_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        MAILPN_Popups.showSuccessPopup(mailpn_qr_ajax.strings.delivery_success);
                        // Close QR popup and refresh
                        setTimeout(function() {
                            MAILPN_Popups.close();
                        }, 1500);
                    } else {
                        MAILPN_Popups.showErrorPopup('Error marking ticket as delivered');
                    }
                },
                error: function() {
                    MAILPN_Popups.showErrorPopup('Network error occurred');
                }
            });
        },

        validateQRCode: function(qrData) {
            var self = this;
            
            $.ajax({
                url: mailpn_qr_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailpn_validate_qr_code',
                    qr_data: qrData,
                    nonce: mailpn_qr_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        MAILPN_Popups.showSuccessPopup(mailpn_qr_ajax.strings.validation_success);
                        // Close QR popup and refresh
                        setTimeout(function() {
                            MAILPN_Popups.close();
                        }, 1500);
                    } else {
                        MAILPN_Popups.showErrorPopup(response.data.message || 'QR code validation failed');
                    }
                },
                error: function() {
                    MAILPN_Popups.showErrorPopup('Network error during validation');
                }
            });
        },

        showValidationResult: function(data) {
            var resultHtml = `
                <div class="mailpn-qr-validation-result" style="margin: 20px 0; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; color: #155724;">
                    <h4 style="margin: 0 0 10px 0; color: #155724;">Validation Successful!</h4>
                    <p><strong>Order ID:</strong> #${data.order_info.order_id}</p>
                    <p><strong>Customer:</strong> ${data.order_info.customer_name}</p>
                    <p><strong>Total:</strong> ${data.order_info.order_total}</p>
                    <p><strong>Order Date:</strong> ${data.order_info.order_date}</p>
                    <p><strong>Validation Date:</strong> ${data.order_info.validation_date}</p>
                    <p><strong>Ticket Delivered:</strong> ${data.order_info.ticket_delivered === '1' ? 'Yes' : 'No'}</p>
                    ${data.order_info.ticket_delivered === '1' ? `<p><strong>Delivery Date:</strong> ${data.order_info.ticket_delivery_date}</p>` : ''}
                </div>
            `;

            $('.mailpn-qr-modal-body').prepend(resultHtml);
        },

        initQRScanner: function() {
            // Check if device supports camera
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                this.showError('Camera access not supported on this device');
                return;
            }

            var scannerHtml = `
                <div class="mailpn-qr-scanner">
                    <video id="qr-scanner-video" autoplay playsinline></video>
                    <div class="mailpn-qr-scanner-overlay"></div>
                </div>
                <div class="mailpn-qr-scanner-controls" style="margin-top: 15px; text-align: center;">
                    <button type="button" class="mailpn-qr-modal-btn secondary" id="stop-scanner">
                        Stop Scanner
                    </button>
                </div>
            `;

            $('.mailpn-qr-modal-body').html(scannerHtml);

            // Initialize camera
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(function(stream) {
                    var video = document.getElementById('qr-scanner-video');
                    video.srcObject = stream;
                    
                    // Start QR code detection
                    MAILPNQR.startQRDetection(video);
                })
                .catch(function(error) {
                    console.error('Camera access error:', error);
                    MAILPNQR.showError('Unable to access camera');
                });

            // Stop scanner button
            $('#stop-scanner').on('click', function() {
                MAILPNQR.stopQRScanner();
            });
        },

        startQRDetection: function(video) {
            // This is a simplified version - in a real implementation,
            // you would use a QR code detection library like jsQR
            console.log('QR detection started');
            
            // For demo purposes, we'll simulate QR detection after 3 seconds
            setTimeout(function() {
                MAILPNQR.showError('QR code detection library not loaded. Please use manual validation.');
            }, 3000);
        },

        stopQRScanner: function() {
            var video = document.getElementById('qr-scanner-video');
            if (video && video.srcObject) {
                var tracks = video.srcObject.getTracks();
                tracks.forEach(function(track) {
                    track.stop();
                });
            }
            
            // Reset modal content
            $('.mailpn-qr-modal-body').html('<div class="mailpn-qr-loading">Scanner stopped</div>');
        },

        showSuccess: function(message) {
            var successHtml = `
                <div class="mailpn-qr-success" style="margin: 10px 0; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724; text-align: center;">
                    ${message}
                </div>
            `;
            $('.mailpn-qr-modal-body').prepend(successHtml);
        },

        showError: function(message) {
            this.hideLoading();
            var errorHtml = '<div class="mailpn-qr-error-overlay">' +
                '<div class="mailpn-qr-error">' +
                    '<h3>' + mailpn_qr_ajax.strings.error + '</h3>' +
                    '<p>' + message + '</p>' +
                    '<button type="button" class="mailpn-qr-error-close">' + mailpn_qr_ajax.strings.close + '</button>' +
                '</div>' +
            '</div>';
            $('body').append(errorHtml);
        },

        hideError: function() {
            $('.mailpn-qr-error-overlay').remove();
        },

        // API endpoint for external validation
        validateQRCodeAPI: function(qrData, callback) {
            $.ajax({
                url: mailpn_qr_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mailpn_qr_validation_api',
                    qr_data: qrData
                },
                success: function(response) {
                    if (callback) {
                        callback(response);
                    }
                },
                error: function() {
                    if (callback) {
                        callback({ valid: false, message: 'Network error occurred' });
                    }
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        MAILPNQR.init();
    });

    // Make MAILPNQR globally available for external use
    window.MAILPNQR = MAILPNQR;

})(jQuery); 