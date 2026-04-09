/**
 * Dashboard / Statistics JavaScript functionality
 *
 * Handles popup interactions, dashboard interactions using MAILPN_Popups,
 * Chart.js rendering, AJAX period switching, and stat-card popup modals.
 */
jQuery(document).ready(function ($) {
  'use strict';

  /* ──────────────────────────────────
     Chart.js instances
     ────────────────────────────────── */
  var chartCombined = null;
  var chartSent = null;
  var chartOpened = null;
  var chartClicked = null;

  var statsCfg = window.mailpnDashboardStats || {};
  var statsI18n = statsCfg.i18n || {};

  /* ──────────────────────────────────
     Original dashboard functionality
     ────────────────────────────────── */
  var dashboard = {
    init: function () {
      this.bindEvents();
      this.initializeTooltips();
      this.initStats();
    },

    bindEvents: function () {
      // Open popup when clicking on pending scheduled emails card
      $('#pending-scheduled-emails-card').on('click', function () {
        dashboard.openEmailsPopup('pending-scheduled-emails-popup');
      });

      // Refresh stats periodically (every 5 minutes)
      setInterval(function () {
        dashboard.refreshStats();
      }, 300000);

      // Add click effects to original stat cards
      $('.mailpn-stat-card')
        .on('mousedown', function () {
          $(this).addClass('mailpn-stat-card-pressed');
        })
        .on('mouseup mouseleave', function () {
          $(this).removeClass('mailpn-stat-card-pressed');
        });

      // Add hover effects
      $('.mailpn-stat-card').hover(
        function () {
          $(this).addClass('mailpn-stat-card-hover');
        },
        function () {
          $(this).removeClass('mailpn-stat-card-hover');
        }
      );

      // ── Stats widget clicks → open popup ──
      $(document).on('click', '.mailpn-stats-widget[data-popup]', function () {
        var popupId = $(this).data('popup');
        if (popupId) {
          statsOpenPopup($('#' + popupId));
        }
      });

      // ── Stats popup close ──
      $(document).on('click', '.mailpn-stats-popup-close', function () {
        statsClosePopup();
      });

      $(document).on('click', '.mailpn-stats-overlay', function () {
        statsClosePopup();
      });

      $(document).on('keydown', function (e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
          statsClosePopup();
        }
      });

      // ── Period selector ──
      $(document).on('change', '#mailpn-stats-period-select', function () {
        loadStatsPeriod($(this).val());
      });
    },

    openEmailsPopup: function (popupId) {
      var popup = $('#' + popupId);
      if (popup.length) {
        var popupBody = popup.find('.mailpn-popup-body');
        if (popupBody.length && typeof MAILPN_Data !== 'undefined') {
          popupBody.html(MAILPN_Data.mailpn_loader);
        }
      }

      if (typeof MAILPN_Popups !== 'undefined') {
        MAILPN_Popups.open(popupId, {
          beforeShow: function () {},
        });
      }

      setTimeout(function () {
        dashboard.loadPopupContent(popupId);
      }, 500);
    },

    loadPopupContent: function (popupId) {
      var popup = $('#' + popupId);
      if (popup.length) {
        var popupBody = popup.find('.mailpn-popup-body');
        if (popupBody.length) {
          if (popupId === 'pending-scheduled-emails-popup') {
            dashboard.loadPendingScheduledEmails(popupBody);
          }
        }
      }
    },

    loadPendingScheduledEmails: function (container) {
      if (typeof MAILPN_Data !== 'undefined') {
        container.html(MAILPN_Data.mailpn_loader);
      }
      setTimeout(function () {
        container.html($('#pending-scheduled-emails-list-content').html());
        dashboard.initializeTableFunctionality(container);
      }, 1000);
    },

    initializeTableFunctionality: function (container) {
      var table = container.find('.mailpn-emails-table');
      if (table.length) {
        var searchInput = $(
          '<input type="text" class="mailpn-search-input" placeholder="' +
            (typeof mailpn_dashboard !== 'undefined'
              ? mailpn_dashboard.search_placeholder || 'Search emails...'
              : 'Search emails...') +
            '">'
        );
        searchInput.insertBefore(table);
        searchInput.on('input', function () {
          var searchTerm = $(this).val().toLowerCase();
          dashboard.filterTable(table, searchTerm);
        });
      }

      container.find('.mailpn-emails-table th').on('click', function () {
        dashboard.sortTable($(this));
      });
    },

    filterTable: function (table, searchTerm) {
      table.find('tbody tr').each(function () {
        var row = $(this);
        var text = row.text().toLowerCase();
        row.toggle(text.includes(searchTerm));
      });
    },

    sortTable: function (header) {
      var table = header.closest('table');
      var tbody = table.find('tbody');
      var rows = tbody.find('tr').toArray();
      var columnIndex = header.index();
      var isAscending = header.hasClass('mailpn-sort-asc');

      table.find('th').removeClass('mailpn-sort-asc mailpn-sort-desc');

      rows.sort(function (a, b) {
        var aValue = $(a).find('td').eq(columnIndex).text().trim();
        var bValue = $(b).find('td').eq(columnIndex).text().trim();
        var aDate = new Date(aValue);
        var bDate = new Date(bValue);

        if (!isNaN(aDate) && !isNaN(bDate)) {
          return isAscending ? bDate - aDate : aDate - bDate;
        }
        return isAscending
          ? bValue.localeCompare(aValue)
          : aValue.localeCompare(bValue);
      });

      tbody.empty().append(rows);
      header.addClass(isAscending ? 'mailpn-sort-desc' : 'mailpn-sort-asc');
    },

    refreshStats: function () {},

    initializeTooltips: function () {
      $('.mailpn-stat-card').each(function () {
        var card = $(this);
        var title = card.find('h3').text();
        var number = card.find('.mailpn-stat-number').text();
        var description = card.find('p').text();
        card.attr('title', title + ': ' + number + ' - ' + description);
      });
    },

    formatNumber: function (num) {
      if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
      if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
      return num.toString();
    },

    animateNumber: function (element, start, end, duration) {
      var startTime = performance.now();
      var difference = end - start;

      function updateNumber(currentTime) {
        var elapsed = currentTime - startTime;
        var progress = Math.min(elapsed / duration, 1);
        var easeOut = 1 - Math.pow(1 - progress, 3);
        var current = start + difference * easeOut;
        element.text(Math.floor(current));
        if (progress < 1) requestAnimationFrame(updateNumber);
      }

      requestAnimationFrame(updateNumber);
    },

    /* ──────────────────────────────────
       Statistics charts initialisation
       ────────────────────────────────── */
    initStats: function () {
      if (statsCfg.chartsData) {
        renderAllCharts(statsCfg.chartsData);
      }
    },
  };

  /* ──────────────────────────────────
     Stats popup management
     ────────────────────────────────── */
  function statsOpenPopup($popup) {
    if (!$popup.length) return;
    $('.mailpn-stats-overlay').show();
    $popup.show();
  }

  function statsClosePopup() {
    $('.mailpn-stats-overlay').hide();
    $('.mailpn-stats-popup').hide();
  }

  /* ──────────────────────────────────
     AJAX period loading
     ────────────────────────────────── */
  function loadStatsPeriod(period) {
    var $wrap = $('.mailpn-dashboard');
    $wrap.addClass('mailpn-stats-loading');

    $.post(statsCfg.ajaxUrl, {
      action: 'mailpn_ajax',
      mailpn_ajax_type: 'mailpn_dashboard_stats_period',
      mailpn_ajax_nonce: statsCfg.nonce,
      period: period,
    })
      .done(function (raw) {
        var res = typeof raw === 'string' ? JSON.parse(raw) : raw;
        if (res.error_key !== '') return;

        // Update widget values
        if (res.widgets) {
          $.each(res.widgets, function (key, data) {
            var $w = $('.mailpn-stats-widget[data-widget="' + key + '"]');
            $w.find('.mailpn-stats-widget-value').text(data.count);
          });
        }

        // Update widget titles
        if (res.labels && res.labels.widget_period && statsCfg.widgetLabels) {
          $.each(statsCfg.widgetLabels, function (key, tpl) {
            var $w = $('.mailpn-stats-widget[data-widget="' + key + '"]');
            $w.find('.mailpn-stats-widget-title').text(
              tpl.replace('%s', res.labels.widget_period)
            );
          });
        }

        // Update popup contents
        if (res.popups) {
          $.each(res.popups, function (key, data) {
            var $popup = $('#mailpn-stats-popup-' + key);
            if ($popup.length) {
              $popup.find('h2').text(data.title);
              $popup.find('.mailpn-stats-popup-body').html(data.html);
            }
          });
        }

        // Update chart title
        if (res.labels && res.labels.chart_title) {
          $('#mailpn-stats-chart-combined-title span').text(
            res.labels.chart_title
          );
        }

        // Update charts
        if (res.charts) {
          renderAllCharts(res.charts);
        }
      })
      .fail(function () {})
      .always(function () {
        $wrap.removeClass('mailpn-stats-loading');
      });
  }

  /* ──────────────────────────────────
     Chart rendering
     ────────────────────────────────── */
  function renderAllCharts(data) {
    if (!data || !data.labels) return;
    renderCombinedChart(data);
    renderBarChart(
      'mailpn-stats-chart-sent',
      data.labels,
      data.sent,
      'rgba(63, 81, 181, 0.7)',
      'sent'
    );
    renderBarChart(
      'mailpn-stats-chart-opened',
      data.labels,
      data.opened,
      'rgba(0, 150, 136, 0.7)',
      'opened'
    );
    renderBarChart(
      'mailpn-stats-chart-clicked',
      data.labels,
      data.clicked,
      'rgba(255, 152, 0, 0.7)',
      'clicked'
    );
  }

  function renderCombinedChart(data) {
    var ctx = document.getElementById('mailpn-stats-chart-combined');
    if (!ctx) return;
    if (chartCombined) chartCombined.destroy();

    chartCombined = new Chart(ctx.getContext('2d'), {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [
          {
            label: statsI18n.sent || 'Sent',
            data: data.sent,
            borderColor: '#3f51b5',
            backgroundColor: 'rgba(63, 81, 181, 0.08)',
            fill: true,
            tension: 0.3,
            pointRadius: 3,
            pointHoverRadius: 5,
            borderWidth: 2,
          },
          {
            label: statsI18n.opened || 'Opened',
            data: data.opened,
            borderColor: '#009688',
            backgroundColor: 'rgba(0, 150, 136, 0.08)',
            fill: true,
            tension: 0.3,
            pointRadius: 3,
            pointHoverRadius: 5,
            borderWidth: 2,
          },
          {
            label: statsI18n.clicked || 'Clicks',
            data: data.clicked,
            borderColor: '#ff9800',
            backgroundColor: 'rgba(255, 152, 0, 0.08)',
            fill: true,
            tension: 0.3,
            pointRadius: 3,
            pointHoverRadius: 5,
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { intersect: false, mode: 'index' },
        plugins: { legend: { position: 'top' } },
        scales: {
          x: { ticks: { maxTicksLimit: 12, maxRotation: 45 } },
          y: { beginAtZero: true, ticks: { precision: 0 } },
        },
      },
    });
  }

  function renderBarChart(canvasId, labels, data, bgColor, type) {
    var ctx = document.getElementById(canvasId);
    if (!ctx) return;

    if (type === 'sent' && chartSent) chartSent.destroy();
    if (type === 'opened' && chartOpened) chartOpened.destroy();
    if (type === 'clicked' && chartClicked) chartClicked.destroy();

    var chart = new Chart(ctx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            data: data,
            backgroundColor: bgColor,
            hoverBackgroundColor: bgColor,
            borderRadius: 4,
            borderSkipped: false,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { maxTicksLimit: 12, maxRotation: 45 } },
          y: { beginAtZero: true, ticks: { precision: 0 } },
        },
      },
    });

    if (type === 'sent') chartSent = chart;
    if (type === 'opened') chartOpened = chart;
    if (type === 'clicked') chartClicked = chart;
  }

  // Initialize dashboard
  dashboard.init();

  // Expose functions globally
  window.MAILPN_Dashboard = { dashboard: dashboard };

  // Inject CSS for original card interactions
  var additionalCSS =
    '.mailpn-stat-card-pressed { transform: translateY(-2px) scale(0.98) !important; }' +
    '.mailpn-stat-card-hover { transform: translateY(-3px) !important; }' +
    '.mailpn-search-input { width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }' +
    '.mailpn-search-input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2); }' +
    '.mailpn-emails-table th { cursor: pointer; user-select: none; position: relative; }' +
    '.mailpn-emails-table th:hover { background-color: #e9ecef; }' +
    ".mailpn-emails-table th::after { content: '\\2195'; position: absolute; right: 8px; opacity: 0.3; }" +
    ".mailpn-emails-table th.mailpn-sort-asc::after { content: '\\2191'; opacity: 1; }" +
    ".mailpn-emails-table th.mailpn-sort-desc::after { content: '\\2193'; opacity: 1; }";

  var style = document.createElement('style');
  style.textContent = additionalCSS;
  document.head.appendChild(style);
});
