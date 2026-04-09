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
      this.initStats();
    },

    bindEvents: function () {
      // Refresh stats periodically (every 5 minutes)
      setInterval(function () {
        dashboard.refreshStats();
      }, 300000);

      // ── Stats widget clicks → open popup via MAILPN_Popups ──
      $(document).on('click', '.mailpn-stats-widget[data-popup]', function () {
        var popupId = $(this).data('popup');
        if (popupId && typeof MAILPN_Popups !== 'undefined') {
          MAILPN_Popups.open(popupId);
        }
      });

      // ── Period selector ──
      $(document).on('change', '#mailpn-stats-period-select', function () {
        loadStatsPeriod($(this).val());
      });
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
              $popup.find('.mailpn-popup-header h3').text(data.title);
              $popup.find('.mailpn-popup-body').html(data.html);
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

  // Inject CSS for table sort indicators
  var additionalCSS =
    '.mailpn-emails-table th { cursor: pointer; user-select: none; position: relative; }' +
    '.mailpn-emails-table th:hover { background-color: #e9ecef; }' +
    ".mailpn-emails-table th::after { content: '\\2195'; position: absolute; right: 8px; opacity: 0.3; }" +
    ".mailpn-emails-table th.mailpn-sort-asc::after { content: '\\2191'; opacity: 1; }" +
    ".mailpn-emails-table th.mailpn-sort-desc::after { content: '\\2193'; opacity: 1; }";

  var style = document.createElement('style');
  style.textContent = additionalCSS;
  document.head.appendChild(style);
});
