(function($) {
	'use strict';

  $(document).ready(function() {
    if($('.mailpn-tooltip').length) {
      $('.mailpn-tooltip').tooltipster({maxWidth: 300, delayTouch:[0, 4000], customClass: 'mailpn-tooltip'});
    }

    if ($('.mailpn-select').length) {
      $('.mailpn-select').each(function(index) {
        if ($(this).children('option').length < 7) {
          $(this).select2({minimumResultsForSearch: -1, allowClear: true});
        }else{
          $(this).select2({allowClear: true});
        }
      });
    }

    $.trumbowyg.svgPath = mailpn_trumbowyg.path;
    $('.mailpn-wysiwyg').each(function(index, element) {
      $(this).trumbowyg();
    });
  });
})(jQuery);
