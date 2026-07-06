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

  // User Notifications Manager
  var userSearchBtn = document.getElementById('mailpn-user-search-btn');
  var userSearchInput = document.getElementById('mailpn-user-search-input');
  var userNotificationsResults = document.getElementById('mailpn-user-notifications-results');
  var userNotificationsWaiting = document.querySelector('.mailpn-user-notifications-waiting');

  if (userSearchBtn && userSearchInput && userNotificationsResults) {
    // Search users
    userSearchBtn.addEventListener('click', function () {
      var searchTerm = userSearchInput.value.trim();
      if (searchTerm.length < 2) {
        if (typeof mailpn_get_main_message === 'function') {
          mailpn_get_main_message('Please enter at least 2 characters', 'red');
        }
        return;
      }

      userNotificationsWaiting.style.display = 'block';
      userNotificationsResults.innerHTML = '';

      var fd = new FormData();
      fd.append('action', 'mailpn_ajax');
      fd.append('mailpn_ajax_type', 'mailpn_search_users_notifications');
      fd.append('mailpn_ajax_nonce', mailpnSettingsFooter.nonce);
      fd.append('search_term', searchTerm);

      fetch(mailpnSettingsFooter.ajaxUrl, { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          userNotificationsWaiting.style.display = 'none';

          if (res.error_key) {
            if (typeof mailpn_get_main_message === 'function') {
              mailpn_get_main_message(res.error_content || 'Search error', 'red');
            }
            return;
          }

          if (!res.users || res.users.length === 0) {
            userNotificationsResults.innerHTML = '<div class="mailpn-info-box mailpn-info-box-orange"><p>No users found with that search term.</p></div>';
            return;
          }

          var html = '<div class="mailpn-user-notifications-list"><table class="mailpn-table mailpn-width-100-percent"><thead><tr>';
          html += '<th>User</th><th>Email</th><th>Notifications</th><th>Actions</th></tr></thead><tbody>';

          res.users.forEach(function (user) {
            var statusClass = user.notifications_active ? 'mailpn-badge-green' : 'mailpn-badge-red';
            var statusText = user.notifications_active ? 'Enabled' : 'Disabled';
            var toggleIcon = user.notifications_active ? 'notifications_off' : 'notifications_active';
            var toggleTooltip = user.notifications_active ? 'Disable notifications' : 'Enable notifications';
            var toggleIconClass = user.notifications_active ? 'mailpn-icon-red' : 'mailpn-icon-green';
            var toggleNewStatus = user.notifications_active ? 'off' : 'on';

            html += '<tr>';
            html += '<td>' + user.display_name + '</td>';
            html += '<td>' + user.email + '</td>';
            html += '<td><span class="mailpn-badge ' + statusClass + '">' + statusText + '</span></td>';
            html += '<td class="mailpn-user-actions">';

            // Edit user
            html += '<a href="' + mailpnSettingsFooter.adminUrl + 'user-edit.php?user_id=' + user.id + '" target="_blank" class="mailpn-icon-btn mailpn-tooltip" title="Edit user">';
            html += '<i class="material-icons-outlined">edit</i>';
            html += '</a>';

            // Toggle notifications
            html += '<button type="button" class="mailpn-icon-btn ' + toggleIconClass + ' mailpn-toggle-user-notifications mailpn-tooltip" title="' + toggleTooltip + '" data-user-id="' + user.id + '" data-new-status="' + toggleNewStatus + '">';
            html += '<i class="material-icons-outlined">' + toggleIcon + '</i>';
            html += '</button>';

            // History
            html += '<button type="button" class="mailpn-icon-btn mailpn-view-user-stats mailpn-tooltip" title="View history and statistics" data-user-id="' + user.id + '" data-user-name="' + user.display_name + '" data-user-email="' + user.email + '">';
            html += '<i class="material-icons-outlined">bar_chart</i>';
            html += '</button>';

            // Autologin (only if userspn is active)
            if (res.userspn_active && user.autologin_link) {
              html += '<a href="' + user.autologin_link + '" target="_blank" rel="noopener noreferrer" class="mailpn-icon-btn mailpn-icon-purple mailpn-tooltip" title="User autologin">';
              html += '<i class="material-icons-outlined">login</i>';
              html += '</a>';
            }

            html += '</td>';
            html += '</tr>';
          });

          html += '</tbody></table></div>';
          userNotificationsResults.innerHTML = html;

          // Reinitialize tooltips
          if (typeof MAILPN_Tooltips !== 'undefined' && MAILPN_Tooltips.init) {
            MAILPN_Tooltips.init();
          }
        })
        .catch(function () {
          userNotificationsWaiting.style.display = 'none';
          if (typeof mailpn_get_main_message === 'function') {
            mailpn_get_main_message('Error searching users', 'red');
          }
        });
    });

    // Enter key on search input
    userSearchInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        userSearchBtn.click();
      }
    });

    // Toggle user notifications
    document.addEventListener('click', function (e) {
      if (e.target.closest('.mailpn-toggle-user-notifications')) {
        var btn = e.target.closest('.mailpn-toggle-user-notifications');
        var userId = btn.getAttribute('data-user-id');
        var newStatus = btn.getAttribute('data-new-status');

        btn.disabled = true;

        var fd = new FormData();
        fd.append('action', 'mailpn_ajax');
        fd.append('mailpn_ajax_type', 'mailpn_toggle_user_notifications');
        fd.append('mailpn_ajax_nonce', mailpnSettingsFooter.nonce);
        fd.append('user_id', userId);
        fd.append('new_status', newStatus);

        fetch(mailpnSettingsFooter.ajaxUrl, { method: 'POST', body: fd })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            btn.disabled = false;

            if (res.error_key) {
              if (typeof mailpn_get_main_message === 'function') {
                mailpn_get_main_message(res.error_content || 'Error updating notifications', 'red');
              }
              return;
            }

            if (typeof mailpn_get_main_message === 'function') {
              mailpn_get_main_message('Notifications updated successfully', 'green');
            }

            // Refresh the search
            userSearchBtn.click();
          })
          .catch(function () {
            btn.disabled = false;
            if (typeof mailpn_get_main_message === 'function') {
              mailpn_get_main_message('Error updating notifications', 'red');
            }
          });
      }

      // View user stats
      if (e.target.closest('.mailpn-view-user-stats')) {
        var btn = e.target.closest('.mailpn-view-user-stats');
        var userId = btn.getAttribute('data-user-id');
        var userName = btn.getAttribute('data-user-name');
        var userEmail = btn.getAttribute('data-user-email');

        var popupId = 'mailpn-user-stats-popup-' + userId;

        // Remove existing popup
        var existingPopup = document.getElementById(popupId);
        if (existingPopup) {
          existingPopup.remove();
        }

        // Create popup with loading state
        var popupHtml = '<div id="' + popupId + '" class="mailpn-popup mailpn-popup-size-large mailpn-user-stats-popup">';
        popupHtml += '<div class="mailpn-popup-content">';
        popupHtml += '<button type="button" class="mailpn-popup-close-wrapper"><i class="material-icons-outlined">close</i></button>';
        popupHtml += '<div class="mailpn-popup-body mailpn-popup-loading">';
        popupHtml += '<div class="mailpn-loader-circle-waiting"><div></div><div></div><div></div><div></div></div>';
        popupHtml += '<p class="mailpn-popup-loading-text">Loading statistics...</p>';
        popupHtml += '</div>';
        popupHtml += '</div>';
        popupHtml += '</div>';

        document.body.insertAdjacentHTML('beforeend', popupHtml);

        if (typeof MAILPN_Popups !== 'undefined' && MAILPN_Popups.open) {
          MAILPN_Popups.open(popupId);
        }

        // Fetch stats
        var fd = new FormData();
        fd.append('action', 'mailpn_ajax');
        fd.append('mailpn_ajax_type', 'mailpn_get_user_notification_stats');
        fd.append('mailpn_ajax_nonce', mailpnSettingsFooter.nonce);
        fd.append('user_id', userId);

        fetch(mailpnSettingsFooter.ajaxUrl, { method: 'POST', body: fd })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            if (res.error_key) {
              document.getElementById(popupId).querySelector('.mailpn-popup-body').innerHTML =
                '<div class="mailpn-info-box mailpn-info-box-red"><p>' + (res.error_content || 'Error loading statistics') + '</p></div>';
              return;
            }

            var stats = res.stats;
            var history = res.history;
            var openRate = stats.total_sent > 0 ? ((stats.total_opened / stats.total_sent) * 100).toFixed(1) : 0;
            var clickRate = stats.total_sent > 0 ? ((stats.total_clicked / stats.total_sent) * 100).toFixed(1) : 0;

            var contentHtml = '<div class="mailpn-popup-header"><h3>Statistics for ' + userName + '</h3><p>' + userEmail + '</p></div>';
            contentHtml += '<div class="mailpn-popup-body">';

            // Stats summary with new design
            contentHtml += '<div class="mailpn-stats-summary">';
            contentHtml += '<div class="mailpn-stat-card">';
            contentHtml += '<div class="mailpn-stat-value mailpn-color-primary">' + stats.total_sent + '</div>';
            contentHtml += '<div class="mailpn-stat-label">Total Sent</div>';
            contentHtml += '</div>';
            contentHtml += '<div class="mailpn-stat-card">';
            contentHtml += '<div class="mailpn-stat-value mailpn-color-green">' + stats.total_opened + '</div>';
            contentHtml += '<div class="mailpn-stat-label">Opened</div>';
            contentHtml += '<div class="mailpn-stat-sublabel">' + openRate + '% open rate</div>';
            contentHtml += '</div>';
            contentHtml += '<div class="mailpn-stat-card">';
            contentHtml += '<div class="mailpn-stat-value mailpn-color-blue">' + stats.total_clicked + '</div>';
            contentHtml += '<div class="mailpn-stat-label">With Clicks</div>';
            contentHtml += '<div class="mailpn-stat-sublabel">' + clickRate + '% click rate</div>';
            contentHtml += '</div>';
            contentHtml += '<div class="mailpn-stat-card">';
            contentHtml += '<div class="mailpn-stat-label">Last Sent</div>';
            contentHtml += '<div class="mailpn-stat-sublabel mailpn-stat-last-sent">' + (stats.last_sent || 'N/A') + '</div>';
            contentHtml += '</div>';
            contentHtml += '</div>';

            // History table
            contentHtml += '<div class="mailpn-history-section">';
            contentHtml += '<h4>Sending History (last 50)</h4>';
            if (history.length === 0) {
              contentHtml += '<div class="mailpn-info-box mailpn-info-box-orange"><p>No sending history for this user.</p></div>';
            } else {
              contentHtml += '<div class="mailpn-history-table-container">';
              contentHtml += '<table class="mailpn-table mailpn-width-100-percent"><thead><tr>';
              contentHtml += '<th>Subject</th><th>Type</th><th>Send Date</th><th>Status</th><th>Opened</th><th>Clicks</th></tr></thead><tbody>';

              history.forEach(function (item) {
                contentHtml += '<tr>';
                contentHtml += '<td>' + (item.subject || 'No subject') + '</td>';
                contentHtml += '<td>' + (item.mail_type || 'N/A') + '</td>';
                contentHtml += '<td>' + (item.sent_date || 'N/A') + '</td>';
                contentHtml += '<td><span class="mailpn-badge ' + (item.success ? 'mailpn-badge-green' : 'mailpn-badge-red') + '">' + (item.success ? 'Sent' : 'Error') + '</span></td>';
                contentHtml += '<td>' + (item.opened ? '<i class="material-icons-outlined mailpn-color-green">check_circle</i>' : '-') + '</td>';
                contentHtml += '<td>' + (item.has_clicks ? item.clicks_count : '-') + '</td>';
                contentHtml += '</tr>';
              });

              contentHtml += '</tbody></table></div>';
            }
            contentHtml += '</div>'; // Close mailpn-history-section

            contentHtml += '</div>'; // Close mailpn-popup-body

            document.getElementById(popupId).querySelector('.mailpn-popup-content').innerHTML =
              '<button type="button" class="mailpn-popup-close-wrapper"><i class="material-icons-outlined">close</i></button>' + contentHtml;
          })
          .catch(function () {
            document.getElementById(popupId).querySelector('.mailpn-popup-body').innerHTML =
              '<div class="mailpn-info-box mailpn-info-box-red"><p>Error loading statistics</p></div>';
          });
      }
    });
  }
})();
