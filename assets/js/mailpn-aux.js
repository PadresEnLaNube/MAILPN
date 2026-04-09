(function($) {
	'use strict';

  $(document).ready(function() {
    if (window.MAILPN_Tooltips) { MAILPN_Tooltips.init(); }

    if ($('.mailpn-select').length && $.fn.MAILPN_Selector) {
      $('.mailpn-select').each(function(index) {
        if ($(this).attr('multiple') == 'true') {
          // For a multiple select
          $(this).MAILPN_Selector({
            multiple: true,
            searchable: true,
            placeholder: typeof mailpn_i18n !== 'undefined' ? mailpn_i18n.select_options : '',
          });
        } else {
          // For a single select
          $(this).MAILPN_Selector();
        }
      });
    }

    if ($.trumbowyg && typeof mailpn_trumbowyg !== 'undefined' && $('.mailpn-wysiwyg').length) {
      $.trumbowyg.svgPath = mailpn_trumbowyg.path;
      $('.mailpn-wysiwyg').each(function(index, element) {
        $(this).trumbowyg();
      });
    }
  });
})(jQuery);
