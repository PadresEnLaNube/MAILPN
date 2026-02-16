/**
 * MAILPN Notifications JavaScript
 * Handles notification interactions and AJAX calls
 */

jQuery(document).ready(function($) {
	'use strict';

    // Function to expand/collapse content and mark as read
    function toggleContent(notificationId, markAsRead) {
        var $expandedContent = $('#expanded-content-' + notificationId);
        var $icon = $('.mailpn-notification-icon-btn.expand-content[data-notification-id="' + notificationId + '"]').find('i');
        var $notification = $('[data-notification-id="' + notificationId + '"]');

        if ($expandedContent.hasClass('show')) {
            $expandedContent.removeClass('show');
            $icon.text('expand_more');
        } else {
            $expandedContent.addClass('show');
            $icon.text('expand_less');

            // Mark as read automatically when expanding
            if (markAsRead && $notification.hasClass('mailpn-notification-unread')) {
                $.ajax({
                    url: mailpn_notifications_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'mailpn_mark_notification_read',
                        notification_id: notificationId,
                        nonce: mailpn_notifications_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $notification.removeClass('mailpn-notification-unread').addClass('mailpn-notification-read');
                            var $markButton = $notification.find('.mailpn-notification-icon-btn.mark-read');
                            if ($markButton.length) {
                                $markButton.closest('.mailpn-tooltip').replaceWith('<div class="mailpn-tooltip"><button type="button" class="mailpn-notification-icon-btn mark-unread" data-notification-id="' + notificationId + '" title="' + mailpn_notifications_ajax.mark_unread_text + '"><i class="material-icons-outlined">mark_email_unread</i></button><span class="mailpn-tooltiptext">' + mailpn_notifications_ajax.mark_unread_text + '</span></div>');
                            }
                        }
                    }
                });
            }
        }
    }

    // Expand/collapse content via button (delegated for dynamic content)
    $(document).on('click', '.mailpn-notification-icon-btn.expand-content', function() {
        var notificationId = $(this).data('notification-id');
        toggleContent(notificationId, false);
    });

    // Expand/collapse content via title click (delegated for dynamic content)
    $(document).on('click', '.expandable-title', function() {
        var notificationId = $(this).data('notification-id');
        toggleContent(notificationId, true);
    });

    // Mark single notification as read (delegated for dynamic content)
    $(document).on('click', '.mailpn-notification-icon-btn.mark-read', function() {
        var notificationId = $(this).data('notification-id');
        var $notification = $('.mailpn-notification[data-notification-id="' + notificationId + '"]');
        var $button = $(this);

        $.ajax({
            url: mailpn_notifications_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mailpn_mark_notification_read',
                notification_id: notificationId,
                nonce: mailpn_notifications_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $notification.removeClass('mailpn-notification-unread').addClass('mailpn-notification-read');
                    $button.closest('.mailpn-tooltip').replaceWith('<div class="mailpn-tooltip"><button type="button" class="mailpn-notification-icon-btn mark-unread" data-notification-id="' + notificationId + '" title="' + mailpn_notifications_ajax.mark_unread_text + '"><i class="material-icons-outlined">mark_email_unread</i></button><span class="mailpn-tooltiptext">' + mailpn_notifications_ajax.mark_unread_text + '</span></div>');
                }
            }
        });
    });

    // Mark single notification as unread (delegated for dynamic content)
    $(document).on('click', '.mailpn-notification-icon-btn.mark-unread', function() {
        var notificationId = $(this).data('notification-id');
        var $notification = $('.mailpn-notification[data-notification-id="' + notificationId + '"]');
        var $button = $(this);

        $.ajax({
            url: mailpn_notifications_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mailpn_mark_notification_unread',
                notification_id: notificationId,
                nonce: mailpn_notifications_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $notification.removeClass('mailpn-notification-read').addClass('mailpn-notification-unread');
                    $button.closest('.mailpn-tooltip').replaceWith('<div class="mailpn-tooltip"><button type="button" class="mailpn-notification-icon-btn mark-read" data-notification-id="' + notificationId + '" title="' + mailpn_notifications_ajax.mark_read_text + '"><i class="material-icons-outlined">mark_email_read</i></button><span class="mailpn-tooltiptext">' + mailpn_notifications_ajax.mark_read_text + '</span></div>');
                }
            }
        });
    });

    // Mark all notifications as read (delegated for dynamic content)
    $(document).on('click', '.mailpn-notification-icon-btn.mark-all-read', function() {
        var userId = $(this).data('user-id');
        var $button = $(this);
        var $icon = $button.find('i');
        var originalIcon = $icon.text();

        $icon.text('hourglass_empty').prop('disabled', true);

        $.ajax({
            url: mailpn_notifications_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mailpn_mark_all_notifications_read',
                user_id: userId,
                nonce: mailpn_notifications_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.mailpn-notification-unread').removeClass('mailpn-notification-unread').addClass('mailpn-notification-read');
                    $('.mailpn-notification-icon-btn.mark-read').each(function() {
                        var notificationId = $(this).data('notification-id');
                        $(this).closest('.mailpn-tooltip').replaceWith('<div class="mailpn-tooltip"><button type="button" class="mailpn-notification-icon-btn mark-unread" data-notification-id="' + notificationId + '" title="' + mailpn_notifications_ajax.mark_unread_text + '"><i class="material-icons-outlined">mark_email_unread</i></button><span class="mailpn-tooltiptext">' + mailpn_notifications_ajax.mark_unread_text + '</span></div>');
                    });
                }
                $icon.text(originalIcon).prop('disabled', false);
            },
            error: function() {
                $icon.text(originalIcon).prop('disabled', false);
            }
        });
    });
});
