(function($) {
    'use strict';
  
    window.MAILPN_Popups = {
      open: function(popup, options = {}) {
        var popupElement = typeof popup === 'string' ? $('#' + popup) : popup;
        
        if (!popupElement.length) {
          return;
        }
  
        if (typeof options.beforeShow === 'function') {
          options.beforeShow();
        }
  
        // Show overlay
        if (!$('.mailpn-menu-more-overlay').length) {
          $('body').append('<div class="mailpn-menu-more-overlay"></div>');
        }
  
        // Show the popup
        popupElement.addClass('mailpn-popup-active').fadeIn('fast');
        $('.mailpn-menu-more-overlay').fadeIn('fast');
        document.body.classList.add('mailpn-popup-open');
  
        // Add close button if not present
        if (!popupElement.find('.mailpn-popup-close').length) {
          var closeButton = $('<button class="mailpn-popup-close-wrapper"><i class="material-icons-outlined">close</i></button>');
          closeButton.on('click', function() {
            MAILPN_Popups.close();
          });
          popupElement.append(closeButton);
        }
  
        // Store and call callbacks if provided
        if (options.beforeShow) {
          popupElement.data('beforeShow', options.beforeShow);
        }
        if (options.afterClose) {
          popupElement.data('afterClose', options.afterClose);
        }
      },
  
      close: function() {
        // Hide all popups
        $('.mailpn-popup').fadeOut('fast');
  
        // Hide overlay
        $('.mailpn-menu-more-overlay').fadeOut('fast');
  
        // Call afterClose callback if exists
        $('.mailpn-popup').each(function() {
          const afterClose = $(this).data('afterClose');
          if (typeof afterClose === 'function') {
            afterClose();
            $(this).removeData('afterClose');
          }
        });
      }
    };
  
    // Initialize popup functionality
    $(document).ready(function() {
      // Close popup when clicking overlay
      $(document).on('click', '.mailpn-menu-more-overlay', function(e) {
        MAILPN_Popups.close();
      });
  
      // Close popup when pressing ESC key
      $(document).on('keyup', function(e) {
        if (e.keyCode === 27) { // ESC key
          MAILPN_Popups.close();
        }
      });
  
      // Close popup when clicking close button
      $(document).on('click', '.mailpn-popup-close', function(e) {
        e.preventDefault();
        MAILPN_Popups.close();
      });
    });
  })(jQuery); 