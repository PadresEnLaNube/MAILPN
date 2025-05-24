<?php
/**
 * Click tracking functionality.
 *
 * This class handles the click tracking functionality for email links.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */

class MAILPN_Click_Tracking {
    /**
     * Track a click on a link
     *
     * @param int $mail_id The ID of the email
     * @param int $user_id The ID of the user who clicked
     * @param string $original_url The original URL that was clicked
     * @return bool Whether the click was successfully tracked
     */
    public static function track_click($mail_id, $user_id, $original_url) {
        // Find the mail record post
        $args = [
            'post_type' => 'mailpn_rec',
            'posts_per_page' => 1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'mailpn_rec_mail_id',
                    'value' => $mail_id
                ],
                [
                    'key' => 'mailpn_rec_to',
                    'value' => $user_id
                ]
            ],
            'orderby' => 'date',
            'order' => 'DESC'
        ];
        
        $query = new WP_Query($args);
        
        if (!$query->have_posts()) {
            return false;
        }
        
        $record_id = $query->posts[0]->ID;
        
        // Get existing clicks or initialize new array
        $clicks = get_post_meta($record_id, 'mailpn_rec_clicks', true);
        if (!is_array($clicks)) {
            $clicks = [];
        }
        
        // Add new click
        $clicks[] = [
            'url' => $original_url,
            'clicked_at' => current_time('mysql'),
            'ip_address' => self::get_client_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''
        ];
        
        // Update meta
        return update_post_meta($record_id, 'mailpn_rec_clicks', $clicks);
    }

    /**
     * Get click statistics for an email
     *
     * @param int $mail_id The ID of the email
     * @return array Statistics about clicks for this email
     */
    public static function get_click_stats($mail_id) {
        $args = [
            'post_type' => 'mailpn_rec',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'mailpn_rec_mail_id',
                    'value' => $mail_id
                ]
            ]
        ];
        
        $query = new WP_Query($args);
        $total_clicks = 0;
        $unique_users = [];
        $clicks_by_url = [];
        
        if ($query->have_posts()) {
            foreach ($query->posts as $record) {
                $clicks = get_post_meta($record->ID, 'mailpn_rec_clicks', true);
                if (!is_array($clicks)) {
                    continue;
                }
                
                $user_id = get_post_meta($record->ID, 'mailpn_rec_to', true);
                $unique_users[$user_id] = true;
                
                foreach ($clicks as $click) {
                    $total_clicks++;
                    
                    if (!isset($clicks_by_url[$click['url']])) {
                        $clicks_by_url[$click['url']] = 0;
                    }
                    $clicks_by_url[$click['url']]++;
                }
            }
        }
        
        // Convert clicks_by_url to objects for consistency
        $clicks_by_url_objects = [];
        foreach ($clicks_by_url as $url => $count) {
            $clicks_by_url_objects[] = (object)[
                'original_url' => $url,
                'click_count' => $count
            ];
        }
        
        // Sort by click count descending
        usort($clicks_by_url_objects, function($a, $b) {
            return $b->click_count - $a->click_count;
        });
        
        return [
            'total_clicks' => $total_clicks,
            'unique_clicks' => count($unique_users),
            'clicks_by_url' => $clicks_by_url_objects
        ];
    }

    /**
     * Replace links in email content with tracking links
     *
     * @param string $content The email content
     * @param int $mail_id The ID of the email
     * @param int $user_id The ID of the recipient
     * @return string Modified content with tracking links
     */
    public static function replace_links($content, $mail_id, $user_id) {
        $pattern = '/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i';
        
        return preg_replace_callback($pattern, function($matches) use ($mail_id, $user_id) {
            $original_url = $matches[1];
            
            // Skip if it's already a tracking link or a mailto: link
            if (strpos($original_url, 'mailpn-track') !== false || strpos($original_url, 'mailto:') === 0) {
                return $matches[0];
            }

            // Create tracking URL
            $tracking_url = add_query_arg([
                'mailpn-track' => 1,
                'mail_id' => $mail_id,
                'user_id' => $user_id,
                'url' => urlencode($original_url)
            ], home_url('/'));

            // Replace the original URL with the tracking URL
            return str_replace($original_url, $tracking_url, $matches[0]);
        }, $content);
    }

    /**
     * Get client IP address
     *
     * @return string Client IP address
     */
    private static function get_client_ip() {
        $ip = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
} 