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
        if (!$('.mailpn-popup-overlay').length) {
          $('body').append('<div class="mailpn-popup-overlay"></div>');
        }
  
        // Show the popup
        popupElement.addClass('mailpn-popup-active').fadeIn('fast');
        $('.mailpn-popup-overlay').fadeIn('fast');
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
        $('.mailpn-popup-overlay').fadeOut('fast');
  
        // Call afterClose callback if exists
        $('.mailpn-popup').each(function() {
          const afterClose = $(this).data('afterClose');
          if (typeof afterClose === 'function') {
            afterClose();
            $(this).removeData('afterClose');
          }
        });
      },

      showSuccessPopup: function(message) {
        var popupId = 'mailpn-success-popup';
        var popupHtml = '<div id="' + popupId + '" class="mailpn-popup mailpn-success-popup">' +
          '<div class="mailpn-popup-content">' +
            '<div class="mailpn-popup-header">' +
              '<h3>Success</h3>' +
            '</div>' +
            '<div class="mailpn-popup-body">' +
              '<p>' + message + '</p>' +
            '</div>' +
            '<div class="mailpn-popup-footer">' +
              '<button type="button" class="mailpn-popup-close">Close</button>' +
            '</div>' +
          '</div>' +
        '</div>';

        // Remove existing popup if any
        $('#' + popupId).remove();
        
        // Add popup to body
        $('body').append(popupHtml);
        
        // Open popup
        this.open(popupId);
        
        // Auto close after 3 seconds
        setTimeout(function() {
          MAILPN_Popups.close();
        }, 3000);
      },

      showErrorPopup: function(message) {
        var popupId = 'mailpn-error-popup';
        var popupHtml = '<div id="' + popupId + '" class="mailpn-popup mailpn-error-popup">' +
          '<div class="mailpn-popup-content">' +
            '<div class="mailpn-popup-header">' +
              '<h3>Error</h3>' +
            '</div>' +
            '<div class="mailpn-popup-body">' +
              '<p>' + message + '</p>' +
            '</div>' +
            '<div class="mailpn-popup-footer">' +
              '<button type="button" class="mailpn-popup-close">Close</button>' +
            '</div>' +
          '</div>' +
        '</div>';

        // Remove existing popup if any
        $('#' + popupId).remove();
        
        // Add popup to body
        $('body').append(popupHtml);
        
        // Open popup
        this.open(popupId);
        
        // Auto close after 5 seconds for errors
        setTimeout(function() {
          MAILPN_Popups.close();
        }, 5000);
      }
    };
  
    // Initialize popup functionality
    $(document).ready(function() {
      // Close popup when clicking overlay
      $(document).on('click', '.mailpn-popup-overlay', function(e) {
        MAILPN_Popups.close();
      });
  
      // Close popup when pressing ESC key
      $(document).on('keyup', function(e) {
        if (e.keyCode === 27) { // ESC key
          MAILPN_Popups.close();
        }
      });
  
      // Close popup when clicking close button
      $(document).on('click', '.mailpn-popup-close, .mailpn-popup-close-wrapper', function(e) {
        e.preventDefault();
        MAILPN_Popups.close();
      });
    });
  })(jQuery); 