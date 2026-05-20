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
    mailpn_btn.html('<div class="mailpn-waiting"><div class="mailpn-loader-circle-waiting"><div></div><div></div><div></div><div></div></div></div> Sending...');

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

  // Queue Details popup
  $(document).on('click', '.mailpn-btn-queue-details', function(e) {
    e.preventDefault();
    var mail_id = $(this).data('mail-id');

    $.post(mailpn_ajax.ajax_url, {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_get_queue_details',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      mail_id: mail_id,
    }, function(response) {
      var result = $.parseJSON(response);

      if (result.error_key !== '') {
        mailpn_get_main_message('Error loading queue details');
        return;
      }

      // Build popup content
      var popup_content = '<div class="mailpn-queue-details-popup">';
      popup_content += '<h3>' + (mailpn_i18n.queue_details_title || 'Queue Details') + '</h3>';

      // Status section
      popup_content += '<div class="mailpn-queue-section">';
      popup_content += '<h4>' + (mailpn_i18n.queue_status || 'Queue Status') + '</h4>';

      if (result.is_paused) {
        popup_content += '<div class="mailpn-queue-status mailpn-status-paused-badge">';
        popup_content += '<div>';
        popup_content += '<i class="material-icons-outlined">pause_circle</i> ';
        popup_content += '</div>';
        popup_content += '<div>';
        popup_content += '<strong>' + (mailpn_i18n.paused || 'Paused') + '</strong>';

        if (result.paused_by_errors) {
          popup_content += '<p>' + (mailpn_i18n.paused_by_errors_msg || 'Paused due to consecutive errors') + '</p>';
          popup_content += '<p>' + (mailpn_i18n.consecutive_errors || 'Consecutive errors') + ': ' + result.consecutive_errors + ' / ' + result.consecutive_limit + '</p>';
        } else if (result.hit_daily_limit) {
          popup_content += '<p>' + (mailpn_i18n.paused_daily_limit || 'Paused due to daily limit') + '</p>';
        } else if (result.paused_daily_limit) {
          popup_content += '<p>' + (mailpn_i18n.paused_daily_limit || 'Paused due to daily limit') + '</p>';
        }

        popup_content += '</div>';
        popup_content += '</div>';
      } else {
        popup_content += '<div class="mailpn-queue-status mailpn-status-active-badge">';
        popup_content += '<div>';
        popup_content += '<i class="material-icons-outlined">check_circle</i> ';
        popup_content += '</div>';
        popup_content += '<div>';
        popup_content += '<strong>' + (mailpn_i18n.active || 'Active') + '</strong>';
        popup_content += '</div>';
        popup_content += '</div>';
      }
      popup_content += '</div>';

      // Limits section
      popup_content += '<div class="mailpn-queue-section">';
      popup_content += '<h4>' + (mailpn_i18n.sending_limits || 'Sending Limits') + '</h4>';
      popup_content += '<ul class="mailpn-queue-stats">';
      popup_content += '<li><strong>' + (mailpn_i18n.daily_sent || 'Sent today') + ':</strong> ' + result.mails_sent_today + ' / ' + result.daily_limit + '</li>';
      popup_content += '<li><strong>' + (mailpn_i18n.rate_limit || 'Rate limit') + ':</strong> ' + result.rate_limit + ' ' + (mailpn_i18n.emails_per_10min || 'emails every 10 minutes') + '</li>';
      popup_content += '</ul>';
      popup_content += '</div>';

      // Pending emails section - Show GLOBAL queue
      if (result.total_pending > 0) {
        popup_content += '<div class="mailpn-queue-section">';
        popup_content += '<h4>' + (mailpn_i18n.pending_emails || 'Pending Emails') + ' (' + result.total_pending + ')</h4>';

        if (result.showing_sample) {
          popup_content += '<p class="mailpn-queue-sample-note">' + (mailpn_i18n.showing_first_30 || 'Showing first 30 of {count}').replace('{count}', result.total_pending) + '</p>';
        }

        popup_content += '<ul class="mailpn-queue-pending-list">';
        result.pending_list.forEach(function(user) {
          popup_content += '<li>';
          popup_content += '<strong>#' + user.id + ' ' + user.name + '</strong>';
          popup_content += '<span class="mailpn-user-email">' + user.email + '</span>';
          popup_content += '<span class="mailpn-template-tag">' + user.template_title + '</span>';
          popup_content += '</li>';
        });
        popup_content += '</ul>';
        popup_content += '</div>';
      } else {
        popup_content += '<div class="mailpn-queue-section">';
        popup_content += '<p>' + (mailpn_i18n.no_pending_emails || 'No emails pending in queue') + '</p>';
        popup_content += '</div>';
      }

      popup_content += '<div class="mailpn-popup-buttons">';

      // Add resume button if paused
      if (result.is_paused) {
        popup_content += '<button class="mailpn-btn mailpn-btn-mini mailpn-btn-transparent mailpn-btn-resume-queue">';
        popup_content += '<i class="material-icons-outlined">play_arrow</i> ';
        popup_content += (mailpn_i18n.resume_queue || 'Resume Queue');
        popup_content += '</button>';
      }

      popup_content += '<button class="mailpn-btn mailpn-btn-mini mailpn-btn-transparent mailpn-btn-close-queue-details">' + (mailpn_i18n.close || 'Close') + '</button>';
      popup_content += '</div>';
      popup_content += '</div>';

      // Show popup
      var popup_id = 'mailpn-queue-details-popup';
      var full_popup_html = '<div id="' + popup_id + '" class="mailpn-popup mailpn-popup-size-large mailpn-queue-details-popup-wrapper">' +
        '<div class="mailpn-popup-content">' + popup_content + '</div>' +
        '</div>';

      $('#' + popup_id).remove();
      $('body').append(full_popup_html);

      if (typeof MAILPN_Popups !== 'undefined' && MAILPN_Popups.open) {
        MAILPN_Popups.open(popup_id);
      } else {
        $('#' + popup_id).show();
        if (!$('.mailpn-popup-overlay').length) {
          $('body').append('<div class="mailpn-popup-overlay"></div>');
          $('.mailpn-popup-overlay').fadeIn('fast');
        }
      }
    });
  });

  // Close queue details popup
  $(document).on('click', '.mailpn-btn-close-queue-details', function(e) {
    e.preventDefault();
    if (typeof MAILPN_Popups !== 'undefined' && MAILPN_Popups.close) {
      MAILPN_Popups.close();
    }
  });

  // Resume queue button
  $(document).on('click', '.mailpn-btn-resume-queue', function(e) {
    e.preventDefault();
    var btn = $(this);
    
    if (!confirm(mailpn_i18n.confirm_resume_queue || 'Are you sure you want to resume the queue? Make sure you have fixed the issue that caused the errors.')) {
      return;
    }

    btn.prop('disabled', true).html('<div class="mailpn-waiting"><div class="mailpn-loader-circle-waiting"><div></div><div></div><div></div><div></div></div></div> ' + (mailpn_i18n.resuming || 'Resuming...'));

    $.post(mailpn_ajax.ajax_url, {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_resume_queue',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
    }, function(response) {
      var result = $.parseJSON(response);

      if (result.error_key !== '') {
        mailpn_get_main_message('Error resuming queue');
        btn.prop('disabled', false).html('<i class="material-icons-outlined">play_arrow</i> ' + (mailpn_i18n.resume_queue || 'Resume Queue'));
      } else {
        mailpn_get_main_message(mailpn_i18n.queue_resumed || 'Queue resumed successfully');
        setTimeout(function() {
          location.reload();
        }, 1000);
      }
    });
  });

  // Error details popup
  $(document).on('click', '.mailpn-view-error-details', function(e) {
    e.preventDefault();

    var rec_id = $(this).data('rec-id');
    var link = $(this);
    var originalHTML = link.html();

    link.html('<div class="mailpn-waiting"><div class="mailpn-loader-circle-waiting"><div></div><div></div><div></div><div></div></div></div>');

    $.post(mailpn_ajax.ajax_url, {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_get_error_details',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      rec_id: rec_id,
    }, function(response) {
      link.html(originalHTML);

      var result = $.parseJSON(response);

      if (result.error_key !== '') {
        alert('Error loading details: ' + result.error_key);
        return;
      }

      // Build popup content
      var popup_content = '<div class="mailpn-error-details-popup">';
      popup_content += '<h3><i class="material-icons-outlined">error_outline</i> ' + (mailpn_i18n.error_details || 'Error Details') + '</h3>';

      // Recipient info
      if (result.user_info || result.to_email) {
        popup_content += '<div class="mailpn-error-section">';
        popup_content += '<h4>' + (mailpn_i18n.recipient || 'Recipient') + '</h4>';
        if (result.user_info) {
          popup_content += '<p><strong>#' + result.user_info.id + ' ' + result.user_info.name + '</strong><br>';
          popup_content += '<a href="mailto:' + result.user_info.email + '">' + result.user_info.email + '</a></p>';
        } else if (result.to_email) {
          popup_content += '<p><a href="mailto:' + result.to_email + '">' + result.to_email + '</a></p>';
        }
        popup_content += '</div>';
      }

      // Template info
      if (result.template_info) {
        popup_content += '<div class="mailpn-error-section">';
        popup_content += '<h4>' + (mailpn_i18n.template || 'Template') + '</h4>';
        popup_content += '<p><strong>' + result.template_info.title + '</strong> (ID: ' + result.template_info.id + ')</p>';
        if (result.subject) {
          popup_content += '<p><small>' + (mailpn_i18n.subject || 'Subject') + ': ' + result.subject + '</small></p>';
        }
        popup_content += '</div>';
      }

      // Error message
      popup_content += '<div class="mailpn-error-section">';
      popup_content += '<h4>' + (mailpn_i18n.error_message || 'Error Message') + '</h4>';
      if (result.error_lines && result.error_lines.length > 0) {
        popup_content += '<div class="mailpn-error-message-box">';
        result.error_lines.forEach(function(line) {
          if (line.trim()) {
            popup_content += '<p>' + line + '</p>';
          }
        });
        popup_content += '</div>';
      } else {
        popup_content += '<p class="mailpn-no-details">' + (mailpn_i18n.no_error_details || 'No error details available') + '</p>';
      }
      popup_content += '</div>';

      // Technical details
      popup_content += '<div class="mailpn-error-section">';
      popup_content += '<h4>' + (mailpn_i18n.technical_details || 'Technical Details') + '</h4>';
      popup_content += '<table class="mailpn-error-tech-table">';
      if (result.sent_datetime) {
        popup_content += '<tr><td><strong>' + (mailpn_i18n.timestamp || 'Timestamp') + ':</strong></td><td>' + result.sent_datetime + '</td></tr>';
      }
      if (result.server_ip) {
        popup_content += '<tr><td><strong>' + (mailpn_i18n.server_ip || 'Server IP') + ':</strong></td><td>' + result.server_ip + '</td></tr>';
      }
      if (result.headers) {
        var headers_preview = result.headers.substring(0, 100);
        if (result.headers.length > 100) headers_preview += '...';
        popup_content += '<tr><td><strong>' + (mailpn_i18n.headers || 'Headers') + ':</strong></td><td><code>' + headers_preview + '</code></td></tr>';
      }
      popup_content += '<tr><td><strong>' + (mailpn_i18n.record_id || 'Record ID') + ':</strong></td><td>' + result.rec_id + '</td></tr>';
      popup_content += '</table>';
      popup_content += '</div>';

      popup_content += '<div class="mailpn-popup-buttons">';
      popup_content += '<button class="mailpn-btn mailpn-btn-mini mailpn-btn-transaparent mailpn-btn-close-error-details">' + (mailpn_i18n.close || 'Close') + '</button>';
      popup_content += '</div>';
      popup_content += '</div>';

      // Show popup using MAILPN_Popups
      var popup_id = 'mailpn-error-details-popup-' + rec_id;
      var full_popup_html = '<div id="' + popup_id + '" class="mailpn-popup mailpn-error-details-popup-wrapper">' +
        '<div class="mailpn-popup-content">' + popup_content + '</div>' +
        '</div>';

      $('#' + popup_id).remove();
      $('body').append(full_popup_html);

      if (typeof MAILPN_Popups !== 'undefined' && MAILPN_Popups.open) {
        MAILPN_Popups.open(popup_id);
      } else {
        $('#' + popup_id).show();
        if (!$('.mailpn-popup-overlay').length) {
          $('body').append('<div class="mailpn-popup-overlay"></div>');
          $('.mailpn-popup-overlay').fadeIn('fast');
        }
      }
    }).fail(function() {
      link.html(originalHTML);
      alert('Network error');
    });
  });

  // Close error details popup
  $(document).on('click', '.mailpn-btn-close-error-details', function(e) {
    e.preventDefault();
    if (typeof MAILPN_Popups !== 'undefined' && MAILPN_Popups.close) {
      MAILPN_Popups.close();
    }
  });

  // Deliverability Check
  $(document).on('click', '#mailpn-check-deliverability-btn', function(e) {
    e.preventDefault();
    var btn = $(this);
    var originalHTML = btn.html();

    // Show loading
    btn.prop('disabled', true);
    btn.html('<div class="mailpn-waiting mailpn-display-inline-block"><div class="mailpn-loader-circle-waiting"><div></div><div></div><div></div><div></div></div></div>');
    $('#mailpn-deliverability-results').addClass('mailpn-display-none-soft');
    $('#mailpn-deliverability-loading').removeClass('mailpn-display-none-soft');

    $.post(mailpn_ajax.ajax_url, {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_check_deliverability',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
    }, function(response) {
      var result = $.parseJSON(response);

      // Hide loading
      $('#mailpn-deliverability-loading').addClass('mailpn-display-none-soft');
      btn.prop('disabled', false).html(originalHTML);

      if (result.error_key !== '') {
        mailpn_get_main_message('Error checking deliverability', 'error');
        return;
      }

      // Build results HTML
      var score = result.score;
      var checks = result.checks;
      var scoreColor = score >= 80 ? '#4caf50' : (score >= 60 ? '#ff9800' : '#f44336');
      var scoreIcon = score >= 80 ? 'check_circle' : (score >= 60 ? 'warning' : 'error');

      var html = '<div class="mailpn-deliverability-score-box mailpn-mb-20 mailpn-p-20" style="background: ' + scoreColor + '; color: white; border-radius: 8px;">';
      html += '<div class="mailpn-text-align-center">';
      html += '<i class="material-icons-outlined" style="font-size: 48px; vertical-align: middle;">' + scoreIcon + '</i>';
      html += '<h2 style="margin: 10px 0; color: white;">' + (mailpn_i18n.deliverability_score || 'Deliverability Score') + ': ' + score + '/100</h2>';
      html += '</div>';
      html += '</div>';

      $('.mailpn-deliverability-score').html(html);

      // Build checks HTML
      var checksHTML = '<div class="mailpn-deliverability-checks-list">';
      $.each(checks, function(key, check) {
        var checkIcon = check.status === 'passed' ? 'check_circle' : (check.status === 'warning' ? 'warning' : 'cancel');
        var checkColor = check.status === 'passed' ? '#4caf50' : (check.status === 'warning' ? '#ff9800' : '#f44336');

        checksHTML += '<div class="mailpn-deliverability-check-item mailpn-mb-30 mailpn-p-20" style="border-left: 4px solid ' + checkColor + '; background: #f9f9f9; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
        checksHTML += '<div class="mailpn-display-table mailpn-width-100-percent">';
        checksHTML += '<div class="mailpn-display-inline-table" style="width: 50px;">';
        checksHTML += '<i class="material-icons-outlined" style="color: ' + checkColor + '; font-size: 36px; vertical-align: middle;">' + checkIcon + '</i>';
        checksHTML += '</div>';
        checksHTML += '<div class="mailpn-display-inline-table" style="width: calc(100% - 50px); vertical-align: middle;">';
        checksHTML += '<strong style="font-size: 16px;">' + check.name + '</strong>';
        checksHTML += '<p style="margin: 8px 0 0 0; color: #666; line-height: 1.5;">' + check.message + '</p>';

        // Add suggestion if check failed or has warning
        if (check.suggestion && (check.status === 'failed' || check.status === 'warning')) {
          checksHTML += '<div class="mailpn-mt-10 mailpn-p-10" style="background: #fff; border-radius: 4px; border-left: 3px solid ' + checkColor + ';">';
          checksHTML += '<strong style="color: ' + checkColor + ';"><i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">lightbulb</i> ' + (mailpn_i18n.suggestion || 'Suggestion') + ':</strong> ';
          checksHTML += '<span style="color: #444;">' + check.suggestion + '</span>';
          checksHTML += '</div>';
        }

        checksHTML += '</div>';
        checksHTML += '</div>';
        checksHTML += '</div>';
      });
      checksHTML += '</div>';

      $('.mailpn-deliverability-checks').html(checksHTML);
      $('#mailpn-deliverability-results').removeClass('mailpn-display-none-soft');

    }).fail(function() {
      $('#mailpn-deliverability-loading').addClass('mailpn-display-none-soft');
      btn.prop('disabled', false).html(originalHTML);
      mailpn_get_main_message('Network error', 'error');
    });
  });

  // Analyze Headers
  $(document).on('click', '#mailpn-analyze-headers-btn', function(e) {
    e.preventDefault();
    var btn = $(this);
    var originalHTML = btn.html();
    var headers = $('#mailpn-header-textarea').val().trim();

    if (headers === '') {
      mailpn_get_main_message('Please paste email headers first', 'error');
      return;
    }

    // Show loading
    btn.prop('disabled', true);
    btn.html('<div class="mailpn-waiting mailpn-display-inline-block"><div class="mailpn-loader-circle-waiting"><div></div><div></div><div></div><div></div></div></div>');

    $.post(mailpn_ajax.ajax_url, {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_analyze_headers',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      headers: headers,
    }, function(response) {
      var result = $.parseJSON(response);
      btn.prop('disabled', false).html(originalHTML);

      if (result.error_key !== '') {
        mailpn_get_main_message(result.error_content || 'Error analyzing headers', 'error');
        return;
      }

      // Build results HTML
      var score = result.score;
      var analysis = result.analysis;
      var scoreColor = score >= 80 ? '#4caf50' : (score >= 60 ? '#ff9800' : '#f44336');
      var scoreIcon = score >= 80 ? 'check_circle' : (score >= 60 ? 'warning' : 'error');

      var html = '<div class="mailpn-mt-20">';
      html += '<div class="mailpn-deliverability-score-box mailpn-mb-20 mailpn-p-20" style="background: ' + scoreColor + '; color: white; border-radius: 8px;">';
      html += '<div class="mailpn-text-align-center">';
      html += '<i class="material-icons-outlined" style="font-size: 48px; vertical-align: middle;">' + scoreIcon + '</i>';
      html += '<h3 style="margin: 10px 0; color: white;">' + (mailpn_i18n.header_analysis_score || 'Header Analysis Score') + ': ' + score + '/100</h3>';
      html += '</div>';
      html += '</div>';

      // Build analysis HTML
      html += '<div class="mailpn-header-analysis-checks">';
      $.each(analysis, function(key, check) {
        var checkIcon = check.status === 'passed' ? 'check_circle' : (check.status === 'warning' ? 'warning' : 'cancel');
        var checkColor = check.status === 'passed' ? '#4caf50' : (check.status === 'warning' ? '#ff9800' : '#f44336');

        html += '<div class="mailpn-deliverability-check-item mailpn-mb-30 mailpn-p-20" style="border-left: 4px solid ' + checkColor + '; background: #f9f9f9; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
        html += '<div class="mailpn-display-table mailpn-width-100-percent">';
        html += '<div class="mailpn-display-inline-table" style="width: 50px;">';
        html += '<i class="material-icons-outlined" style="color: ' + checkColor + '; font-size: 36px; vertical-align: middle;">' + checkIcon + '</i>';
        html += '</div>';
        html += '<div class="mailpn-display-inline-table" style="width: calc(100% - 50px); vertical-align: middle;">';
        html += '<strong style="font-size: 16px;">' + check.name + '</strong>';
        html += '<p style="margin: 8px 0 0 0; color: #666; line-height: 1.5;">' + check.message + '</p>';

        if (check.suggestion && (check.status === 'failed' || check.status === 'warning')) {
          html += '<div class="mailpn-mt-10 mailpn-p-10" style="background: #fff; border-radius: 4px; border-left: 3px solid ' + checkColor + ';">';
          html += '<strong style="color: ' + checkColor + ';"><i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">lightbulb</i> ' + (mailpn_i18n.suggestion || 'Suggestion') + ':</strong> ';
          html += '<span style="color: #444;">' + check.suggestion + '</span>';
          html += '</div>';
        }

        html += '</div>';
        html += '</div>';
        html += '</div>';
      });
      html += '</div>';
      html += '</div>';

      $('#mailpn-header-analysis-results').html(html).removeClass('mailpn-display-none-soft');

    }).fail(function() {
      btn.prop('disabled', false).html(originalHTML);
      mailpn_get_main_message('Network error', 'error');
    });
  });

  // Mail-Tester button
  $(document).on('click', '#mailpn-mailtester-btn', function(e) {
    e.preventDefault();
    // Open Mail-Tester in new window
    window.open('https://www.mail-tester.com/', '_blank');
  });

  // Send test email to external tester
  $(document).on('click', '#mailpn-send-test-email-btn', function(e) {
    e.preventDefault();
    var btn = $(this);
    var originalHTML = btn.html();
    var email = $('#mailpn-tester-email').val().trim();

    if (email === '') {
      mailpn_get_main_message('Please enter an email address', 'error');
      $('#mailpn-tester-email').focus();
      return;
    }

    // Basic email validation
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      mailpn_get_main_message('Please enter a valid email address', 'error');
      $('#mailpn-tester-email').focus();
      return;
    }

    // Show loading
    btn.prop('disabled', true);
    btn.html('<div class="mailpn-waiting mailpn-display-inline-block"><div class="mailpn-loader-circle-waiting"><div></div><div></div><div></div><div></div></div></div>');
    $('#mailpn-test-email-result').addClass('mailpn-display-none-soft');

    $.post(mailpn_ajax.ajax_url, {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_send_test_email_external',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      to_email: email,
    }, function(response) {
      var result = $.parseJSON(response);
      btn.prop('disabled', false).html(originalHTML);

      if (result.error_key !== '') {
        var errorMsg = result.error_content || 'Error sending test email';
        mailpn_get_main_message(errorMsg, 'error');

        var resultHTML = '<div class="mailpn-p-20" style="background: #ffebee; border-left: 4px solid #f44336; border-radius: 4px;">';
        resultHTML += '<i class="material-icons-outlined mailpn-vertical-align-middle" style="color: #f44336;">error</i> ';
        resultHTML += '<strong style="color: #c62828;">' + errorMsg + '</strong>';
        resultHTML += '</div>';
        $('#mailpn-test-email-result').html(resultHTML).removeClass('mailpn-display-none-soft');
        return;
      }

      mailpn_get_main_message(result.message, 'success');

      var resultHTML = '<div class="mailpn-p-20" style="background: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 4px;">';
      resultHTML += '<i class="material-icons-outlined mailpn-vertical-align-middle" style="color: #4caf50;">check_circle</i> ';
      resultHTML += '<strong style="color: #2e7d32;">' + result.message + '</strong>';
      resultHTML += '<p style="margin: 10px 0 0 0; color: #555;">';
      resultHTML += (mailpn_i18n.check_mailtester_results || 'Go back to Mail-Tester and click "Then check your score" to see your deliverability report.');
      resultHTML += '</p>';
      resultHTML += '</div>';
      $('#mailpn-test-email-result').html(resultHTML).removeClass('mailpn-display-none-soft');

    }).fail(function() {
      btn.prop('disabled', false).html(originalHTML);
      mailpn_get_main_message('Network error', 'error');
    });
  });

})(jQuery);
