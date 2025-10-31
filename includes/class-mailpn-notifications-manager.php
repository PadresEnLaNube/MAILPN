<?php
/**
 * Notifications Manager.
 *
 * This class handles email notifications functionality for users.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Notifications_Manager {

    /**
     * Display email notifications for the current user
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     * @since    1.0.0
     */
    public function display_notifications($atts = array()) {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return '<p>' . __('You must be logged in to view notifications.', 'mailpn') . '</p>';
        }

        $current_user_id = get_current_user_id();
        
        // Parse shortcode attributes
        $atts = shortcode_atts(array(
            'limit' => 10,
            'show_read' => 'true',
            'show_unread' => 'true',
            'class' => '',
        ), $atts);

        $limit = intval($atts['limit']);
        $show_read = $atts['show_read'] === 'true';
        $show_unread = $atts['show_unread'] === 'true';

        // Build query arguments
        $args = array(
            'post_type' => 'mailpn_rec',
            'posts_per_page' => $limit,
            'meta_query' => array(
                array(
                    'key' => 'mailpn_rec_to',
                    'value' => $current_user_id,
                    'compare' => '='
                )
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        );

        // Add read status filter if needed
        if (!$show_read || !$show_unread) {
            $read_status_query = array();
            
            if ($show_read) {
                $read_status_query[] = array(
                    'key' => 'mailpn_rec_read',
                    'value' => '1',
                    'compare' => '='
                );
            }
            
            if ($show_unread) {
                $read_status_query[] = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'mailpn_rec_read',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'mailpn_rec_read',
                        'value' => '0',
                        'compare' => '='
                    )
                );
            }
            
            if (!empty($read_status_query)) {
                $args['meta_query'][] = array(
                    'relation' => 'OR',
                    $read_status_query
                );
            }
        }

        $query = new WP_Query($args);
        
        if (!$query->have_posts()) {
            return '<div class="mailpn-notifications-empty ' . esc_attr($atts['class']) . '">
                <p>' . __('No notifications found.', 'mailpn') . '</p>
            </div>';
        }

        ob_start();
        ?>
        <div class="mailpn-notifications-container <?php echo esc_attr($atts['class']); ?>">             
            <div class="mailpn-notifications-list">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $post_id = get_the_ID();
                    $is_read = get_post_meta($post_id, 'mailpn_rec_read', true);
                    $mail_type = get_post_meta($post_id, 'mailpn_rec_type', true);
                    $mail_subject = get_post_meta($post_id, 'mailpn_rec_subject', true);
                    $sent_datetime = get_post_meta($post_id, 'mailpn_rec_sent_datetime', true);
                    $mail_result = get_post_meta($post_id, 'mailpn_rec_mail_result', true);
                    $opened = get_post_meta($post_id, 'mailpn_rec_opened', true);
                    
                    $notification_class = 'mailpn-notification';
                    if ($is_read) {
                        $notification_class .= ' mailpn-notification-read';
                    } else {
                        $notification_class .= ' mailpn-notification-unread';
                    }
                    ?>
                    
                    <div class="<?php echo esc_attr($notification_class); ?>" data-notification-id="<?php echo esc_attr($post_id); ?>">
                        <div class="mailpn-notification-content">
                            <div class="mailpn-notification-header">
                                <div class="mailpn-notification-title">
                                    <h4 class="mailpn-notification-subject expandable-title" data-notification-id="<?php echo esc_attr($post_id); ?>" style="cursor: pointer; margin: 0;">
                                        <?php echo esc_html($mail_subject ?: get_the_title()); ?>
                                    </h4>
                                </div>
                                
                                <div class="mailpn-notification-actions">
                                    <div class="mailpn-tooltip">
                                        <button type="button" class="mailpn-notification-icon-btn expand-content" data-notification-id="<?php echo esc_attr($post_id); ?>" title="<?php _e('View content', 'mailpn'); ?>">
                                            <i class="material-icons-outlined">expand_more</i>
                                        </button>
                                        <span class="mailpn-tooltiptext"><?php _e('View content', 'mailpn'); ?></span>
                                    </div>
                                    
                                    <?php if (!$is_read): ?>
                                        <div class="mailpn-tooltip">
                                            <button type="button" class="mailpn-notification-icon-btn mark-read" data-notification-id="<?php echo esc_attr($post_id); ?>" title="<?php _e('Mark as read', 'mailpn'); ?>">
                                                <i class="material-icons-outlined">mark_email_read</i>
                                            </button>
                                            <span class="mailpn-tooltiptext"><?php _e('Mark as read', 'mailpn'); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="mailpn-tooltip">
                                            <button type="button" class="mailpn-notification-icon-btn mark-unread" data-notification-id="<?php echo esc_attr($post_id); ?>" title="<?php _e('Mark as unread', 'mailpn'); ?>">
                                                <i class="material-icons-outlined">mark_email_unread</i>
                                            </button>
                                            <span class="mailpn-tooltiptext"><?php _e('Mark as unread', 'mailpn'); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mailpn-notification-body">
                                <div class="mailpn-notification-meta">
                                    <span class="mailpn-notification-date">
                                        <i class="material-icons-outlined">schedule</i>
                                        <?php echo esc_html($this->format_notification_date($sent_datetime ?: get_the_date())); ?>
                                    </span>
                                    
                                    <span class="mailpn-notification-status">
                                        <?php if ($mail_result): ?>
                                            <i class="material-icons-outlined mailpn-color-green">check_circle</i>
                                            <?php _e('Sent successfully', 'mailpn'); ?>
                                        <?php else: ?>
                                            <i class="material-icons-outlined mailpn-color-red">error</i>
                                            <?php _e('Failed to send', 'mailpn'); ?>
                                        <?php endif; ?>
                                    </span>
                                    
                                    <?php if ($opened): ?>
                                        <span class="mailpn-notification-opened">
                                            <i class="material-icons-outlined mailpn-color-blue">visibility</i>
                                            <?php _e('Opened', 'mailpn'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mailpn-notification-expanded-content" id="expanded-content-<?php echo esc_attr($post_id); ?>">
                                <div class="mailpn-notification-content-preview">
                                    <?php 
                                    $mail_content = get_post_meta($post_id, 'mailpn_rec_content_html', true);
                                    if (empty($mail_content)) {
                                        $mail_content = get_post_meta($post_id, 'mailpn_rec_content', true);
                                    }
                                    if (empty($mail_content)) {
                                        $mail_content = get_the_content();
                                    }
                                    
                                    // Display content interpreted (HTML)
                                    if (!empty($mail_content)) {
                                        // If it's HTML content, process it and display
                                        if (strpos($mail_content, '<') !== false) {
                                            // Remove <style> blocks and external stylesheet links to avoid CSS leaking as text
                                            $content_html = preg_replace('/<style[^>]*>[\s\S]*?<\\/style>/i', '', $mail_content);
                                            $content_html = preg_replace('/<link[^>]*rel=["\']?stylesheet["\']?[^>]*>/i', '', $content_html);
                                            echo wp_kses_post($content_html);
                                        } else {
                                            // If it's plain text, convert line breaks to HTML
                                            echo wp_kses_post(nl2br(esc_html($mail_content)));
                                        }
                                    } else {
                                        echo '<p><em>' . __('No content available', 'mailpn') . '</em></p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <?php
        // Enqueue notifications JavaScript
        wp_enqueue_script('mailpn-notifications', plugin_dir_url(__FILE__) . '../assets/js/mailpn-notifications.js', array('jquery'), '1.0.0', true);
        
        // Localize script with AJAX data
        wp_localize_script('mailpn-notifications', 'mailpn_notifications_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mailpn_notification_nonce'),
            'mark_read_text' => __('Mark as read', 'mailpn'),
            'mark_unread_text' => __('Mark as unread', 'mailpn'),
            'processing_text' => __('Processing...', 'mailpn')
        ));
        ?>
        <?php
        
        wp_reset_postdata();
        return ob_get_clean();
    }

    /**
     * Display unread notifications counter
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     * @since    1.0.0
     */
    public function display_notifications_counter($atts = array()) {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return '';
        }

        $current_user_id = get_current_user_id();
        
        // Parse shortcode attributes
        $atts = shortcode_atts(array(
            'class' => '',
            'show_zero' => 'false',
            'format' => 'number', // 'number', 'badge', 'text'
        ), $atts);

        $unread_count = $this->get_unread_notifications_count($current_user_id);
        
        // Don't show anything if count is 0 and show_zero is false
        if ($unread_count == 0 && $atts['show_zero'] !== 'true') {
            return '';
        }

        ob_start();
        
        switch ($atts['format']) {
            case 'badge':
                ?>
                <span class="mailpn-notifications-badge <?php echo esc_attr($atts['class']); ?>" data-count="<?php echo esc_attr($unread_count); ?>">
                    <?php echo esc_html($unread_count); ?>
                </span>
                <?php
                break;
                
            case 'text':
                ?>
                <span class="mailpn-notifications-text <?php echo esc_attr($atts['class']); ?>">
                    <?php 
                    if ($unread_count == 0) {
                        _e('No unread notifications', 'mailpn');
                    } else {
                        printf(_n('%d unread notification', '%d unread notifications', $unread_count, 'mailpn'), $unread_count);
                    }
                    ?>
                </span>
                <?php
                break;
                
            case 'number':
            default:
                ?>
                <span class="mailpn-notifications-counter <?php echo esc_attr($atts['class']); ?>" data-count="<?php echo esc_attr($unread_count); ?>">
                    <?php echo esc_html($unread_count); ?>
                </span>
                <?php
                break;
        }
        
        return ob_get_clean();
    }

    /**
     * Get unread notifications count for a user
     *
     * @param int $user_id User ID
     * @return int Unread count
     * @since    1.0.0
     */
    public function get_unread_notifications_count($user_id) {
        $args = array(
            'post_type' => 'mailpn_rec',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'mailpn_rec_to',
                    'value' => $user_id,
                    'compare' => '='
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'mailpn_rec_read',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'mailpn_rec_read',
                        'value' => '0',
                        'compare' => '='
                    )
                )
            )
        );

        $query = new WP_Query($args);
        
        // Debug logging removed
        
        return $query->found_posts;
    }

    /**
     * Get total records count for a user (for debugging)
     *
     * @param int $user_id User ID
     * @return int Total count
     * @since    1.0.0
     */
    public function get_total_records_count($user_id) {
        $args = array(
            'post_type' => 'mailpn_rec',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'mailpn_rec_to',
                    'value' => $user_id,
                    'compare' => '='
                )
            )
        );

        $query = new WP_Query($args);
        return $query->found_posts;
    }

    /**
     * Get all notifications for a user
     *
     * @param int $user_id User ID
     * @param array $args Query arguments
     * @return WP_Query Query object
     * @since    1.0.0
     */
    public function get_user_notifications($user_id, $args = array()) {
        $default_args = array(
            'post_type' => 'mailpn_rec',
            'posts_per_page' => 10,
            'meta_query' => array(
                array(
                    'key' => 'mailpn_rec_to',
                    'value' => $user_id,
                    'compare' => '='
                )
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        );

        $args = wp_parse_args($args, $default_args);
        return new WP_Query($args);
    }

    /**
     * Mark notification as read
     *
     * @param int $notification_id Notification ID
     * @param int $user_id User ID (optional, defaults to current user)
     * @return bool Success status
     * @since    1.0.0
     */
    public function mark_notification_read($notification_id, $user_id = null) {
        if ($user_id === null) {
            $user_id = get_current_user_id();
        }

        // Verify the notification belongs to the user
        $notification_user_id = get_post_meta($notification_id, 'mailpn_rec_to', true);
        if ($notification_user_id != $user_id) {
            return false;
        }

        // Mark as read
        update_post_meta($notification_id, 'mailpn_rec_read', '1');
        update_post_meta($notification_id, 'mailpn_rec_read_at', current_time('mysql'));

        return true;
    }

    /**
     * Mark notification as unread
     *
     * @param int $notification_id Notification ID
     * @param int $user_id User ID (optional, defaults to current user)
     * @return bool Success status
     * @since    1.0.0
     */
    public function mark_notification_unread($notification_id, $user_id = null) {
        if ($user_id === null) {
            $user_id = get_current_user_id();
        }

        // Verify the notification belongs to the user
        $notification_user_id = get_post_meta($notification_id, 'mailpn_rec_to', true);
        if ($notification_user_id != $user_id) {
            return false;
        }

        // Mark as unread
        update_post_meta($notification_id, 'mailpn_rec_read', '0');
        delete_post_meta($notification_id, 'mailpn_rec_read_at');

        return true;
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int $user_id User ID
     * @return int Number of notifications marked as read
     * @since    1.0.0
     */
    public function mark_all_notifications_read($user_id) {
        // Get all unread notifications for the user
        $args = array(
            'post_type' => 'mailpn_rec',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'mailpn_rec_to',
                    'value' => $user_id,
                    'compare' => '='
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'mailpn_rec_read',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'mailpn_rec_read',
                        'value' => '0',
                        'compare' => '='
                    )
                )
            )
        );

        $query = new WP_Query($args);
        $marked_count = 0;

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                update_post_meta($post_id, 'mailpn_rec_read', '1');
                update_post_meta($post_id, 'mailpn_rec_read_at', current_time('mysql'));
                $marked_count++;
            }
            wp_reset_postdata();
        }

        return $marked_count;
    }

    /**
     * Get mail type icon
     *
     * @param string $mail_type Mail type
     * @return string Icon name
     * @since    1.0.0
     */
    private function get_mail_type_icon($mail_type) {
        $icons = array(
            'welcome' => 'waving_hand',
            'newsletter' => 'campaign',
            'password_reset' => 'lock_reset',
            'order_confirmation' => 'shopping_cart',
            'order_complete' => 'check_circle',
            'abandoned_cart' => 'shopping_cart_checkout',
            'custom' => 'email',
        );
        
        return isset($icons[$mail_type]) ? $icons[$mail_type] : 'email';
    }

    /**
     * Get mail type label
     *
     * @param string $mail_type Mail type
     * @return string Label
     * @since    1.0.0
     */
    private function get_mail_type_label($mail_type) {
        $labels = array(
            'welcome' => __('Welcome Email', 'mailpn'),
            'newsletter' => __('Newsletter', 'mailpn'),
            'password_reset' => __('Password Reset', 'mailpn'),
            'order_confirmation' => __('Order Confirmation', 'mailpn'),
            'order_complete' => __('Order Complete', 'mailpn'),
            'abandoned_cart' => __('Abandoned Cart', 'mailpn'),
            'custom' => __('Custom Email', 'mailpn'),
        );
        
        return isset($labels[$mail_type]) ? $labels[$mail_type] : ucfirst($mail_type);
    }

    /**
     * Format notification date
     *
     * @param string $date Date string
     * @return string Formatted date
     * @since    1.0.0
     */
    private function format_notification_date($date) {
        $timestamp = strtotime($date);
        $now = current_time('timestamp');
        $diff = $now - $timestamp;
        
        if ($diff < 3600) { // Less than 1 hour
            $minutes = floor($diff / 60);
            if ($minutes < 1) {
                return __('Just now', 'mailpn');
            }
            return sprintf(_n('%d minute ago', '%d minutes ago', $minutes, 'mailpn'), $minutes);
        } elseif ($diff < 86400) { // Less than 24 hours
            $hours = floor($diff / 3600);
            return sprintf(_n('%d hour ago', '%d hours ago', $hours, 'mailpn'), $hours);
        } elseif ($diff < 604800) { // Less than 1 week
            $days = floor($diff / 86400);
            return sprintf(_n('%d day ago', '%d days ago', $days, 'mailpn'), $days);
        } else {
            return date_i18n(get_option('date_format'), $timestamp);
        }
    }

    /**
     * Get notification statistics for a user
     *
     * @param int $user_id User ID
     * @return array Statistics
     * @since    1.0.0
     */
    public function get_user_notification_stats($user_id) {
        $total_args = array(
            'post_type' => 'mailpn_rec',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'mailpn_rec_to',
                    'value' => $user_id,
                    'compare' => '='
                )
            )
        );

        $unread_args = array(
            'post_type' => 'mailpn_rec',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'mailpn_rec_to',
                    'value' => $user_id,
                    'compare' => '='
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'mailpn_rec_read',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'mailpn_rec_read',
                        'value' => '0',
                        'compare' => '='
                    )
                )
            )
        );

        $total_query = new WP_Query($total_args);
        $unread_query = new WP_Query($unread_args);

        return array(
            'total' => $total_query->found_posts,
            'unread' => $unread_query->found_posts,
            'read' => $total_query->found_posts - $unread_query->found_posts
        );
    }

    /**
     * Render notifications before form filter
     *
     * @param string $content Current content
     * @param array $args Additional arguments
     * @return string Modified content with notifications
     * @since    1.0.0
     */
    public function mailpn_render_notifications_before_form($content, $args = array()) {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return $content;
        }

        // Get current user ID
        $current_user_id = get_current_user_id();
        
        // Get unread count
        $unread_count = $this->get_unread_notifications_count($current_user_id);
        
        if ($unread_count == 0) {
            $no_notifications = '<div class="mailpn-no-notifications" style="background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; text-align: center; color: #6c757d;">';
            $no_notifications .= '<p>No hay notificaciones de correo electrónico sin leer.</p>';
            $no_notifications .= '</div>';
            return $content . $no_notifications;
        }

        // Render notifications section
        ob_start();
        ?>
        <div class="mailpn-notifications-after-form">
            <div class="mailpn-notifications-summary">
                <div class="mailpn-notifications-summary-header">
                    <h4><?php _e('Email Notifications', 'mailpn'); ?></h4>
                    <div class="mailpn-notifications-actions">
                        <div class="mailpn-tooltip">
                            <button type="button" class="mailpn-notification-icon-btn mark-all-read" data-user-id="<?php echo esc_attr($current_user_id); ?>" title="<?php _e('Mark all as read', 'mailpn'); ?>">
                                <i class="material-icons-outlined">mark_email_read</i>
                            </button>
                            <span class="mailpn-tooltiptext"><?php _e('Mark all as read', 'mailpn'); ?></span>
                        </div>
                    </div>
                </div>
                <p class="mailpn-notifications-summary-text"><?php printf(_n('You have %d unread email notification', 'You have %d unread email notifications', $unread_count, 'mailpn'), $unread_count); ?></p>
                <div class="mailpn-notifications-list">
                    <?php echo do_shortcode('[mailpn-notifications limit="5" show_read="false"]'); ?>
                </div>
            </div>
        </div>
        <?php
        $notifications_content = ob_get_clean();

        // Append notifications to the original content
        return $content . $notifications_content;
    }

    /**
     * Add notifications icon to user profile
     *
     * @param string $content Current profile content
     * @param array $args Additional arguments
     * @return string Modified content with notifications icon
     * @since    1.0.0
     */
    public function add_notifications_icon_to_profile($content, $args = array()) {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return $content;
        }

		// Do not render in admin dashboard
		if (is_admin()) {
			return $content;
		}

        // Get current user ID
        $current_user_id = get_current_user_id();
        
        // Get unread count
        $unread_count = $this->get_unread_notifications_count($current_user_id);
        
        // If no unread notifications, return original content
        if ($unread_count == 0) {
            return $content;
        }

        // Create notifications icon
        ob_start();
        ?>
        <div class="mailpn-profile-notifications-icon" style="position: relative; display: inline-block; margin: 10px;">
            <div class="mailpn-tooltip">
                <a href="#" class="mailpn-notifications-profile-link" style="display: inline-block; padding: 8px; background: var(--mailpn-bg-color-main); color: white; border-radius: 50%; text-decoration: none; transition: all 0.3s ease;" title="<?php printf(_n('%d unread notification', '%d unread notifications', $unread_count, 'mailpn'), $unread_count); ?>">
                    <i class="material-icons-outlined" style="font-size: 24px;">notifications</i>
                </a>
                <span class="mailpn-tooltiptext"><?php printf(_n('%d unread notification', '%d unread notifications', $unread_count, 'mailpn'), $unread_count); ?></span>
            </div>
            
            <!-- Notification badge -->
            <span class="mailpn-notifications-badge" style="position: absolute; top: -5px; right: -5px; background: #f44336; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; font-weight: 600; min-width: 18px; text-align: center; line-height: 1.2;">
                <?php echo esc_html($unread_count); ?>
            </span>
        </div>
        
        <?php
        // Enqueue profile notifications JavaScript
        wp_enqueue_script('mailpn-profile-notifications', plugin_dir_url(__FILE__) . '../assets/js/mailpn-profile-notifications.js', array('jquery'), '1.0.0', true);
        
        // Localize script with data
        wp_localize_script('mailpn-profile-notifications', 'mailpn_profile_notifications', array(
            'no_section_message' => __('No notifications section found on this page.', 'mailpn')
        ));
        ?>
        <?php
        $notifications_icon = ob_get_clean();

        // Prepend notifications icon to the content
        return $notifications_icon . $content;
    }
}
