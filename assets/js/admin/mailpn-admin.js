(function($) {
	'use strict';

	$(document).on('click', '.mailpn-tab-links', function(e){
    e.preventDefault();
    var tab_link = $(this);
    var tab_wrapper = $(this).closest('.mailpn-tabs-wrapper');
    
    tab_wrapper.find('.mailpn-tab-links').each(function(index, element) {
      $(this).removeClass('active');
      $($(this).attr('data-mailpn-id')).addClass('mailpn-display-none');
    });

    tab_wrapper.find('.mailpn-tab-content').each(function(index, element) {
      $(this).addClass('mailpn-display-none');
    });
    
    tab_link.addClass('active');
    tab_wrapper.find('#' + tab_link.attr('data-mailpn-id')).removeClass('mailpn-display-none');
  });

  $(document).on('click', '.mailpn-options-save-btn', function(e){
    e.preventDefault();
    var mailpn_btn = $(this);
    mailpn_btn.addClass('mailpn-link-disabled').siblings('.mailpn-waiting').removeClass('mailpn-display-none');

    var ajax_url = mailpn_ajax.ajax_url;

    var data = {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_options_save',
      mailpn_ajax_nonce: mailpn_ajax.mailpn_ajax_nonce,
      mailpn_ajax_keys: [],
    };

    if (!(typeof window['mailpn_window_vars'] !== 'undefined')) {
      window['mailpn_window_vars'] = [];
    }

    $('.mailpn-options-fields input:not([type="submit"]), .mailpn-options-fields select, .mailpn-options-fields textarea').each(function(index, element) {
      if ($(this).attr('multiple') && $(this).parents('.mailpn-html-multi-group').length) {
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

      data.mailpn_ajax_keys.push({
        id: element.id,
        node: element.nodeName,
        type: element.type,
      });
    });

    $.post(ajax_url, data, function(response) {
      console.log(data);console.log(response);
      if ($.parseJSON(response)['error_key'] != '') {
        mailpn_get_main_message(mailpn_i18n.an_error_has_occurred);
      }else {
        mailpn_get_main_message(mailpn_i18n.saved_successfully);
      }

      mailpn_btn.removeClass('mailpn-link-disabled').siblings('.mailpn-waiting').addClass('mailpn-display-none')
    });

    delete window['mailpn_window_vars'];
  });

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
      console.log(data);console.log(response);
      if ($.parseJSON(response)['error_key'] == 'mailpn_resend_errors_error') {
        mailpn_get_main_message($.parseJSON(response)['error_content']);
      }else {
        location.reload();
        mailpn_get_main_message(mailpn_i18n.saved_successfully);
      }

      mailpn_btn.removeClass('mailpn-link-disabled').siblings('.mailpn-waiting').addClass('mailpn-display-none')
    });
  });
})(jQuery);