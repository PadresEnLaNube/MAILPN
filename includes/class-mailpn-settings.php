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
        'default' => '500',
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
      esc_html__('Dashboard', 'mailpn'), 
      esc_html__('Dashboard', 'mailpn'), 
      'manage_options', 
      'mailpn_dashboard',
      [$this, 'mailpn_dashboard_page']
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

    // Add submenu for email queue management
    add_submenu_page(
      'mailpn_options',
      esc_html__('Email Queue', 'mailpn'),
      esc_html__('Email Queue', 'mailpn'),
      'manage_options',
      'mailpn-email-queue',
      [$this, 'mailpn_email_queue_page']
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
	 * Dashboard page
	 *
	 * @since    1.0.0
	 */
	public function mailpn_dashboard_page() {
		$dashboard = new MAILPN_Dashboard();
		$dashboard->render_dashboard_page();
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
				
				// Add to queue
				$result = $mailing->mailpn_queue_add($scheduled_email['email_id'], $scheduled_email['user_id']);
				
				if ($result) {
					// Process queue immediately
					$mailing->mailpn_queue_process();
					
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
      // Check if this is a fresh activation using transient
      if (get_transient('mailpn_activation_redirect')) {
        // Clear the transient
        delete_transient('mailpn_activation_redirect');
        // Redirect to settings page
        wp_safe_redirect(esc_url(admin_url('admin.php?page=mailpn_options')));
        exit;
      }
    }
  }

  /**
   * Check for activation redirect on admin init
   */
  public function mailpn_admin_init() {
    // Check if this is a fresh activation using transient
    if (get_transient('mailpn_activation_redirect')) {
      // Clear the transient
      delete_transient('mailpn_activation_redirect');
      // Redirect to settings page
      wp_safe_redirect(esc_url(admin_url('admin.php?page=mailpn_options')));
      exit;
    }
  }

  /**
   * Add admin notice and redirect script for fresh activations (fallback)
   */
  public function mailpn_admin_notices() {
    // Check if this is a fresh activation using transient
    if (get_transient('mailpn_activation_redirect')) {
      // Clear the transient immediately to prevent multiple redirects
      delete_transient('mailpn_activation_redirect');
      
      // Add JavaScript redirect
      ?>
      <script type="text/javascript">
        window.location.href = '<?php echo esc_url(admin_url('admin.php?page=mailpn_options')); ?>';
      </script>
      <?php
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
   * Check if a user's email is in the exception lists
   *
   * @param int $user_id The user ID
   * @return bool True if email should be excluded, false otherwise
   */
  public function mailpn_is_email_excepted($user_id) {
    $user = get_userdata($user_id);
    if (!$user) {
      error_log("MAILPN: User $user_id doesn't exist, cannot check email exceptions");
      return false;
    }
    
    $user_email = $user->user_email;
    error_log("MAILPN: Checking email exceptions for user $user_id with email: $user_email");
    
    $mailpn_exception_emails = get_option('mailpn_exception_emails');
    $mailpn_exception_emails_domains = get_option('mailpn_exception_emails_domains');
    $mailpn_exception_emails_addresses = get_option('mailpn_exception_emails_addresses');

    error_log("MAILPN: Exception settings - Emails: $mailpn_exception_emails, Domains: $mailpn_exception_emails_domains, Addresses: $mailpn_exception_emails_addresses");

    // Exception domains and emails check
    if ($mailpn_exception_emails == 'on') {
      if ($mailpn_exception_emails_domains == 'on') {
        $mailpn_exception_emails_domain = get_option('mailpn_exception_emails_domain');

        if (!empty($mailpn_exception_emails_domain)) {
          error_log("MAILPN: Checking against domain exceptions: " . implode(', ', $mailpn_exception_emails_domain));
          foreach ($mailpn_exception_emails_domain as $mailpn_exception_email_domain) {
            if (strpos($user_email, $mailpn_exception_email_domain) !== false) {
              error_log("MAILPN: User $user_id email $user_email matches domain exception: $mailpn_exception_email_domain");
              return true;
            }
          }
        } else {
          error_log("MAILPN: Domain exceptions enabled but no domains configured");
        }
      }

      if ($mailpn_exception_emails_addresses == 'on') {
        $mailpn_exception_emails_address = get_option('mailpn_exception_emails_address');

        if (!empty($mailpn_exception_emails_address)) {
          error_log("MAILPN: Checking against email exceptions: " . implode(', ', $mailpn_exception_emails_address));
          if (in_array($user_email, $mailpn_exception_emails_address)) {
            error_log("MAILPN: User $user_id email $user_email matches email exception");
            return true;
          }
        } else {
          error_log("MAILPN: Email exceptions enabled but no emails configured");
        }
      }
    } else {
      error_log("MAILPN: Email exceptions are disabled");
    }

    error_log("MAILPN: User $user_id email $user_email is not in exception list");
    return false;
  }

  /**
   * Process pending welcome registrations
   *
   * @param int|null $user_id The user ID (optional)
   * @param string|null $role The role (optional)
   * @param array|null $old_roles The old roles (optional)
   */
  public function mailpn_process_pending_welcome_registrations($user_id = null, $role = null, $old_roles = null) {
    $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
    
    // Ensure $pending_registrations is always an array
    if (!is_array($pending_registrations)) {
      $pending_registrations = [];
    }
    
    if (empty($pending_registrations)) {
      error_log("MAILPN: No pending registrations to process");
      return;
    }
    
    error_log("MAILPN: Processing " . count($pending_registrations) . " pending registrations");
    
    $updated_pending_registrations = [];
    $processed_count = 0;
    $skipped_count = 0;
    
    foreach ($pending_registrations as $registration) {
      // If already processed, keep it for cleanup later
      if ($registration['processed']) {
        $updated_pending_registrations[] = $registration;
        $processed_count++;
        continue;
      }
      
      $reg_user_id = $registration['user_id'];
      $user = get_userdata($reg_user_id);
      
      // If user doesn't exist, discard this registration
      if (!$user) {
        error_log("MAILPN: User $reg_user_id doesn't exist, discarding registration");
        $skipped_count++;
        continue;
      }
      
      error_log("MAILPN: Processing registration for user $reg_user_id (email: {$user->user_email})");
      
      // If user has the newsletter subscriber role, mark as processed and keep
      if (in_array('userspn_newsletter_subscriber', $user->roles)) {
        $registration['processed'] = true;
        $updated_pending_registrations[] = $registration;
        error_log("MAILPN: User $reg_user_id has newsletter subscriber role, marking as processed");
        $processed_count++;
        continue;
      }
      
      // If user's email is in the exception lists, mark as processed and keep
      if ($this->mailpn_is_email_excepted($reg_user_id)) {
        $registration['processed'] = true;
        $updated_pending_registrations[] = $registration;
        error_log("MAILPN: User $reg_user_id email is in exception list, marking as processed");
        $processed_count++;
        continue;
      }
      
      // Try to send welcome emails
      $sent = $this->mailpn_trigger_welcome_emails($reg_user_id);
      
      // If an email was sent, mark as processed
      if ($sent) {
        $registration['processed'] = true;
        error_log("MAILPN: Successfully processed welcome emails for user $reg_user_id");
        $processed_count++;
      } else {
        error_log("MAILPN: Failed to send welcome emails for user $reg_user_id - will retry later");
        $skipped_count++;
      }
      
      // Always add the registration back to the list (it's either now processed or will be retried)
      $updated_pending_registrations[] = $registration;
    }
    
    error_log("MAILPN: Processing complete - Processed: $processed_count, Skipped: $skipped_count, Total: " . count($updated_pending_registrations));
    
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
    
    // If user's email is in the exception lists, mark as processed
    if ($this->mailpn_is_email_excepted($reg_user_id)) {
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
   * Clean up all processed pending registrations (weekly cleanup)
   */
  public function mailpn_cleanup_processed_pending_registrations() {
    $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
    
    // Ensure $pending_registrations is always an array
    if (!is_array($pending_registrations)) {
      $pending_registrations = [];
    }
    
    $cleaned_registrations = [];
    
    foreach ($pending_registrations as $registration) {
      // Keep only unprocessed registrations
      if (!$registration['processed']) {
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
    // Check if user's email is in the exception lists
    if ($this->mailpn_is_email_excepted($user_id)) {
      error_log("MAILPN: User $user_id email is in exception list, skipping welcome emails");
      return false;
    }
    
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
      error_log("MAILPN: No welcome email templates found for user $user_id");
      return false;
    }
    
    error_log("MAILPN: Found " . count($welcome_emails) . " welcome email templates for user $user_id");
    
    $mailing_plugin = new MAILPN_Mailing();
    $email_queued = false;
    $user = get_userdata($user_id);
    
    foreach ($welcome_emails as $email_id) {
      // Check if user should receive this email based on distribution settings
      $distribution = get_post_meta($email_id, 'mailpn_distribution', true);
      $should_send = false;
      
      error_log("MAILPN: Email $email_id has distribution: $distribution");
      
      switch ($distribution) {
        case 'public':
          $should_send = true;
          error_log("MAILPN: Public distribution - should send to user $user_id");
          break;
        case 'private_role':
          $user_roles = get_post_meta($email_id, 'mailpn_distribution_role', true);
          if (!empty($user_roles) && !empty($user)) {
            foreach ($user_roles as $role) {
              if (in_array($role, $user->roles)) {
                $should_send = true;
                error_log("MAILPN: User $user_id has role $role - should send email $email_id");
                break;
              }
            }
          }
          if (!$should_send) {
            error_log("MAILPN: User $user_id roles (" . implode(',', $user->roles) . ") don't match email $email_id roles (" . implode(',', $user_roles) . ")");
          }
          break;
        case 'private_user':
          $user_list = get_post_meta($email_id, 'mailpn_distribution_user', true);
          if (!empty($user_list) && in_array($user_id, $user_list)) {
            $should_send = true;
            error_log("MAILPN: User $user_id is in private user list - should send email $email_id");
          } else {
            error_log("MAILPN: User $user_id is not in private user list for email $email_id");
          }
          break;
      }
      
      if (!$should_send) {
        error_log("MAILPN: Skipping email $email_id for user $user_id - distribution mismatch");
        continue;
      }
      
      // Check if delay is enabled for this welcome email
      $delay_enabled = get_post_meta($email_id, 'mailpn_welcome_delay_enabled', true);
      
      if ($delay_enabled === 'on') {
        // Schedule the email for delayed sending
        $this->mailpn_schedule_delayed_welcome_email($email_id, $user_id);
        error_log("MAILPN: Scheduled delayed welcome email $email_id for user $user_id");
      } else {
        // Send immediately
        $result = $mailing_plugin->mailpn_queue_add($email_id, $user_id);
        error_log("MAILPN: Added welcome email $email_id to queue for user $user_id - result: " . ($result ? 'success' : 'failed'));
      }
      $email_queued = true;
    }
    
    error_log("MAILPN: Final result for user $user_id - email_queued: " . ($email_queued ? 'true' : 'false'));
    return $email_queued;
  }
  
  /**
   * Schedule a delayed welcome email
   *
   * @param int $email_id The email template ID
   * @param int $user_id The user ID
   */
  public function mailpn_schedule_delayed_welcome_email($email_id, $user_id) {
    error_log("MAILPN: Scheduling delayed welcome email - Email ID: $email_id, User ID: $user_id");
    
    // Check if user's email is in the exception lists
    if ($this->mailpn_is_email_excepted($user_id)) {
      error_log("MAILPN: User $user_id email is in exception list, skipping delayed email scheduling");
      return;
    }
    
    $delay_value = get_post_meta($email_id, 'mailpn_welcome_delay_value', true);
    $delay_unit = get_post_meta($email_id, 'mailpn_welcome_delay_unit', true);
    
    error_log("MAILPN: Delay settings - Value: $delay_value, Unit: $delay_unit");
    
    if (empty($delay_value) || empty($delay_unit)) {
      error_log("MAILPN: Delay settings are empty, skipping delayed email scheduling");
      return;
    }
    
    // Calculate the delay in seconds
    $delay_seconds = $this->mailpn_calculate_delay_seconds($delay_value, $delay_unit);
    
    error_log("MAILPN: Calculated delay: $delay_seconds seconds");
    
    if ($delay_seconds <= 0) {
      error_log("MAILPN: Invalid delay calculated, skipping delayed email scheduling");
      return;
    }
    
    // Calculate the scheduled time
    $scheduled_time = time() + $delay_seconds;
    
    error_log("MAILPN: Scheduled time: " . date('Y-m-d H:i:s', $scheduled_time));
    
    // Get existing scheduled emails
    $scheduled_emails = get_option('mailpn_scheduled_welcome_emails', []);
    
    // Ensure $scheduled_emails is always an array
    if (!is_array($scheduled_emails)) {
      $scheduled_emails = [];
    }
    
    // Add the new scheduled email
    $scheduled_emails[] = [
      'email_id' => $email_id,
      'user_id' => $user_id,
      'scheduled_time' => $scheduled_time,
      'created_time' => time()
    ];
    
    $result = update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
    error_log("MAILPN: Updated scheduled emails option - Result: " . ($result ? 'success' : 'failed') . ", Total scheduled: " . count($scheduled_emails));
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

  /**
   * Diagnostic function to check pending registrations status
   * This can be called manually to debug why registrations are not being processed
   */
  public function mailpn_diagnose_pending_registrations() {
    $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
    
    if (!is_array($pending_registrations)) {
      $pending_registrations = [];
    }
    
    if (empty($pending_registrations)) {
      echo "No pending registrations found.\n";
      return;
    }
    
    echo "Found " . count($pending_registrations) . " pending registrations:\n\n";
    
    $unprocessed_count = 0;
    $processed_count = 0;
    
    foreach ($pending_registrations as $index => $registration) {
      $user_id = $registration['user_id'];
      $registration_time = $registration['registration_time'];
      $processed = $registration['processed'];
      $days_old = round((time() - $registration_time) / DAY_IN_SECONDS, 1);
      
      $user = get_userdata($user_id);
      $user_email = $user ? $user->user_email : 'User not found';
      $user_roles = $user ? implode(', ', $user->roles) : 'N/A';
      
      echo "Registration #$index:\n";
      echo "  User ID: $user_id\n";
      echo "  Email: $user_email\n";
      echo "  Roles: $user_roles\n";
      echo "  Registration time: " . date('Y-m-d H:i:s', $registration_time) . " ($days_old days ago)\n";
      echo "  Processed: " . ($processed ? 'Yes' : 'No') . "\n";
      
      if (!$processed) {
        $unprocessed_count++;
        
        // Check why it's not processed
        if (!$user) {
          echo "  Reason: User doesn't exist\n";
        } elseif (in_array('userspn_newsletter_subscriber', $user->roles)) {
          echo "  Reason: User has newsletter subscriber role\n";
        } elseif ($this->mailpn_is_email_excepted($user_id)) {
          echo "  Reason: User email is in exception list\n";
        } else {
          // Check if there are welcome email templates
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
            echo "  Reason: No welcome email templates found\n";
          } else {
            echo "  Reason: Welcome email templates exist but distribution may not match\n";
            echo "  Available templates: " . implode(', ', $welcome_emails) . "\n";
            
            // Check distribution for each template
            foreach ($welcome_emails as $email_id) {
              $distribution = get_post_meta($email_id, 'mailpn_distribution', true);
              echo "    Template $email_id distribution: $distribution\n";
              
              if ($distribution === 'private_role') {
                $user_roles = get_post_meta($email_id, 'mailpn_distribution_role', true);
                echo "    Required roles: " . implode(', ', $user_roles) . "\n";
              } elseif ($distribution === 'private_user') {
                $user_list = get_post_meta($email_id, 'mailpn_distribution_user', true);
                echo "    User list: " . implode(', ', $user_list) . "\n";
              }
            }
          }
        }
      } else {
        $processed_count++;
      }
      
      echo "\n";
    }
    
    echo "Summary:\n";
    echo "  Total registrations: " . count($pending_registrations) . "\n";
    echo "  Processed: $processed_count\n";
    echo "  Unprocessed: $unprocessed_count\n";
    
    // Check cron status
    $next_cron = wp_next_scheduled('mailpn_cron_ten_minutes');
    if ($next_cron) {
      echo "  Next cron execution: " . date('Y-m-d H:i:s', $next_cron) . "\n";
    } else {
      echo "  Cron not scheduled!\n";
    }
  }

  /**
   * Force process pending registrations manually
   * This can be called to process stuck registrations
   */
  public function mailpn_force_process_pending_registrations() {
    echo "Starting forced processing of pending registrations...\n";
    
    // First, diagnose the current state
    $this->mailpn_diagnose_pending_registrations();
    
    echo "\n--- Processing registrations ---\n";
    
    // Process all pending registrations
    $this->mailpn_process_pending_welcome_registrations();
    
    echo "\n--- Processing complete ---\n";
    
    // Diagnose again to see the results
    $this->mailpn_diagnose_pending_registrations();
    
    echo "\nForced processing complete.\n";
  }

  /**
   * Clean up old unprocessed registrations (older than 30 days)
   * This can help remove stuck registrations that can't be processed
   */
  public function mailpn_cleanup_stuck_pending_registrations() {
    $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
    
    if (!is_array($pending_registrations)) {
      $pending_registrations = [];
    }
    
    $thirty_days_ago = time() - (30 * DAY_IN_SECONDS);
    $cleaned_registrations = [];
    $removed_count = 0;
    
    foreach ($pending_registrations as $registration) {
      // Keep processed registrations and recent unprocessed ones
      if ($registration['processed'] || $registration['registration_time'] > $thirty_days_ago) {
        $cleaned_registrations[] = $registration;
      } else {
        $removed_count++;
        error_log("MAILPN: Removed stuck registration for user {$registration['user_id']} (older than 30 days)");
      }
    }
    
    update_option('mailpn_pending_welcome_registrations', $cleaned_registrations);
    
    if ($removed_count > 0) {
      error_log("MAILPN: Cleaned up $removed_count stuck pending registrations");
    }
    
    return $removed_count;
  }

  /**
   * Clean up problematic pending registrations
   * Removes registrations for non-existent users and those that can't be processed
   */
  public function mailpn_cleanup_problematic_pending_registrations() {
    $pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
    
    if (!is_array($pending_registrations)) {
      $pending_registrations = [];
    }
    
    $cleaned_registrations = [];
    $removed_count = 0;
    $removed_reasons = [];
    
    foreach ($pending_registrations as $registration) {
      $reg_user_id = $registration['user_id'];
      $user = get_userdata($reg_user_id);
      
      // Remove registrations for non-existent users
      if (!$user) {
        $removed_count++;
        $removed_reasons['non_existent_users'] = ($removed_reasons['non_existent_users'] ?? 0) + 1;
        error_log("MAILPN: Removed registration for non-existent user $reg_user_id");
        continue;
      }
      
      // Remove registrations for users with newsletter subscriber role
      if (in_array('userspn_newsletter_subscriber', $user->roles)) {
        $removed_count++;
        $removed_reasons['newsletter_subscribers'] = ($removed_reasons['newsletter_subscribers'] ?? 0) + 1;
        error_log("MAILPN: Removed registration for newsletter subscriber user $reg_user_id");
        continue;
      }
      
      // Remove registrations for users with emails in exception list
      if ($this->mailpn_is_email_excepted($reg_user_id)) {
        $removed_count++;
        $removed_reasons['excepted_emails'] = ($removed_reasons['excepted_emails'] ?? 0) + 1;
        error_log("MAILPN: Removed registration for excepted email user $reg_user_id");
        continue;
      }
      
      // Check if user can receive any welcome emails
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
      
      $can_receive_emails = false;
      foreach ($welcome_emails as $email_id) {
        $distribution = get_post_meta($email_id, 'mailpn_distribution', true);
        
        if ($distribution === 'public') {
          $can_receive_emails = true;
          break;
        } elseif ($distribution === 'private_role') {
          $user_roles = get_post_meta($email_id, 'mailpn_distribution_role', true);
          if (!empty($user_roles)) {
            foreach ($user_roles as $role) {
              if (in_array($role, $user->roles)) {
                $can_receive_emails = true;
                break 2;
              }
            }
          }
        } elseif ($distribution === 'private_user') {
          $user_list = get_post_meta($email_id, 'mailpn_distribution_user', true);
          if (!empty($user_list) && in_array($reg_user_id, $user_list)) {
            $can_receive_emails = true;
            break;
          }
        }
      }
      
      // Remove registrations for users who can't receive any welcome emails
      if (!$can_receive_emails) {
        $removed_count++;
        $removed_reasons['role_mismatch'] = ($removed_reasons['role_mismatch'] ?? 0) + 1;
        error_log("MAILPN: Removed registration for user $reg_user_id - no matching email templates");
        continue;
      }
      
      // Keep this registration
      $cleaned_registrations[] = $registration;
    }
    
    update_option('mailpn_pending_welcome_registrations', $cleaned_registrations);
    
    if ($removed_count > 0) {
      error_log("MAILPN: Cleaned up $removed_count problematic pending registrations");
      foreach ($removed_reasons as $reason => $count) {
        error_log("MAILPN: - $reason: $count");
      }
    }
    
    return [
      'removed_count' => $removed_count,
      'removed_reasons' => $removed_reasons,
      'remaining_count' => count($cleaned_registrations)
    ];
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

  /**
   * Email Queue Management Page
   *
   * @since    1.0.0
   */
  public function mailpn_email_queue_page() {
    // Handle manual processing of queue
    if (isset($_POST['mailpn_process_queue']) && wp_verify_nonce($_POST['mailpn_process_queue_nonce'], 'mailpn_process_queue')) {
      $mailing = new MAILPN_Mailing();
      $mailing->mailpn_queue_process();
      echo '<div class="notice notice-success"><p>' . esc_html__('Email queue processed successfully.', 'mailpn') . '</p></div>';
    }

    // Handle clearing queue
    if (isset($_POST['mailpn_clear_queue']) && wp_verify_nonce($_POST['mailpn_clear_queue_nonce'], 'mailpn_clear_queue')) {
      update_option('mailpn_queue', []);
      echo '<div class="notice notice-success"><p>' . esc_html__('Email queue cleared successfully.', 'mailpn') . '</p></div>';
    }

    // Handle removing specific email from queue
    if (isset($_POST['mailpn_remove_from_queue']) && wp_verify_nonce($_POST['mailpn_remove_from_queue_nonce'], 'mailpn_remove_from_queue')) {
      $mail_id = intval($_POST['mailpn_mail_id']);
      $user_id = intval($_POST['mailpn_user_id']);
      
      $mailpn_queue = get_option('mailpn_queue', []);
      
      if (isset($mailpn_queue[$mail_id]) && in_array($user_id, $mailpn_queue[$mail_id])) {
        $mailpn_queue[$mail_id] = array_diff($mailpn_queue[$mail_id], [$user_id]);
        
        // Remove empty mail entries
        if (empty($mailpn_queue[$mail_id])) {
          unset($mailpn_queue[$mail_id]);
        }
        
        update_option('mailpn_queue', $mailpn_queue);
        echo '<div class="notice notice-success"><p>' . esc_html__('Email removed from queue successfully.', 'mailpn') . '</p></div>';
      }
    }

    ?>
    <div class="mailpn-options mailpn-max-width-1000 mailpn-margin-auto mailpn-mt-50 mailpn-mb-50">
      <div class="mailpn-display-table mailpn-width-100-percent">
        <div class="mailpn-display-inline-table mailpn-width-70-percent mailpn-tablet-display-block mailpn-tablet-width-100-percent">
          <h1 class="mailpn-mb-30"><?php esc_html_e('Email Queue Management', 'mailpn'); ?></h1>
        </div>
        <div class="mailpn-display-inline-table mailpn-width-30-percent mailpn-tablet-display-block mailpn-tablet-width-100-percent mailpn-text-align-center">
          <form method="post" style="display: inline;">
            <?php wp_nonce_field('mailpn_process_queue', 'mailpn_process_queue_nonce'); ?>
            <input type="submit" name="mailpn_process_queue" class="button button-primary" value="<?php esc_attr_e('Process Queue Now', 'mailpn'); ?>">
          </form>
          <form method="post" style="display: inline; margin-left: 10px;">
            <?php wp_nonce_field('mailpn_clear_queue', 'mailpn_clear_queue_nonce'); ?>
            <input type="submit" name="mailpn_clear_queue" class="button button-secondary" value="<?php esc_attr_e('Clear Queue', 'mailpn'); ?>" onclick="return confirm('<?php esc_attr_e('Are you sure you want to clear the entire email queue?', 'mailpn'); ?>')">
          </form>
        </div>
      </div>

      <!-- EMAIL QUEUE SECTION -->
      <div class="mailpn-email-queue mailpn-mb-30">
        <h2><?php esc_html_e('Pending Email Queue', 'mailpn'); ?></h2>
        <p><?php esc_html_e('These are emails waiting to be sent. They will be processed automatically every 10 minutes or you can process them manually.', 'mailpn'); ?></p>
        
        <?php
        $mailpn_queue = get_option('mailpn_queue', []);
        
        // Filter out templates with no pending emails
        $pending_templates = [];
        foreach ($mailpn_queue as $mail_id => $user_ids) {
          if (!empty($user_ids)) {
            $pending_templates[$mail_id] = $user_ids;
          }
        }
        
        if (empty($pending_templates)) {
          echo '<p>' . esc_html__('No emails in queue.', 'mailpn') . '</p>';
        } else {
          $total_emails = 0;
          foreach ($pending_templates as $mail_id => $user_ids) {
            $total_emails += count($user_ids);
          }
          
          echo '<p><strong>' . sprintf(esc_html__('Total emails in queue: %d', 'mailpn'), $total_emails) . '</strong></p>';
          ?>
          <table class="wp-list-table widefat fixed striped">
            <thead>
              <tr>
                <th><?php esc_html_e('Email Template', 'mailpn'); ?></th>
                <th><?php esc_html_e('Recipients', 'mailpn'); ?></th>
                <th><?php esc_html_e('Queue Count', 'mailpn'); ?></th>
                <th><?php esc_html_e('Actions', 'mailpn'); ?></th>
              </tr>
            </thead>
            <tbody>
          <?php

          foreach ($pending_templates as $mail_id => $user_ids) {
            $email_post = get_post($mail_id);
            $email_title = $email_post ? $email_post->post_title : esc_html__('Unknown Template', 'mailpn');
            $email_type = $email_post ? get_post_meta($mail_id, 'mailpn_type', true) : '';
            
            // Get user details in mailpn_rec format
            $user_details = [];
            foreach ($user_ids as $user_id) {
              $user = get_userdata($user_id);
              if ($user) {
                // Format: display_name-user_id (email)
                $user_display = $user->display_name . '-' . $user_id . ' (' . $user->user_email . ')';
                $user_details[] = $user_display;
              }
            }
            
            ?>
            <tr>
              <td>
                <strong><?php echo esc_html($email_title); ?></strong><br>
                <small>ID: <?php echo esc_html($mail_id); ?></small><br>
                <small>Type: <?php echo esc_html($email_type); ?></small>
              </td>
              <td>
                <?php if (!empty($user_details)): ?>
                  <div style="max-height: 200px; overflow-y: auto;">
                    <?php foreach ($user_details as $user_detail): ?>
                      <div style="margin-bottom: 5px;">
                        <?php echo esc_html($user_detail); ?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <em><?php esc_html_e('No valid recipients', 'mailpn'); ?></em>
                <?php endif; ?>
              </td>
              <td>
                <strong><?php echo esc_html(count($user_ids)); ?></strong> <?php esc_html_e('emails', 'mailpn'); ?>
              </td>
              <td>
                <form method="post" style="display: inline;">
                  <?php wp_nonce_field('mailpn_remove_from_queue', 'mailpn_remove_from_queue_nonce'); ?>
                  <input type="hidden" name="mailpn_mail_id" value="<?php echo esc_attr($mail_id); ?>">
                  <input type="hidden" name="mailpn_user_id" value="<?php echo esc_attr(implode(',', $user_ids)); ?>">
                  <input type="submit" name="mailpn_remove_from_queue" class="button button-small button-secondary" value="<?php esc_attr_e('Remove All', 'mailpn'); ?>" onclick="return confirm('<?php esc_attr_e('Are you sure you want to remove all emails for this template from the queue?', 'mailpn'); ?>')">
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

      <!-- QUEUE STATISTICS -->
      <div class="mailpn-queue-statistics mailpn-mb-30">
        <h2><?php esc_html_e('Queue Statistics', 'mailpn'); ?></h2>
        <?php
        $queue_paused = get_option('mailpn_queue_paused', false);
        $emails_sent_today = get_option('mailpn_emails_sent_today', 0);
        $daily_limit = get_option('mailpn_sent_every_day', 500);
        $emails_per_batch = get_option('mailpn_sent_every_ten_minutes', 5);
        
        ?>
        <table class="wp-list-table widefat fixed striped">
          <tbody>
            <tr>
              <td><strong><?php esc_html_e('Queue Status', 'mailpn'); ?></strong></td>
              <td><?php echo $queue_paused ? '<span style="color: red;">' . esc_html__('PAUSED', 'mailpn') . '</span>' : '<span style="color: green;">' . esc_html__('ACTIVE', 'mailpn') . '</span>'; ?></td>
            </tr>
            <tr>
              <td><strong><?php esc_html_e('Emails Sent Today', 'mailpn'); ?></strong></td>
              <td><?php echo esc_html($emails_sent_today); ?> / <?php echo esc_html($daily_limit); ?></td>
            </tr>
            <tr>
              <td><strong><?php esc_html_e('Emails Per Batch', 'mailpn'); ?></strong></td>
              <td><?php echo esc_html($emails_per_batch); ?></td>
            </tr>
            <tr>
              <td><strong><?php esc_html_e('Next Processing', 'mailpn'); ?></strong></td>
              <td><?php esc_html_e('Every 10 minutes via cron job', 'mailpn'); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <?php
  }
}