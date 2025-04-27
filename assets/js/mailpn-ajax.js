(function($) {
	'use strict';

  $(document).ready(function() {
    $(document).on('submit', '.mailpn-form', function(e){
      var mailpn_form = $(this);
      var mailpn_btn = mailpn_form.find('input[type="submit"]');
      mailpn_btn.addClass('mailpn-link-disabled').siblings('.mailpn-waiting').removeClass('mailpn-display-none');

      var ajax_url = mailpn_ajax.ajax_url;
      var data = {
        action: 'mailpn_ajax_nopriv',
        ajax_nonce: mailpn_ajax.ajax_nonce,
        mailpn_ajax_nopriv_type: 'mailpn_form_save',
        mailpn_form_id: mailpn_form.attr('id'),
        mailpn_form_type: mailpn_btn.attr('data-mailpn-type'),
        mailpn_form_subtype: mailpn_btn.attr('data-mailpn-subtype'),
        mailpn_form_user_id: mailpn_btn.attr('data-mailpn-user-id'),
        mailpn_form_post_id: mailpn_btn.attr('data-mailpn-post-id'),
        mailpn_form_post_type: mailpn_form.attr('data-mailpn-post-type'),
        ajax_keys: [],
      };

      if (!(typeof window['mailpn_window_vars'] !== 'undefined')) {
        window['mailpn_window_vars'] = [];
      }

      $(mailpn_form.find('input:not([type="submit"]), select, textarea')).each(function(index, element) {
        if ($(this).parents('.mailpn-html-multi-group').length) {
          if (!(typeof window['mailpn_window_vars']['form_field_' + element.name] !== 'undefined')) {
            window['mailpn_window_vars']['form_field_' + element.name] = [];
          }

          window['mailpn_window_vars']['form_field_' + element.name].push($(element).val());

          data[element.name] = window['mailpn_window_vars']['form_field_' + element.name];
        }else{
          if ($(this).is(':checkbox')) {
            if ($(this).is(':checked')) {
              data[element.name] = $(element).val();
            }else{
              data[element.name] = '';
            }
          }else if ($(this).is(':radio')) {
            if ($(this).is(':checked')) {
              data[element.name] = $(element).val();
            }
          }else{
            data[element.name] = $(element).val();
          }
        }

        data.ajax_keys.push({
          id: element.name,
          node: element.nodeName,
          type: element.type,
        });
      });

      $.post(ajax_url, data, function(response) {
        var response_json = $.parseJSON(response);

        if (response_json['error_key'] == 'mailpn_form_save_error_unlogged') {
          mailpn_get_main_message(mailpn_i18n.user_unlogged);

          if (!$('.userspn-profile-wrapper .user-unlogged').length) {
            $('.userspn-profile-wrapper').prepend('<div class="userspn-alert userspn-alert-warning user-unlogged">' + mailpn_i18n.user_unlogged + '</div>');
          }

          MILLA_Popups.open($('#userspn-profile-popup'));
          $('#userspn-login input#user_login').focus();
        }else if (response_json['error_key'] != '') {
          mailpn_get_main_message(mailpn_i18n.an_error_has_occurred);
        }else {
          mailpn_get_main_message(mailpn_i18n.saved_successfully);
        }

        if (response_json['update_list']) {
          $('.mailpn-' + data.mailpn_form_post_type + '-list').html(response_json['update_html']);
        }

        if (response_json['popup_close']) {
          MILLA_Popups.close();
          $('.mailpn-menu-more-overlay').fadeOut('fast');
        }

        if (response_json['check'] == 'post_check') {
          MILLA_Popups.close();
          $('.mailpn-menu-more-overlay').fadeOut('fast');
          $('.mailpn-' + data.mailpn_form_post_type + '[data-mailpn-' + data.mailpn_form_post_type + '-id="' + data.mailpn_form_post_id + '"] .mailpn-check-wrapper i').text('basecpt_alt');
        }else if (response_json['check'] == 'post_uncheck') {
          MILLA_Popups.close();
          $('.mailpn-menu-more-overlay').fadeOut('fast');
          $('.mailpn-' + data.mailpn_form_post_type + '[data-mailpn-' + data.mailpn_form_post_type + '-id="' + data.mailpn_form_post_id + '"] .mailpn-check-wrapper i').text('radio_button_unchecked');
        }

        mailpn_btn.removeClass('mailpn-link-disabled').siblings('.mailpn-waiting').addClass('mailpn-display-none')
      });

      delete window['mailpn_window_vars'];
      return false;
    });

    $(document).on('click', '.mailpn-popup-open-ajax', function(e) {
      e.preventDefault();

      var mailpn_btn = $(this);
      var mailpn_ajax_type = mailpn_btn.attr('data-mailpn-ajax-type');
      var mailpn_basecpt_id = mailpn_btn.closest('.mailpn-basecpt').attr('data-mailpn-basecpt-id');
      var popup_element = $('#' + mailpn_btn.attr('data-mailpn-popup-id'));

      MAILPN_Popups.open(popup_element, {
        beforeShow: function(instance, popup) {
          var ajax_url = mailpn_ajax.ajax_url;
          var data = {
            action: 'mailpn_ajax',
            mailpn_ajax_type: mailpn_ajax_type,
            mailpn_basecpt_id: mailpn_basecpt_id ? mailpn_basecpt_id : '',
            ajax_nonce: mailpn_ajax.ajax_nonce
          };

          // Log the data being sent
          console.log('MAILPN AJAX - Sending request with data:', data);

          $.ajax({
            url: ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
              try {
                // First try to parse the response as JSON
                var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;
                
                // Check for error key in response
                if (jsonResponse.error_key) {
                  mailpn_get_main_message('MAILPN AJAX - Server returned error:', jsonResponse.error_key);
                  // Display the error message if available, otherwise show generic error
                  var errorMessage = jsonResponse.error_ || mailpn_i18n.an_error_has_occurred;
                  mailpn_get_main_message(errorMessage);
                  return;
                }

                // Check for HTML content
                if (jsonResponse.html) {
                  console.log('MAILPN AJAX - HTML content received');
                  popup_element.find('.mailpn-popup-content').html(jsonResponse.html);
                  
                  // Initialize media uploaders if function exists
                  if (typeof initMediaUpload === 'function') {
                    $('.mailpn-image-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'image');
                    });
                    $('.mailpn-audio-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'audio');
                    });
                    $('.mailpn-video-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'video');
                    });
                  }
                } else {
                  console.log('MAILPN AJAX - Response missing HTML content');
                  console.log(mailpn_i18n.an_error_has_occurred);
                }
              } catch (e) {
                console.log('MAILPN AJAX - Failed to parse response:', e);
                console.log('Raw response:', response);
                console.log(mailpn_i18n.an_error_has_occurred);
              }
            },
            error: function(xhr, status, error) {
              console.log('MAILPN AJAX - Request failed:', status, error);
              console.log('Response:', xhr.responseText);
              console.log(mailpn_i18n.an_error_has_occurred);
            }
          });
        },
        afterClose: function() {
          popup_element.find('.mailpn-popup-content').html('<div class="mailpn-loader-circle-wrapper"><div class="mailpn-text-align-center"><div class="mailpn-loader-circle"><div></div><div></div><div></div><div></div></div></div></div>');
        },
      });
    });

    $(document).on('click', '.mailpn-test-email-btn', function(e) {
      e.preventDefault();

      var btn = $(this);
      var loader = btn.siblings('.mailpn-waiting');
      var result = btn.siblings('.mailpn-test-email-result');

      btn.addClass('mailpn-link-disabled');
      loader.removeClass('mailpn-display-none-soft');
      result.html('');

      var ajax_url = mailpn_ajax.ajax_url;
      var data = {
        action: 'mailpn_ajax',
        mailpn_ajax_type: 'mailpn_test_email_send',
        ajax_nonce: mailpn_ajax.ajax_nonce,
      };

      $.post(ajax_url, data, function(response) {
        console.log('response');console.log(response);
        response = JSON.parse(response);

        if (response.error_key == '') {
          mailpn_get_main_message(response.error_content);
          result.html('<span class="mailpn-alert mailpn-alert-success">' + response.error_content + '</span>');
        } else {
          mailpn_get_main_message(response.error_content);
          result.html('<span class="mailpn-alert mailpn-alert-error">' + response.error_content + '</span>');
        }

        btn.removeClass('mailpn-link-disabled');
        loader.addClass('mailpn-display-none-soft');
      });
    });
  });
})(jQuery);
