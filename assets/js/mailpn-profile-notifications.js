/**
 * MAILPN Profile Notifications Icon JavaScript
 * Handles profile notifications icon interactions
 */

jQuery(document).ready(function($) {
    'use strict';

    // Handle click on notifications icon
    $('.mailpn-notifications-profile-link').on('click', function(e) {
        e.preventDefault();
        
        // Scroll to notifications section if it exists
        var $notificationsSection = $('.mailpn-notifications-container');
        if ($notificationsSection.length) {
            $('html, body').animate({
                scrollTop: $notificationsSection.offset().top - 100
            }, 500);
        } else {
            // If no notifications section, show a message
            alert(mailpn_profile_notifications.no_section_message);
        }
    });
});
