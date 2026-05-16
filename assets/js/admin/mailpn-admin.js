(function($) {
	'use strict';

  function mailpn_show_resend_errors_popup(post_id, load_all) {
    var ajax_url = mailpn_ajax.ajax_url;
    var limit = load_all ? -1 : 20;

    // Show loading if expanding
    if (load_all) {
      $('.mailpn-errors-list-container').html('<p style="text-align:center; padding:20px;">Loading all errors...</p>');
    }

    var data_get_errors = {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_get_errors_list',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      mailpn_mail_id: post_id,
      limit: limit,
    };

    $.post(ajax_url, data_get_errors, function(response) {
      var result = $.parseJSON(response);

      if (result['error_key'] !== '') {
        mailpn_get_main_message(result['error_content']);
        return;
      }

      // Build popup content
      var error_list = result['error_list'];
      var total_errors = result['total_errors'];
      var showing_count = result['showing_count'];
      var showing_all = result['showing_all'];

      var popup_content = '<div class="mailpn-resend-errors-popup">';
      popup_content += '<h3>' + mailpn_i18n.resend_errors_title + '</h3>';
      popup_content += '<p>' + mailpn_i18n.resend_errors_description.replace('%d', total_errors) + '</p>';

      if (total_errors > 0) {
        // Show count info
        if (!showing_all) {
          popup_content += '<p class="mailpn-errors-count-info">';
          popup_content += (mailpn_i18n.showing_errors || 'Showing {showing} of {total} errors')
            .replace('{showing}', showing_count)
            .replace('{total}', total_errors);
          popup_content += '</p>';
        }

        popup_content += '<div class="mailpn-errors-list-container">';
        popup_content += '<ul class="mailpn-errors-list">';

        error_list.forEach(function(error) {
          popup_content += '<li>';
          popup_content += '<strong>#' + error.user_id + ' ' + error.name + '</strong>';
          popup_content += '<br><a href="mailto:' + error.email + '">' + error.email + '</a>';
          popup_content += '</li>';
        });

        popup_content += '</ul>';
        popup_content += '</div>';

        // Add "Load all" button if not showing all
        if (!showing_all) {
          popup_content += '<div class="mailpn-load-all-errors">';
          popup_content += '<button class="mailpn-btn mailpn-btn-link mailpn-btn-load-all-errors" data-post-id="' + post_id + '">';
          popup_content += '<i class="material-icons-outlined">expand_more</i> ';
          popup_content += (mailpn_i18n.load_all_errors || 'Load all {count} errors').replace('{count}', total_errors);
          popup_content += '</button>';
          popup_content += '</div>';
        }
      }

      popup_content += '<div class="mailpn-popup-buttons">';
      popup_content += '<button class="mailpn-btn mailpn-btn-mini mailpn-btn-confirm-resend" data-post-id="' + post_id + '">' + mailpn_i18n.confirm_resend + '</button>';
      popup_content += '<button class="mailpn-btn mailpn-btn-mini mailpn-btn-transaparent mailpn-btn-cancel-resend">' + mailpn_i18n.cancel + '</button>';
      popup_content += '</div>';
      popup_content += '</div>';

      if (load_all) {
        // Just update the content
        $('.mailpn-resend-errors-popup').html($(popup_content).html());
      } else {
        // Build complete popup with wrapper (following MAILPN_Popups pattern)
        var popup_id = 'mailpn-resend-errors-popup-' + post_id;
        var full_popup_html = '<div id="' + popup_id + '" class="mailpn-popup mailpn-resend-popup">' +
          '<div class="mailpn-popup-content">' + popup_content + '</div>' +
          '</div>';

        // Remove existing popup if any
        $('#' + popup_id).remove();

        // Add popup to body
        $('body').append(full_popup_html);

        // Show popup using MAILPN_Popups system
        if (typeof MAILPN_Popups !== 'undefined' && MAILPN_Popups.open) {
          MAILPN_Popups.open(popup_id);
        } else {
          // Fallback: show with simple overlay
          $('#' + popup_id).show();
          if (!$('.mailpn-popup-overlay').length) {
            $('body').append('<div class="mailpn-popup-overlay"></div>');
            $('.mailpn-popup-overlay').fadeIn('fast');
          }
        }
      }
    });
  }

  $(document).on('click', '.mailpn-btn-error-resend', function(e) {
    e.preventDefault();
    var post_id = $(this).data('mailpn-post-id');
    mailpn_show_resend_errors_popup(post_id, false);
  });

  // Handle "Load all errors" button
  $(document).on('click', '.mailpn-btn-load-all-errors', function(e) {
    e.preventDefault();
    var post_id = $(this).data('post-id');
    mailpn_show_resend_errors_popup(post_id, true);
  });

  // Handle confirm resend button
  $(document).on('click', '.mailpn-btn-confirm-resend', function(e) {
    e.preventDefault();
    var post_id = $(this).data('post-id');
    var mailpn_btn = $(this);

    mailpn_btn.prop('disabled', true).text(mailpn_i18n.sending || 'Sending...');

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
        // Close popup
        if (typeof MAILPN_Popups !== 'undefined' && MAILPN_Popups.close) {
          MAILPN_Popups.close();
        }
      } else {
        location.reload();
        mailpn_get_main_message(mailpn_i18n.saved_successfully);
      }
    });
  });

  // Handle cancel button
  $(document).on('click', '.mailpn-btn-cancel-resend', function(e) {
    e.preventDefault();
    if (typeof MAILPN_Popups !== 'undefined' && MAILPN_Popups.close) {
      MAILPN_Popups.close();
    }
  });

  $(document).on('click', '.mailpn-btn-resend-all', function(e) {
    e.preventDefault();
    var mailpn_btn = $(this);
    var post_id = $(this).data('mailpn-post-id');
    mailpn_btn.addClass('mailpn-link-disabled').siblings('.mailpn-waiting').removeClass('mailpn-display-none');

    var ajax_url = mailpn_ajax.ajax_url;

    var data = {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_resend_all',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      mailpn_mail_id: post_id,
    };

    $.post(ajax_url, data, function(response) {
      if ($.parseJSON(response)['error_key'] == 'mailpn_resend_all_error') {
        mailpn_get_main_message($.parseJSON(response)['error_content']);
      }else {
        location.reload();
        mailpn_get_main_message(mailpn_i18n.saved_successfully);
      }

      mailpn_btn.removeClass('mailpn-link-disabled').siblings('.mailpn-waiting').addClass('mailpn-display-none')
    });
  });

  $(document).on('click', '.mailpn-btn-force-send-periodic', function(e) {
    e.preventDefault();
    var mailpn_btn = $(this);
    var post_id = $(this).data('mailpn-post-id');
    mailpn_btn.addClass('mailpn-link-disabled').siblings('.mailpn-waiting').removeClass('mailpn-display-none');

    $.post(mailpn_ajax.ajax_url, {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_force_send_periodic',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      mailpn_mail_id: post_id,
    }, function(response) {
      var result = $.parseJSON(response);
      if (result['error_key'] !== '') {
        mailpn_get_main_message(result['error_content']);
      } else {
        location.reload();
      }
      mailpn_btn.removeClass('mailpn-link-disabled').siblings('.mailpn-waiting').addClass('mailpn-display-none');
    });
  });

  // Shortcodes help toggle
  $(document).on('click', '.mailpn-sc-toggle', function(e) {
    e.preventDefault();
    $(this).closest('.mailpn-sc-help').toggleClass('mailpn-sc-collapsed');
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