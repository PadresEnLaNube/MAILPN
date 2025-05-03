<?php
/**
 * Settings manager.
 *
 * This class defines plugin settings, both in dashboard or in front-end.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Settings {
  public function get_options() {
    $mailpn_options['mailpn_section_contents_start'] = [
      'section' => 'start',
      'label' => __('Email contents', 'mailpn'),
      'description' => __('This options section sets information that the email will be showing.', 'mailpn'),
    ];
      $mailpn_options['mailpn_from_name'] = [
        'id' => 'mailpn_from_name',
        'class' => 'mailpn-input mailpn-vertical-align-top mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'label' => 'From name',
        'placeholder' => 'Email sent from name...',
        'description' => 'This option sets the sender name that will appear in the emails that the system sends.',
      ];
      $mailpn_options['mailpn_from_email'] = [
        'id' => 'mailpn_from_email',
        'class' => 'mailpn-input mailpn-vertical-align-top mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'email',
        'label' => 'From email address',
        'placeholder' => 'example@test.com',
        'description' => 'This option sets the sender email that will appear in the emails that the system sends.',
      ];
      $mailpn_options['mailpn_image_header'] = [
        'id' => 'mailpn_image_header',
        'class' => 'mailpn-input mailpn-vertical-align-top mailpn-width-100-percent',
        'input' => 'image',
        'label' => 'Email header image',
        'placeholder' => 'Email header image',
      ];
      $mailpn_options['mailpn_image_footer'] = [
        'id' => 'mailpn_image_footer',
        'class' => 'mailpn-input mailpn-vertical-align-top mailpn-width-100-percent',
        'input' => 'image',
        'label' => 'Email footer image',
        'placeholder' => 'Email footer image',
      ];
      $mailpn_options['mailpn_legal_name'] = [
        'id' => 'mailpn_legal_name',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'label' => __('Enterprise legal name', 'mailpn'),
        'placeholder' => __('Enterprise legal name', 'mailpn'),
      ];
      $mailpn_options['mailpn_legal_address'] = [
        'id' => 'mailpn_legal_address',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'label' => __('Email legal address', 'mailpn'),
        'placeholder' => __('Email legal address', 'mailpn'),
      ];
      $mailpn_options['mailpn_footer_reason'] = [
        'id' => 'mailpn_footer_reason',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'text',
        'label' => __('Custom reasons to receive email block', 'mailpn'),
        'placeholder' => __('Custom reasons to receive email block', 'mailpn'),
      ];
    $mailpn_options['mailpn_section_contents_end'] = [
      'section' => 'end',
    ];
    $mailpn_options['mailpn_section_mechanics_start'] = [
      'section' => 'start',
      'label' => __('Email mechanics', 'mailpn'),
      'description' => __('Set the way in which the emails will be sent.', 'mailpn'),
    ];
      $mailpn_options['mailpn_sent_every_ten_minutes'] = [
        'id' => 'mailpn_sent_every_ten_minutes',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'label' => __('Emails sent every ten minutes', 'mailpn'),
        'description' => __('Set the number of emails that the system will send every 10 mimutes. Default emails sent will be 5.', 'mailpn'),
      ];
      $mailpn_options['mailpn_sent_every_day'] = [
        'id' => 'mailpn_sent_every_day',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'label' => __('Emails sent every day', 'mailpn'),
        'description' => __('You can limit the number of emails sent everyday. Check your mail server settings and the system will automatically be adjusted to this number. Default emails sent will be 500.', 'mailpn'),
      ];
      $mailpn_options['mailpn_new_user_notifications'] = [
        'id' => 'mailpn_new_user_notifications',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'checkbox',
        'label' => __('New users active notifications', 'mailpn'),
        'description' => __('If you turn on this options the system will activate users notifications automatically on new accounts creation. It implieas that contacts will receive platform communications from the their user creation. Please, take into account to add this feature in your policies pages.', 'mailpn'),
      ];
      $mailpn_options['mailpn_errors_to_admin'] = [
        'id' => 'mailpn_errors_to_admin',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'checkbox',
        'label' => __('Send errors to admin account', 'mailpn'),
        'description' => __('Send an email to the administrator account whenever an error is produced on sending.', 'mailpn'),
      ];
      $mailpn_options['mailpn_password_new'] = [
        'id' => 'mailpn_password_new',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'checkbox',
        'parent' => 'this',
        'label' => __('New user creation welcome email', 'mailpn'),
        'description' => __('Send a New user creation welcome email instead of the WP template.', 'mailpn'),
      ];
        $mailpn_options['mailpn_password_new_before'] = [
          'id' => 'mailpn_password_new_before',
          'class' => 'mailpn-input mailpn-width-100-percent',
          'input' => 'editor',
          'parent' => 'mailpn_password_new',
          'parent_option' => 'on',
          'label' => __('Email contents before password creation link', 'mailpn'),
          'description' => __('If you complete this field the contents before the password creation link of the email will be customized with your information.', 'mailpn'),
        ];
        $mailpn_options['mailpn_password_new_after'] = [
          'id' => 'mailpn_password_new_after',
          'class' => 'mailpn-input mailpn-width-100-percent',
          'input' => 'editor',
          'parent' => 'mailpn_password_new',
          'parent_option' => 'on',
          'label' => __('Email contents after password creation link', 'mailpn'),
          'description' => __('If you complete this field the contents after the password creation link of the email will be customized with your information.', 'mailpn'),
        ];
      $mailpn_options['mailpn_password_retrieve'] = [
        'id' => 'mailpn_password_retrieve',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'checkbox',
        'parent' => 'this',
        'label' => __('Custom Password reset email', 'mailpn'),
        'placeholder' => __('Custom Password reset email', 'mailpn'),
        'description' => __('Send a custom Password reset email instead of the WP template.', 'mailpn'),
      ];
        $mailpn_options['mailpn_password_retrieve_before'] = [
          'id' => 'mailpn_password_retrieve_before',
          'class' => 'mailpn-input mailpn-width-100-percent',
          'input' => 'editor',
          'parent' => 'mailpn_password_retrieve',
          'parent_option' => 'on',
          'label' => __('Email contents before reset password link', 'mailpn'),
          'description' => __('If you complete this field the contents before the reset password link of the email will be customized with your information.', 'mailpn'),
        ];
        $mailpn_options['mailpn_password_retrieve_after'] = [
          'id' => 'mailpn_password_retrieve_after',
          'class' => 'mailpn-input mailpn-width-100-percent',
          'input' => 'editor',
          'parent' => 'mailpn_password_retrieve',
          'parent_option' => 'on',
          'label' => __('Email contents after reset password link', 'mailpn'),
          'description' => __('If you complete this field the contents after the reset password link of the email will be customized with your information.', 'mailpn'),
        ];
    $mailpn_options['mailpn_section_mechanics_end'] = [
      'section' => 'end',
    ];
    $mailpn_options['mailpn_section_design_start'] = [
      'section' => 'start',
      'label' => __('Email design', 'mailpn'),
    ];
      $mailpn_options['mailpn_max_width'] = [
        'id' => 'mailpn_max_width',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'number',
        'label' => __('Email max width in pixels', 'mailpn'),
        'placeholder' => __('Email max width in pixels', 'mailpn'),
      ];
      $mailpn_options['mailpn_links_color'] = [
        'id' => 'mailpn_links_color',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'color',
        'label' => __('Color of the email links', 'mailpn'),
      ];
    $mailpn_options['mailpn_section_design_end'] = [
      'section' => 'end',
    ];
    $mailpn_options['mailpn_submit'] = [
      'id' => 'mailpn_submit',
      'input' => 'input',
      'type' => 'submit',
      'value' => __('Save options', 'mailpn'),
    ];

    return $mailpn_options;
  }

	/**
	 * Administrator menu.
	 *
	 * @since    1.0.0
	 */
	public function mailpn_admin_menu() {
    add_menu_page(
      esc_html__('Mail Settings', 'mailpn'), 
      esc_html__('Mail Settings', 'mailpn'), 
      'administrator', 
      'mailpn_options', 
      [$this, 'mailpn_options'], 
      esc_url(MAILPN_URL . 'assets/media/mailpn-menu-icon.svg')
    );
    add_submenu_page(
      'mailpn_options', 
      esc_html__('Mail Templates', 'mailpn'), 
      esc_html__('Mail Templates', 'mailpn'), 
      'administrator', 
      'edit.php?post_type=mailpn_mail'
    );
    add_submenu_page(
      'mailpn_options', 
      esc_html__('Mail Records', 'mailpn'), 
      esc_html__('Mail Records', 'mailpn'), 
      'administrator', 
      'edit.php?post_type=mailpn_rec'
    );

    global $menu;
    if (!empty($menu)) {
      foreach ($menu as $menu_index => $menu_item) {
        if ($menu_item[2] == 'mailpn_options') {
          $menu[$menu_index][0] = esc_html__('Mailing Manager', 'mailpn');
        }
      }
    }
	}

	public function mailpn_options() {
	  ?>
	    <div class="mailpn-options mailpn-max-width-1000 mailpn-margin-auto mailpn-mt-50 mailpn-mb-50">
        <img src="<?php echo esc_url(MAILPN_URL . 'assets/media/banner-1544x500.png'); ?>" alt="<?php esc_html_e('Plugin main Banner', 'mailpn'); ?>" title="<?php esc_html_e('Plugin main Banner', 'mailpn'); ?>" class="mailpn-width-100-percent mailpn-border-radius-20 mailpn-mb-30">

        <div class="mailpn-display-table mailpn-width-100-percent">
          <div class="mailpn-display-inline-table mailpn-width-70-percent mailpn-tablet-display-block mailpn-tablet-width-100-percent">
            <h1 class="mailpn-mb-30"><?php esc_html_e('Mailing Manager - MAILPN Settings', 'mailpn'); ?></h1>
          </div>
          <div class="mailpn-display-inline-table mailpn-width-30-percent mailpn-tablet-display-block mailpn-tablet-width-100-percent mailpn-text-align-center">
            <?php echo do_shortcode('[mailpn-test-email-button]'); ?>
          </div>
        </div>

        <div class="mailpn-options-fields mailpn-mb-30">
          <form action="" method="post" id="mailpn_form" class="mailpn-form mailpn-p-30">
            <?php 
              $options = self::get_options();
              
              foreach ($options as $mailpn_option): 
                MAILPN_Forms::mailpn_input_wrapper_builder($mailpn_option, 'option', 0, 0, 'half');
              endforeach; 
            ?>
          </form> 
        </div>
      </div>
	  <?php
	}

  public function activated_plugin($plugin) {
    if($plugin == 'mailpn/mailpn.php') {
      wp_redirect(esc_url(admin_url('admin.php?page=mailpn_options')));exit();
    }
  }

  public function mailpn_wp_mail_from($email_address) {
    $from_mail = get_option('mailpn_from_email');
    return !empty($from_mail) ? $from_mail : $email_address;
  }
  
  public function mailpn_wp_mail_from_name($email_name) {
    $from_name = get_option('mailpn_from_name');
    return !empty($from_name) ? $from_name : $email_name;
  }

  public function mailpn_user_register($user_id) {
    if (get_option('mailpn_new_user_notifications') == 'on') {
      update_user_meta($user_id, 'userspn_notifications', 'on');
    }
  }

  public function mailpn_init_hook() {
    if (!isset($_GET['mailpn_action'])) {
        return;
    }

    switch (sanitize_text_field($_GET['mailpn_action'])) {
        case 'popup_open':
            if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'mailpn_action')) {
                wp_die(__('Security check failed: invalid nonce', 'mailpn'));
            }
            
            if (!isset($_GET['mailpn_popup'])) {
                wp_safe_redirect(home_url());
                exit();
            }
            // The popup will be handled by JavaScript
            break;
            
        case 'subscription-unsubscribe':
            if (!isset($_GET['subscription-unsubscribe-nonce']) || 
                !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['subscription-unsubscribe-nonce'])), 'subscription-unsubscribe')) {
                wp_safe_redirect(home_url('?mailpn_notice=subscription-unsubscribe-error'));
                exit();
            }
            
            // Add user check
            $user_id = isset($_GET['user']) ? absint($_GET['user']) : 0;
            if (!$user_id || !current_user_can('edit_user', $user_id)) {
                wp_safe_redirect(home_url('?mailpn_notice=subscription-unsubscribe-error'));
                exit();
            }
            
            update_user_meta($user_id, 'userspn_notifications', '');
            wp_safe_redirect(home_url('?mailpn_notice=subscription-unsubscribe-success'));
            exit();
            break;
    }
  }

  public function mailpn_pre_get_posts($query) {
    global $pagenow;
    $meta_query = [];

    if (is_admin() && $pagenow == 'edit.php') {
      switch (!empty($_GET['post_type']) && $_GET['post_type']) {
        case 'mailpn_rec':
          if (!isset($_GET['orderby'])) {
            $query->set('orderby', 'publish_date');
            $query->set('order', 'desc');
          }
      }

      if (!empty($meta_query)) {
        $query->set('meta_query', ['relation'  => 'AND', $meta_query]);
      }
    }
  }
}