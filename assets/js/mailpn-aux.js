(function($) {
	'use strict';

  $(document).ready(function() {
    if($('.mailpn-tooltip').length) {
      $('.mailpn-tooltip').tooltipster({maxWidth: 300, delayTouch:[0, 4000], customClass: 'mailpn-tooltip'});
    }

    if ($('.mailpn-select').length) {
      $('.mailpn-select').each(function(index) {
        if ($(this).attr('multiple') == 'true') {
          // For a multiple select
          $(this).MAILPN_Selector({
            multiple: true,
            searchable: true,
            placeholder: mailpn_i18n.select_options,
          });
        } else {
          // For a single select
          $(this).MAILPN_Selector();
        }
      });
    }

    $.trumbowyg.svgPath = mailpn_trumbowyg.path;
    $('.mailpn-wysiwyg').each(function(index, element) {
      $(this).trumbowyg();
    });
  });
})(jQuery);
