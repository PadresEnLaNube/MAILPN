(function($) {
	'use strict';

  $(document).on('click', '.mailpn-btn-error-resend', function(e) {
    e.preventDefault();
    var mailpn_btn = $(this);
    var post_id = $(this).data('mailpn-post-id');
    mailpn_btn.addClass('mailpn-link-disabled').siblings('.mailpn-waiting').removeClass('mailpn-display-none');

    var ajax_url = mailpn_ajax.ajax_url;

    var data = {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_resend_errors',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      mailpn_mail_id: post_id,
    };

    $.post(ajax_url, data, function(response) {
      if ($.parseJSON(response)['error_key'] == 'mailpn_resend_errors_error') {
        mailpn_get_main_message($.parseJSON(response)['error_content']);
      }else {
        location.reload();
        mailpn_get_main_message(mailpn_i18n.saved_successfully);
      }

      mailpn_btn.removeClass('mailpn-link-disabled').siblings('.mailpn-waiting').addClass('mailpn-display-none')
    });
  });

  // MailPN Stats Popup
  $(document).on('click', '.mailpn-stats-button', function(e) {
    e.preventDefault();
    const postId = $(this).data('post-id');
    const popupContent = $('#mailpn-stats-popup-' + postId).html();
    
    // Use the existing popup system
    MAILPN_Popups.open(popupContent, {
      id: 'mailpn-stats-popup-' + postId,
      class: 'mailpn-stats-popup',
      closeButton: true,
      overlayClose: true,
      escClose: true
    });
  });

  // MailPN Test Email Button
  $(document).on('click', '.mailpn-btn-test-email', function(e) {
    e.preventDefault();
    var mailpn_btn = $(this);
    var post_id = $(this).data('mailpn-post-id');
    var user_id = $(this).data('mailpn-user-id');
    
    // Check if mailpn_ajax object exists
    if (typeof mailpn_ajax === 'undefined') {
      mailpn_get_main_message('AJAX functionality not available. Please refresh the page.', 'error');
      return;
    }
    
    // Check if required properties exist
    if (!mailpn_ajax.ajax_url || !mailpn_ajax.mailpn_ajax_nonce) {
      mailpn_get_main_message('AJAX configuration is incomplete. Please refresh the page.', 'error');
      return;
    }
    
    // Disable button and show loading
    mailpn_btn.addClass('mailpn-link-disabled').prop('disabled', true);
    mailpn_btn.html('<i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16">hourglass_empty</i> Sending...');

    var ajax_url = mailpn_ajax.ajax_url;

    var data = {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_send_test_email_campaign',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      mailpn_mail_id: post_id,
      post_id: post_id,
      user_id: user_id,
    };

    $.post(ajax_url, data, function(response) {
      try {
        var result = $.parseJSON(response);
        if (result.error_key == '') {
          mailpn_get_main_message(result.error_content, 'success');
        } else {
          mailpn_get_main_message(result.error_content, 'error');
        }
      } catch (e) {
        mailpn_get_main_message('An error occurred while sending the test email.', 'error');
      }

      // Re-enable button and restore original text
      mailpn_btn.removeClass('mailpn-link-disabled').prop('disabled', false);
      mailpn_btn.html('Send test email');
    }).fail(function(xhr, status, error) {
      mailpn_get_main_message('Network error occurred while sending the test email.', 'error');
      
      // Re-enable button and restore original text
      mailpn_btn.removeClass('mailpn-link-disabled').prop('disabled', false);
      mailpn_btn.html('Send test email');
    });
  });
})(jQuery);