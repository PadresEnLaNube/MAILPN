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
      $mailpn_options['mailpn_open_tracking'] = [
        'id' => 'mailpn_open_tracking',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'checkbox',
        'label' => __('Enable open tracking', 'mailpn'),
        'description' => __('Track when recipients open emails using a tracking pixel. Warning: Uses inline JavaScript which may affect spam score. Disable if you prioritize deliverability over tracking.', 'mailpn'),
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
      $mailpn_options['mailpn_wp_emails_wrapper'] = [
        'id' => 'mailpn_wp_emails_wrapper',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'checkbox',
        'label' => __('Format all WordPress emails', 'mailpn'),
        'description' => __('Automatically wrap all WordPress core emails (password change, email change, admin notifications, etc.) with the MailPN email template. Adds header/footer, makes links clickable, removes [Site Name] from subjects, and logs all sends.', 'mailpn'),
      ];
      $mailpn_options['mailpn_wc_emails_wrapper'] = [
        'id' => 'mailpn_wc_emails_wrapper',
        'class' => 'mailpn-input mailpn-width-100-percent',
        'input' => 'input',
        'type' => 'checkbox',
        'label' => __('Format all WooCommerce emails', 'mailpn'),
        'description' => __('Automatically wrap all WooCommerce emails (order confirmations, shipping notifications, etc.) with the MailPN email template. Requires WooCommerce plugin to be active. Adds header/footer, applies custom styling, and logs all sends.', 'mailpn'),
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
          $mailpn_options['mailpn_exception_emails_domains_whitelist'] = [
            'id' => 'mailpn_exception_emails_domains_whitelist',
            'class' => 'mailpn-input mailpn-width-100-percent',
            'input' => 'input',
            'type' => 'checkbox',
            'parent' => 'this mailpn_exception_emails_domains',
            'parent_option' => 'on',
            'label' => __('Whitelist addresses from excluded domains', 'mailpn'),
            'placeholder' => __('Whitelist addresses from excluded domains', 'mailpn'),
            'description' => __('Allow specific email addresses to receive emails even if their domain is excluded.', 'mailpn'),
          ];
            $mailpn_options['mailpn_exception_emails_domains_whitelist_list'] = [
              'id' => 'mailpn_exception_emails_domains_whitelist_list',
              'class' => 'mailpn-input mailpn-width-100-percent',
              'input' => 'html_multi',
              'multi_array' => ['address', ],
              'parent' => 'mailpn_exception_emails_domains_whitelist',
              'parent_option' => 'on',
              'label' => __('Whitelisted email addresses', 'mailpn'),
              'placeholder' => __('Whitelisted email addresses', 'mailpn'),
              'description' => __('Set the email addresses that will be allowed to receive emails even if their domain is excluded.', 'mailpn'),
              'html_multi_fields' => [
                $mailpn_exception_emails_domains_whitelist_address = [
                  'id' => 'mailpn_exception_emails_domains_whitelist_address',
                  'class' => 'mailpn-input mailpn-width-100-percent',
                  'input' => 'input',
                  'type' => 'text',
                  'multiple' => true,
                  'label' => esc_html(__('Address (for example: comercial@' . $mailpn_domain, 'mailpn')),
                  'placeholder' => esc_html(__('comercial@' . $mailpn_domain, 'mailpn')),
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
        $mailpn_options['mailpn_consecutive_errors_limit'] = [
          'id' => 'mailpn_consecutive_errors_limit',
          'class' => 'mailpn-input mailpn-width-100-percent',
          'input' => 'input',
          'type' => 'number',
          'label' => __('Consecutive errors limit', 'mailpn'),
          'description' => __('Set the maximum number of consecutive errors allowed before stopping the sending process. When this limit is reached, the queue will be paused and an email will be sent to the administrator. Default: 10, Minimum: 1.', 'mailpn'),
          'default' => '10',
        ];
  $mailpn_options['mailpn_section_mechanics_end'] = [
      'section' => 'end',
    ];

    // SMTP Configuration Section
    $mailpn_options['mailpn_section_smtp_start'] = [
      'section' => 'start',
      'label' => __('SMTP Configuration', 'mailpn'),
      'description' => __('Configure your SMTP server settings for sending emails. To set up SMTP: 1) Enable SMTP by checking the "Enable SMTP" checkbox, 2) Enter your SMTP Host (e.g., smtp.gmail.com for Gmail, smtp.mail.yahoo.com for Yahoo), 3) Set the SMTP Port (commonly 587 for TLS or 465 for SSL), 4) Select the Security type (TLS, SSL, or None), 5) If your server requires authentication, enable "SMTP Authentication" and enter your Username and Password (for Gmail, you must use an App Password, not your regular password), 6) Optionally set the From Email and From Name that will appear as the sender. After configuration, test your settings by sending a test email.', 'mailpn'),
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

    $mailpn_options['mailpn_smtp_wp_native_emails'] = [
      'id' => 'mailpn_smtp_wp_native_emails',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'parent' => 'mailpn_smtp_enabled',
      'parent_option' => 'on',
      'label' => __('Use SMTP for native WordPress emails', 'mailpn'),
      'description' => __('When enabled, all native WordPress emails (password recovery, new user notification, comment notifications, admin notifications, etc.) will be sent via your SMTP server instead of the default PHP mail(). Requires SMTP to be enabled and configured above.', 'mailpn'),
    ];

    $mailpn_options['mailpn_section_smtp_end'] = [
      'section' => 'end',
    ];

    // Email Security & Deliverability Section
    $mailpn_options['mailpn_section_deliverability_start'] = [
      'section' => 'start',
      'label' => __('Email Security & Deliverability', 'mailpn'),
      'description' => __('Analyze your email configuration to ensure maximum deliverability and avoid spam filters. This tool checks DNS records (SPF, DKIM, DMARC), blacklists, and email content.', 'mailpn'),
    ];

      // Subsection: Deliverability Analysis
      $mailpn_options['mailpn_subsection_deliverability_analysis_start'] = [
        'section' => 'start',
        'label' => __('Deliverability Analysis', 'mailpn'),
        'description' => __('Automatically checks your email configuration including DNS records (SPF, DKIM, DMARC, MX), SMTP settings, and sender configuration. Provides a deliverability score (0-100) and specific recommendations for improving email delivery and avoiding spam filters.', 'mailpn'),
      ];

        $mailpn_options['mailpn_deliverability_checker'] = [
          'id' => 'mailpn_deliverability_checker',
          'input' => 'deliverability_checker',
        ];

      $mailpn_options['mailpn_subsection_deliverability_analysis_end'] = [
        'section' => 'end',
      ];

      // Subsection: Advanced Header Analysis
      $mailpn_options['mailpn_subsection_header_analysis_start'] = [
        'section' => 'start',
        'label' => __('Advanced Header Analysis (Optional)', 'mailpn'),
        'description' => __('Send a test email to yourself, then copy and paste the complete email headers here for deep analysis. This will check authentication results, DKIM signatures, SPF validation, and more.', 'mailpn'),
      ];

        $mailpn_options['mailpn_header_analyzer'] = [
          'id' => 'mailpn_header_analyzer',
          'input' => 'header_analyzer',
        ];

      $mailpn_options['mailpn_subsection_header_analysis_end'] = [
        'section' => 'end',
      ];

      // Subsection: External Service Test
      $mailpn_options['mailpn_subsection_external_test_start'] = [
        'section' => 'start',
        'label' => __('External Service Test (Optional)', 'mailpn'),
        'description' => __('Use external services like Mail-Tester to get a comprehensive spam score and detailed deliverability report.', 'mailpn'),
      ];

        $mailpn_options['mailpn_external_tester'] = [
          'id' => 'mailpn_external_tester',
          'input' => 'external_tester',
        ];

      $mailpn_options['mailpn_subsection_external_test_end'] = [
        'section' => 'end',
      ];

    $mailpn_options['mailpn_section_deliverability_end'] = [
      'section' => 'end',
    ];

    $mailpn_options['mailpn_section_roles_start'] = [
      'section' => 'start',
      'label' => __('User Role Management', 'mailpn'),
      'description' => __('Manage which users have the Mailing Manager role. Users with this role can manage email campaigns and settings.', 'mailpn'),
    ];
      $mailpn_options['mailpn_role_manager_selector'] = [
        'id' => 'mailpn_role_manager_selector',
        'input' => 'user_role_selector',
        'label' => __('Mailing Manager - PN Role', 'mailpn'),
        'role' => 'mailpn_role_manager',
        'role_label' => __('Mailing Manager - PN', 'mailpn'),
      ];
    $mailpn_options['mailpn_section_roles_end'] = [
      'section' => 'end',
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
      esc_html__('Settings', 'mailpn'),
      esc_html__('Settings', 'mailpn'),
      'manage_options', 
      'mailpn_options', 
      [$this, 'mailpn_options'], 
      esc_url(MAILPN_URL . 'assets/media/mailpn-menu-icon.svg'),
    );

    add_submenu_page(
      'mailpn_options',
      esc_html__('Templates', 'mailpn'),
      esc_html__('Templates', 'mailpn'),
      'manage_options',
      'edit.php?post_type=mailpn_mail',
    );

    add_submenu_page(
      'mailpn_options',
      esc_html__('Sendings', 'mailpn'),
      esc_html__('Sendings', 'mailpn'),
      'manage_options',
      'edit.php?post_type=mailpn_rec',
    );

    add_submenu_page(
      'mailpn_options',
      esc_html__('Statistics', 'mailpn'),
      esc_html__('Statistics', 'mailpn'),
      'manage_options',
      'mailpn_dashboard',
      [$this, 'mailpn_dashboard_page']
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

        <div class="mailpn-options-fields mailpn-mb-30 mailpn-settings-pb-80">
          <form action="" method="post" id="mailpn_form" class="mailpn-form mailpn-p-30">
            <?php
              $options = $this->mailpn_get_options();

              foreach ($options as $mailpn_option):
                // Use full width for deliverability tools
                $format = 'half';
                if (isset($mailpn_option['id']) && in_array($mailpn_option['id'], [
                  'mailpn_deliverability_checker',
                  'mailpn_header_analyzer',
                  'mailpn_external_tester'
                ])) {
                  $format = 'full';
                }
                MAILPN_Forms::mailpn_input_wrapper_builder($mailpn_option, 'option', 0, 0, $format);
              endforeach;
            ?>
            <input type="submit" name="mailpn_submit" id="mailpn_submit" class="mailpn-settings-hidden-submit" data-mailpn-type="option" value="<?php esc_attr_e('Save options', 'mailpn'); ?>">
          </form>
        </div>
      </div>

      <?php
      // --- Recommended plugins ---
      $pn_family = [
        'pn-customers-manager' => [
          'name' => 'PN Customers Manager',
          'file' => 'pn-customers-manager/pn-customers-manager.php',
          'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 800 720"><path d="m 360,-240 v -80 H 680 V -604 Q 680,-721 598.5,-802.5 517,-884 400,-884 283,-884 201.5,-802.5 120,-721 120,-604 v 244 H 80 Q 47,-360 23.5,-383.5 0,-407 0,-440 v -80 Q 0,-541 10.5,-559.5 21,-578 40,-589 l 3,-53 q 8,-68 39.5,-126 31.5,-58 79,-101 47.5,-43 109,-67 61.5,-24 129.5,-24 68,0 129,24 61,24 109,66.5 48,42.5 79,100.5 31,58 40,126 l 3,52 q 19,9 29.5,27 10.5,18 10.5,38 v 92 q 0,20 -10.5,38 -10.5,18 -29.5,27 v 49 q 0,33 -23.5,56.5 Q 713,-240 680,-240 Z m -80,-280 q -17,0 -28.5,-11.5 Q 240,-543 240,-560 q 0,-17 11.5,-28.5 11.5,-11.5 28.5,-11.5 17,0 28.5,11.5 11.5,11.5 11.5,28.5 0,17 -11.5,28.5 Q 297,-520 280,-520 Z m 240,0 q -17,0 -28.5,-11.5 Q 480,-543 480,-560 q 0,-17 11.5,-28.5 11.5,-11.5 28.5,-11.5 17,0 28.5,11.5 11.5,11.5 11.5,28.5 0,17 -11.5,28.5 Q 537,-520 520,-520 Z m -359,-62 q -7,-106 64,-182 71,-76 177,-76 89,0 156.5,56.5 Q 626,-727 640,-639 549,-640 472.5,-688 396,-736 355,-818 339,-738 287.5,-675.5 236,-613 161,-582 Z" fill="#0000aa"/></svg>',
          'settings_page' => 'pn_customers_manager_options',
          'desc' => __('CRM with AI-powered WhatsApp and Instagram chat.', 'mailpn'),
        ],
        'userspn' => [
          'name' => 'UsersPN',
          'file' => 'userspn/userspn.php',
          'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z" fill="#00aa44"/></svg>',
          'settings_page' => 'userspn_options',
          'desc' => __('User management and registration forms.', 'mailpn'),
        ],
        'pn-tasks-manager' => [
          'name' => 'PN Tasks Manager',
          'file' => 'pn-tasks-manager/pn-tasks-manager.php',
          'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="m438-240 226-226-58-58-169 169-84-84-57 57 142 142ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z" fill="#552200"/></svg>',
          'settings_page' => 'pn_tasks_manager_options',
          'desc' => __('Task and project management.', 'mailpn'),
        ],
        'pn-cookies-manager' => [
          'name' => 'PN Cookies Manager',
          'file' => 'pn-cookies-manager/pn-cookies-manager.php',
          'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M480-80Q397-80 324-111.5 251-143 197-197 143-251 111.5-324 80-397 80-480q0-75 29-147 29-72 81-128.5 52-56.5 125-91 73-34.5 160-34.5 21 0 43 2 22 2 45 7-9 45 6 85 15 40 45 66.5 30 26.5 71.5 36.5 41.5 10 85.5-5-26 59 7.5 113 33.5 54 99.5 56 1 11 1.5 20.5.5 9.5.5 20.5 0 82-31.5 154.5-31.5 72.5-85.5 127-54 54.5-127 86Q563-80 480-80Zm-60-480q25 0 42.5-17.5Q480-595 480-620q0-25-17.5-42.5T420-680q-25 0-42.5 17.5T360-620q0 25 17.5 42.5T420-560Zm-80 200q25 0 42.5-17.5Q400-395 400-420q0-25-17.5-42.5T340-480q-25 0-42.5 17.5T280-420q0 25 17.5 42.5T340-360Zm260 40q17 0 28.5-11.5Q640-343 640-360q0-17-11.5-28.5T600-400q-17 0-28.5 11.5T560-360q0 17 11.5 28.5T600-320ZM480-160q122 0 216.5-84 94.5-84 103.5-214-50-22-78.5-60-28.5-38-38.5-85-77-11-132-66-55-55-68-132-80-2-140.5 29-60.5 31-101 79.5-40.5 48.5-61 105.5-20.5 57-20.5 107 0 133 93.5 226.5T480-160Zm0-324Z" fill="#803300"/></svg>',
          'settings_page' => 'pn_cookies_manager_options',
          'desc' => __('Cookie consent and GDPR compliance.', 'mailpn'),
        ],
      ];
      $pn_recommended = ['userspn'];
      if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
      $pn_installed = get_plugins();
      $pn_rp_badge = 0;
      foreach ($pn_recommended as $pn_s) {
        if (isset($pn_family[$pn_s]) && !isset($pn_installed[$pn_family[$pn_s]['file']])) {
          $pn_rp_badge++;
        }
      }
      ?>

      <!-- Sticky settings footer bar -->
      <div id="mailpn-settings-footer" class="mailpn-settings-footer">
        <div class="mailpn-settings-footer-inner">
          <div class="mailpn-settings-footer-left">
            <span class="mailpn-settings-footer-plugin-name">Mailing Manager</span>
            <span class="mailpn-settings-footer-version">v<?php echo esc_html(MAILPN_VERSION); ?></span>
          </div>
          <div class="mailpn-settings-footer-right">
            <button type="button" id="mailpn-settings-recommended" class="mailpn-settings-footer-icon-btn pn-cm-rp-btn mailpn-tooltip" title="<?php esc_attr_e('Recommended plugins', 'mailpn'); ?>">
              <span class="material-icons-outlined">add</span>
              <?php if ($pn_rp_badge > 0) : ?>
                <span class="pn-cm-rp-badge"><?php echo (int) $pn_rp_badge; ?></span>
              <?php endif; ?>
            </button>
            <input type="file" id="mailpn-settings-import-file" class="mailpn-settings-hidden-input" accept=".json">
            <button type="button" id="mailpn-settings-import" class="mailpn-settings-footer-icon-btn mailpn-tooltip" title="<?php esc_attr_e('Import settings', 'mailpn'); ?>">
              <span class="material-icons-outlined">file_upload</span>
            </button>
            <button type="button" id="mailpn-settings-export" class="mailpn-settings-footer-icon-btn mailpn-tooltip" title="<?php esc_attr_e('Export settings', 'mailpn'); ?>">
              <span class="material-icons-outlined">file_download</span>
            </button>
            <button type="button" id="mailpn-settings-save" class="mailpn-btn mailpn-btn-mini">
              <?php esc_html_e('Save options', 'mailpn'); ?>
            </button>
          </div>
        </div>
      </div>

      <!-- Recommended plugins popup -->
      <div class="mailpn-popup-overlay mailpn-display-none-soft" style="z-index:1000000;"></div>
      <div id="mailpn-recommended-plugins" class="mailpn-popup mailpn-popup-size-medium mailpn-display-none-soft" style="z-index:1000001;">
        <div class="mailpn-popup-content" style="padding:30px;">
          <h3 style="margin:0 0 8px;"><?php esc_html_e('Recommended Plugins', 'mailpn'); ?></h3>
          <p style="color:#787c82;margin:0 0 20px;"><?php esc_html_e('Enhance your workflow with these companion plugins.', 'mailpn'); ?></p>
          <div class="pn-cm-rp-list">
            <?php foreach ($pn_family as $pn_slug => $pn_pl) :
              $pn_is_installed = isset($pn_installed[$pn_pl['file']]);
              $pn_is_active    = $pn_is_installed && is_plugin_active($pn_pl['file']);
              $pn_is_rec       = in_array($pn_slug, $pn_recommended, true);
            ?>
            <div class="pn-cm-rp-card" data-slug="<?php echo esc_attr($pn_slug); ?>">
              <div class="pn-cm-rp-icon"><?php echo $pn_pl['icon']; ?></div>
              <div class="pn-cm-rp-info">
                <div class="pn-cm-rp-name">
                  <?php echo esc_html($pn_pl['name']); ?>
                  <?php if ($pn_is_rec) : ?>
                    <span class="pn-cm-rp-recommended"><?php esc_html_e('Recommended', 'mailpn'); ?></span>
                  <?php endif; ?>
                </div>
                <div class="pn-cm-rp-desc"><?php echo esc_html($pn_pl['desc']); ?></div>
              </div>
              <div class="pn-cm-rp-action">
                <?php if ($pn_is_active) : ?>
                  <span class="pn-cm-rp-active-badge"><?php esc_html_e('Active', 'mailpn'); ?></span>
                <?php elseif ($pn_is_installed) : ?>
                  <button type="button" class="mailpn-btn mailpn-btn-mini mailpn-btn-transparent pn-cm-rp-activate" data-slug="<?php echo esc_attr($pn_slug); ?>"><?php esc_html_e('Activate', 'mailpn'); ?></button>
                <?php else : ?>
                  <button type="button" class="mailpn-btn mailpn-btn-mini mailpn-btn-transparent pn-cm-rp-install" data-slug="<?php echo esc_attr($pn_slug); ?>"><?php esc_html_e('Install', 'mailpn'); ?></button>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <?php
      wp_enqueue_script(
        'mailpn-settings-footer',
        MAILPN_URL . 'assets/js/admin/mailpn-settings-footer.js',
        ['mailpn-popups'],
        MAILPN_VERSION,
        true
      );

      $pn_rp_settings = [];
      foreach ($pn_family as $pn_slug => $pn_pl) {
        $pn_rp_settings[$pn_slug] = admin_url('admin.php?page=' . $pn_pl['settings_page']);
      }

      wp_localize_script('mailpn-settings-footer', 'mailpnSettingsFooter', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('mailpn-nonce'),
        'settingsPages' => $pn_rp_settings,
        'i18n'    => [
          'confirmImport'  => __('This will overwrite your current settings. Continue?', 'mailpn'),
          'importSuccess'  => __('Settings imported successfully. Reloading...', 'mailpn'),
          'importError'    => __('Error importing settings.', 'mailpn'),
          'invalidFile'    => __('Invalid JSON file.', 'mailpn'),
          'exportError'    => __('Error exporting settings.', 'mailpn'),
          'installing'     => __('Installing...', 'mailpn'),
          'activating'     => __('Activating...', 'mailpn'),
          'installError'   => __('Error installing plugin.', 'mailpn'),
          'activateError'  => __('Error activating plugin.', 'mailpn'),
          'active'         => __('Active', 'mailpn'),
          'activate'       => __('Activate', 'mailpn'),
        ],
      ]);
	  ?>
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
      return false;
    }

    return MAILPN_Mailing::mailpn_is_email_address_excepted($user->user_email);
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
      return;
    }
    
    
    
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
        $skipped_count++;
        continue;
      }
      
      
      
      // If user has the newsletter subscriber role, mark as processed and keep
      if (in_array('userspn_newsletter_subscriber', $user->roles)) {
        $registration['processed'] = true;
        $updated_pending_registrations[] = $registration;
        
        $processed_count++;
        continue;
      }
      
      // If user's email is in the exception lists, mark as processed and keep
      if ($this->mailpn_is_email_excepted($reg_user_id)) {
        $registration['processed'] = true;
        $updated_pending_registrations[] = $registration;
        
        $processed_count++;
        continue;
      }
      
      // Try to send welcome emails
      $sent = $this->mailpn_trigger_welcome_emails($reg_user_id);
      
      // If an email was sent, mark as processed
      if ($sent) {
        $registration['processed'] = true;
        
        $processed_count++;
      } else {
        $skipped_count++;
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
      
      return false;
    }
    
    // Get all welcome email templates (include 'draft' so that templates
    // saved as WP drafts still schedule delayed welcome emails)
    $welcome_emails = get_posts([
      'fields' => 'ids',
      'numberposts' => -1,
      'post_type' => 'mailpn_mail',
      'post_status' => ['publish', 'draft'],
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

    // Collect all matching welcome emails for this user
    $matching_emails = [];
    foreach ($welcome_emails as $email_id) {
      if (!MAILPN_Mailing::mailpn_user_matches_distribution($email_id, $user_id)) {
        continue;
      }
      $matching_emails[] = $email_id;
    }

    if (empty($matching_emails)) {
      return false;
    }

    // Check if any matching email has the priority flag enabled
    $priority_email_id = null;
    foreach ($matching_emails as $email_id) {
      if (get_post_meta($email_id, 'mailpn_welcome_priority', true) === 'on') {
        $priority_email_id = $email_id;
        break;
      }
    }

    // If a priority email was found, only send that one
    $emails_to_send = $priority_email_id ? [$priority_email_id] : $matching_emails;

    foreach ($emails_to_send as $email_id) {
      // Check if delay is enabled for this welcome email
      $delay_enabled = get_post_meta($email_id, 'mailpn_welcome_delay_enabled', true);

      if ($delay_enabled === 'on') {
        // Schedule the email for delayed sending
        $this->mailpn_schedule_delayed_welcome_email($email_id, $user_id);
      } else {
        // Send immediately
        $result = $mailing_plugin->mailpn_queue_add($email_id, $user_id);
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
    
    
    // Check if user's email is in the exception lists
    if ($this->mailpn_is_email_excepted($user_id)) {
      return;
    }
    
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
    
    // Add the new scheduled email
    $scheduled_emails[] = [
      'email_id' => $email_id,
      'user_id' => $user_id,
      'scheduled_time' => $scheduled_time,
      'created_time' => time()
    ];
    
    $result = update_option('mailpn_scheduled_welcome_emails', $scheduled_emails);
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
        if (MAILPN_Mailing::mailpn_user_matches_distribution($email_id, $reg_user_id)) {
          $can_receive_emails = true;
          break;
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
          if (!isset($_GET['_wpnonce'])) {
              wp_die(esc_html__('Security check failed: invalid nonce', 'mailpn'));
          }
          
          $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce']));
          $nonce_valid = false;
          
          // Try the general nonce first (for backward compatibility)
          if (wp_verify_nonce($nonce, 'mailpn_action')) {
              $nonce_valid = true;
          } else {
              // Try user-specific nonces - check if it's a user-specific nonce format
              if (isset($_GET['user_id'])) {
                  $user_id = absint($_GET['user_id']);
                  if ($user_id && wp_verify_nonce($nonce, 'mailpn_action_' . $user_id)) {
                      $nonce_valid = true;
                  }
              }
          }
          
          if (!$nonce_valid) {
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