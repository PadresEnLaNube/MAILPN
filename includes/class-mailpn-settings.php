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
  public function mailpn_get_options() {
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
        'label' => __('Legal name', 'mailpn'),
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
      $mailpn_options['mailpn_click_tracking'] = [
        'id' => 'mailpn_click_tracking',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'checkbox',
        'label' => __('Enable click tracking', 'mailpn'),
        'description' => __('Track clicks on links in emails to gather statistics about user engagement.', 'mailpn'),
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
      $mailpn_options['mailpn_exception_emails'] = [
        'id' => 'mailpn_exception_emails',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'checkbox',
        'parent' => 'this',
        'label' => __('Exception emails', 'mailpn'),
        'placeholder' => __('Exception emails', 'mailpn'),
        'description' => __('Set the email addresses or domains that will be excluded from the email sending.', 'mailpn'),
      ];
        $mailpn_domain = str_replace(['www.', 'http://', 'https://'], '', get_bloginfo('url'));

        $mailpn_options['mailpn_exception_emails_domains'] = [
          'id' => 'mailpn_exception_emails_domains',
          'class' => 'mailpn-input mailpn-width-100-percent',
          'input' => 'input',
          'type' => 'checkbox',
          'parent' => 'this mailpn_exception_emails',
          'parent_option' => 'on',
          'label' => __('Exception emails domains', 'mailpn'),
          'placeholder' => __('Exception emails domains', 'mailpn'),
          'description' => __('You can set complete domains that will be excluded from the email sending.', 'mailpn'),
        ];
          $mailpn_options['mailpn_exception_emails_domains_list'] = [
            'id' => 'mailpn_exception_emails_domains_list',
            'class' => 'mailpn-input mailpn-width-100-percent',
            'input' => 'html_multi',
            'multi_array' => ['address', ],
            'parent' => 'mailpn_exception_emails_domains',
            'parent_option' => 'on',
            'label' => __('Exception emails domains list', 'mailpn'),
            'placeholder' => __('Exception emails domains list', 'mailpn'),
            'description' => __('Set the domains that will be excluded from the email sending. You can set the domains separated by commas.', 'mailpn'),
            'html_multi_fields' => [
              $mailpn_exception_emails_domain = [
                'id' => 'mailpn_exception_emails_domain',
                'class' => 'mailpn-input mailpn-width-100-percent',
                'input' => 'input',
                'type' => 'text',
                'multiple' => true,
                'label' => esc_html(__('Domain (for example: @' . $mailpn_domain, 'mailpn')),
                'placeholder' => esc_html(__('@' . $mailpn_domain, 'mailpn')),
              ],
            ]
          ];
        $mailpn_options['mailpn_exception_emails_addresses'] = [
          'id' => 'mailpn_exception_emails_addresses',
          'class' => 'mailpn-input mailpn-width-100-percent',
          'input' => 'input',
          'type' => 'checkbox',
          'parent' => 'this mailpn_exception_emails',
          'parent_option' => 'on',
          'label' => __('Exception emails addresses', 'mailpn'),
          'placeholder' => __('Exception emails addresses', 'mailpn'),
          'description' => __('You can set the addresses that will be excluded from the email sending.', 'mailpn'),
        ];
          $mailpn_options['mailpn_exception_emails_addresses_list'] = [
            'id' => 'mailpn_exception_emails_addresses_list',
            'class' => 'mailpn-input mailpn-width-100-percent',
            'input' => 'html_multi',
            'multi_array' => ['address', ],
            'parent' => 'mailpn_exception_emails_addresses',
            'parent_option' => 'on',
            'label' => __('Exception emails addresses list', 'mailpn'),
            'placeholder' => __('Exception emails addresses list', 'mailpn'),
            'description' => __('Set the addresses that will be excluded from the email sending. You can set the addresses separated by commas.', 'mailpn'),
            'html_multi_fields' => [
              $mailpn_exception_emails_address = [
                'id' => 'mailpn_exception_emails_address',
                'class' => 'mailpn-input mailpn-width-100-percent',
                'input' => 'input',
                'type' => 'text',
                'multiple' => 'true',
                'parent' => 'mailpn_exception_emails_addresses_list',
                'label' => esc_html(__('Address (for example: info@' . $mailpn_domain, 'mailpn')),
                'placeholder' => esc_html(__('info@' . $mailpn_domain, 'mailpn')),
              ],
            ]
          ];
        $mailpn_options['mailpn_errors_to_admin'] = [
          'id' => 'mailpn_errors_to_admin',
          'class' => 'mailpn-input mailpn-width-100-percent',
          'input' => 'input',
          'type' => 'checkbox',
          'label' => __('Send errors to admin account', 'mailpn'),
          'description' => __('Send an email to the administrator account whenever an error is produced on sending. It can reduce dramatically the number of emails sent to the users.', 'mailpn'),
        ];
  $mailpn_options['mailpn_section_mechanics_end'] = [
      'section' => 'end',
    ];

    // SMTP Configuration Section
    $mailpn_options['mailpn_section_smtp_start'] = [
      'section' => 'start',
      'label' => __('SMTP Configuration', 'mailpn'),
      'description' => __('Configure your SMTP server settings for sending emails.', 'mailpn'),
    ];

    $mailpn_options['mailpn_smtp_enabled'] = [
      'id' => 'mailpn_smtp_enabled',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'parent' => 'this',
      'label' => __('Enable SMTP', 'mailpn'),
      'description' => __('Enable SMTP for sending emails instead of using the default mail() function.', 'mailpn'),
    ];

    $mailpn_options['mailpn_smtp_host'] = [
      'id' => 'mailpn_smtp_host',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'parent' => 'mailpn_smtp_enabled',
      'parent_option' => 'on',
      'label' => __('SMTP Host', 'mailpn'),
      'placeholder' => 'smtp.example.com',
      'description' => __('The hostname of your SMTP server.', 'mailpn'),
    ];

    $mailpn_options['mailpn_smtp_port'] = [
      'id' => 'mailpn_smtp_port',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'parent' => 'mailpn_smtp_enabled',
      'parent_option' => 'on',
      'label' => __('SMTP Port', 'mailpn'),
      'placeholder' => '587',
      'description' => __('The port number of your SMTP server. Common ports: 587 (TLS), 465 (SSL).', 'mailpn'),
    ];

    $mailpn_options['mailpn_smtp_secure'] = [
      'id' => 'mailpn_smtp_secure',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'parent' => 'mailpn_smtp_enabled',
      'parent_option' => 'on',
      'options' => [
        'none' => __('None', 'mailpn'),
        'ssl' => __('SSL', 'mailpn'),
        'tls' => __('TLS', 'mailpn'),
      ],
      'label' => __('SMTP Security', 'mailpn'),
      'description' => __('The security type for your SMTP connection.', 'mailpn'),
    ];

    $mailpn_options['mailpn_smtp_auth'] = [
      'id' => 'mailpn_smtp_auth',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'parent' => 'this mailpn_smtp_enabled',
      'parent_option' => 'on',
      'label' => __('SMTP Authentication', 'mailpn'),
      'description' => __('Enable if your SMTP server requires authentication.', 'mailpn'),
    ];

    $mailpn_options['mailpn_smtp_username'] = [
      'id' => 'mailpn_smtp_username',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'parent' => 'mailpn_smtp_auth',
      'parent_option' => 'on',
      'label' => __('SMTP Username', 'mailpn'),
      'placeholder' => __('your@email.com', 'mailpn'),
      'description' => __('Your SMTP username or email address.', 'mailpn'),
    ];

    $mailpn_options['mailpn_smtp_password'] = [
      'id' => 'mailpn_smtp_password',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'password',
      'parent' => 'mailpn_smtp_auth',
      'parent_option' => 'on',
      'label' => __('SMTP Password', 'mailpn'),
      'placeholder' => '••••••••',
      'description' => __('Your SMTP password or app password. For Gmail, you MUST use an App Password (not your regular password). To create an App Password: 1) Enable 2-Factor Authentication on your Google Account, 2) Go to Security → App passwords, 3) Generate a password for "Mail".', 'mailpn'),
      'autocomplete' => 'new-password',
    ];

    $mailpn_options['mailpn_smtp_from_email'] = [
      'id' => 'mailpn_smtp_from_email',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'email',
      'parent' => 'mailpn_smtp_enabled',
      'parent_option' => 'on',
      'label' => __('From Email (SMTP)', 'mailpn'),
      'placeholder' => __('your@email.com', 'mailpn'),
      'description' => __('The email address that will be used as the sender when using SMTP.', 'mailpn'),
    ];

    $mailpn_options['mailpn_smtp_from_name'] = [
      'id' => 'mailpn_smtp_from_name',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'parent' => 'mailpn_smtp_enabled',
      'parent_option' => 'on',
      'label' => __('From Name (SMTP)', 'mailpn'),
      'placeholder' => 'Your Name',
      'description' => __('The name that will be used as the sender when using SMTP.', 'mailpn'),
    ];

    $mailpn_options['mailpn_section_smtp_end'] = [
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
      esc_html__('Email Settings', 'mailpn'), 
      esc_html__('Email Settings', 'mailpn'), 
      'manage_options', 
      'mailpn_options', 
      [$this, 'mailpn_options'], 
      esc_url(MAILPN_URL . 'assets/media/mailpn-menu-icon.svg'),
    );

    add_submenu_page(
      'mailpn_options', 
      esc_html__('Email Templates', 'mailpn'), 
      esc_html__('Email Templates', 'mailpn'), 
      'manage_options', 
      'edit.php?post_type=mailpn_mail',
    );
    
    add_submenu_page(
      'mailpn_options', 
      esc_html__('Emails sent', 'mailpn'), 
      esc_html__('Emails sent', 'mailpn'), 
      'manage_options', 
      'edit.php?post_type=mailpn_rec',
    );

    // Add submenu for pending welcome registrations and scheduled welcome emails (unified)
    add_submenu_page(
      'mailpn_options',
      esc_html__('Welcome Email Management', 'mailpn'),
      esc_html__('Welcome Email Management', 'mailpn'),
      'manage_options',
      'mailpn-welcome-management',
      [$this, 'mailpn_welcome_management_page']
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
              $options = $this->mailpn_get_options();
              
              foreach ($options as $mailpn_option): 
                MAILPN_Forms::mailpn_input_wrapper_builder($mailpn_option, 'option', 0, 0, 'half');
              endforeach; 
            ?>
          </form> 
        </div>
      </div>
	  <?php
	}

	/**
	 * Welcome Email Management page (unified)
	 *
	 * @since    1.0.0
	 */
	public function mailpn_welcome_management_page() {
		// Handle manual processing for scheduled emails
		if (isset($_POST['mailpn_process_scheduled']) && wp_verify_nonce($_POST['mailpn_process_scheduled_nonce'], 'mailpn_process_scheduled')) {
			$cron = new MAILPN_Cron();
			$cron->mailpn_process_scheduled_welcome_emails();
			echo '<div class="notice notice-success"><p>' . esc_html__('Scheduled emails processed successfully.', 'mailpn') . '</p></div>';
		}
		
		// Handle manual actions for scheduled emails
		if (isset($_POST['mailpn_send_now']) && wp_verify_nonce($_POST['mailpn_send_now_nonce'], 'mailpn_send_now')) {
			$index = intval($_POST['mailpn_email_index']);
			$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
			
			if (isset($scheduled_emails[$index])) {
				$scheduled_email = $scheduled_emails[$index];
				$mailing = new MAILPN_Mailing();
				
				$result = $mailing->mailpn_queue_add($scheduled_email['email_id'], $scheduled_email['user_id']);
				
				if ($result) {
					// Remove from scheduled list
					unset($scheduled_emails[$index]);
					$scheduled_emails = array_values($scheduled_emails);
					update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
					
					echo '<div class="notice notice-success"><p>' . esc_html__('Email sent immediately and removed from scheduled list.', 'mailpn') . '</p></div>';
				} else {
					echo '<div class="notice notice-error"><p>' . esc_html__('Failed to send email.', 'mailpn') . '</p></div>';
				}
			}
		}
		
		if (isset($_POST['mailpn_remove_scheduled']) && wp_verify_nonce($_POST['mailpn_remove_scheduled_nonce'], 'mailpn_remove_scheduled')) {
			$index = intval($_POST['mailpn_email_index']);
			$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
			
			if (isset($scheduled_emails[$index])) {
				unset($scheduled_emails[$index]);
				$scheduled_emails = array_values($scheduled_emails);
				update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
				
				echo '<div class="notice notice-success"><p>' . esc_html__('Scheduled email removed successfully.', 'mailpn') . '</p></div>';
			}
		}

		// Handle manual processing for pending registrations
		if (isset($_POST['mailpn_process_pending']) && wp_verify_nonce($_POST['mailpn_process_pending_nonce'], 'mailpn_process_pending')) {
			$this->mailpn_process_pending_welcome_registrations();
			echo '<div class="notice notice-success"><p>' . esc_html__('Pending registrations processed successfully.', 'mailpn') . '</p></div>';
		}
		
		?>
		<div class="mailpn-options mailpn-max-width-1000 mailpn-margin-auto mailpn-mt-50 mailpn-mb-50">
			<div class="mailpn-display-table mailpn-width-100-percent">
				<div class="mailpn-display-inline-table mailpn-width-70-percent mailpn-tablet-display-block mailpn-tablet-width-100-percent">
					<h1 class="mailpn-mb-30"><?php esc_html_e('Welcome Email Management', 'mailpn'); ?></h1>
				</div>
				<div class="mailpn-display-inline-table mailpn-width-30-percent mailpn-tablet-display-block mailpn-tablet-width-100-percent mailpn-text-align-center">
					<form method="post" style="display: inline;">
						<?php wp_nonce_field('mailpn_process_scheduled', 'mailpn_process_scheduled_nonce'); ?>
						<input type="submit" name="mailpn_process_scheduled" class="button button-primary" value="<?php esc_attr_e('Process Scheduled Emails', 'mailpn'); ?>">
					</form>
					<form method="post" style="display: inline;">
						<?php wp_nonce_field('mailpn_process_pending', 'mailpn_process_pending_nonce'); ?>
						<input type="submit" name="mailpn_process_pending" class="button button-secondary" value="<?php esc_attr_e('Process Pending Registrations', 'mailpn'); ?>">
					</form>
				</div>
			</div>

      <!-- PENDING WELCOME REGISTRATIONS SECTION -->
      <div class="mailpn-pending-registrations mailpn-mb-30">
        <h2><?php esc_html_e('Pending Welcome Registrations', 'mailpn'); ?></h2>
        <p><?php esc_html_e('These are user registrations waiting to be processed for welcome emails. They will be processed automatically when user roles are properly defined.', 'mailpn'); ?></p>
        
        <?php
        $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
        
        // Ensure $pending_registrations is always an array
        if (!is_array($pending_registrations)) {
          $pending_registrations = [];
        }
        
        if (empty($pending_registrations)) {
          echo '<p>' . esc_html__('No pending welcome registrations.', 'mailpn') . '</p>';
        } else {
          ?>
          <table class="wp-list-table widefat fixed striped">
            <thead>
              <tr>
                <th><?php esc_html_e('User', 'mailpn'); ?></th>
                <th><?php esc_html_e('Email', 'mailpn'); ?></th>
                <th><?php esc_html_e('Registration Time', 'mailpn'); ?></th>
                <th><?php esc_html_e('User Roles', 'mailpn'); ?></th>
                <th><?php esc_html_e('Status', 'mailpn'); ?></th>
              </tr>
            </thead>
            <tbody>
          <?php

          foreach ($pending_registrations as $registration) {
            $user = get_userdata($registration['user_id']);
            
            if (!$user) {
              $user_name = esc_html__('User not found', 'mailpn');
              $user_email = esc_html__('N/A', 'mailpn');
              $user_roles = esc_html__('N/A', 'mailpn');
            } else {
              $user_name = $user->display_name;
              $user_email = $user->user_email;
              $user_roles = !empty($user->roles) ? implode(', ', $user->roles) : esc_html__('No roles', 'mailpn');
            }
            
            $registration_time = date('Y-m-d H:i:s', $registration['registration_time']);
            $status = $registration['processed'] ? 
              '<span style="color: green;">' . esc_html__('Processed', 'mailpn') . '</span>' : 
              '<span style="color: orange;">' . esc_html__('Pending', 'mailpn') . '</span>';
            
            ?>
              <tr>
                <td><?php echo esc_html($user_name); ?></td>
                <td><?php echo esc_html($user_email); ?></td>
                <td><?php echo esc_html($registration_time); ?></td>
                <td><?php echo esc_html($user_roles); ?></td>
                <td><?php echo $status; ?></td>
              </tr>
            <?php
          }
          ?>
            </tbody>
          </table>
          <?php
        }
        ?>
      </div>

			<!-- SCHEDULED WELCOME EMAILS SECTION -->
			<div class="mailpn-scheduled-emails mailpn-mb-30">
				<h2><?php esc_html_e('Scheduled Welcome Emails', 'mailpn'); ?></h2>
				<?php
				$scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
				
				// Ensure $scheduled_emails is always an array
				if (!is_array($scheduled_emails)) {
					$scheduled_emails = [];
				}
				
				if (empty($scheduled_emails)) {
					echo '<p>' . esc_html__('No pending scheduled welcome emails.', 'mailpn') . '</p>';
				} else {
          ?>
					<table class="wp-list-table widefat fixed striped">
            <thead>
              <tr>
                <th><?php esc_html_e('Email Template', 'mailpn'); ?></th>
                <th><?php esc_html_e('User', 'mailpn'); ?></th>
                <th><?php esc_html_e('Scheduled Time', 'mailpn'); ?></th>
                <th><?php esc_html_e('Created Time', 'mailpn'); ?></th>
                <th><?php esc_html_e('Status', 'mailpn'); ?></th>
                <th><?php esc_html_e('Actions', 'mailpn'); ?></th>
              </tr>
            </thead>
					  <tbody>
					<?php

					foreach ($scheduled_emails as $index => $scheduled_email) {
						$email_post = get_post($scheduled_email['email_id']);
						$user = get_userdata($scheduled_email['user_id']);
						
						$email_title = $email_post ? $email_post->post_title : esc_html__('Unknown', 'mailpn');
						$user_name = $user ? $user->display_name : esc_html__('Unknown', 'mailpn');
						$user_email = $user ? $user->user_email : esc_html__('Unknown', 'mailpn');
						$scheduled_time = date('Y-m-d H:i:s', $scheduled_email['scheduled_time']);
						$created_time = date('Y-m-d H:i:s', $scheduled_email['created_time']);
						
						$current_time = time();
						$status = ($scheduled_email['scheduled_time'] <= $current_time) ? 
							'<span style="color: green; font-weight: bold;">' . esc_html__('Ready to send', 'mailpn') . '</span>' : 
							'<span style="color: orange;">' . esc_html__('Scheduled', 'mailpn') . '</span>';
						
            ?>
						<tr>
						<td>
							<strong><?php echo esc_html($email_title); ?></strong><br>
							<small>ID: <?php echo esc_html($scheduled_email['email_id']); ?></small>
						</td>
						<td>
							<strong><?php echo esc_html($user_name); ?></strong><br>
							<small><?php echo esc_html($user_email); ?></small><br>
							<small>ID: <?php echo esc_html($scheduled_email['user_id']); ?></small>
						</td>
						<td><?php echo esc_html($scheduled_time); ?></td>
						<td><?php echo esc_html($created_time); ?></td>
						<td><?php echo $status; ?></td>
						<td>
							<form method="post" style="display: inline;">
								<?php wp_nonce_field('mailpn_send_now', 'mailpn_send_now_nonce'); ?>
								<input type="hidden" name="mailpn_email_index" value="<?php echo esc_attr($index); ?>">
								<input type="submit" name="mailpn_send_now" class="button button-small button-primary" value="<?php esc_attr_e('Send Now', 'mailpn'); ?>" onclick="return confirm('<?php esc_attr_e('Are you sure you want to send this email now?', 'mailpn'); ?>')">
							</form>
							<form method="post" style="display: inline;">
								<?php wp_nonce_field('mailpn_remove_scheduled', 'mailpn_remove_scheduled_nonce'); ?>
								<input type="hidden" name="mailpn_email_index" value="<?php echo esc_attr($index); ?>">
								<input type="submit" name="mailpn_remove_scheduled" class="button button-small button-secondary" value="<?php esc_attr_e('Remove', 'mailpn'); ?>" onclick="return confirm('<?php esc_attr_e('Are you sure you want to remove this scheduled email?', 'mailpn'); ?>')">
							</form>
						</td>
						</tr>
            <?php
					}
          ?>
					</tbody>
					</table>
          <?php
				}
				?>
			</div>

			<div class="mailpn-sent-emails mailpn-mb-30">
				<h2><?php esc_html_e('Recently Sent Scheduled Emails', 'mailpn'); ?></h2>
				<?php
				$scheduled_logs = get_option('mailpn_scheduled_welcome_logs', []);
				
				// Ensure $scheduled_logs is always an array
				if (!is_array($scheduled_logs)) {
					$scheduled_logs = [];
				}
				
				if (empty($scheduled_logs)) {
          ?>
					  <p><?php esc_html_e('No recently sent scheduled welcome emails.', 'mailpn'); ?></p>
          <?php
				} else {
					// Show only the last 20 sent emails
					$recent_logs = array_slice($scheduled_logs, -20);
					
					?>
					<table class="wp-list-table widefat fixed striped">
            <thead>
              <tr>
                <th><?php esc_html_e('Email Template', 'mailpn'); ?></th>
                <th><?php esc_html_e('User', 'mailpn'); ?></th>
                <th><?php esc_html_e('Scheduled Time', 'mailpn'); ?></th>
                <th><?php esc_html_e('Sent Time', 'mailpn'); ?></th>
                <th><?php esc_html_e('Delay', 'mailpn'); ?></th>
              </tr>
            </thead>
					  <tbody>
					<?php

					foreach ($recent_logs as $log) {
						$email_post = get_post($log['email_id']);
						$user = get_userdata($log['user_id']);
						
						$email_title = $email_post ? $email_post->post_title : esc_html__('Unknown', 'mailpn');
						$user_name = $user ? $user->display_name : esc_html__('Unknown', 'mailpn');
						$user_email = $user ? $user->user_email : esc_html__('Unknown', 'mailpn');
						$scheduled_time = date('Y-m-d H:i:s', $log['scheduled_time']);
						$sent_time = date('Y-m-d H:i:s', $log['sent_time']);
						
						// Calculate delay
						$delay_seconds = $log['scheduled_time'] - $log['created_time'];
						$delay_text = '';
						if ($delay_seconds > 0) {
							if ($delay_seconds >= DAY_IN_SECONDS) {
								$delay_text = round($delay_seconds / DAY_IN_SECONDS, 1) . ' ' . esc_html__('days', 'mailpn');
							} elseif ($delay_seconds >= HOUR_IN_SECONDS) {
								$delay_text = round($delay_seconds / HOUR_IN_SECONDS, 1) . ' ' . esc_html__('hours', 'mailpn');
							} else {
								$delay_text = round($delay_seconds / 60, 1) . ' ' . esc_html__('minutes', 'mailpn');
							}
						} else {
							$delay_text = esc_html__('Immediate', 'mailpn');
						}
						
						?>
              <tr>
                <td>
					<strong><?php echo esc_html($email_title); ?></strong><br>
					<small>ID: <?php echo esc_html($log['email_id']); ?></small>
				</td>
                <td>
					<strong><?php echo esc_html($user_name); ?></strong><br>
					<small><?php echo esc_html($user_email); ?></small><br>
					<small>ID: <?php echo esc_html($log['user_id']); ?></small>
				</td>
                <td><?php echo esc_html($scheduled_time); ?></td>
                <td><?php echo esc_html($sent_time); ?></td>
                <td><?php echo esc_html($delay_text); ?></td>
              </tr>
            <?php
					}

          ?>
            </tbody>
          </table>
          <?php
				}
				?>
			</div>
			
			<div class="mailpn-system-info mailpn-mb-30">
				<h2><?php esc_html_e('System Information', 'mailpn'); ?></h2>
				<table class="wp-list-table widefat fixed striped">
					<tbody>
						<tr>
							<td><strong><?php esc_html_e('Current Time', 'mailpn'); ?></strong></td>
							<td><?php echo esc_html(date('Y-m-d H:i:s')); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e('Timezone', 'mailpn'); ?></strong></td>
							<td><?php echo esc_html(wp_timezone_string()); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e('Next 10-minute Cron', 'mailpn'); ?></strong></td>
							<td>
								<?php 
								$next_cron = wp_next_scheduled('mailpn_cron_ten_minutes');
								if ($next_cron) {
									echo esc_html(date('Y-m-d H:i:s', $next_cron));
								} else {
									echo '<span style="color: red;">' . esc_html__('Not scheduled', 'mailpn') . '</span>';
								}
								?>
							</td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e('Total Scheduled Emails', 'mailpn'); ?></strong></td>
							<td><?php echo esc_html(count($scheduled_emails)); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e('Total Sent Logs', 'mailpn'); ?></strong></td>
							<td><?php echo esc_html(count($scheduled_logs)); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e('Total Pending Registrations', 'mailpn'); ?></strong></td>
							<td><?php echo esc_html(count($pending_registrations)); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

  public function mailpn_activated_plugin($plugin) {
    if($plugin == 'mailpn/mailpn.php') {
      wp_safe_redirect(esc_url(admin_url('admin.php?page=mailpn_options')));exit;
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
    
    // Instead of triggering welcome emails immediately, accumulate the registration
    // for later processing when roles are properly defined
    $this->mailpn_accumulate_user_registration($user_id);

    // Immediately try to process to handle simple registration cases without delay
    $this->mailpn_process_pending_welcome_registrations();
  }
  
  /**
   * Accumulate user registration for later welcome email processing
   *
   * @param int $user_id The user ID
   */
  public function mailpn_accumulate_user_registration($user_id) {
    $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
    
    // Ensure $pending_registrations is always an array
    if (!is_array($pending_registrations)) {
      $pending_registrations = [];
    }
    
    // Add this user to pending registrations
    $pending_registrations[] = [
      'user_id' => $user_id,
      'registration_time' => time(),
      'processed' => false
    ];
    
    update_option('mailpn_pending_welcome_registrations', $pending_registrations);
  }
  
  /**
   * Process pending user registrations for welcome emails
   * This should be called after user roles are properly defined
   * 
   * @param int $user_id The user ID (optional, for hook compatibility)
   * @param string $role The new role (optional, for set_user_role hook)
   * @param array $old_roles The old roles (optional, for set_user_role hook)
   */
  public function mailpn_process_pending_welcome_registrations($user_id = null, $role = null, $old_roles = null) {
    $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
    
    // Ensure $pending_registrations is always an array
    if (!is_array($pending_registrations)) {
      $pending_registrations = [];
    }
    
    if (empty($pending_registrations)) {
      return;
    }
    
    $updated_pending_registrations = [];
    
    foreach ($pending_registrations as $registration) {
      // If already processed, keep it for cleanup later
      if ($registration['processed']) {
        $updated_pending_registrations[] = $registration;
        continue;
      }
      
      $reg_user_id = $registration['user_id'];
      $user = get_userdata($reg_user_id);
      
      // If user doesn't exist, discard this registration
      if (!$user) {
        continue;
      }
      
      // If user has the newsletter subscriber role, mark as processed and keep
      if (in_array('userspn_newsletter_subscriber', $user->roles)) {
        $registration['processed'] = true;
        $updated_pending_registrations[] = $registration;
        continue;
      }
      
      // Try to send welcome emails
      $sent = $this->mailpn_trigger_welcome_emails($reg_user_id);
      
      // If an email was sent, mark as processed
      if ($sent) {
        $registration['processed'] = true;
      }
      
      // Always add the registration back to the list (it's either now processed or will be retried)
      $updated_pending_registrations[] = $registration;
    }
    
    update_option('mailpn_pending_welcome_registrations', $updated_pending_registrations);
  }
  
  /**
   * Process a specific welcome registration
   *
   * @param array $registration The registration data
   * @return bool True if processed successfully, false otherwise
   */
  public function mailpn_process_specific_welcome_registration($registration) {
    $reg_user_id = $registration['user_id'];
    $user = get_userdata($reg_user_id);
    
    // If user doesn't exist, return false
    if (!$user) {
      return false;
    }
    
    // If user has the newsletter subscriber role, mark as processed
    if (in_array('userspn_newsletter_subscriber', $user->roles)) {
      return true;
    }
    
    // Try to send welcome emails
    $sent = $this->mailpn_trigger_welcome_emails($reg_user_id);
    
    return $sent;
  }
  
  /**
   * Clean up old processed registrations (older than 7 days)
   */
  public function mailpn_cleanup_old_pending_registrations() {
    $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
    
    // Ensure $pending_registrations is always an array
    if (!is_array($pending_registrations)) {
      $pending_registrations = [];
    }
    
    $seven_days_ago = time() - (7 * DAY_IN_SECONDS);
    $cleaned_registrations = [];
    
    foreach ($pending_registrations as $registration) {
      // Keep only recent registrations or unprocessed ones
      if ($registration['registration_time'] > $seven_days_ago || !$registration['processed']) {
        $cleaned_registrations[] = $registration;
      }
    }
    
    update_option('mailpn_pending_welcome_registrations', $cleaned_registrations);
  }
  
  /**
   * Trigger welcome emails for newly registered users
   *
   * @param int $user_id The user ID
   */
  public function mailpn_trigger_welcome_emails($user_id) {
    // Get all welcome email templates
    $welcome_emails = get_posts([
      'fields' => 'ids',
      'numberposts' => -1,
      'post_type' => 'mailpn_mail',
      'post_status' => 'publish',
      'meta_query' => [
        [
          'key' => 'mailpn_type',
          'value' => 'email_welcome',
          'compare' => '='
        ]
      ]
    ]);
    
    if (empty($welcome_emails)) {
      return false;
    }
    
    $mailing_plugin = new MAILPN_Mailing();
    $email_queued = false;
    
    foreach ($welcome_emails as $email_id) {
      // Check if user should receive this email based on distribution settings
      $distribution = get_post_meta($email_id, 'mailpn_distribution', true);
      $should_send = false;
      
      switch ($distribution) {
        case 'public':
          $should_send = true;
          break;
        case 'private_role':
          $user_roles = get_post_meta($email_id, 'mailpn_distribution_role', true);
          $user = get_userdata($user_id);
          if (!empty($user_roles) && !empty($user)) {
            foreach ($user_roles as $role) {
              if (in_array($role, $user->roles)) {
                $should_send = true;
                break;
              }
            }
          }
          break;
        case 'private_user':
          $user_list = get_post_meta($email_id, 'mailpn_distribution_user', true);
          if (!empty($user_list) && in_array($user_id, $user_list)) {
            $should_send = true;
          }
          break;
      }
      
      if (!$should_send) {
        continue;
      }
      
      // Check if delay is enabled for this welcome email
      $delay_enabled = get_post_meta($email_id, 'mailpn_welcome_delay_enabled', true);
      
      if ($delay_enabled === 'on') {
        // Schedule the email for delayed sending
        $this->mailpn_schedule_delayed_welcome_email($email_id, $user_id);
      } else {
        // Send immediately
        $mailing_plugin->mailpn_queue_add($email_id, $user_id);
      }
      $email_queued = true;
    }
    return $email_queued;
  }
  
  /**
   * Schedule a delayed welcome email
   *
   * @param int $email_id The email template ID
   * @param int $user_id The user ID
   */
  public function mailpn_schedule_delayed_welcome_email($email_id, $user_id) {
    $delay_value = get_post_meta($email_id, 'mailpn_welcome_delay_value', true);
    $delay_unit = get_post_meta($email_id, 'mailpn_welcome_delay_unit', true);
    
    if (empty($delay_value) || empty($delay_unit)) {
      return;
    }
    
    // Calculate the delay in seconds
    $delay_seconds = $this->mailpn_calculate_delay_seconds($delay_value, $delay_unit);
    
    if ($delay_seconds <= 0) {
      return;
    }
    
    // Calculate the scheduled time
    $scheduled_time = time() + $delay_seconds;
    
    // Get existing scheduled emails
    $scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
    
    // Ensure $scheduled_emails is always an array
    if (!is_array($scheduled_emails)) {
      $scheduled_emails = [];
    }
    
    // Add this email to the scheduled list
    $scheduled_emails[] = [
      'email_id' => $email_id,
      'user_id' => $user_id,
      'scheduled_time' => $scheduled_time,
      'created_time' => time()
    ];
    
    update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
  }
  
  /**
   * Calculate delay in seconds based on value and unit
   *
   * @param int $value The delay value
   * @param string $unit The delay unit (hours, days, weeks, months, years)
   * @return int The delay in seconds
   */
  public function mailpn_calculate_delay_seconds($value, $unit) {
    $value = intval($value);
    
    switch ($unit) {
      case 'hours':
        return $value * HOUR_IN_SECONDS;
      case 'days':
        return $value * DAY_IN_SECONDS;
      case 'weeks':
        return $value * WEEK_IN_SECONDS;
      case 'months':
        return $value * MONTH_IN_SECONDS;
      case 'years':
        return $value * YEAR_IN_SECONDS;
      default:
        return 0;
    }
  }

  public function mailpn_init_hook() {
    if (!isset($_GET['mailpn_action'])) {
        return;
    }

    switch (sanitize_text_field($_GET['mailpn_action'])) {
        case 'popup_open':
          if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'mailpn_action')) {
              wp_die(esc_html__('Security check failed: invalid nonce', 'mailpn'));
          }
          
          if (!isset($_GET['mailpn_popup'])) {
              wp_safe_redirect(home_url());
              exit;
          }
          // The popup will be handled by JavaScript
          break;
        case 'subscription-unsubscribe':
            if (!isset($_GET['subscription-unsubscribe-nonce']) || 
                !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['subscription-unsubscribe-nonce'])), 'subscription-unsubscribe')) {
                wp_safe_redirect(home_url('?mailpn_notice=subscription-unsubscribe-error'));
                exit;
            }
            
            // Add user check
            $user_id = isset($_GET['user']) ? absint($_GET['user']) : 0;
            if (!$user_id || !current_user_can('edit_user', $user_id)) {
                wp_safe_redirect(home_url('?mailpn_notice=subscription-unsubscribe-error'));
                exit;
            }
            
            update_user_meta($user_id, 'userspn_notifications', '');
            wp_safe_redirect(home_url('?mailpn_notice=subscription-unsubscribe-success'));
            exit;
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

  /**
   * Adds the Settings link to the plugin list
   */
  public function mailpn_plugin_action_links($links) {
      $settings_link = '<a href="admin.php?page=mailpn_options">' . esc_html__('Settings', 'mailpn') . '</a>';
      array_unshift($links, $settings_link);
      
      return $links;
  }
}