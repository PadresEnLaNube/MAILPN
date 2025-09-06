<?php
/**
 * The Dashboard functionalities of the plugin.
 *
 * Defines the behaviour of the plugin on Dashboard functions.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Dashboard {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Constructor
	}

	/**
	 * Get recent sent emails count (last 7 days)
	 *
	 * @since    1.0.0
	 * @return   int    Number of sent emails in the last 7 days
	 */
	public function get_recent_sent_emails_count() {
		$one_week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
		
		$args = array(
			'post_type' => 'mailpn_rec',
			'post_status' => 'publish',
			'date_query' => array(
				array(
					'after' => $one_week_ago,
					'inclusive' => true,
				),
			),
			'posts_per_page' => -1,
			'fields' => 'ids',
		);
		
		$recent_emails = get_posts($args);
		return count($recent_emails);
	}

	/**
	 * Get pending scheduled emails count
	 *
	 * @since    1.0.0
	 * @return   int    Number of pending scheduled emails
	 */
	public function get_pending_scheduled_emails_count() {
		$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
		
		if (!is_array($scheduled_emails)) {
			$scheduled_emails = [];
		}
		
		return count($scheduled_emails);
	}

	/**
	 * Get recent sent emails details (last 7 days)
	 *
	 * @since    1.0.0
	 * @return   array    Array of recent sent emails
	 */
	public function get_recent_sent_emails_details() {
		$one_week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
		
		$args = array(
			'post_type' => 'mailpn_rec',
			'post_status' => 'publish',
			'date_query' => array(
				array(
					'after' => $one_week_ago,
					'inclusive' => true,
				),
			),
			'posts_per_page' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
		);
		
		$recent_emails = get_posts($args);
		$emails_details = array();
		
		foreach ($recent_emails as $email) {
			$user_id = get_post_meta($email->ID, 'mailpn_rec_to', true);
			$mail_id = get_post_meta($email->ID, 'mailpn_rec_mail', true);
			$user = get_userdata($user_id);
			$mail_post = get_post($mail_id);
			
			$emails_details[] = array(
				'id' => $email->ID,
				'date' => $email->post_date,
				'user_email' => $user ? $user->user_email : __('Unknown User', 'mailpn'),
				'user_name' => $user ? $user->display_name : __('Unknown User', 'mailpn'),
				'mail_subject' => $mail_post ? $mail_post->post_title : __('Unknown Email', 'mailpn'),
				'mail_type' => $mail_post ? get_post_meta($mail_id, 'mailpn_type', true) : '',
			);
		}
		
		return $emails_details;
	}

	/**
	 * Get pending scheduled emails details
	 *
	 * @since    1.0.0
	 * @return   array    Array of pending scheduled emails
	 */
	public function get_pending_scheduled_emails_details() {
		$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
		
		if (!is_array($scheduled_emails)) {
			$scheduled_emails = [];
		}
		
		$emails_details = array();
		
		foreach ($scheduled_emails as $scheduled_email) {
			$user = get_userdata($scheduled_email['user_id']);
			$mail_post = get_post($scheduled_email['email_id']);
			
			$emails_details[] = array(
				'user_id' => $scheduled_email['user_id'],
				'email_id' => $scheduled_email['email_id'],
				'scheduled_time' => $scheduled_email['scheduled_time'],
				'created_time' => $scheduled_email['created_time'],
				'user_email' => $user ? $user->user_email : __('Unknown User', 'mailpn'),
				'user_name' => $user ? $user->display_name : __('Unknown User', 'mailpn'),
				'mail_subject' => $mail_post ? $mail_post->post_title : __('Unknown Email', 'mailpn'),
				'scheduled_date' => date('Y-m-d H:i:s', $scheduled_email['scheduled_time']),
				'created_date' => date('Y-m-d H:i:s', $scheduled_email['created_time']),
			);
		}
		
		// Sort by scheduled time
		usort($emails_details, function($a, $b) {
			return $a['scheduled_time'] - $b['scheduled_time'];
		});
		
		return $emails_details;
	}

	/**
	 * Render dashboard page
	 *
	 * @since    1.0.0
	 */
	public function render_dashboard_page() {
		$recent_sent_count = $this->get_recent_sent_emails_count();
		$pending_scheduled_count = $this->get_pending_scheduled_emails_count();
		
		?>
		<div class="mailpn-dashboard mailpn-max-width-1000 mailpn-margin-auto mailpn-mt-50 mailpn-mb-50">
			<img src="<?php echo esc_url(MAILPN_URL . 'assets/media/banner-1544x500.png'); ?>" alt="<?php esc_html_e('Plugin main Banner', 'mailpn'); ?>" title="<?php esc_html_e('Plugin main Banner', 'mailpn'); ?>" class="mailpn-width-100-percent mailpn-border-radius-20 mailpn-mb-30">

			<div class="mailpn-display-table mailpn-width-100-percent">
				<div class="mailpn-display-inline-table mailpn-width-100-percent">
					<h1 class="mailpn-mb-30"><?php esc_html_e('Mailing Manager - Dashboard', 'mailpn'); ?></h1>
				</div>
			</div>

			<div class="mailpn-dashboard-stats mailpn-mb-30">
				<div class="mailpn-stats-grid">
					<!-- Recent Sent Emails Card -->
					<div class="mailpn-stat-card" id="recent-sent-emails-card">
						<div class="mailpn-stat-icon">
							<span class="dashicons dashicons-email-alt"></span>
						</div>
						<div class="mailpn-stat-content">
							<h3><?php esc_html_e('Recent Sent Emails', 'mailpn'); ?></h3>
							<div class="mailpn-stat-number"><?php echo esc_html($recent_sent_count); ?></div>
							<p><?php esc_html_e('Last 7 days', 'mailpn'); ?></p>
						</div>
					</div>

					<!-- Pending Scheduled Emails Card -->
					<div class="mailpn-stat-card" id="pending-scheduled-emails-card">
						<div class="mailpn-stat-icon">
							<span class="dashicons dashicons-clock"></span>
						</div>
						<div class="mailpn-stat-content">
							<h3><?php esc_html_e('Pending Scheduled Emails', 'mailpn'); ?></h3>
							<div class="mailpn-stat-number"><?php echo esc_html($pending_scheduled_count); ?></div>
							<p><?php esc_html_e('Awaiting delivery', 'mailpn'); ?></p>
						</div>
					</div>
				</div>
			</div>

			<!-- Recent Sent Emails Popup -->
			<div id="recent-sent-emails-popup" class="mailpn-popup mailpn-popup-size-large">
				<div class="mailpn-popup-content mailpn-pt-0">
					<div class="mailpn-popup-header">
						<h3><?php esc_html_e('Recent Sent Emails - Last 7 Days', 'mailpn'); ?></h3>
					</div>
					<div class="mailpn-popup-body">
						<div id="recent-sent-emails-list">
							<?php echo $this->render_recent_sent_emails_list(); ?>
						</div>
					</div>
				</div>
			</div>

			<!-- Pending Scheduled Emails Popup -->
			<div id="pending-scheduled-emails-popup" class="mailpn-popup mailpn-popup-size-large">
				<div class="mailpn-popup-content mailpn-pt-0">
					<div class="mailpn-popup-header">
						<h3><?php esc_html_e('Pending Scheduled Emails', 'mailpn'); ?></h3>
					</div>
					<div class="mailpn-popup-body">
						<div id="pending-scheduled-emails-list">
							<?php echo $this->render_pending_scheduled_emails_list(); ?>
						</div>
					</div>
				</div>
			</div>
			<!-- Hidden containers for popup content -->
			<div id="recent-sent-emails-list-content" style="display:none;">
				<?php echo $this->render_recent_sent_emails_list(); ?>
			</div>
			<div id="pending-scheduled-emails-list-content" style="display:none;">
				<?php echo $this->render_pending_scheduled_emails_list(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render recent sent emails list
	 *
	 * @since    1.0.0
	 * @return   string    HTML content
	 */
	private function render_recent_sent_emails_list() {
		$emails = $this->get_recent_sent_emails_details();
		
		if (empty($emails)) {
			return '<p class="mailpn-no-data">' . esc_html__('No emails sent in the last 7 days.', 'mailpn') . '</p>';
		}
		
		ob_start();
		?>
		<table class="mailpn-emails-table">
			<thead>
				<tr>
					<th><?php esc_html_e('Date', 'mailpn'); ?></th>
					<th><?php esc_html_e('Recipient', 'mailpn'); ?></th>
					<th><?php esc_html_e('Email Subject', 'mailpn'); ?></th>
					<th><?php esc_html_e('Type', 'mailpn'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($emails as $email): ?>
				<tr>
					<td><?php echo esc_html(date('Y-m-d H:i', strtotime($email['date']))); ?></td>
					<td>
						<strong><?php echo esc_html($email['user_name']); ?></strong><br>
						<small><?php echo esc_html($email['user_email']); ?></small>
					</td>
					<td><?php echo esc_html($email['mail_subject']); ?></td>
					<td><?php echo esc_html($email['mail_type']); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render pending scheduled emails list
	 *
	 * @since    1.0.0
	 * @return   string    HTML content
	 */
	private function render_pending_scheduled_emails_list() {
		$emails = $this->get_pending_scheduled_emails_details();
		
		if (empty($emails)) {
			return '<p class="mailpn-no-data">' . esc_html__('No pending scheduled emails.', 'mailpn') . '</p>';
		}
		
		ob_start();
		?>
		<table class="mailpn-emails-table">
			<thead>
				<tr>
					<th><?php esc_html_e('Scheduled Date', 'mailpn'); ?></th>
					<th><?php esc_html_e('Recipient', 'mailpn'); ?></th>
					<th><?php esc_html_e('Email Subject', 'mailpn'); ?></th>
					<th><?php esc_html_e('Created Date', 'mailpn'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($emails as $email): ?>
				<tr>
					<td><?php echo esc_html($email['scheduled_date']); ?></td>
					<td>
						<strong><?php echo esc_html($email['user_name']); ?></strong><br>
						<small><?php echo esc_html($email['user_email']); ?></small>
					</td>
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