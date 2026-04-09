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
})();
