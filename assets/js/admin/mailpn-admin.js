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
      mail_id: post_id,
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
})(jQuery);