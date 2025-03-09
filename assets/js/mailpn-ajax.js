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
          if (!(typeof window['mailpn_window_vars']['form_field_' + element.id] !== 'undefined')) {
            window['mailpn_window_vars']['form_field_' + element.id] = [];
          }

          window['mailpn_window_vars']['form_field_' + element.id].push($(element).val());

          data[element.id] = window['mailpn_window_vars']['form_field_' + element.id];
        }else{
          if ($(this).is(':checkbox') || $(this).is(':radio')) {
            if ($(this).is(':checked')) {
              data[element.id] = $(element).val();
            }else{
              data[element.id] = '';
            }
          }else{
            data[element.id] = $(element).val();
          }
        }

        data.ajax_keys.push({
          id: element.id,
          node: element.nodeName,
          type: element.type,
        });
      });

      $.post(ajax_url, data, function(response) {
        console.log(data);console.log(response);
        if ($.parseJSON(response)['error_key'] == 'mailpn_form_save_error_unlogged') {
          mailpn_get_main_message(mailpn_i18n.user_unlogged);

          if (!$('.userswph-profile-wrapper .user-unlogged').length) {
            $('.userswph-profile-wrapper').prepend('<div class="userswph-alert userswph-alert-warning user-unlogged">' + mailpn_i18n.user_unlogged + '</div>');
          }

          $.fancybox.open($('#userswph-profile-popup'), {touch: false});
          $('#userswph-login input#user_login').focus();
        }else if ($.parseJSON(response)['error_key'] == 'mailpn_form_save_error') {
          mailpn_get_main_message(mailpn_i18n.an_error_has_occurred);
        }else {
          mailpn_get_main_message(mailpn_i18n.saved_successfully);
        }

        if ($.parseJSON(response)['update'] != '') {
          $('.mailpn-' + data.mailpn_form_post_type + '-list').html($.parseJSON(response)['update_html']);
        }

        if ($.parseJSON(response)['popup'] == 'close') {
          $.fancybox.close(true);
          $('.mailpn-menu-more-overlay').fadeOut('fast');
        }

        if ($.parseJSON(response)['check'] == 'post_check') {
          $.fancybox.close(true);
          $('.mailpn-menu-more-overlay').fadeOut('fast');
          $('.mailpn-' + data.mailpn_form_post_type + '[data-mailpn-' + data.mailpn_form_post_type + '-id="' + data.mailpn_form_post_id + '"] .mailpn-check-wrapper i').text('mail_alt');
        }else if ($.parseJSON(response)['check'] == 'post_uncheck') {
          $.fancybox.close(true);
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
      var mail_id = mailpn_btn.closest('.mailpn-mail').attr('data-mailpn-mail-id');
      var popup_element = $('#' + mailpn_btn.attr('data-mailpn-popup-id'));

      $.fancybox.open(popup_element, {
        touch: false,
        beforeShow: function(instance, current, e) {
          var ajax_url = mailpn_ajax.ajax_url;
          var data = {
            action: 'mailpn_ajax',
            mailpn_ajax_type: mailpn_ajax_type,
            mail_id: mail_id,
          };

          $.post(ajax_url, data, function(response) {
            console.log('data');console.log(data);
            if ($.parseJSON(response)['error_key'] != '') {
              mailpn_get_main_message($.parseJSON(response)['error']);
            }else{
              popup_element.find('.mailpn-popup-content').html($.parseJSON(response)['html']);
            }
          });
        },
        afterClose: function(instance, current, e) {
         popup_element.find('.mailpn-popup-content').html('<div class="mailpn-loader-circle-wrapper"><div class="mailpn-text-align-center"><div class="mailpn-loader-circle"><div></div><div></div><div></div><div></div></div></div></div>');
        },
      },);
    });

    $(document).on('click', '.mailpn-duplicate-post', function(e) {
      e.preventDefault();

      $('.mailpn-mails').fadeOut('fast');
      var mailpn_btn = $(this);
      var mail_id = mailpn_btn.closest('.mailpn-mail').attr('data-mailpn-mail-id');

      var ajax_url = mailpn_ajax.ajax_url;
      var data = {
        action: 'mailpn_ajax',
        mailpn_ajax_type: 'mailpn_mail_duplicate',
        mail_id: mail_id,
      };

      $.post(ajax_url, data, function(response) {
        console.log('data');console.log(data);console.log('response');console.log(response);
        if ($.parseJSON(response)['error_key'] != '') {
          mailpn_get_main_message($.parseJSON(response)['error']);
        }else{
          $('.mailpn-mails').html($.parseJSON(response)['html']);
        }
        
        $('.mailpn-mails').fadeIn('slow');
        $('.mailpn-menu-more-overlay').fadeOut('fast');
      });
    });

    $(document).on('click', '.mailpn-remove-post', function(e) {
      e.preventDefault();

      $('.mailpn-mails').fadeOut('fast');
      var mail_id = $('.mailpn-menu-more.mailpn-active').closest('.mailpn-mail').attr('data-mailpn-mail-id');

      var ajax_url = mailpn_ajax.ajax_url;
      var data = {
        action: 'mailpn_ajax',
        mailpn_ajax_type: 'mailpn_mail_remove',
        mail_id: mail_id,
      };

      $.post(ajax_url, data, function(response) {
        console.log('data');console.log(data);console.log('response');console.log(response);
        if ($.parseJSON(response)['error_key'] != '') {
          mailpn_get_main_message($.parseJSON(response)['error']);
        }else{
          $('.mailpn-mails').html($.parseJSON(response)['html']);
        }
        
        $('.mailpn-mails').fadeIn('slow');
        $('.mailpn-menu-more-overlay').fadeOut('fast');
        $.fancybox.close();
      });
    });
  });
})(jQuery);
