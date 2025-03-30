(function($) {
	'use strict';

	$(document).on('click', '.wph-tab-links', function(e){
    e.preventDefault();
    var tab_link = $(this);
    var tab_wrapper = $(this).closest('.wph-tabs-wrapper');
    
    tab_wrapper.find('.wph-tab-links').each(function(index, element) {
      $(this).removeClass('active');
      $($(this).attr('data-wph-id')).addClass('wph-display-none');
    });

    tab_wrapper.find('.wph-tab-content').each(function(index, element) {
      $(this).addClass('wph-display-none');
    });
    
    tab_link.addClass('active');
    tab_wrapper.find('#' + tab_link.attr('data-wph-id')).removeClass('wph-display-none');
  });

  $(document).on('click', '.mailpn-options-save-btn', function(e){
    e.preventDefault();
    var mailpn_btn = $(this);
    mailpn_btn.addClass('mailpn-link-disabled').siblings('.mailpn-waiting').removeClass('mailpn-display-none');

    var ajax_url = mailpn_ajax.ajax_url;

    var data = {
      action: 'mailpn_ajax',
      ajax_nonce: mailpn_ajax.ajax_nonce,
      mailpn_ajax_type: 'mailpn_options_save',
      ajax_keys: [],
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

      data.ajax_keys.push({
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
      ajax_nonce: mailpn_ajax.ajax_nonce,
      mailpn_ajax_type: 'mailpn_resend_errors',
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