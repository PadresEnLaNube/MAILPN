(function () {
  'use strict';
  var saveBtn = document.getElementById('mailpn-settings-save');
  var exportBtn = document.getElementById('mailpn-settings-export');
  var importBtn = document.getElementById('mailpn-settings-import');
  var fileInput = document.getElementById('mailpn-settings-import-file');
  if (!saveBtn) return;

  var menuToggle = document.getElementById('wp-admin-bar-menu-toggle');
  var footer = document.getElementById('mailpn-settings-footer');
  if (menuToggle && footer) {
    menuToggle.addEventListener('click', function () {
      setTimeout(function () {
        footer.style.display = document.body.classList.contains('wp-responsive-open') ? 'none' : '';
      }, 0);
    });
  }

  saveBtn.addEventListener('click', function () {
    var form = document.getElementById('mailpn_form');
    if (form) form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
  });

  exportBtn.addEventListener('click', function () {
    var fd = new FormData();
    fd.append('action', 'mailpn_ajax');
    fd.append('mailpn_ajax_type', 'mailpn_settings_export');
    fd.append('mailpn_ajax_nonce', mailpnSettingsFooter.nonce);
    fetch(mailpnSettingsFooter.ajaxUrl, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.error_key) { if (typeof mailpn_get_main_message === 'function') mailpn_get_main_message(mailpnSettingsFooter.i18n.exportError, 'red'); return; }
        var blob = new Blob([JSON.stringify(res.settings, null, 2)], { type: 'application/json' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'mailpn-settings-' + new Date().toISOString().slice(0, 10) + '.json';
        document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
      })
      .catch(function () { if (typeof mailpn_get_main_message === 'function') mailpn_get_main_message(mailpnSettingsFooter.i18n.exportError, 'red'); });
  });

  importBtn.addEventListener('click', function () { fileInput.value = ''; fileInput.click(); });

  fileInput.addEventListener('change', function () {
    var file = fileInput.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function (e) {
      var data;
      try { data = JSON.parse(e.target.result); } catch (err) { if (typeof mailpn_get_main_message === 'function') mailpn_get_main_message(mailpnSettingsFooter.i18n.invalidFile, 'red'); return; }
      if (!confirm(mailpnSettingsFooter.i18n.confirmImport)) return;
      var fd = new FormData();
      fd.append('action', 'mailpn_ajax');
      fd.append('mailpn_ajax_type', 'mailpn_settings_import');
      fd.append('mailpn_ajax_nonce', mailpnSettingsFooter.nonce);
      fd.append('settings', JSON.stringify(data));
      fetch(mailpnSettingsFooter.ajaxUrl, { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res.error_key) { if (typeof mailpn_get_main_message === 'function') mailpn_get_main_message(res.error_content || mailpnSettingsFooter.i18n.importError, 'red'); return; }
          if (typeof mailpn_get_main_message === 'function') mailpn_get_main_message(mailpnSettingsFooter.i18n.importSuccess, 'green');
          setTimeout(function () { location.reload(); }, 1500);
        })
        .catch(function () { if (typeof mailpn_get_main_message === 'function') mailpn_get_main_message(mailpnSettingsFooter.i18n.importError, 'red'); });
    };
    reader.readAsText(file);
  });

  // ── Recommended plugins ──────────────────────────────────────

  var rpBtn   = document.getElementById('mailpn-settings-recommended');
  var rpPopup = document.getElementById('mailpn-recommended-plugins');

  if (rpBtn && rpPopup) {
    // Open popup
    rpBtn.addEventListener('click', function () {
      if (window.MAILPN_Popups) {
        MAILPN_Popups.open('mailpn-recommended-plugins');
      }
    });

    // Event delegation for install / activate buttons inside the popup
    rpPopup.addEventListener('click', function (e) {
      var installBtn  = e.target.closest('.pn-cm-rp-install');
      var activateBtn = e.target.closest('.pn-cm-rp-activate');

      if (installBtn)  handleRpInstall(installBtn);
      if (activateBtn) handleRpActivate(activateBtn);
    });
  }

  function handleRpInstall(btn) {
    var slug      = btn.getAttribute('data-slug');
    var card      = btn.closest('.pn-cm-rp-card');
    var actionDiv = card.querySelector('.pn-cm-rp-action');
    var i18n      = mailpnSettingsFooter.i18n;

    btn.disabled    = true;
    btn.textContent = i18n.installing;

    var fd = new FormData();
    fd.append('action', 'mailpn_ajax');
    fd.append('mailpn_ajax_type', 'mailpn_install_plugin');
    fd.append('mailpn_ajax_nonce', mailpnSettingsFooter.nonce);
    fd.append('slug', slug);

    fetch(mailpnSettingsFooter.ajaxUrl, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.error_key) {
          btn.disabled    = false;
          btn.textContent = 'Install';
          if (typeof mailpn_get_main_message === 'function') {
            mailpn_get_main_message(res.error_content || i18n.installError, 'red');
          }
          return;
        }

        // Replace with Activate button
        actionDiv.innerHTML = '<button type="button" class="mailpn-btn mailpn-btn-mini mailpn-btn-transparent pn-cm-rp-activate" data-slug="' + slug + '">' + i18n.activate + '</button>';

        // Update badge
        updateRpBadge(-1);
      })
      .catch(function () {
        btn.disabled    = false;
        btn.textContent = 'Install';
        if (typeof mailpn_get_main_message === 'function') {
          mailpn_get_main_message(i18n.installError, 'red');
        }
      });
  }

  function handleRpActivate(btn) {
    var slug      = btn.getAttribute('data-slug');
    var card      = btn.closest('.pn-cm-rp-card');
    var actionDiv = card.querySelector('.pn-cm-rp-action');
    var i18n      = mailpnSettingsFooter.i18n;

    btn.disabled    = true;
    btn.textContent = i18n.activating;

    var fd = new FormData();
    fd.append('action', 'mailpn_ajax');
    fd.append('mailpn_ajax_type', 'mailpn_activate_plugin');
    fd.append('mailpn_ajax_nonce', mailpnSettingsFooter.nonce);
    fd.append('slug', slug);

    fetch(mailpnSettingsFooter.ajaxUrl, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.error_key) {
          btn.disabled    = false;
          btn.textContent = i18n.activate;
          if (typeof mailpn_get_main_message === 'function') {
            mailpn_get_main_message(res.error_content || i18n.activateError, 'red');
          }
          return;
        }

        // Show Active badge
        actionDiv.innerHTML = '<span class="pn-cm-rp-active-badge">' + i18n.active + '</span>';

        // Open settings page in new tab
        var settingsUrl = (mailpnSettingsFooter.settingsPages || {})[slug];
        if (settingsUrl) {
          window.open(settingsUrl, '_blank');
        }
      })
      .catch(function () {
        btn.disabled    = false;
        btn.textContent = i18n.activate;
        if (typeof mailpn_get_main_message === 'function') {
          mailpn_get_main_message(i18n.activateError, 'red');
        }
      });
  }

  function updateRpBadge(delta) {
    var badge = document.querySelector('.pn-cm-rp-badge');
    if (!badge) return;
    var count = parseInt(badge.textContent, 10) + delta;
    if (count <= 0) {
      badge.remove();
    } else {
      badge.textContent = count;
    }
  }

  // ── Design Preview Live Update ──────────────────────────────────────

  var previewMode = 'desktop'; // desktop or mobile
  var previewContent = document.getElementById('mailpn-design-preview-content');
  var previewH1 = document.getElementById('preview-h1');
  var previewH2 = document.getElementById('preview-h2');
  var previewH3 = document.getElementById('preview-h3');
  var previewParagraph = document.getElementById('preview-paragraph');
  var previewParagraph2 = document.getElementById('preview-paragraph-2');
  var previewList = document.getElementById('preview-list');
  var previewButton = document.getElementById('preview-button');
  var previewFooter = document.getElementById('preview-footer');
  var desktopBtn = document.getElementById('mailpn-preview-desktop');
  var mobileBtn = document.getElementById('mailpn-preview-mobile');

  if (previewContent && previewH1 && previewButton) {
    // Design settings inputs
    var fontFamilyInput = document.getElementById('mailpn_font_family');
    var fontSizeDesktopInput = document.getElementById('mailpn_font_size_desktop');
    var fontSizeMobileInput = document.getElementById('mailpn_font_size_mobile');
    var headingH1Input = document.getElementById('mailpn_heading_size_h1');
    var headingH2Input = document.getElementById('mailpn_heading_size_h2');
    var headingH3Input = document.getElementById('mailpn_heading_size_h3');
    var lineHeightInput = document.getElementById('mailpn_line_height');
    var bgColorInput = document.getElementById('mailpn_background_color');
    var textColorInput = document.getElementById('mailpn_text_color');
    var buttonBgInput = document.getElementById('mailpn_button_bg_color');
    var buttonTextInput = document.getElementById('mailpn_button_text_color');
    var buttonRadiusInput = document.getElementById('mailpn_button_border_radius');
    var footerBgInput = document.getElementById('mailpn_footer_bg_color');
    var footerTextInput = document.getElementById('mailpn_footer_text_color');

    // Preview mode toggle
    if (desktopBtn && mobileBtn) {
      desktopBtn.addEventListener('click', function(e) {
        e.preventDefault();
        previewMode = 'desktop';
        desktopBtn.classList.add('active');
        mobileBtn.classList.remove('active');
        updatePreview();
      });

      mobileBtn.addEventListener('click', function(e) {
        e.preventDefault();
        previewMode = 'mobile';
        mobileBtn.classList.add('active');
        desktopBtn.classList.remove('active');
        updatePreview();
      });
    }

    function updatePreview() {
      // Get current values
      var fontFamily = fontFamilyInput ? fontFamilyInput.value : 'Arial, sans-serif';
      var fontSize = previewMode === 'mobile'
        ? (fontSizeMobileInput ? fontSizeMobileInput.value : '16')
        : (fontSizeDesktopInput ? fontSizeDesktopInput.value : '14');
      var h1Size = headingH1Input ? headingH1Input.value : '26';
      var h2Size = headingH2Input ? headingH2Input.value : '22';
      var h3Size = headingH3Input ? headingH3Input.value : '20';
      var lineHeight = lineHeightInput ? lineHeightInput.value : '1.6';
      var bgColor = bgColorInput ? bgColorInput.value : '#ffffff';
      var textColor = textColorInput ? textColorInput.value : '#333333';
      var buttonBg = buttonBgInput ? buttonBgInput.value : '#ffffff';
      var buttonText = buttonTextInput ? buttonTextInput.value : '#ffffff';
      var buttonRadius = buttonRadiusInput ? buttonRadiusInput.value : '4';
      var footerBg = footerBgInput ? footerBgInput.value : '#ffffff';
      var footerText = footerTextInput ? footerTextInput.value : '#6c757d';

      // Apply to preview container
      previewContent.style.fontFamily = fontFamily;
      previewContent.style.backgroundColor = bgColor;
      previewContent.style.color = textColor;
      previewContent.style.fontSize = fontSize + 'px';
      previewContent.style.lineHeight = lineHeight;

      // Apply to headings (mobile sizes)
      if (previewH1) {
        previewH1.style.fontSize = h1Size + 'px';
        previewH1.style.fontFamily = fontFamily;
        previewH1.style.color = textColor;
        previewH1.style.lineHeight = '1.3';
      }
      if (previewH2) {
        previewH2.style.fontSize = h2Size + 'px';
        previewH2.style.fontFamily = fontFamily;
        previewH2.style.color = textColor;
        previewH2.style.lineHeight = '1.3';
      }
      if (previewH3) {
        previewH3.style.fontSize = h3Size + 'px';
        previewH3.style.fontFamily = fontFamily;
        previewH3.style.color = textColor;
        previewH3.style.lineHeight = '1.3';
      }

      // Apply to paragraphs
      if (previewParagraph) {
        previewParagraph.style.fontSize = fontSize + 'px';
        previewParagraph.style.fontFamily = fontFamily;
        previewParagraph.style.color = textColor;
        previewParagraph.style.lineHeight = lineHeight;
      }
      if (previewParagraph2) {
        previewParagraph2.style.fontSize = fontSize + 'px';
        previewParagraph2.style.fontFamily = fontFamily;
        previewParagraph2.style.color = textColor;
        previewParagraph2.style.lineHeight = lineHeight;
      }

      // Apply to list
      if (previewList) {
        previewList.style.fontSize = fontSize + 'px';
        previewList.style.fontFamily = fontFamily;
        previewList.style.color = textColor;
        previewList.style.lineHeight = lineHeight;
      }

      // Apply to button
      if (previewButton) {
        previewButton.style.backgroundColor = buttonBg;
        previewButton.style.color = buttonText;
        previewButton.style.borderRadius = buttonRadius + 'px';
        previewButton.style.fontFamily = fontFamily;
        previewButton.style.fontSize = fontSize + 'px';
      }

      // Apply to footer
      if (previewFooter) {
        previewFooter.style.backgroundColor = footerBg;
        previewFooter.style.color = footerText;
        previewFooter.style.fontFamily = fontFamily;
        var footerSmall = previewFooter.querySelector('small');
        if (footerSmall) {
          footerSmall.style.color = footerText;
        }
      }
    }

    // Add event listeners to all inputs
    var inputs = [
      fontFamilyInput, fontSizeDesktopInput, fontSizeMobileInput,
      headingH1Input, headingH2Input, headingH3Input, lineHeightInput,
      bgColorInput, textColorInput, buttonBgInput, buttonTextInput,
      buttonRadiusInput, footerBgInput, footerTextInput
    ];

    inputs.forEach(function(input) {
      if (input) {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
      }
    });

    // Initial update
    setTimeout(updatePreview, 100);
  }

  // ── Font Family Selector Style ──────────────────────────────────────
  var fontFamilySelect = document.getElementById('mailpn_font_family');
  if (fontFamilySelect) {
    function updateFontFamilySelectStyle() {
      var selectedFont = fontFamilySelect.value || 'Arial, sans-serif';
      fontFamilySelect.style.fontFamily = selectedFont;
    }

    fontFamilySelect.addEventListener('change', updateFontFamilySelectStyle);
    // Initial style
    updateFontFamilySelectStyle();
  }
})();
