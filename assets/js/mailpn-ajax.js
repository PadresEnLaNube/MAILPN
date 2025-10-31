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
        mailpn_ajax_nopriv_nonce: mailpn_ajax.mailpn_ajax_nonce,
        mailpn_ajax_nopriv_type: 'mailpn_form_save',
        mailpn_form_id: mailpn_form.attr('id'),
        mailpn_form_type: mailpn_btn.attr('data-mailpn-type'),
        mailpn_form_subtype: mailpn_btn.attr('data-mailpn-subtype'),
        mailpn_form_user_id: mailpn_btn.attr('data-mailpn-user-id'),
        mailpn_form_post_id: mailpn_btn.attr('data-mailpn-post-id'),
        mailpn_form_post_type: mailpn_form.attr('data-mailpn-post-type'),
        mailpn_ajax_keys: [],
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

        data.mailpn_ajax_keys.push({
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
          $('.mailpn-' + data.mailpn_form_post_type + '[data-mailpn-' + data.mailpn_form_post_type + '-id="' + data.mailpn_form_post_id + '"] .mailpn-check-wrapper i').text('mail_alt');
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
        mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      };

      $.post(ajax_url, data, function(response) {
        response = JSON.parse(response);

        if (response.error_key == '') {
          mailpn_get_main_message(response.error_content);
          result.html('<p class="mailpn-alert mailpn-alert-success">' + response.error_content + '</p>');
        } else {
          mailpn_get_main_message(response.error_content);
          result.html('<p class="mailpn-alert mailpn-alert-error">' + response.error_content + '</p>');
        }

        btn.removeClass('mailpn-link-disabled');
        loader.addClass('mailpn-display-none-soft');
      });
    });
  });
})(jQuery);
