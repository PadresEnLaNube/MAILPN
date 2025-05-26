<?php
/**
 * The Mailing functionalities of the plugin.
 *
 * Defines the behaviour of the plugin on Mailing functions.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Mailing {
  public function mailpn_text($atts) {
    /* echo do_shortcode('[mailpn-text query="addressee_name"]'); */
    $atts = shortcode_atts([
      'user_id' => 'addressee_user_id',
      'query' => 'addressee_name',
    ], $atts);
    
    $user_id = $atts['user_id'];
    $query = $atts['query'];

    $user_info = get_userdata($user_id);

    ob_start();

    switch ($query) {
      case 'addressee_name':
        return $user_info->first_name . ' ' . $user_info->last_name;
      case 'addressee_first_name':
        return $user_info->last_name;
      case 'addressee_last_name':
        return $user_info->last_name;
      case 'addressee_email':
        return $user_info->user_email;
      case 'addressee_id':
        return $user_info->ID;
      case 'addressee_nickname':
        return $user_info->nickname;
    }
  }

  public function mailpn_contents($atts) {
    /* echo do_shortcode('[mailpn-test mailpn_user_id="1"]'); */
    $atts = shortcode_atts([
      'post_id' => 0,
    ], $atts);
    
    $post_id = $atts['post_id'];
    /* echo do_shortcode('[mailpn-contents]'); */

    $mail_type = get_post_meta($post_id, 'mailpn_type', true);

    if ($mail_type == 'email_published_content') {
      $mail_content = get_post_meta($post_id, 'mailpn_updated_content', true);
      $mail_cpt = get_post_meta($post_id, 'mailpn_updated_content_cpt', true);
      $mail_amount = get_post_meta($post_id, 'mailpn_updated_content_new_amount', true);

      ob_start();
      
      switch ($mail_content) {
        case 'email_published_content_new':
          $mail_posts = get_posts(['fields' => 'ids', 'numberposts' => $mail_amount, 'post_type' => $mail_cpt, 'post_status' => ['any'], 'orderby' => 'publish_date', 'order' => 'DESC', ]);

          if (!empty($mail_posts)) {
            foreach ($mail_posts as $mail_post_id) {
              ?>
                <div class="mailpn-post">
                  <h3><a href="<?php echo esc_url(get_permalink($mail_post_id)); ?>"><?php echo esc_html(get_the_title($mail_post_id)); ?></a></h3>
                </div>
              <?php
            }
          }
          break;
        case 'email_published_content_period':
          // code...
          break;
        case 'email_published_content_date':
          // code...
          break;
      }

      $mailpn_return_string = ob_get_contents(); 
      ob_end_clean(); 
      return $mailpn_return_string;
    }

    return false;
  }

  public function mailpn_sender($atts, $mailpn_content = null) {
    /* echo do_shortcode('[mailpn-sender mailpn_type="email_welcome" mailpn_user_to="1" mailpn_subject="Mail Subject"]<h2 class="mailpn_h2_styles">Title</h2><p class="mailpn_p_styles">Paragraph</p>[/mailpn-sender]'); */
    $atts = shortcode_atts([
      'mailpn_user_to' => 1,
      'mailpn_id' => 0,
      'post_id' => 0,
      'post_parent_id' => 0,
      'mailpn_once' => 0,
      'mailpn_type' => '',
      'mailpn_subject' => '',
    ], $atts);
    
    $mailpn_user_to = $atts['mailpn_user_to'];
    $mailpn_id = $atts['mailpn_id'];
    $post_id = $atts['post_id'];
    $post_parent_id = $atts['post_parent_id'];
    $mailpn_once = $atts['mailpn_once'];
    $mailpn_type = $atts['mailpn_type'];
    $mailpn_subject = $atts['mailpn_subject'];

    $mailpn_result = 0;
    $user_email = get_userdata($mailpn_user_to)->user_email;

    
    $mailpn_exception_emails = get_option('mailpn_exception_emails');
    $mailpn_exception_emails_domains = get_option('mailpn_exception_emails_domains');
    $mailpn_exception_emails_addresses = get_option('mailpn_exception_emails_addresses');

    // Exception domains and emails check
    if ($mailpn_exception_emails == 'on') {
      if ($mailpn_exception_emails_domains == 'on') {
        $mailpn_exception_emails_domain = get_option('mailpn_exception_emails_domain');

        if (!empty($mailpn_exception_emails_domain)) {
          foreach ($mailpn_exception_emails_domain as $mailpn_exception_email_domain) {
            if (strpos($user_email, $mailpn_exception_email_domain) !== false) {
              return false;
            }
          }
        }
      }

      if ($mailpn_exception_emails_addresses == 'on') {
        $mailpn_exception_emails_address = get_option('mailpn_exception_emails_address');

        if (!empty($mailpn_exception_emails_address) && in_array($user_email, $mailpn_exception_emails_address)) {
          return false;
        }
      }
    }

    $mailpn_type = !empty($mailpn_type) ? $mailpn_type : (!empty($mailpn_id) ? get_post_meta($mailpn_id, 'mailpn_type', true) : bin2hex(openssl_random_pseudo_bytes(6)));
    $mailpn_subject = !empty($mailpn_subject) ? $mailpn_subject : (!empty($mailpn_id) ? esc_html(get_the_title($mailpn_id)) : esc_html(__('Mail subject', 'mailpn')));

    $mailpn_content = !empty($mailpn_id) ? get_post($mailpn_id)->post_content : $mailpn_content;

    if (!empty($mailpn_content)) {
      $content_filters = apply_filters('mailpn_content_filters', [
        '[user-name]' => '[user-name user_id="' . $mailpn_user_to . '"]',
        '[post-name]' => '[post-name post_id="' . $post_id . '"]',
        '[new-contents]' => '[new-contents post_id="' . $post_id . '"]',
      ], $post_id, $post_parent_id);

      foreach ($content_filters as $filter_base => $filter_final) {
        if (strpos($mailpn_content, $filter_base) !== false) {
          $mailpn_content = str_replace($filter_base, $filter_final, $mailpn_content);
        }
      }
    }

    $mailpn_content = do_shortcode($mailpn_content);
    
    // Replace links with tracking links if click tracking is enabled
    if (!empty($mailpn_id) && !empty($mailpn_user_to) && get_option('mailpn_click_tracking') === 'on') {
        $mailpn_content = MAILPN_Click_Tracking::replace_links($mailpn_content, $mailpn_id, $mailpn_user_to);
    }

    $mailpn_attachments = [];
    $attachments = !empty($mailpn_id) ? get_post_meta($mailpn_id, 'mailpn_attachments', true) : [];

    if (!empty($attachments)) {
      foreach ($attachments as $attachment_id) {
        $mailpn_attachments[] = get_attached_file($attachment_id);
      }
    }

    $mailpn_socials = [
      // 'Facebook' => ['img_src' => esc_url(home_url()) . '/wp-content/uploads/2019/08/pn-facebook.png', 'url' => ''],
    ];

    $mailpn_legal_name = get_option('mailpn_legal_name');
    $mailpn_legal_address = get_option('mailpn_legal_address');

    $headers[] = 'Content-Type:text/html;charset=UTF-8';
    $headers[] = get_bloginfo('url');

    $mailpn_message = self::mailpn_template($mailpn_subject, $mailpn_content, $mailpn_socials, $mailpn_legal_name, $mailpn_legal_address, $mailpn_user_to, $mailpn_id);

    if (filter_var($mailpn_user_to, FILTER_VALIDATE_EMAIL)) {
      $mailpn_result = wp_mail($mailpn_user_to, $mailpn_subject, $mailpn_message, $headers, $mailpn_attachments);
    }elseif (class_exists('USERSPN') && (get_user_meta($mailpn_user_to, 'userspn_notifications', true) == 'on' || in_array($mailpn_type, ['email_verify_code'])) && !empty($user_email) && !(self::mailpn_once_mailed($mailpn_id, $mailpn_user_to, $mailpn_once, $mailpn_type))) {
      $mailpn_result = wp_mail($user_email, $mailpn_subject, $mailpn_message, $headers, $mailpn_attachments);
    }else{
      $wph_meta_value = [
        'mailpn_user_to' => $mailpn_user_to,
        'user_email' => $user_email,
        'mailpn_type' => $mailpn_type,
        'mailpn_subject' => $mailpn_subject,
        'notifications' => get_user_meta($mailpn_user_to, 'userspn_notifications', true),
        'once' => !(self::mailpn_once_mailed($mailpn_id, $mailpn_user_to, $mailpn_once, $mailpn_type)),
      ];

      $unique_id = strtotime('now') . '-' . $mailpn_user_to;

      if(!empty($unique_id)) {
        if(empty(get_option('mailpn_error'))) {
          update_option('mailpn_error', [$mailpn_id => [$unique_id => $wph_meta_value]]);
        }else{
          $wph_option_new = get_option('mailpn_error', true);
          $wph_option_new[$mailpn_id][] = [$unique_id => $wph_meta_value];
          update_option('mailpn_error', $wph_option_new);
        }
      }
        
      return false;
    }
    
    $post_functions = new MAILPN_Functions_Post();

    if ($mailpn_result) {
      $post_functions->mailpn_insert_post($mailpn_subject, $mailpn_message, '', esc_url($mailpn_subject), 'mailpn_rec', 'publish', 1, 0, [], [], [
        'mailpn_rec_content' => $mailpn_message,
        'mailpn_rec_type' => $mailpn_type,
        'mailpn_rec_to' => $mailpn_user_to,
        'mailpn_rec_to_email' => $user_email,
        'mailpn_rec_attachments' => $mailpn_attachments,
        'mailpn_rec_mail_id' => $mailpn_id,
        'mailpn_rec_mail_result' => $mailpn_result,
        'mailpn_rec_post_id' => $post_id,
      ], false);

      return true;
    }else{
      $post_functions->mailpn_insert_post($mailpn_subject, $mailpn_message, '', esc_url($mailpn_subject), 'mailpn_rec', 'publish', 1, 0, [], [], [
        'mailpn_rec_content' => $mailpn_message,
        'mailpn_rec_type' => $mailpn_type,
        'mailpn_rec_to' => $mailpn_user_to,
        'mailpn_rec_to_email' => $user_email,
        'mailpn_rec_attachments' => $mailpn_attachments,
        'mailpn_rec_mail_id' => $mailpn_id,
        'mailpn_rec_mail_result' => $mailpn_result,
        'mailpn_rec_post_id' => $post_id,
      ], false);

      if (get_option('mailpn_errors_to_admin') == 'on') {
        $error_email = wp_mail(get_bloginfo('admin_email'), 'Error sending mail - ' . get_bloginfo('name'), 'mailpn_user_to: ' . $mailpn_user_to . '<br>mailpn_type: ' . $mailpn_type . '<br>mailpn_subject: ' . $mailpn_subject . '<br>mailpn_attachments: ' . implode(', ', $mailpn_attachments) . '<br>', $headers, $mailpn_attachments);
      }

      return false;
    }
  }

  public function mailpn_template($mailpn_subject, $mailpn_content, $mailpn_socials, $mailpn_legal_name, $mailpn_legal_address, $mailpn_user_to, $mailpn_id = 0) {
    $mailpn_template_css = file_get_contents(MAILPN_DIR . 'assets/css/mail-template.css');

    wp_register_style('mail-template-css', false);
    wp_enqueue_style('mail-template-css');
    wp_add_inline_style('mail-template-css', $mailpn_template_css);
    
    ob_start();
    ?>
      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
          <meta name="viewport" content="width=device-width" />
          <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
          <title><?php echo esc_html($mailpn_subject); ?></title>          
        </head>

        <body class="mailpn-content">
          <table class="mailpn-table-main" align="center">
            <tbody>
              <?php if (!empty(get_option('mailpn_image_header'))): ?>
                <tr style="text-align:center;">
                  <td class="text-align-center mailpn-mb-30" align="center">
                    <a target="_blank" href="<?php echo esc_url(home_url()); ?>" class="mailpn-header-image" style="color:#3d731a;text-decoration:none;"><img src="<?php echo esc_url(wp_get_attachment_image_src(get_option('mailpn_image_header'), 'full')[0]); ?>" border="0" alt="<?php echo esc_attr($mailpn_legal_name); ?>" style="max-height:150px;width:auto;margin-right:5px;margin-left:5px;margin-bottom:30px;"></a>
                  </td>
                </tr>
              <?php endif ?>

              <tr style="text-align:left;">
                <td>
                  <?php echo do_shortcode($mailpn_content); ?>
                </td>
              </tr>

              <?php if (!empty($mailpn_socials)): ?>
                <tr>
                  <td>
                    <p><?php esc_html_e('Follow us to keep in touch.', 'mailpn'); ?></p>
                  </td>
                </tr>

                <tr>
                  <table class="mailpn-table-social" align="center">
                    <tr>
                      <?php foreach ($mailpn_socials as $mailpn_social_name => $mailpn_social_data): ?>
                        <td class="mailpn-td-social">
                          <div align="center">
                            <a target="_blank" href="<?php echo esc_url($mailpn_social_data['url']); ?>" class="<?php echo esc_attr($mailpn_social_name); ?>"><img src="<?php echo esc_url($mailpn_social_data['img_src']); ?>" alt="<?php echo esc_attr($mailpn_social_name); ?>" class="mailpn-social-img"></a>
                          </div>
                        </td>
                      <?php endforeach ?>
                    </tr>
                  </table>
                </tr>
              <?php endif ?>

              <?php if (!empty(get_option('mailpn_image_footer'))): ?>
                <tr>
                  <td class="text-align-center" align="center">
                    <a target="_blank" href="<?php echo esc_url(home_url()); ?>" class="mailpn-header-image"><img src="<?php echo esc_url(wp_get_attachment_image_src(get_option('mailpn_image_footer'), 'full')[0]); ?>" border="0" alt="<?php echo esc_attr($mailpn_legal_name); ?>" style="height:50px;width:auto;margin-right:5px;margin-left:5px;"></a>
                  </td>
                </tr>
              <?php endif ?>

              <tr>
                <td width="100%" valign="top" align="center">
                  <div style="padding:20px;" class="mailpn-td-footer text-align-center">
                    <small class="text-align-center">
                      <p>© <?php echo esc_html($mailpn_legal_name); ?> <?php echo esc_html(gmdate('Y')); ?>.<br><?php esc_html_e('All rights reserved', 'mailpn'); ?>.</p>
                      <p><?php 
                        $footer_reason = get_option('mailpn_footer_reason');
                        echo !empty($footer_reason) 
                          ? esc_html($footer_reason) 
                          : esc_html__('You receive this email for your relationship with the project.', 'mailpn');
                      ?></p>

                      <?php if (class_exists('USERSPN') && !empty($mailpn_user_to)): ?>
                        <table align="center">
                          <tr>
                            <td align="center">
                              <a href="<?php echo esc_url(add_query_arg(['mailpn_action' => 'popup_open', 'mailpn_popup' => 'userspn-profile-popup', 'mailpn_tab' => 'notifications', '_wpnonce' => wp_create_nonce('mailpn_action')], home_url())); ?>"><small><?php esc_html_e('Manage subscription', 'mailpn'); ?></small></a>
                            </td>
                            
                            <td></td>

                            <td align="center">
                              <?php if (!filter_var($mailpn_user_to, FILTER_VALIDATE_EMAIL)): ?>
                                <?php echo wp_kses_post(self::mailpn_subscription_unsubscribe_btn($mailpn_user_to)); ?>
                              <?php endif ?>
                            </td>
                          </tr>
                        </table>
                      <?php endif ?>

                      <p><?php echo esc_html($mailpn_legal_address); ?></p>

                      <!-- Fallback confirmation button -->
                      <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; display: none;" id="mailpn-confirm-read">
                        <p style="margin-bottom: 10px;"><?php esc_html_e('Please confirm that you have read this email:', 'mailpn'); ?></p>
                        <a href="<?php echo esc_url(home_url('wp-json/mailpn/v1/track/' . $mailpn_user_to . '/' . $mailpn_id . '?confirm=1')); ?>" style="display: inline-block; padding: 8px 16px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">
                          <?php esc_html_e('Confirm Reading', 'mailpn'); ?>
                        </a>
                      </div>
                    </small>
                  </div>
                </td>
                
                <td>
                  <img src="<?php echo esc_url(home_url('wp-json/mailpn/v1/track/' . $mailpn_user_to . '/' . $mailpn_id)); ?>" width="1" height="1" alt="" style="display:none" onload="document.getElementById('mailpn-confirm-read').style.display='none';" onerror="document.getElementById('mailpn-confirm-read').style.display='block';" />
                </td>
              </tr>
            </tbody>
          </table>
        </body>
      </html> 
    <?php
    $mailpn_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $mailpn_return_string;
  }

  /**
   * Register the tracking pixel endpoint
   */
  public function register_tracking_endpoint() {
    register_rest_route('mailpn/v1', '/track/(?P<user_id>\d+)/(?P<mail_id>\d+)', [
      'methods' => 'GET',
      'callback' => [$this, 'handle_tracking_pixel'],
      'permission_callback' => '__return_true'
    ]);
  }

  /**
   * Handle the tracking pixel request
   */
  public function handle_tracking_pixel($request) {
    $user_id = $request->get_param('user_id');
    $mail_id = $request->get_param('mail_id');
    
    // Build meta query based on whether we have a specific mail_id
    $meta_query = [
      [
        'key' => 'mailpn_rec_to',
        'value' => $user_id
      ]
    ];

    // If we have a specific mail_id, add it to the query
    if (!empty($mail_id)) {
      $meta_query[] = [
        'key' => 'mailpn_rec_mail_id',
        'value' => $mail_id
      ];
    }
    
    // Get the email record
    $args = [
      'post_type' => 'mailpn_rec',
      'posts_per_page' => 1,
      'meta_query' => $meta_query,
      'orderby' => 'date',
      'order' => 'DESC'
    ];
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
      $post = $query->posts[0];
      
      // Update the email open status
      update_post_meta($post->ID, 'mailpn_rec_opened', true);
      update_post_meta($post->ID, 'mailpn_rec_opened_at', current_time('mysql'));
    }
    
    // Return a 1x1 transparent pixel
    header('Content-Type: image/gif');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Use a predefined constant for the 1x1 transparent GIF
    echo "\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00\x21\xf9\x04\x01\x00\x00\x00\x00\x2c\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02\x44\x01\x00\x3b";
    exit;
  }

  public function mailpn_user_name($atts) {
    $a = extract(shortcode_atts([
      'user_id' => 0,
    ], $atts));

    if (!empty($user_id)) {
      $user_info = get_userdata($user_id);

      if (!empty($user_info->first_name)) {
        return $user_info->first_name;
      }else if (!empty($user_info->last_name)) {
        return $user_info->last_name;
      }else if (!empty($user_info->user_nicename)){
        return $user_info->user_nicename;
      }else{
        return $user_info->user_email;
      }
    }
  }

  public function mailpn_post_name($atts) {
    $a = extract(shortcode_atts([
      'post_id' => 0,
    ], $atts));

    if (!empty($post_id)) {
      ob_start();
      ?>
        <a href="<?php echo esc_url(get_permalink($post_id)); ?>"><strong><?php echo esc_html(get_the_title($post_id)); ?></strong></a>
      <?php
      $wph_return_string = ob_get_contents(); 
      ob_end_clean(); 
      return $wph_return_string;
    }
  }

  public function mailpn_new_contents($atts) {
    $a = extract(shortcode_atts([
      'post_id' => 0,
    ], $atts));

    if (!empty($post_id)) {
      $post_type = !empty(get_post_meta($post_id, 'mailpn_updated_content_cpt', true)) ? get_post_meta($post_id, 'mailpn_updated_content_cpt', true) : 'post';

      $posts_atts = [
        'fields' => 'ids',
        'numberposts' => 1,
        'post_type' => $post_type,
        'post_status' => 'publish',
        'orderby' => 'publish_date',
        'order' => 'DESC',
      ];
      
      if (class_exists('Polylang')) {
        $posts_atts['lang'] = pll_current_language('slug');
      }
      
      $posts = get_posts($posts_atts);

      if (!empty($posts)) {
        foreach ($posts as $mail_post_id) {
          ob_start();
          ?>
            <a href="<?php echo esc_url(get_permalink($mail_post_id)); ?>"><strong><?php echo esc_html(get_the_title($mail_post_id)); ?></strong></a>
          <?php
        }
      }

      $wph_return_string = ob_get_contents(); 
      ob_end_clean(); 
      return $wph_return_string;
    }
  }

  public function mailpn_tools($atts) {
    /* echo do_shortcode('[mailpn-tools post_id="1"]'); */
    $a = extract(shortcode_atts([
      'post_id' => 0,
    ], $atts));
  
    if (empty($post_id)) {
      return false;
    }

    $mailpn_type = get_post_meta($post_id, 'mailpn_type', true);

    ob_start();
    ?>
      <?php if (in_array($mailpn_type, ['email_one_time', 'email_published_content', 'email_coded'])): ?>
        <?php $mailpn_status = get_post_meta($post_id, 'mailpn_status', true); ?>
        
        <?php if (!empty(get_option('mailpn_error')[$post_id])): ?>
          <div class="mailpn-error">
            <p class="mailpn-alert-error"><?php esc_html_e('Some errors have occurred. Please check if contacts have their notifications enabled.', 'mailpn'); ?></p>

            <ul class="mailpn-ml-20">
              <?php foreach (get_option('mailpn_error')[$post_id] as $unique_id => $mailpn_error): ?>
                <?php $user_info = get_userdata($mailpn_error['mailpn_user_to']); ?>

                <?php if (!empty($user_info)): ?>
                  <li>
                    <p><a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $mailpn_error['mailpn_user_to'])); ?>" class="mailpn-color-main-0 mailpn-font-weight-bold mailpn-mr-10" target="_blank"><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-main-0">person</i> #<?php echo esc_html($mailpn_error['mailpn_user_to']); ?> <?php echo esc_html($user_info->first_name) . ' ' . esc_html($user_info->last_name); ?></a> (<a href="mailto:<?php echo esc_html($user_info->user_email); ?>" target="_blank"><?php echo esc_html($user_info->user_email); ?></a>)</p>
                  </li>
                <?php endif ?>
              <?php endforeach ?>
            </ul>

            <a href="#" data-mailpn-post-id="<?php echo esc_html($post_id); ?>" class="mailpn-btn mailpn-btn-mini mailpn-btn-error-resend"><?php esc_html_e('Resend emails', 'mailpn'); ?></a><?php esc_html(MAILPN_Data::loader()); ?>
          </div>
        <?php else: ?>
          <div class="mailpn-progress">
            <?php if ($mailpn_status == 'sent'): ?>
              <p class="mailpn-alert-success"><?php esc_html_e('This mail has already been sent.', 'mailpn'); ?></p>
            <?php elseif ($mailpn_status == 'queue'): ?>
              <?php 
                $emails_pending = count(get_option('mailpn_queue')[$post_id]);
                $emails_sent = count(get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'mailpn_rec', 'post_status' => ['any'], 'meta_key' => 'mailpn_rec_mail_id', 'meta_value' => $post_id, 'orderby' => 'ID', 'order' => 'ASC', ]));
                $emails_total = $emails_pending + $emails_sent;
                $mails_sent_every_ten_minutes = (!empty(get_option('mailpn_sent_every_ten_minutes'))) ? get_option('mailpn_sent_every_ten_minutes') : 5;
              ?>

              <div class="mailpn-alert-warning mailpn-font-size-20">
                <?php esc_html_e('This mail is being sent.', 'mailpn'); ?> <?php esc_html(MAILPN_Data::mailpn_loader(true)); ?>
                  
                <div class="mailpn-progress-bar">
                  <p class="mailpn-font-weight-bold"><?php echo number_format(((intval($emails_sent) * 100) / intval($emails_total)), 1); ?>% <?php esc_html_e('of total job', 'mailpn'); ?> (<?php echo esc_html($emails_sent); ?> <?php esc_html_e('emails sent', 'mailpn'); ?> <?php esc_html_e('of', 'mailpn'); ?> <?php echo esc_html($emails_total); ?>)</p>
                </div>

                <div class="mailpn-text-align-right">
                  <p>* <?php esc_html_e('Sending', 'mailpn'); ?> <?php echo esc_html($mails_sent_every_ten_minutes); ?> <?php esc_html_e('emails every ten minutes', 'mailpn'); ?>.</p>
                </div>
              </div>
            <?php else: ?>
              <p class="mailpn-alert-warning"><?php esc_html_e('Publish or update to begin sending.', 'mailpn'); ?></p>
            <?php endif ?>
          </div>

          <div class="mailpn-sent-to">
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=mailpn_rec&mailpn_type_filter=' . $mailpn_type)); ?>" target="_blank" class="mailpn-btn mailpn-btn-mini"><?php esc_html_e('View latest submissions', 'mailpn'); ?></a>
          </div>
        <?php endif ?>

        <?php if (in_array($mailpn_type, ['email_published_content'])): ?>
          <?php
            $post_type = !empty(get_post_meta($post_id, 'mailpn_updated_content_cpt', true)) ? get_post_meta($post_id, 'mailpn_updated_content_cpt', true) : 'post';

            $posts_atts = [
              'fields' => 'ids',
              'numberposts' => -1,
              'post_type' => $post_type,
              'post_status' => 'future',
              'orderby' => 'publish_date',
              'order' => 'ASC',
            ];
            
            if (class_exists('Polylang')) {
              $posts_atts['lang'] = pll_current_language('slug');
            }
            
            $posts = get_posts($posts_atts);
          ?>
            <h3><?php esc_html_e('Any content you publish in the post type selected will be sent.', 'mailpn'); ?></h3>
            
            <?php if (!empty($posts)): ?>
              <h3><?php esc_html_e('Also there are new contents to be published in the future and sent by email.', 'mailpn'); ?></h3>

              <ul class="mailpn-ml-20">
                <?php foreach ($posts as $post_id): ?>
                  <li>
                    <span><i class="material-icons-outlined mailpn-font-size-20 mailpn-vertical-align-middle">calendar_today</i> <?php echo esc_html(gmdate(get_option('date_format') . ' ' . get_option('time_format'), strtotime(get_post($post_id)->post_date))); ?></span>
                    <a href="<?php echo esc_url(get_permalink($post_id)); ?>" target="_blank"><?php echo esc_html(get_the_title($post_id)); ?></a>  
                  </li>
                <?php endforeach ?>
              </ul>
              
            <?php endif ?>
        <?php endif ?>
      <?php endif ?>
    <?php
    $mailpn_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $mailpn_return_string;
  }

  public function mailpn_resend_errors($post_id) {
    $mailpn_error = get_option('mailpn_error');
    $mailpn_error_data = $mailpn_error[$post_id];

    if (!empty($mailpn_error_data)) {
      foreach ($mailpn_error_data as $unique_id => $mailpn_error_data) {
        self::mailpn_queue_add($post_id, $mailpn_error_data['mailpn_user_to']);
      }
    } 

    $mailpn_error[$post_id] = [];
    update_option('mailpn_error', $mailpn_error);
  }

  public function mailpn_subscription_unsubscribe_btn($user_id){
    ob_start();

    $url = add_query_arg([
        'mailpn_action' => 'subscription-unsubscribe',
        'user' => $user_id,
      ],
      esc_url(home_url())
    );

    $url_nonced = wp_nonce_url(html_entity_decode($url), 'subscription-unsubscribe', 'subscription-unsubscribe-nonce');
    ?>
      <a href="<?php echo esc_attr($url_nonced); ?>"><small><?php esc_html_e('Unsubscribe', 'mailpn'); ?></small></a>
    <?php
    $mailpn_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $mailpn_return_string;
  }

  public function mailpn_queue_process() {
    $mailpn_queue = get_option('mailpn_queue');
    $mailpn_queue_paused = get_option('mailpn_queue_paused');
    $mailing_counter = 0;
    $mailpn_mails_sent_today = (!empty(get_option('mailpn_mails_sent_today'))) ? get_option('mailpn_mails_sent_today') : 0;
    $mails_sent_every_ten_minutes = (!empty(get_option('mailpn_sent_every_ten_minutes'))) ? get_option('mailpn_sent_every_ten_minutes') : 5;
    $mails_sent_every_day = (!empty(get_option('mailpn_sent_every_day'))) ? get_option('mailpn_sent_every_day') : 500;

    if (!empty($mailpn_queue)) {
      if (empty($mailpn_queue_paused)) {
        foreach ($mailpn_queue as $mail_id => $mail_users) {
          if (!empty($mail_users)) {
            foreach ($mail_users as $index => $user_id) {
              $mail_result = do_shortcode('[mailpn-sender mailpn_user_to="' . $user_id . '" mailpn_id="' . $mail_id . '"]');

              unset($mailpn_queue[$mail_id][array_search($user_id, $mailpn_queue[$mail_id])]);
              update_option('mailpn_queue', $mailpn_queue);

              if ($mail_result) {
                $mailing_counter++;
              }

              if ($index == (count($mail_users) - 1)) {
                update_post_meta($mail_id, 'mailpn_status', 'sent');

                $mailpn_meta_value = current_time('timestamp');
                if(empty(get_post_meta($mail_id, 'mailpn_timestamp_sent', true))) {
                  update_post_meta($mail_id, 'mailpn_timestamp_sent', [$mailpn_meta_value]);
                }else{
                  $wph_post_meta_new = get_post_meta($mail_id, 'mailpn_timestamp_sent', true);
                  $wph_post_meta_new[] = $mailpn_meta_value;
                  update_post_meta($mail_id, 'mailpn_timestamp_sent', $wph_post_meta_new);
                }
              }

              if ($mailing_counter >= $mails_sent_every_ten_minutes) {
                $mailpn_mails_sent_today = $mailpn_mails_sent_today + $mailing_counter;
                update_option('mailpn_mails_sent_today', $mailpn_mails_sent_today);

                if ($mailpn_mails_sent_today >= ($mails_sent_every_day - $mails_sent_every_ten_minutes)) {
                  update_option('mailpn_queue_paused', strtotime('now'));
                }

                return true;
              }
            }
          }else{
            update_post_meta($mail_id, 'mailpn_status', 'sent');
          }
        }
      }else{
        if (strtotime(datetime: gmdate('+1 day', $mailpn_queue_paused)) < strtotime('now')) {
          delete_option('mailpn_queue_paused');
          delete_option('mailpn_mails_sent_today');
        }
      }
    }else{
      return false;
    }
  }

  public function mailpn_queue_add($mail_id, $user_id) {
    $mailpn_queue = get_option('mailpn_queue');

    if (!empty($mail_id) && !empty($user_id)) {
      if (empty($mailpn_queue)) {
        update_option('mailpn_queue', [$mail_id => [$user_id]]);
      }else{
        if (!array_key_exists($mail_id, $mailpn_queue) || (array_key_exists($mail_id, $mailpn_queue) && !in_array($user_id, $mailpn_queue[$mail_id]))) {
          if (!array_key_exists($mail_id, $mailpn_queue)) {
            $mailpn_queue[$mail_id] = [];
            update_option('mailpn_queue', $mailpn_queue);
          }

          $mailpn_queue[$mail_id][] = $user_id;
          update_option('mailpn_queue', $mailpn_queue);
        }
      }

      return true;
    }

    return false;
  }

  public function mailpn_once_mailed($mail_id, $user_id, $mailpn_once, $mailpn_type) {
    if (!empty($user_id)) {
      if ($mailpn_type != 'email_verify_code') {
        if ($mailpn_once) {
          if (!empty($mailpn_type) && !empty(get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'mailpn_rec', 'post_status' => 'publish', 'meta_query' => ['relation'  => 'AND', ['key' => 'mailpn_rec_type', 'value' => $mailpn_type, ], ['key' => 'mailpn_rec_to', 'value' => $user_id, ], ], 'orderby' => 'ID', 'order' => 'ASC', ]))) {
            return true;
          }
        }else{
          if (((!empty($mail_id) && in_array(get_post_meta($mail_id, 'mailpn_type', true), ['email_one_time', 'email_welcome', 'newsletter_welcome'])) || (in_array(get_post_meta($mail_id, 'mailpn_type', true), ['email_coded']) && get_post_meta($mail_id, 'mailpn_email_coded_once', true) == 'on')) && get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'mailpn_rec', 'post_status' => 'publish', 'meta_query' => ['relation'  => 'AND', ['key' => 'mailpn_rec_mail_id', 'value' => $mail_id, ], ['key' => 'mailpn_rec_to', 'value' => $user_id, ], ], 'orderby' => 'ID', 'order' => 'ASC', ])) {
            return true;
          }
        }
      }else{
        return false;
      }
    }

    return false;
  }

  public function mailpn_get_users_to($mail_id) {
    if (get_post_type($mail_id) != 'mailpn_mail') {
      return false;
    }

    $mail_distribution = get_post_meta($mail_id, 'mailpn_distribution', true);

    if ($mail_distribution == 'private_role') {
      $user_ids = [];
      $mailpn_distribution_role = get_post_meta($mail_id, 'mailpn_distribution_role', true);

      if (!empty($mailpn_distribution_role)) {
        foreach ($mailpn_distribution_role as $role) {
          $users_role = get_users(['fields' => 'ids', 'number' => -1, 'role' => $role, ]);
          
          if (!empty($users_role)) {
            foreach ($users_role as $user_id) {
              $user_ids[] = $user_id;
            }
          }
        }
      }

      return $user_ids;
    }elseif ($mail_distribution == 'private_user') {
      return get_post_meta($mail_id, 'mailpn_distribution_user', true);
    }else{
      return get_users(['fields' => 'ids', 'number' => -1, ]);
    }
  }

  public function mailpn_transition_post_status($new_status, $old_status, $post) {
    if (!($new_status !== 'publish' || $old_status === 'publish')) {
      $post_id = $post->ID;

      $emails_new_content_atts = ['fields' => 'ids',
        'numberposts' => -1,
        'post_type' => 'mailpn_mail',
        'post_status' => 'any', 
        'meta_key' => 'mailpn_type', 
        'meta_value' => 'email_published_content',
      ];

      if (class_exists('Polylang')) {
        $emails_new_content_atts['lang'] = pll_current_language('slug');
      }

      $emails_new_content = get_posts($emails_new_content_atts);

      if (!empty($emails_new_content)) {
        foreach ($emails_new_content as $mail_id) {
          update_post_meta($mail_id, 'mailpn_status', '');

          $mailpn_updated_content_cpt = get_post_meta($mail_id, 'mailpn_updated_content_cpt', true);
          
          if (!empty($mailpn_updated_content_cpt) && $mailpn_updated_content_cpt == $post->post_type) {
            $users_to = self::mailpn_get_users_to($mail_id);

            if (!empty($users_to)) {
              foreach ($users_to as $index => $user_id) {
                self::mailpn_queue_add($mail_id, $user_id);
                
                if ($index == (count($users_to) - 1)) {
                  update_post_meta($mail_id, 'mailpn_status', 'queue');
                }
              }
            }
          }
        }
      }
    }
  }

  public function mailpn_retrieve_password_message($message, $key, $user_login, $user_data) {
    ob_start();
    ?>
      <?php if (!empty(get_option('mailpn_password_retrieve_before'))): ?>
        <?php echo wp_kses_post(get_option('mailpn_password_retrieve_before')); ?>
      <?php else: ?>
        <p><?php esc_html_e('Hello', 'mailpn'); ?> [user-name].</p>

        <p><?php esc_html_e('We have received a request to reset the password for your account. If you made this request, click the link below to change your password:', 'mailpn'); ?></p>
      <?php endif ?>

      <div class="mailpn-text-align-center mailpn-mt-30 mailpn-mb-50">
        <a href="<?php echo esc_url(home_url('wp-login.php?action=rp&key=' . $key . '&login=') . rawurlencode($user_login)); ?>" class="mailpn-btn"><?php esc_html_e('Reset your password', 'mailpn'); ?></a>
      </div>

      <?php if (!empty(get_option('mailpn_password_retrieve_after'))): ?>
        <?php echo wp_kses_post(get_option('mailpn_password_retrieve_after')); ?>
      <?php else: ?>
        <p><?php esc_html_e('If you didn\'t make this request, ignore this email or', 'mailpn'); ?> <a href="mailto:<?php echo esc_html(get_option('admin_email')); ?>"><?php esc_html_e('please report it', 'mailpn'); ?></a>.</p>

        <p><?php esc_html_e('Thank you!', 'mailpn'); ?></p>
      <?php endif ?>
    <?php
    $mail_content = ob_get_contents(); 
    ob_end_clean(); 

    do_shortcode('[mailpn-sender mailpn_type="email_password_reset" mailpn_user_to="' . $user_data->ID . '" mailpn_subject="' . esc_html(__('Password reset', 'mailpn')) . '"]' . $mail_content . '[/mailpn-sender]');

    return '';
  }

  public function mailpn_wp_new_user_notification_email($wp_new_user_notification_email, $user, $blogname) {
    $user_login = stripslashes($user->user_login);
    $user_email = stripslashes($user->user_email);
    $login_url  = wp_login_url();

    ob_start();
    ?>
      <?php if (!empty(get_option('mailpn_password_new_before'))): ?>
        <?php echo wp_kses_post(get_option('mailpn_password_new_before')); ?>
      <?php else: ?>
        <p><?php esc_html_e('Hello', 'mailpn'); ?>.</p>

        <p><?php esc_html_e('You have a new user created on our platform. Please create a strong password to login.', 'mailpn'); ?></p>
        <p><?php esc_html_e('First of all, access the url below and use any of these credentials:', 'mailpn'); ?></p>
      <?php endif ?>

      <ul>
        <li><?php esc_html_e('Username', 'mailpn'); ?>: <?php echo esc_html($user_login); ?></li>
        <li><?php esc_html_e('Email', 'mailpn'); ?>: <?php echo esc_html($user_email); ?></li>
      </ul>

      <div class="mailpn-text-align-center mailpn-mt-30 mailpn-mb-50">
        <a href="<?php echo esc_url(wp_lostpassword_url(home_url())); ?>" class="mailpn-btn"><?php esc_html_e('Create new password', 'mailpn'); ?></a>
      </div>

      <?php if (!empty(get_option('mailpn_password_new_after'))): ?>
        <?php echo wp_kses_post(get_option('mailpn_password_new_after')); ?>
      <?php else: ?>
        <p><?php esc_html_e('Thank you!', 'mailpn'); ?></p>
      <?php endif ?>
    <?php
    $mail_content = ob_get_contents(); 
    ob_end_clean();

    $mailpn_socials = [];
    $mailpn_legal_name = get_option('mailpn_legal_name');
    $mailpn_legal_address = get_option('mailpn_legal_address');
    $mailpn_user_to = $user->ID;

    $wp_new_user_notification_email['subject'] = $blogname . ' ' . esc_html(__('New user', 'mailpn'));
    $wp_new_user_notification_email['headers'] = ['Content-Type: text/html; charset=UTF-8'];
    $wp_new_user_notification_email['message'] = $this->mailpn_template($blogname . ' ' . esc_html(__('New user', 'mailpn')), $mail_content, $mailpn_socials, $mailpn_legal_name, $mailpn_legal_address, $mailpn_user_to, 0);

    return $wp_new_user_notification_email;
  }

  public function mailpn_test_email_btn($atts) {
    if (!current_user_can('manage_options')) {
      return '';
    }

    ob_start();
    ?>
      <div class="mailpn-test-email-wrapper">
        <a class="mailpn-test-email-btn mailpn-btn mailpn-btn-mini">
          <?php esc_html_e('Send test email', 'mailpn'); ?>
        </a>
        <?php esc_html(MAILPN_Data::mailpn_loader()); ?>

        <span class="mailpn-test-email-result"></span>
      </div>
    <?php
    $mailpn_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $mailpn_return_string;
  }
}
