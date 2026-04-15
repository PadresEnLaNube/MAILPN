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
})();
