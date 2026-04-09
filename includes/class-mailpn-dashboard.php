<?php
/**
 * The Dashboard / Statistics functionalities of the plugin.
 *
 * Defines the behaviour of the plugin on Dashboard functions
 * including email analytics: sent, opened, clicked, rates, and charts.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Dashboard {

	private static $periods = ['day', 'week', 'month', 'year', 'all'];

	public function __construct() {
		// Constructor
	}

	/* ──────────────────────────────────
	   Period helpers
	   ────────────────────────────────── */

	private static function get_period_labels() {
		return [
			'day'   => __('24 h', 'mailpn'),
			'week'  => __('7 days', 'mailpn'),
			'month' => __('30 days', 'mailpn'),
			'year'  => __('1 year', 'mailpn'),
			'all'   => __('All time', 'mailpn'),
		];
	}

	private static function get_period_select_labels() {
		return [
			'day'   => __('Last 24 hours', 'mailpn'),
			'week'  => __('Last 7 days', 'mailpn'),
			'month' => __('Last 30 days', 'mailpn'),
			'year'  => __('Last year', 'mailpn'),
			'all'   => __('All time', 'mailpn'),
		];
	}

	private static function get_date_threshold($period) {
		switch ($period) {
			case 'day':   return gmdate('Y-m-d H:i:s', strtotime('-24 hours'));
			case 'week':  return gmdate('Y-m-d H:i:s', strtotime('-7 days'));
			case 'month': return gmdate('Y-m-d H:i:s', strtotime('-30 days'));
			case 'year':  return gmdate('Y-m-d H:i:s', strtotime('-365 days'));
			case 'all':   return '1970-01-01 00:00:00';
		}
		return gmdate('Y-m-d H:i:s', strtotime('-7 days'));
	}

	private static function get_chart_title($period) {
		$map = [
			'day'   => __('Last 24 hours trend', 'mailpn'),
			'week'  => __('Last 7 days trend', 'mailpn'),
			'month' => __('Last 30 days trend', 'mailpn'),
			'year'  => __('Last year trend', 'mailpn'),
			'all'   => __('All-time trend', 'mailpn'),
		];
		return $map[$period] ?? $map['week'];
	}

	/* ──────────────────────────────────
	   Existing dashboard data methods
	   ────────────────────────────────── */

	public function get_recent_sent_emails_count() {
		$one_week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
		$args = array(
			'post_type'      => 'mailpn_rec',
			'post_status'    => 'publish',
			'date_query'     => array(array('after' => $one_week_ago, 'inclusive' => true)),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		);
		return count(get_posts($args));
	}

	public function get_pending_scheduled_emails_count() {
		$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
		if (!is_array($scheduled_emails)) { $scheduled_emails = []; }
		return count($scheduled_emails);
	}

	public function get_recent_sent_emails_details() {
		$one_week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
		$args = array(
			'post_type'      => 'mailpn_rec',
			'post_status'    => 'publish',
			'date_query'     => array(array('after' => $one_week_ago, 'inclusive' => true)),
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		$recent_emails  = get_posts($args);
		$emails_details = array();

		foreach ($recent_emails as $email) {
			$user_id       = get_post_meta($email->ID, 'mailpn_rec_to', true);
			$rec_to_email  = get_post_meta($email->ID, 'mailpn_rec_to_email', true);
			$mail_id       = get_post_meta($email->ID, 'mailpn_rec_mail', true);
			$user          = get_userdata($user_id);
			$mail_post     = get_post($mail_id);
			$fallback_email = !empty($rec_to_email) ? $rec_to_email : (filter_var($user_id, FILTER_VALIDATE_EMAIL) ? $user_id : '');

			if ($user) {
				$display_name  = $user->display_name;
				$display_email = $user->user_email;
			} elseif (empty($user_id) || !is_numeric($user_id)) {
				$display_name  = !empty($fallback_email) ? $fallback_email : __('Email address', 'mailpn');
				$display_email = $fallback_email;
			} else {
				$display_name  = __('Deleted user', 'mailpn');
				$display_email = $fallback_email;
			}

			$emails_details[] = array(
				'id'           => $email->ID,
				'date'         => $email->post_date,
				'user_email'   => $display_email,
				'user_name'    => $display_name,
				'mail_subject' => $mail_post ? $mail_post->post_title : __('Unknown Email', 'mailpn'),
				'mail_type'    => $mail_post ? get_post_meta($mail_id, 'mailpn_type', true) : '',
			);
		}
		return $emails_details;
	}

	public function get_pending_scheduled_emails_details() {
		$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
		if (!is_array($scheduled_emails)) { $scheduled_emails = []; }
		$emails_details = array();

		foreach ($scheduled_emails as $scheduled_email) {
			$user      = get_userdata($scheduled_email['user_id']);
			$mail_post = get_post($scheduled_email['email_id']);
			$emails_details[] = array(
				'user_id'        => $scheduled_email['user_id'],
				'email_id'       => $scheduled_email['email_id'],
				'scheduled_time' => $scheduled_email['scheduled_time'],
				'created_time'   => $scheduled_email['created_time'],
				'user_email'     => $user ? $user->user_email : __('Unknown User', 'mailpn'),
				'user_name'      => $user ? $user->display_name : __('Unknown User', 'mailpn'),
				'mail_subject'   => $mail_post ? $mail_post->post_title : __('Unknown Email', 'mailpn'),
				'scheduled_date' => date('Y-m-d H:i:s', $scheduled_email['scheduled_time']),
				'created_date'   => date('Y-m-d H:i:s', $scheduled_email['created_time']),
			);
		}

		usort($emails_details, function($a, $b) {
			return $a['scheduled_time'] - $b['scheduled_time'];
		});
		return $emails_details;
	}

	/* ──────────────────────────────────
	   Statistics — Emails sent
	   ────────────────────────────────── */

	private static function get_stat_emails_sent($period) {
		global $wpdb;
		$since = self::get_date_threshold($period);

		$where = $period === 'all'
			? "WHERE post_type = 'mailpn_rec' AND post_status IN ('publish','private')"
			: $wpdb->prepare(
				"WHERE post_type = 'mailpn_rec' AND post_status IN ('publish','private') AND post_date >= %s",
				$since
			);

		$count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} {$where}");

		$rows = $wpdb->get_results(
			"SELECT ID, post_title, post_date FROM {$wpdb->posts} {$where} ORDER BY post_date DESC LIMIT 50"
		);

		return ['count' => $count, 'html' => self::build_emails_table($rows, 'sent')];
	}

	/* ──────────────────────────────────
	   Statistics — Emails opened
	   ────────────────────────────────── */

	private static function get_stat_emails_opened($period) {
		global $wpdb;
		$since = self::get_date_threshold($period);

		$where_period = $period === 'all' ? '' : $wpdb->prepare("AND p.post_date >= %s", $since);

		$count = (int) $wpdb->get_var(
			"SELECT COUNT(DISTINCT pm.post_id) FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE pm.meta_key = '_mailpn_opened' AND pm.meta_value = '1'
			AND p.post_type = 'mailpn_rec' {$where_period}"
		);

		$rows = $wpdb->get_results(
			"SELECT p.ID, p.post_title, p.post_date FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
			WHERE pm.meta_key = '_mailpn_opened' AND pm.meta_value = '1'
			AND p.post_type = 'mailpn_rec' {$where_period}
			ORDER BY p.post_date DESC LIMIT 50"
		);

		return ['count' => $count, 'html' => self::build_emails_table($rows, 'opened')];
	}

	/* ──────────────────────────────────
	   Statistics — Emails clicked
	   ────────────────────────────────── */

	private static function get_stat_emails_clicked($period) {
		global $wpdb;
		$table_clicks = $wpdb->prefix . 'mailpn_click_tracking';
		$since = self::get_date_threshold($period);

		if ($wpdb->get_var("SHOW TABLES LIKE '{$table_clicks}'") !== $table_clicks) {
			return ['count' => 0, 'html' => '<p class="mailpn-no-data">' . esc_html__('Click tracking table not available.', 'mailpn') . '</p>'];
		}

		$where_period = $period === 'all' ? '' : $wpdb->prepare("WHERE ct.clicked_at >= %s", $since);

		$count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_clicks} ct {$where_period}");

		$rows = $wpdb->get_results(
			"SELECT ct.email_id, ct.url, ct.clicked_at, p.post_title
			FROM {$table_clicks} ct
			LEFT JOIN {$wpdb->posts} p ON p.ID = ct.email_id
			{$where_period}
			ORDER BY ct.clicked_at DESC LIMIT 50"
		);

		return ['count' => $count, 'html' => self::build_clicks_table($rows)];
	}

	/* ──────────────────────────────────
	   Statistics — Rates
	   ────────────────────────────────── */

	private static function get_open_rate($sent, $opened) {
		return $sent <= 0 ? 0 : round(($opened / $sent) * 100, 1);
	}

	private static function get_click_rate($sent, $clicked) {
		return $sent <= 0 ? 0 : round(($clicked / $sent) * 100, 1);
	}

	/* ──────────────────────────────────
	   Statistics — Chart data
	   ────────────────────────────────── */

	private static function get_charts_data($period) {
		global $wpdb;

		switch ($period) {
			case 'day':
				$group_format = '%Y-%m-%d %H:00';
				$label_format = 'H:i';
				$steps    = 24;
				$interval = 'HOUR';
				break;
			case 'week':
				$group_format = '%Y-%m-%d';
				$label_format = 'D d';
				$steps    = 7;
				$interval = 'DAY';
				break;
			case 'month':
				$group_format = '%Y-%m-%d';
				$label_format = 'M d';
				$steps    = 30;
				$interval = 'DAY';
				break;
			case 'year':
				$group_format = '%Y-%m';
				$label_format = 'M Y';
				$steps    = 12;
				$interval = 'MONTH';
				break;
			case 'all':
			default:
				$group_format = '%Y-%m';
				$label_format = 'M Y';
				$steps    = 24;
				$interval = 'MONTH';
				break;
		}

		$since = self::get_date_threshold($period);

		// Sent
		$where_since = $period === 'all' ? '' : $wpdb->prepare("AND post_date >= %s", $since);
		$sent_rows = $wpdb->get_results(
			"SELECT DATE_FORMAT(post_date, '{$group_format}') AS period_key, COUNT(*) AS cnt
			FROM {$wpdb->posts}
			WHERE post_type = 'mailpn_rec' AND post_status IN ('publish','private') {$where_since}
			GROUP BY period_key ORDER BY period_key ASC",
			ARRAY_A
		);
		$sent_map = [];
		foreach ($sent_rows as $r) { $sent_map[$r['period_key']] = (int) $r['cnt']; }

		// Opened
		$opened_rows = $wpdb->get_results(
			"SELECT DATE_FORMAT(p.post_date, '{$group_format}') AS period_key, COUNT(DISTINCT pm.post_id) AS cnt
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE pm.meta_key = '_mailpn_opened' AND pm.meta_value = '1'
			AND p.post_type = 'mailpn_rec' {$where_since}
			GROUP BY period_key ORDER BY period_key ASC",
			ARRAY_A
		);
		$opened_map = [];
		foreach ($opened_rows as $r) { $opened_map[$r['period_key']] = (int) $r['cnt']; }

		// Clicked
		$table_clicks = $wpdb->prefix . 'mailpn_click_tracking';
		$clicked_map  = [];
		if ($wpdb->get_var("SHOW TABLES LIKE '{$table_clicks}'") === $table_clicks) {
			$click_where = $period === 'all' ? '' : $wpdb->prepare("WHERE clicked_at >= %s", $since);
			$click_rows = $wpdb->get_results(
				"SELECT DATE_FORMAT(clicked_at, '{$group_format}') AS period_key, COUNT(*) AS cnt
				FROM {$table_clicks} {$click_where}
				GROUP BY period_key ORDER BY period_key ASC",
				ARRAY_A
			);
			foreach ($click_rows as $r) { $clicked_map[$r['period_key']] = (int) $r['cnt']; }
		}

		// Build arrays
		$labels       = [];
		$data_sent    = [];
		$data_opened  = [];
		$data_clicked = [];
		$now = current_time('timestamp');

		for ($i = $steps - 1; $i >= 0; $i--) {
			switch ($interval) {
				case 'HOUR':
					$ts    = strtotime("-{$i} hours", $now);
					$key   = gmdate('Y-m-d H:00', $ts);
					$label = date_i18n($label_format, $ts);
					break;
				case 'DAY':
					$ts    = strtotime("-{$i} days", $now);
					$key   = gmdate('Y-m-d', $ts);
					$label = date_i18n($label_format, $ts);
					break;
				case 'MONTH':
					$ts    = strtotime("-{$i} months", $now);
					$key   = gmdate('Y-m', $ts);
					$label = date_i18n($label_format, $ts);
					break;
			}

			$labels[]       = $label;
			$data_sent[]    = $sent_map[$key] ?? 0;
			$data_opened[]  = $opened_map[$key] ?? 0;
			$data_clicked[] = $clicked_map[$key] ?? 0;
		}

		return [
			'labels'  => $labels,
			'sent'    => $data_sent,
			'opened'  => $data_opened,
			'clicked' => $data_clicked,
		];
	}

	/* ──────────────────────────────────
	   HTML helpers — tables
	   ────────────────────────────────── */

	private static function build_emails_table($rows, $type = 'sent') {
		if (empty($rows)) {
			return '<p class="mailpn-no-data">' . esc_html__('No data for this period.', 'mailpn') . '</p>';
		}

		$icon = $type === 'opened' ? 'mark_email_read' : 'send';

		ob_start();
		?>
		<table class="mailpn-emails-table">
			<thead>
				<tr>
					<th><?php esc_html_e('Subject', 'mailpn'); ?></th>
					<th><?php esc_html_e('Date', 'mailpn'); ?></th>
					<th><?php esc_html_e('Status', 'mailpn'); ?></th>
					<th><?php esc_html_e('Actions', 'mailpn'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($rows as $row):
					$post_id  = is_object($row) ? ($row->ID ?? 0) : 0;
					$title    = is_object($row) ? ($row->post_title ?? '—') : '—';
					$date     = is_object($row) ? ($row->post_date ?? '') : '';
					$edit_url = $post_id ? get_edit_post_link($post_id, 'raw') : '#';

					$is_opened    = $post_id ? get_post_meta($post_id, '_mailpn_opened', true) : false;
					$status_icon  = $is_opened ? 'mark_email_read' : 'mark_email_unread';
					$status_label = $is_opened ? __('Opened', 'mailpn') : __('Not opened', 'mailpn');
					$status_class = $is_opened ? 'mailpn-badge-opened' : 'mailpn-badge-pending';
				?>
				<tr>
					<td>
						<a href="<?php echo esc_url($edit_url); ?>" target="_blank" class="mailpn-stats-link">
							<span class="material-icons-outlined mailpn-stats-tbl-icon"><?php echo esc_html($icon); ?></span>
							<?php echo esc_html($title); ?>
						</a>
					</td>
					<td>
						<span class="material-icons-outlined mailpn-stats-tbl-icon">schedule</span>
						<?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', strtotime($date))); ?>
					</td>
					<td>
						<span class="mailpn-stats-badge <?php echo esc_attr($status_class); ?>">
							<span class="material-icons-outlined mailpn-stats-tbl-icon"><?php echo esc_html($status_icon); ?></span>
							<?php echo esc_html($status_label); ?>
						</span>
					</td>
					<td class="mailpn-stats-actions">
						<?php if ($edit_url !== '#'): ?>
							<a href="<?php echo esc_url($edit_url); ?>" target="_blank" title="<?php esc_attr_e('View', 'mailpn'); ?>">
								<span class="material-icons-outlined">visibility</span>
							</a>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		return ob_get_clean();
	}

	private static function build_clicks_table($rows) {
		if (empty($rows)) {
			return '<p class="mailpn-no-data">' . esc_html__('No data for this period.', 'mailpn') . '</p>';
		}

		ob_start();
		?>
		<table class="mailpn-emails-table">
			<thead>
				<tr>
					<th><?php esc_html_e('Email', 'mailpn'); ?></th>
					<th><?php esc_html_e('Clicked URL', 'mailpn'); ?></th>
					<th><?php esc_html_e('Date', 'mailpn'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($rows as $row):
					$title       = $row->post_title ?? '—';
					$url         = $row->url ?? '';
					$date        = $row->clicked_at ?? '';
					$display_url = strlen($url) > 50 ? substr($url, 0, 47) . '...' : $url;
				?>
				<tr>
					<td>
						<span class="material-icons-outlined mailpn-stats-tbl-icon">email</span>
						<?php echo esc_html($title); ?>
					</td>
					<td>
						<a href="<?php echo esc_url($url); ?>" target="_blank" class="mailpn-stats-link" title="<?php echo esc_attr($url); ?>">
							<span class="material-icons-outlined mailpn-stats-tbl-icon">link</span>
							<?php echo esc_html($display_url); ?>
						</a>
					</td>
					<td>
						<span class="material-icons-outlined mailpn-stats-tbl-icon">schedule</span>
						<?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', strtotime($date))); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		return ob_get_clean();
	}

	/* ──────────────────────────────────
	   AJAX handler — period change
	   ────────────────────────────────── */

	public static function get_charts_data_for_enqueue($period) {
		$valid = ['day', 'week', 'month', 'year', 'all'];
		if (!in_array($period, $valid)) { $period = 'week'; }
		return self::get_charts_data($period);
	}

	public static function ajax_dashboard_stats_period() {
		$period = isset($_POST['period']) ? sanitize_text_field(wp_unslash($_POST['period'])) : 'week';
		if (!in_array($period, self::$periods)) { $period = 'week'; }

		$period_labels = self::get_period_labels();
		$sent    = self::get_stat_emails_sent($period);
		$opened  = self::get_stat_emails_opened($period);
		$clicked = self::get_stat_emails_clicked($period);
		$open_rate  = self::get_open_rate($sent['count'], $opened['count']);
		$click_rate = self::get_click_rate($sent['count'], $clicked['count']);
		$charts  = self::get_charts_data($period);

		echo wp_json_encode([
			'error_key' => '',
			'widgets'   => [
				'sent'       => ['count' => $sent['count']],
				'opened'     => ['count' => $opened['count']],
				'clicked'    => ['count' => $clicked['count']],
				'open_rate'  => ['count' => $open_rate . '%'],
				'click_rate' => ['count' => $click_rate . '%'],
			],
			'popups'    => [
				'sent'    => [
					'title' => sprintf(__('Emails sent (%s)', 'mailpn'), $period_labels[$period]),
					'html'  => $sent['html'],
				],
				'opened'  => [
					'title' => sprintf(__('Emails opened (%s)', 'mailpn'), $period_labels[$period]),
					'html'  => $opened['html'],
				],
				'clicked' => [
					'title' => sprintf(__('Clicks (%s)', 'mailpn'), $period_labels[$period]),
					'html'  => $clicked['html'],
				],
			],
			'charts'    => $charts,
			'labels'    => [
				'widget_period' => $period_labels[$period],
				'chart_title'   => self::get_chart_title($period),
			],
		]);
		exit;
	}

	/* ──────────────────────────────────
	   Render dashboard page
	   ────────────────────────────────── */

	public function render_dashboard_page() {
		$pending_scheduled_count = $this->get_pending_scheduled_emails_count();

		$period = isset($_GET['period']) ? sanitize_text_field(wp_unslash($_GET['period'])) : 'week';
		if (!in_array($period, self::$periods)) { $period = 'week'; }

		$period_labels        = self::get_period_labels();
		$period_select_labels = self::get_period_select_labels();
		$period_label         = $period_labels[$period];

		$sent       = self::get_stat_emails_sent($period);
		$opened     = self::get_stat_emails_opened($period);
		$clicked    = self::get_stat_emails_clicked($period);
		$open_rate  = self::get_open_rate($sent['count'], $opened['count']);
		$click_rate = self::get_click_rate($sent['count'], $clicked['count']);
		$charts     = self::get_charts_data($period);

		$popup_titles = [
			'sent'    => sprintf(__('Emails sent (%s)', 'mailpn'), $period_label),
			'opened'  => sprintf(__('Emails opened (%s)', 'mailpn'), $period_label),
			'clicked' => sprintf(__('Clicks (%s)', 'mailpn'), $period_label),
		];
		?>
		<div class="mailpn-dashboard mailpn-max-width-1000 mailpn-margin-auto mailpn-mt-50 mailpn-mb-50">
			<img src="<?php echo esc_url(MAILPN_URL . 'assets/media/banner-1544x500.png'); ?>" alt="<?php esc_html_e('Plugin main Banner', 'mailpn'); ?>" title="<?php esc_html_e('Plugin main Banner', 'mailpn'); ?>" class="mailpn-width-100-percent mailpn-border-radius-20 mailpn-mb-30">

			<!-- Header -->
			<div class="mailpn-stats-header">
				<h1><?php esc_html_e('Statistics', 'mailpn'); ?></h1>
				<div class="mailpn-stats-period-selector">
					<label for="mailpn-stats-period-select">
						<span class="material-icons-outlined" style="vertical-align:middle;">date_range</span>
						<?php esc_html_e('Period:', 'mailpn'); ?>
					</label>
					<select id="mailpn-stats-period-select">
						<?php foreach ($period_select_labels as $val => $label): ?>
							<option value="<?php echo esc_attr($val); ?>" <?php selected($period, $val); ?>>
								<?php echo esc_html($label); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<!-- Analytics stat widgets -->
			<div class="mailpn-stats-widgets">
				<div class="mailpn-stats-widget mailpn-stats-bg-sent" data-popup="mailpn-stats-popup-sent" data-widget="sent">
					<div class="mailpn-stats-widget-icon"><span class="material-icons-outlined">send</span></div>
					<div class="mailpn-stats-widget-value"><?php echo esc_html($sent['count']); ?></div>
					<div class="mailpn-stats-widget-title">
						<?php printf(esc_html__('Emails sent (%s)', 'mailpn'), esc_html($period_label)); ?>
					</div>
				</div>

				<div class="mailpn-stats-widget mailpn-stats-bg-opened" data-popup="mailpn-stats-popup-opened" data-widget="opened">
					<div class="mailpn-stats-widget-icon"><span class="material-icons-outlined">mark_email_read</span></div>
					<div class="mailpn-stats-widget-value"><?php echo esc_html($opened['count']); ?></div>
					<div class="mailpn-stats-widget-title">
						<?php printf(esc_html__('Emails opened (%s)', 'mailpn'), esc_html($period_label)); ?>
					</div>
				</div>

				<div class="mailpn-stats-widget mailpn-stats-bg-clicked" data-popup="mailpn-stats-popup-clicked" data-widget="clicked">
					<div class="mailpn-stats-widget-icon"><span class="material-icons-outlined">ads_click</span></div>
					<div class="mailpn-stats-widget-value"><?php echo esc_html($clicked['count']); ?></div>
					<div class="mailpn-stats-widget-title">
						<?php printf(esc_html__('Clicks (%s)', 'mailpn'), esc_html($period_label)); ?>
					</div>
				</div>

				<div class="mailpn-stats-widget mailpn-stats-bg-open-rate" data-widget="open_rate">
					<div class="mailpn-stats-widget-icon"><span class="material-icons-outlined">percent</span></div>
					<div class="mailpn-stats-widget-value"><?php echo esc_html($open_rate); ?>%</div>
					<div class="mailpn-stats-widget-title">
						<?php printf(esc_html__('Open rate (%s)', 'mailpn'), esc_html($period_label)); ?>
					</div>
				</div>

				<div class="mailpn-stats-widget mailpn-stats-bg-click-rate" data-widget="click_rate">
					<div class="mailpn-stats-widget-icon"><span class="material-icons-outlined">trending_up</span></div>
					<div class="mailpn-stats-widget-value"><?php echo esc_html($click_rate); ?>%</div>
					<div class="mailpn-stats-widget-title">
						<?php printf(esc_html__('Click rate (%s)', 'mailpn'), esc_html($period_label)); ?>
					</div>
				</div>
			</div>

			<!-- Pending scheduled emails card -->
			<div class="mailpn-dashboard-stats mailpn-mb-30">
				<div class="mailpn-stats-grid">
					<div class="mailpn-stat-card" id="pending-scheduled-emails-card">
						<div class="mailpn-stat-icon"><span class="dashicons dashicons-clock"></span></div>
						<div class="mailpn-stat-content">
							<h3><?php esc_html_e('Pending Scheduled Emails', 'mailpn'); ?></h3>
							<div class="mailpn-stat-number"><?php echo esc_html($pending_scheduled_count); ?></div>
							<p><?php esc_html_e('Awaiting delivery', 'mailpn'); ?></p>
						</div>
					</div>
				</div>
			</div>

			<!-- Charts section -->
			<div class="mailpn-stats-charts">
				<div class="mailpn-stats-charts-grid">
					<div class="mailpn-stats-chart-card mailpn-stats-chart-wide">
						<h3 id="mailpn-stats-chart-combined-title">
							<span class="material-icons-outlined" style="font-size:20px;color:#787c82;">show_chart</span>
							<span><?php echo esc_html(self::get_chart_title($period)); ?></span>
						</h3>
						<div class="mailpn-stats-chart-wrap"><canvas id="mailpn-stats-chart-combined"></canvas></div>
					</div>
					<div class="mailpn-stats-chart-card">
						<h3><span class="material-icons-outlined" style="font-size:20px;color:#787c82;">send</span> <?php esc_html_e('Emails sent', 'mailpn'); ?></h3>
						<div class="mailpn-stats-chart-wrap"><canvas id="mailpn-stats-chart-sent"></canvas></div>
					</div>
					<div class="mailpn-stats-chart-card">
						<h3><span class="material-icons-outlined" style="font-size:20px;color:#787c82;">mark_email_read</span> <?php esc_html_e('Emails opened', 'mailpn'); ?></h3>
						<div class="mailpn-stats-chart-wrap"><canvas id="mailpn-stats-chart-opened"></canvas></div>
					</div>
					<div class="mailpn-stats-chart-card mailpn-stats-chart-wide">
						<h3><span class="material-icons-outlined" style="font-size:20px;color:#787c82;">ads_click</span> <?php esc_html_e('Clicks', 'mailpn'); ?></h3>
						<div class="mailpn-stats-chart-wrap"><canvas id="mailpn-stats-chart-clicked"></canvas></div>
					</div>
				</div>
			</div>

			<!-- Stats Popups (overlay + modals) -->
			<div class="mailpn-stats-overlay" style="display:none;"></div>

			<div id="mailpn-stats-popup-sent" class="mailpn-stats-popup" style="display:none;" data-popup-type="sent">
				<div class="mailpn-stats-popup-content">
					<button class="mailpn-stats-popup-close"><span class="material-icons-outlined">close</span></button>
					<div class="mailpn-stats-popup-inner">
						<h2><?php echo esc_html($popup_titles['sent']); ?></h2>
						<div class="mailpn-stats-popup-body"><?php echo $sent['html']; ?></div>
					</div>
				</div>
			</div>

			<div id="mailpn-stats-popup-opened" class="mailpn-stats-popup" style="display:none;" data-popup-type="opened">
				<div class="mailpn-stats-popup-content">
					<button class="mailpn-stats-popup-close"><span class="material-icons-outlined">close</span></button>
					<div class="mailpn-stats-popup-inner">
						<h2><?php echo esc_html($popup_titles['opened']); ?></h2>
						<div class="mailpn-stats-popup-body"><?php echo $opened['html']; ?></div>
					</div>
				</div>
			</div>

			<div id="mailpn-stats-popup-clicked" class="mailpn-stats-popup" style="display:none;" data-popup-type="clicked">
				<div class="mailpn-stats-popup-content">
					<button class="mailpn-stats-popup-close"><span class="material-icons-outlined">close</span></button>
					<div class="mailpn-stats-popup-inner">
						<h2><?php echo esc_html($popup_titles['clicked']); ?></h2>
						<div class="mailpn-stats-popup-body"><?php echo $clicked['html']; ?></div>
					</div>
				</div>
			</div>

			<!-- Pending scheduled emails popup -->
			<div id="pending-scheduled-emails-popup" class="mailpn-popup mailpn-popup-size-large">
				<div class="mailpn-popup-content mailpn-pt-0">
					<div class="mailpn-popup-header"><h3><?php esc_html_e('Pending Scheduled Emails', 'mailpn'); ?></h3></div>
					<div class="mailpn-popup-body"><div id="pending-scheduled-emails-list"><?php echo $this->render_pending_scheduled_emails_list(); ?></div></div>
				</div>
			</div>

			<div id="pending-scheduled-emails-list-content" style="display:none;"><?php echo $this->render_pending_scheduled_emails_list(); ?></div>
		</div>
		<?php
	}

	/* ──────────────────────────────────
	   Existing render helpers
	   ────────────────────────────────── */

	private function render_recent_sent_emails_list() {
		$emails = $this->get_recent_sent_emails_details();
		if (empty($emails)) {
			return '<p class="mailpn-no-data">' . esc_html__('No emails sent in the last 7 days.', 'mailpn') . '</p>';
		}
		ob_start();
		?>
		<table class="mailpn-emails-table">
			<thead><tr>
				<th><?php esc_html_e('Date', 'mailpn'); ?></th>
				<th><?php esc_html_e('Recipient', 'mailpn'); ?></th>
				<th><?php esc_html_e('Email Subject', 'mailpn'); ?></th>
				<th><?php esc_html_e('Type', 'mailpn'); ?></th>
			</tr></thead>
			<tbody>
			<?php foreach ($emails as $email): ?>
				<tr>
					<td><?php echo esc_html(date('Y-m-d H:i', strtotime($email['date']))); ?></td>
					<td><strong><?php echo esc_html($email['user_name']); ?></strong><br><small><?php echo esc_html($email['user_email']); ?></small></td>
					<td><?php echo esc_html($email['mail_subject']); ?></td>
					<td><?php echo esc_html($email['mail_type']); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		return ob_get_clean();
	}

	private function render_pending_scheduled_emails_list() {
		$emails = $this->get_pending_scheduled_emails_details();
		if (empty($emails)) {
			return '<p class="mailpn-no-data">' . esc_html__('No pending scheduled emails.', 'mailpn') . '</p>';
		}
		ob_start();
		?>
		<table class="mailpn-emails-table">
			<thead><tr>
				<th><?php esc_html_e('Scheduled Date', 'mailpn'); ?></th>
				<th><?php esc_html_e('Recipient', 'mailpn'); ?></th>
				<th><?php esc_html_e('Email Subject', 'mailpn'); ?></th>
				<th><?php esc_html_e('Created Date', 'mailpn'); ?></th>
			</tr></thead>
			<tbody>
			<?php foreach ($emails as $email): ?>
				<tr>
					<td><?php echo esc_html($email['scheduled_date']); ?></td>
					<td><strong><?php echo esc_html($email['user_name']); ?></strong><br><small><?php echo esc_html($email['user_email']); ?></small></td>
					<td><?php echo esc_html($email['mail_subject']); ?></td>
					<td><?php echo esc_html($email['created_date']); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		return ob_get_clean();
	}
}
