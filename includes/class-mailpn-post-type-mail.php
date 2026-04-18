<?php
/**
 * Mail creator.
 *
 * This class defines Mail options, menus and templates.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Post_Type_Mail
{
  public function mailpn_get_fields_meta()
  {
    global $wp_roles, $post;
    $post_id = $post->ID;

    $cpt_options = [];
    foreach (get_post_types() as $post_type) {
      $cpt_options[$post_type] = str_replace('_', ' ', ucfirst($post_type));
    }

    $role_options = [];
    foreach ($wp_roles->roles as $role_key => $role_value) {
      $role_options[$role_key] = $role_value['name'];
    }

    $mailpn_user_options = [];
    foreach (get_users(['fields' => 'ids', 'number' => -1, 'orderby' => 'display_name', 'order' => 'ASC',]) as $user_id) {
      $mailpn_user_options[$user_id] = get_user_by('id', $user_id)->user_email . ' (ID#' . $user_id . ') ' . get_user_meta($user_id, 'first_name', true) . ' ' . get_user_meta($user_id, 'last_name', true);
    }

    $mailpn_attachments_options = [];
    foreach (get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'attachment', 'orderby' => 'display_name', 'order' => 'ASC',]) as $attachment_id) {
      $mailpn_attachments_options[$attachment_id] = get_the_title($attachment_id) . ' (ID#' . get_post($attachment_id)->ID . ')';
    }

    $mailpn_fields_meta = [];
    $mailpn_fields_meta['mailpn_tools'] = [
      'id' => 'mailpn_tools',
      'input' => 'html',
      'html_content' => '[mailpn-tools post_id="' . $post_id . '"]',
    ];
    $mailpn_fields_meta['mailpn_shortcodes_help'] = [
      'id' => 'mailpn_shortcodes_help',
      'input' => 'html',
      'html_content' => '<div class="mailpn-sc-help mailpn-sc-collapsed">'
        . '<a href="#" class="mailpn-sc-toggle mailpn-sc-btn"><i class="material-icons-outlined">code</i> ' . esc_html__('Shortcodes', 'mailpn') . '</a>'
        . '<div class="mailpn-sc-list">'
        . '<span class="mailpn-sc-item"><span id="mailpn-sc-user-name" class="mailpn-sc-code">&#91;user-name&#93;</span><a href="#" class="mailpn-btn-copy" data-mailpn-copy-content="#mailpn-sc-user-name"><i class="material-icons-outlined">content_copy</i></a></span>'
        . '<span class="mailpn-sc-item"><span id="mailpn-sc-post-name" class="mailpn-sc-code">&#91;post-name&#93;</span><a href="#" class="mailpn-btn-copy" data-mailpn-copy-content="#mailpn-sc-post-name"><i class="material-icons-outlined">content_copy</i></a></span>'
        . '<span class="mailpn-sc-item"><span id="mailpn-sc-new-contents" class="mailpn-sc-code">&#91;new-contents&#93;</span><a href="#" class="mailpn-btn-copy" data-mailpn-copy-content="#mailpn-sc-new-contents"><i class="material-icons-outlined">content_copy</i></a></span>'
        . apply_filters('mailpn_shortcode_ui_items', '')
        . '</div>'
        . '</div>',
    ];
    $mailpn_fields_meta['mailpn_type'] = [
      'id' => 'mailpn_type',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => MAILPN_Data::mailpn_mail_types(),
      'required' => 'true',
      'parent' => 'this',
      'label' => __('Email type', 'mailpn'),
      'placeholder' => __('Email type', 'mailpn'),
    ];

    // Welcome email delay configuration
    $mailpn_fields_meta['mailpn_welcome_delay_enabled'] = [
      'id' => 'mailpn_welcome_delay_enabled',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'parent' => 'this mailpn_type',
      'parent_option' => 'email_welcome',
      'label' => __('Enable delayed sending', 'mailpn'),
      'description' => __('If enabled, the welcome email will be sent after a specified delay instead of immediately upon user registration.', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_welcome_delay_value'] = [
      'id' => 'mailpn_welcome_delay_value',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'min' => '1',
      'parent' => 'mailpn_welcome_delay_enabled',
      'parent_option' => 'on',
      'label' => __('Delay value', 'mailpn'),
      'placeholder' => __('Enter delay value', 'mailpn'),
      'description' => __('The number of time units to wait before sending the welcome email.', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_welcome_delay_unit'] = [
      'id' => 'mailpn_welcome_delay_unit',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => [
        'hours' => __('Hours', 'mailpn'),
        'days' => __('Days', 'mailpn'),
        'weeks' => __('Weeks', 'mailpn'),
        'months' => __('Months', 'mailpn'),
        'years' => __('Years', 'mailpn'),
      ],
      'parent' => 'mailpn_welcome_delay_enabled',
      'parent_option' => 'on',
      'label' => __('Delay unit', 'mailpn'),
      'placeholder' => __('Select time unit', 'mailpn'),
      'description' => __('The time unit for the delay period.', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_periodic_period'] = [
      'id' => 'mailpn_periodic_period',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => ['hourly' => __('Each hour', 'mailpn'), 'daily' => __('Each day', 'mailpn'), 'weekly' => __('Each week', 'mailpn'), 'monthly' => __('Each month', 'mailpn'), 'yearly' => __('Each year', 'mailpn'),],
      'parent' => 'mailpn_type',
      'parent_option' => 'email_periodic',
      'label' => __('Sending period', 'mailpn'),
      'placeholder' => __('Sending period', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_updated_content_cpt'] = [
      'id' => 'mailpn_updated_content_cpt',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => $cpt_options,
      'parent' => 'mailpn_type',
      'parent_option' => 'email_published_content',
      'label' => __('Which content will be sent?', 'mailpn'),
      'placeholder' => __('Which content will be sent?', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_updated_content'] = [
      'id' => 'mailpn_updated_content',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => ['email_published_content_new' => __('Each new content', 'mailpn'), 'email_published_content_period' => __('From time to time', 'mailpn'), 'email_published_content_date' => __('In a specific date', 'mailpn'),],
      'parent' => 'this mailpn_type',
      'parent_option' => 'email_published_content',
      'label' => __('When will it be sent?', 'mailpn'),
      'placeholder' => __('When will it be sent?', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_updated_content_period'] = [
      'id' => 'mailpn_updated_content_period',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => ['hourly' => __('Each hour', 'mailpn'), 'daily' => __('Each day', 'mailpn'), 'weekly' => __('Each week', 'mailpn'), 'monthly' => __('Each month', 'mailpn'), 'yearly' => __('Each year', 'mailpn'),],
      'parent' => 'mailpn_updated_content',
      'parent_option' => 'email_published_content_period',
      'label' => __('Sending period', 'mailpn'),
      'placeholder' => __('Sending period', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_updated_content_date'] = [
      'id' => 'mailpn_updated_content_date',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => ['in_minute_on_hour' => __('In a specific minute each hour', 'mailpn'), 'in_hour_on_day' => __('In a specific hour each day', 'mailpn'), 'in_day_on_week' => __('In a specific day each week', 'mailpn'), 'in_day_on_month' => __('In a specific day each month', 'mailpn'), 'in_week_on_year' => __('In a specific week each year', 'mailpn'), 'in_month_on_year' => __('In a specific month each year', 'mailpn'),],
      'parent' => 'mailpn_updated_content',
      'parent_option' => 'email_published_content_date',
      'label' => __('Sending period', 'mailpn'),
      'placeholder' => __('Sending period', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_email_coded_once'] = [
      'id' => 'mailpn_email_coded_once',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'parent' => 'mailpn_type',
      'parent_option' => 'email_coded',
      'label' => __('Only send once', 'mailpn'),
    ];

    // WooCommerce Purchase Email Delay Configuration
    $mailpn_fields_meta['mailpn_woocommerce_purchase_delay_value'] = [
      'id' => 'mailpn_woocommerce_purchase_delay_value',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'min' => '1',
      'parent' => 'mailpn_type',
      'parent_option' => 'email_woocommerce_purchase',
      'label' => __('Delay value', 'mailpn'),
      'placeholder' => __('Enter delay value', 'mailpn'),
      'description' => __('The number of time units to wait before sending the email after purchase.', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_woocommerce_purchase_delay_unit'] = [
      'id' => 'mailpn_woocommerce_purchase_delay_unit',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => [
        'minutes' => __('Minutes', 'mailpn'),
        'hours' => __('Hours', 'mailpn'),
        'days' => __('Days', 'mailpn'),
      ],
      'parent' => 'mailpn_type',
      'parent_option' => 'email_woocommerce_purchase',
      'label' => __('Delay unit', 'mailpn'),
      'placeholder' => __('Select time unit', 'mailpn'),
      'description' => __('The time unit for the delay period.', 'mailpn'),
    ];

    // WooCommerce Abandoned Cart Email Delay Configuration
    $mailpn_fields_meta['mailpn_woocommerce_abandoned_cart_delay_value'] = [
      'id' => 'mailpn_woocommerce_abandoned_cart_delay_value',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'min' => '1',
      'parent' => 'mailpn_type',
      'parent_option' => 'email_woocommerce_abandoned_cart',
      'label' => __('Delay value', 'mailpn'),
      'placeholder' => __('Enter delay value', 'mailpn'),
      'description' => __('The number of time units to wait before sending the abandoned cart email.', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_woocommerce_abandoned_cart_delay_unit'] = [
      'id' => 'mailpn_woocommerce_abandoned_cart_delay_unit',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => [
        'minutes' => __('Minutes', 'mailpn'),
        'hours' => __('Hours', 'mailpn'),
        'days' => __('Days', 'mailpn'),
      ],
      'parent' => 'mailpn_type',
      'parent_option' => 'email_woocommerce_abandoned_cart',
      'label' => __('Delay unit', 'mailpn'),
      'placeholder' => __('Select time unit', 'mailpn'),
      'description' => __('The time unit for the delay period.', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_distribution'] = [
      'id' => 'mailpn_distribution',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => 'checkbox',
      'options' => ['public' => __('Everybody will receive', 'mailpn'), 'private_role' => __('Only users with specific role will receive', 'mailpn'), 'private_user' => __('Only specific users will receive', 'mailpn'),],
      'parent' => 'this',
      'label' => __('Email distribution', 'mailpn'),
      'placeholder' => __('Email distribution', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_distribution_role'] = [
      'id' => 'mailpn_distribution_role',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => $role_options,
      'multiple' => 'true',
      'parent' => 'mailpn_distribution',
      'parent_option' => 'private_role',
      'label' => __('Roles receiving the email', 'mailpn'),
      'placeholder' => __('Roles receiving the email', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_distribution_user'] = [
      'id' => 'mailpn_distribution_user',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => $mailpn_user_options,
      'multiple' => 'true',
      'parent' => 'mailpn_distribution',
      'parent_option' => 'private_user',
      'multiple' => 'true',
      'label' => __('Users receiving the email', 'mailpn'),
      'placeholder' => __('Users receiving the email', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_time'] = [
      'id' => 'mailpn_time',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'parent' => 'this',
      'label' => __('Time email', 'mailpn'),
      'placeholder' => __('Time email', 'mailpn'),
      'description' => __('If you select this option, the email will be active only in the temporal frame selected, after the date and time start, and before the date and time end.', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_date_start'] = [
      'id' => 'mailpn_date_start',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'date',
      'parent' => 'mailpn_time',
      'parent_option' => 'on',
      'label' => __('Mail sending start date', 'mailpn'),
      'placeholder' => __('Mail sending start date', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_time_start'] = [
      'id' => 'mailpn_time_start',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'time',
      'parent' => 'mailpn_time',
      'parent_option' => 'on',
      'label' => __('Mail sending start time', 'mailpn'),
      'placeholder' => __('Mail sending start time', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_date_end'] = [
      'id' => 'mailpn_date_end',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'date',
      'parent' => 'mailpn_time',
      'parent_option' => 'on',
      'label' => __('Mail sending end date', 'mailpn'),
      'placeholder' => __('Mail sending end date', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_time_end'] = [
      'id' => 'mailpn_time_end',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'input' => 'input',
      'type' => 'time',
      'parent' => 'mailpn_time',
      'parent_option' => 'on',
      'label' => __('Mail sending end time', 'mailpn'),
      'placeholder' => __('Mail sending end time', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_attachments'] = [
      'id' => 'mailpn_attachments',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'multiple' => 'true',
      'input' => 'select',
      'options' => $mailpn_attachments_options,
      'label' => __('Mail attachments', 'mailpn'),
      'placeholder' => __('Mail attachments', 'mailpn'),
      'description' => __('You can include several files to be sent with this email as attachments. To see the files here, please upload them in the library and then refresh this page.', 'mailpn'),
    ];

    // Schedule preview section
    $mailpn_fields_meta['mailpn_schedule_preview'] = [
      'id' => 'mailpn_schedule_preview',
      'input' => 'html',
      'html_content' => self::mailpn_get_schedule_preview_html($post_id),
    ];

    $mailpn_fields_meta['mailpn_ajax_nonce'] = [
      'id' => 'mailpn_ajax_nonce',
      'input' => 'input',
      'type' => 'nonce',
    ];

    return $mailpn_fields_meta;
  }

  /**
   * Register Mail.
   *
   * @since    1.0.0
   */
  public function mailpn_register_post_type()
  {
    $labels = [
      'name' => _x('Mail templates', 'Post Type general name', 'mailpn'),
      'singular_name' => _x('Mail template', 'Post Type singular name', 'mailpn'),
      'menu_name' => esc_html(__('Mail templates', 'mailpn')),
      'parent_item_colon' => esc_html(__('Parent Mail template', 'mailpn')),
      'all_items' => esc_html(__('All Mail templates', 'mailpn')),
      'view_item' => esc_html(__('View Mail template', 'mailpn')),
      'add_new_item' => esc_html(__('Add new Mail template', 'mailpn')),
      'add_new' => esc_html(__('Add new Mail template', 'mailpn')),
      'edit_item' => esc_html(__('Edit Mail template', 'mailpn')),
      'update_item' => esc_html(__('Update Mail template', 'mailpn')),
      'search_items' => esc_html(__('Search Mail templates', 'mailpn')),
      'not_found' => esc_html(__('Not Mail template found', 'mailpn')),
      'not_found_in_trash' => esc_html(__('Not Mail template found in Trash', 'mailpn')),
    ];

    $args = [
      'labels' => $labels,
      'rewrite' => ['slug' => (!empty(get_option('mailpn')) ? get_option('mailpn') : 'mailpn'), 'with_front' => false],
      'label' => esc_html(__('Mail template', 'mailpn')),
      'description' => esc_html(__('Mail template description', 'mailpn')),
      'supports' => ['title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'page-attributes',],
      'hierarchical' => false,
      'public' => false,
      'show_ui' => true,
      'show_in_menu' => false,
      'show_in_nav_menus' => false,
      'show_in_admin_bar' => false,
      'can_export' => false,
      'has_archive' => false,
      'exclude_from_search' => true,
      'publicly_queryable' => false,
      'capability_type' => 'page',
      'map_meta_cap' => true,
      'taxonomies' => ['mailpn_mail_category'],
      'show_in_rest' => true,
    ];

    register_post_type('mailpn_mail', $args);
    add_theme_support('post-thumbnails', ['page', 'mailpn_mail']);
  }

  /**
   * Block REST API access for mailpn_mail post type while keeping Gutenberg editor
   *
   * @param WP_Error|null|bool $errors WP_Error if authentication error, null if authentication method wasn't used, true if authentication succeeded.
   * @return WP_Error|null|bool
   */
  public function mailpn_mail_block_rest_api_access($errors)
  {
    // Get the current REST route
    $current_route = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';

    // Check if this is a request to the mailpn_mail endpoint
    if (strpos($current_route, '/wp-json/wp/v2/mailpn_mail') !== false) {
      // Allow all POST/PUT/DELETE requests (needed for saving/updating posts)
      if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        return $errors;
      }

      // For GET requests, only allow access for authenticated users in admin context
      if (!is_user_logged_in() || !is_admin()) {
        return new WP_Error(
          'rest_forbidden',
          __('Sorry, you are not allowed to access this endpoint.', 'mailpn'),
          ['status' => rest_authorization_required_code()]
        );
      }
    }

    return $errors;
  }

  /**
   * Add Mail dashboard metabox.
   *
   * @since    1.0.0
   */
  public function mailpn_add_meta_box()
  {
    add_meta_box('mailpn_meta_box', esc_html(__('Mail template details', 'mailpn')), [$this, 'mailpn_meta_box_function'], 'mailpn_mail', 'normal', 'high', ['__block_editor_compatible_meta_box' => true,]);
  }

  /**
   * Defines Mail dashboard contents.
   *
   * @since    1.0.0
   */
  public function mailpn_meta_box_function($post)
  {
    foreach ($this->mailpn_get_fields_meta() as $mailpn_field) {
      MAILPN_Forms::mailpn_input_wrapper_builder($mailpn_field, 'post', $post->ID);
    }
  }

  public function mailpn_save_post($post_id)
  {
    // If this is an autosave, our form has not been submitted
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    // Check if this is a mailpn_mail post type
    if (get_post_type($post_id) !== 'mailpn_mail') {
      return $post_id;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
      return $post_id;
    }

    // Check if we have the required fields
    if (!array_key_exists('mailpn_type', $_POST)) {
      return $post_id;
    }

    // Always require nonce verification
    if (!array_key_exists('mailpn_ajax_nonce', $_POST)) {
      return $post_id;
    }

    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mailpn_ajax_nonce'])), 'mailpn-nonce')) {
      return $post_id;
    }

    // Don't process if this is a duplicate
    if (!array_key_exists('mailpn_duplicate', $_POST)) {
      foreach ($this->mailpn_get_fields_meta() as $mailpn_field) {
        $mailpn_input = array_key_exists('input', $mailpn_field) ? $mailpn_field['input'] : '';

        if (array_key_exists($mailpn_field['id'], $_POST) || $mailpn_input == 'html_multi') {
          $mailpn_value = array_key_exists($mailpn_field['id'], $_POST) ? MAILPN_Forms::mailpn_sanitizer($_POST[$mailpn_field['id']], $mailpn_field['input'], (!empty($mailpn_field['type']) ? $mailpn_field['type'] : '')) : '';

          if (!empty($mailpn_input)) {
            switch ($mailpn_input) {
              case 'input':
                if (array_key_exists('type', $mailpn_field) && $mailpn_field['type'] == 'checkbox') {
                  if (isset($_POST[$mailpn_field['id']])) {
                    update_post_meta($post_id, $mailpn_field['id'], $mailpn_value);
                  } else {
                    update_post_meta($post_id, $mailpn_field['id'], '');
                  }
                } else {
                  update_post_meta($post_id, $mailpn_field['id'], $mailpn_value);
                }

                break;
              case 'select':
                if (array_key_exists('multiple', $mailpn_field) && $mailpn_field['multiple']) {
                  $multi_array = [];
                  $empty = true;

                  foreach ($_POST[$mailpn_field['id']] as $multi_value) {
                    $multi_array[] = MAILPN_Forms::mailpn_sanitizer($multi_value, $mailpn_field['input'], (!empty($mailpn_field['type']) ? $mailpn_field['type'] : ''));
                  }

                  update_post_meta($post_id, $mailpn_field['id'], $multi_array);
                } else {
                  update_post_meta($post_id, $mailpn_field['id'], $mailpn_value);
                }

                break;
              case 'html_multi':
                foreach ($mailpn_field['html_multi_fields'] as $mailpn_multi_field) {
                  if (array_key_exists($mailpn_multi_field['id'], $_POST)) {
                    $multi_array = [];
                    $empty = true;

                    foreach ($_POST[$mailpn_multi_field['id']] as $multi_value) {
                      if (!empty($multi_value)) {
                        $empty = false;
                      }

                      $multi_array[] = MAILPN_Forms::mailpn_sanitizer($multi_value, $mailpn_multi_field['input'], (!empty($mailpn_multi_field['type']) ? $mailpn_multi_field['type'] : ''));
                    }

                    if (!$empty) {
                      update_post_meta($post_id, $mailpn_multi_field['id'], $multi_array);
                    } else {
                      update_post_meta($post_id, $mailpn_field['id'], '');
                    }
                  }
                }

                break;
              default:
                update_post_meta($post_id, $mailpn_field['id'], $mailpn_value);
                break;
            }
          } else {
            update_post_meta($post_id, $mailpn_field['id'], '');
          }
        }
      }
    }

    // ONE TIME email processing
    if (in_array(get_post_meta($post_id, 'mailpn_type', true), ['email_one_time']) && (empty(get_post_meta($post_id, 'mailpn_status', true)) || !in_array(get_post_meta($post_id, 'mailpn_status', true), ['queue', 'sent']))) {
      $mailing_plugin = new MAILPN_Mailing();

      $users_to = $mailing_plugin->mailpn_get_users_to($post_id);
      if (!empty($users_to)) {
        foreach ($users_to as $index => $user_id) {
          $mailing_plugin->mailpn_queue_add($post_id, $user_id);

          if ($index == (count($users_to) - 1)) {
            update_post_meta($post_id, 'mailpn_status', 'queue');
          }
        }
      }
    }
  }

  public function mailpn_mail_posts_columns($columns)
  {
    $new_columns = [];

    $new_columns['cb']    = isset($columns['cb']) ? $columns['cb'] : '<input type="checkbox" />';
    $new_columns['title'] = isset($columns['title']) ? $columns['title'] : __('Title', 'mailpn');
    $new_columns['mailpn_mail_type']   = __('Mail Type', 'mailpn');
    $new_columns['mailpn_mail_status'] = __('Status', 'mailpn');

    if (isset($columns['date'])) {
      $new_columns['date'] = $columns['date'];
    }

    return $new_columns;
  }

  public function mailpn_mail_posts_custom_column($column_slug, $post_id)
  {
    switch ($column_slug) {
      case 'mailpn_mail_type':
        $mail_type = get_post_meta($post_id, 'mailpn_type', true);
        if ($mail_type) {
          ?>
          <p><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-mr-10">mark_email_read</i>
            <?php echo isset(MAILPN_Data::mailpn_mail_types()[$mail_type]) ? esc_html(MAILPN_Data::mailpn_mail_types()[$mail_type]) : esc_html($mail_type); ?>
          </p>
          <?php
          // Distribution info
          $distribution = get_post_meta($post_id, 'mailpn_distribution', true);
          if (!empty($distribution)) {
            ?>
            <div class="mailpn-column-meta">
              <?php if ($distribution === 'private_role'): ?>
                <?php
                  $roles = get_post_meta($post_id, 'mailpn_distribution_role', true);
                  if (!empty($roles) && is_array($roles)):
                    global $wp_roles;
                ?>
                  <span class="mailpn-column-meta-item" title="<?php esc_attr_e('Roles receiving this email', 'mailpn'); ?>">
                    <i class="material-icons-outlined">group</i>
                    <?php
                      $role_names = [];
                      foreach ($roles as $role_slug) {
                        $role_names[] = isset($wp_roles->roles[$role_slug]) ? $wp_roles->roles[$role_slug]['name'] : $role_slug;
                      }
                      echo esc_html(implode(', ', $role_names));
                    ?>
                  </span>
                <?php endif; ?>
              <?php elseif ($distribution === 'private_user'): ?>
                <?php
                  $user_list = get_post_meta($post_id, 'mailpn_distribution_user', true);
                  $user_count = !empty($user_list) && is_array($user_list) ? count($user_list) : 0;
                ?>
                <span class="mailpn-column-meta-item" title="<?php esc_attr_e('Specific users', 'mailpn'); ?>">
                  <i class="material-icons-outlined">person</i>
                  <?php echo esc_html(sprintf(_n('%d user', '%d users', $user_count, 'mailpn'), $user_count)); ?>
                </span>
              <?php elseif ($distribution === 'public'): ?>
                <span class="mailpn-column-meta-item" title="<?php esc_attr_e('All users', 'mailpn'); ?>">
                  <i class="material-icons-outlined">public</i>
                  <?php esc_html_e('All users', 'mailpn'); ?>
                </span>
              <?php endif; ?>

              <?php
              // Delay info for welcome emails
              if ($mail_type === 'email_welcome') {
                $delay_enabled = get_post_meta($post_id, 'mailpn_welcome_delay_enabled', true);
                if ($delay_enabled === 'on') {
                  $delay_value = get_post_meta($post_id, 'mailpn_welcome_delay_value', true);
                  $delay_unit = get_post_meta($post_id, 'mailpn_welcome_delay_unit', true);
                  if (!empty($delay_value) && !empty($delay_unit)) {
                    $unit_labels = [
                      'hours' => _n_noop('%d hour', '%d hours', 'mailpn'),
                      'days' => _n_noop('%d day', '%d days', 'mailpn'),
                      'weeks' => _n_noop('%d week', '%d weeks', 'mailpn'),
                      'months' => _n_noop('%d month', '%d months', 'mailpn'),
                    ];
                    $delay_label = isset($unit_labels[$delay_unit])
                      ? sprintf(translate_nooped_plural($unit_labels[$delay_unit], intval($delay_value)), intval($delay_value))
                      : $delay_value . ' ' . $delay_unit;
                    ?>
                    <span class="mailpn-column-meta-item mailpn-column-meta-delay" title="<?php esc_attr_e('Sending delay', 'mailpn'); ?>">
                      <i class="material-icons-outlined">schedule</i>
                      <?php echo esc_html($delay_label); ?>
                    </span>
                    <?php
                  }
                }
              }
              ?>
            </div>
          <?php
          }
        } else {
          ?>
          <p><i
              class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-red mailpn-mr-10">mark_email_read</i>
            <?php esc_html_e('Unset email type.', 'mailpn'); ?></p>
          <?php
        }
        break;

      case 'mailpn_mail_status':
        $mailpn_status = get_post_meta($post_id, 'mailpn_status', true);
        $mail_type = get_post_meta($post_id, 'mailpn_type', true);
        $wp_post_status = get_post_status($post_id);
        $sent_count = count(get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'mailpn_rec', 'post_status' => ['any'], 'meta_key' => 'mailpn_rec_mail_id', 'meta_value' => $post_id]));
        $mailpn_timestamps = get_post_meta($post_id, 'mailpn_timestamp_sent', true);
        $last_sent = !empty($mailpn_timestamps) && is_array($mailpn_timestamps) ? end($mailpn_timestamps) : '';
        $send_count_label = $sent_count > 0 ? sprintf(__('%d sent', 'mailpn'), $sent_count) : '';

        if ($mailpn_status === 'queue') {
          // Currently sending
          $mailpn_queue_data = get_option('mailpn_queue');
          $emails_pending = !empty($mailpn_queue_data[$post_id]) ? count($mailpn_queue_data[$post_id]) : 0;
          $emails_total = $emails_pending + $sent_count;
          ?>
          <span class="mailpn-column-status-badge mailpn-column-status-queue mailpn-status-clickable" data-post-id="<?php echo esc_attr($post_id); ?>">
            <i class="material-icons-outlined mailpn-vertical-align-middle">send</i>
            <?php esc_html_e('Sending...', 'mailpn'); ?>
          </span>
          <?php if ($emails_total > 0): ?>
            <span class="mailpn-column-status-detail"><?php echo esc_html($sent_count); ?> <?php esc_html_e('of', 'mailpn'); ?> <?php echo esc_html($emails_total); ?></span>
          <?php endif; ?>
          <?php

        } elseif ($mailpn_status === 'sent' && $mail_type === 'email_periodic') {
          // Periodic email — sent at least once, will send again
          $period = get_post_meta($post_id, 'mailpn_periodic_period', true);
          $next_send = !empty($last_sent) && !empty($period)
            ? $last_sent + MAILPN_Cron::mailpn_periodic_interval_seconds_static($period)
            : 0;
          ?>
          <span class="mailpn-column-status-badge mailpn-column-status-active mailpn-status-clickable" data-post-id="<?php echo esc_attr($post_id); ?>">
            <i class="material-icons-outlined mailpn-vertical-align-middle">autorenew</i>
            <?php esc_html_e('Active', 'mailpn'); ?>
          </span>
          <span class="mailpn-column-status-detail">
            <?php echo esc_html($send_count_label); ?>
            <?php if ($next_send > 0): ?>
              · <?php echo esc_html(sprintf(__('Next: %s', 'mailpn'), date_i18n(get_option('date_format') . ' H:i', $next_send))); ?>
            <?php endif; ?>
          </span>
          <?php

        } elseif ($mailpn_status === 'sent') {
          // Non-periodic sent email
          ?>
          <span class="mailpn-column-status-badge mailpn-column-status-sent mailpn-status-clickable" data-post-id="<?php echo esc_attr($post_id); ?>">
            <i class="material-icons-outlined mailpn-vertical-align-middle">check_circle</i>
            <?php esc_html_e('Sent', 'mailpn'); ?>
          </span>
          <span class="mailpn-column-status-detail">
            <?php if ($sent_count > 0): ?>
              <?php echo esc_html(sprintf(__('%d recipients', 'mailpn'), $sent_count)); ?>
            <?php endif; ?>
            <?php if (!empty($last_sent)): ?>
              · <?php echo esc_html(date_i18n(get_option('date_format'), $last_sent)); ?>
            <?php endif; ?>
          </span>
          <?php

        } elseif ($wp_post_status === 'publish') {
          // Published template with no explicit status
          if ($mail_type === 'email_periodic') {
            // Periodic waiting for first send
            ?>
            <span class="mailpn-column-status-badge mailpn-column-status-scheduled mailpn-status-clickable" data-post-id="<?php echo esc_attr($post_id); ?>">
              <i class="material-icons-outlined mailpn-vertical-align-middle">schedule_send</i>
              <?php esc_html_e('Scheduled', 'mailpn'); ?>
            </span>
            <span class="mailpn-column-status-detail"><?php esc_html_e('Waiting for first send', 'mailpn'); ?></span>
            <?php
          } elseif (in_array($mail_type, ['email_welcome', 'newsletter_welcome', 'email_verify_code', 'email_password_reset', 'email_coded', 'email_published_content', 'email_woocommerce_purchase', 'email_woocommerce_abandoned_cart'])) {
            // Event-triggered email
            ?>
            <span class="mailpn-column-status-badge mailpn-column-status-active mailpn-status-clickable" data-post-id="<?php echo esc_attr($post_id); ?>">
              <i class="material-icons-outlined mailpn-vertical-align-middle">bolt</i>
              <?php esc_html_e('Active', 'mailpn'); ?>
            </span>
            <?php if ($sent_count > 0): ?>
              <span class="mailpn-column-status-detail"><?php echo esc_html($send_count_label); ?></span>
            <?php else: ?>
              <span class="mailpn-column-status-detail"><?php esc_html_e('Waiting for trigger', 'mailpn'); ?></span>
            <?php endif; ?>
            <?php
          } elseif ($mail_type === 'email_one_time' && $sent_count > 0) {
            // One-time already sent (status meta may have been lost)
            ?>
            <span class="mailpn-column-status-badge mailpn-column-status-sent mailpn-status-clickable" data-post-id="<?php echo esc_attr($post_id); ?>">
              <i class="material-icons-outlined mailpn-vertical-align-middle">check_circle</i>
              <?php esc_html_e('Sent', 'mailpn'); ?>
            </span>
            <span class="mailpn-column-status-detail"><?php echo esc_html(sprintf(__('%d recipients', 'mailpn'), $sent_count)); ?></span>
            <?php
          } elseif ($mail_type === 'email_one_time') {
            // One-time ready to send
            ?>
            <span class="mailpn-column-status-badge mailpn-column-status-ready mailpn-status-clickable" data-post-id="<?php echo esc_attr($post_id); ?>">
              <i class="material-icons-outlined mailpn-vertical-align-middle">pending</i>
              <?php esc_html_e('Ready', 'mailpn'); ?>
            </span>
            <?php
          } else {
            // Other published types
            ?>
            <span class="mailpn-column-status-badge mailpn-column-status-active mailpn-status-clickable" data-post-id="<?php echo esc_attr($post_id); ?>">
              <i class="material-icons-outlined mailpn-vertical-align-middle">check_circle</i>
              <?php esc_html_e('Active', 'mailpn'); ?>
            </span>
            <?php if ($sent_count > 0): ?>
              <span class="mailpn-column-status-detail"><?php echo esc_html($send_count_label); ?></span>
            <?php endif; ?>
            <?php
          }

        } else {
          // WordPress draft
          ?>
          <span class="mailpn-column-status-badge mailpn-column-status-draft">
            <i class="material-icons-outlined mailpn-vertical-align-middle">drafts</i>
            <?php esc_html_e('Draft', 'mailpn'); ?>
          </span>
          <?php
        }
        break;
    }
  }

  /**
   * Handle form save action for mail post type.
   *
   * @param int $element_id The element ID (post ID or user ID)
   * @param array $key_value The form data
   * @param string $mailpn_form_type The form type (post, user, option)
   * @param string $mailpn_form_subtype The form subtype
   * @since 1.0.0
   */
  public function mailpn_form_save($element_id, $key_value, $mailpn_form_type, $mailpn_form_subtype)
  {
    $post_type = !empty(get_post_type($element_id)) ? get_post_type($element_id) : 'mailpn_mail';

    if ($post_type == 'mailpn_mail') {
      switch ($mailpn_form_type) {
        case 'post':
          switch ($mailpn_form_subtype) {
            case 'post_new':
              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  if (strpos($key, 'mailpn_') !== false) {
                    ${$key} = $value;
                    delete_post_meta($element_id, $key);
                  }
                }
              }
              break;
            case 'post_edit':
              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  if (strpos($key, 'mailpn_') !== false) {
                    ${$key} = $value;
                    delete_post_meta($element_id, $key);
                  }
                }
              }
              break;
          }
          break;
      }
    }
  }

  /**
   * Build the schedule preview HTML for the meta box.
   *
   * @param int $post_id The mail template post ID.
   * @return string HTML content.
   */
  public static function mailpn_get_schedule_preview_html($post_id) {
    if (empty($post_id) || get_post_type($post_id) !== 'mailpn_mail') {
      return '';
    }

    $mail_type       = get_post_meta($post_id, 'mailpn_type', true);
    $mailpn_status   = get_post_meta($post_id, 'mailpn_status', true);
    $wp_post_status  = get_post_status($post_id);
    $timestamps_sent = get_post_meta($post_id, 'mailpn_timestamp_sent', true);
    $last_sent       = !empty($timestamps_sent) && is_array($timestamps_sent) ? end($timestamps_sent) : 0;

    // Count sent emails
    $sent_count = count(get_posts([
      'fields'      => 'ids',
      'numberposts' => -1,
      'post_type'   => 'mailpn_rec',
      'post_status' => ['any'],
      'meta_key'    => 'mailpn_rec_mail_id',
      'meta_value'  => $post_id,
    ]));

    // Queue info
    $queue_data     = get_option('mailpn_queue');
    $queue_pending  = !empty($queue_data[$post_id]) ? count($queue_data[$post_id]) : 0;

    ob_start();
    ?>
    <div class="mailpn-schedule-preview">
      <h4 style="margin:20px 0 10px;display:flex;align-items:center;gap:6px;">
        <i class="material-icons-outlined" style="font-size:20px;">insights</i>
        <?php esc_html_e('Send overview', 'mailpn'); ?>
      </h4>
      <div class="mailpn-schedule-preview-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:16px;">
        <div class="mailpn-sp-card" style="background:#f0f6fc;border-radius:8px;padding:12px;text-align:center;">
          <div style="font-size:24px;font-weight:600;color:#2271b1;"><?php echo esc_html($sent_count); ?></div>
          <div style="font-size:12px;color:#50575e;"><?php esc_html_e('Emails sent', 'mailpn'); ?></div>
        </div>
        <?php if ($queue_pending > 0): ?>
        <div class="mailpn-sp-card" style="background:#fef8ee;border-radius:8px;padding:12px;text-align:center;">
          <div style="font-size:24px;font-weight:600;color:#dba617;"><?php echo esc_html($queue_pending); ?></div>
          <div style="font-size:12px;color:#50575e;"><?php esc_html_e('In queue', 'mailpn'); ?></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($last_sent)): ?>
        <div class="mailpn-sp-card" style="background:#f0faf0;border-radius:8px;padding:12px;text-align:center;">
          <div style="font-size:14px;font-weight:600;color:#00a32a;"><?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', $last_sent)); ?></div>
          <div style="font-size:12px;color:#50575e;"><?php esc_html_e('Last sent', 'mailpn'); ?></div>
        </div>
        <?php endif; ?>
      </div>

      <?php
      // Upcoming sends for periodic emails
      if ($mail_type === 'email_periodic' && $wp_post_status === 'publish') {
        $period     = get_post_meta($post_id, 'mailpn_periodic_period', true);
        $interval   = !empty($period) ? MAILPN_Cron::mailpn_periodic_interval_seconds_static($period) : 0;
        $period_labels = [
          'hourly'  => __('Each hour', 'mailpn'),
          'daily'   => __('Each day', 'mailpn'),
          'weekly'  => __('Each week', 'mailpn'),
          'monthly' => __('Each month', 'mailpn'),
          'yearly'  => __('Each year', 'mailpn'),
        ];
        $period_label = isset($period_labels[$period]) ? $period_labels[$period] : $period;

        if ($interval > 0) {
          $base_time = !empty($last_sent) ? $last_sent : current_time('timestamp');
          $now       = current_time('timestamp');
          // Find the next occurrence after now
          $next = $base_time + $interval;
          while ($next < $now) {
            $next += $interval;
          }
          ?>
          <h4 style="margin:12px 0 8px;display:flex;align-items:center;gap:6px;">
            <i class="material-icons-outlined" style="font-size:20px;">event_repeat</i>
            <?php esc_html_e('Upcoming scheduled sends', 'mailpn'); ?>
            <span style="font-weight:400;font-size:13px;color:#787c82;margin-left:4px;">(<?php echo esc_html($period_label); ?>)</span>
          </h4>
          <table class="mailpn-emails-table" style="width:100%;margin-bottom:10px;">
            <thead>
              <tr>
                <th style="text-align:left;padding:6px 10px;">#</th>
                <th style="text-align:left;padding:6px 10px;"><?php esc_html_e('Scheduled date', 'mailpn'); ?></th>
                <th style="text-align:left;padding:6px 10px;"><?php esc_html_e('Time remaining', 'mailpn'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i = 0; $i < 5; $i++):
                $send_time = $next + ($i * $interval);
                $diff      = $send_time - $now;
                if ($diff < HOUR_IN_SECONDS) {
                  $remaining = sprintf(__('%d min', 'mailpn'), max(1, intval($diff / 60)));
                } elseif ($diff < DAY_IN_SECONDS) {
                  $remaining = sprintf(__('%d hours', 'mailpn'), intval($diff / HOUR_IN_SECONDS));
                } else {
                  $remaining = sprintf(__('%d days', 'mailpn'), intval($diff / DAY_IN_SECONDS));
                }
              ?>
              <tr>
                <td style="padding:6px 10px;"><?php echo esc_html($i + 1); ?></td>
                <td style="padding:6px 10px;">
                  <i class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">schedule</i>
                  <?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', $send_time)); ?>
                </td>
                <td style="padding:6px 10px;"><?php echo esc_html($remaining); ?></td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
          <?php
        }
      }

      // Send history
      if (!empty($timestamps_sent) && is_array($timestamps_sent) && count($timestamps_sent) > 0) {
        ?>
        <h4 style="margin:12px 0 8px;display:flex;align-items:center;gap:6px;">
          <i class="material-icons-outlined" style="font-size:20px;">history</i>
          <?php esc_html_e('Send history', 'mailpn'); ?>
        </h4>
        <table class="mailpn-emails-table" style="width:100%;margin-bottom:10px;">
          <thead>
            <tr>
              <th style="text-align:left;padding:6px 10px;">#</th>
              <th style="text-align:left;padding:6px 10px;"><?php esc_html_e('Date', 'mailpn'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $reversed = array_reverse($timestamps_sent);
            $shown = array_slice($reversed, 0, 10);
            foreach ($shown as $idx => $ts): ?>
            <tr>
              <td style="padding:6px 10px;"><?php echo esc_html(count($timestamps_sent) - $idx); ?></td>
              <td style="padding:6px 10px;">
                <i class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">check_circle</i>
                <?php echo esc_html(date_i18n(get_option('date_format') . ' H:i:s', $ts)); ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (count($timestamps_sent) > 10): ?>
            <tr>
              <td colspan="2" style="padding:6px 10px;color:#787c82;font-style:italic;">
                <?php echo esc_html(sprintf(__('... and %d more', 'mailpn'), count($timestamps_sent) - 10)); ?>
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
        <?php
      }
      ?>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Build schedule detail HTML for the status popup (AJAX).
   *
   * @param int $post_id The mail template post ID.
   * @return string HTML content.
   */
  public static function mailpn_get_status_popup_html($post_id) {
    if (empty($post_id) || get_post_type($post_id) !== 'mailpn_mail') {
      return '<p>' . esc_html__('Invalid email template.', 'mailpn') . '</p>';
    }

    $mail_type       = get_post_meta($post_id, 'mailpn_type', true);
    $mailpn_status   = get_post_meta($post_id, 'mailpn_status', true);
    $wp_post_status  = get_post_status($post_id);
    $timestamps_sent = get_post_meta($post_id, 'mailpn_timestamp_sent', true);
    $last_sent       = !empty($timestamps_sent) && is_array($timestamps_sent) ? end($timestamps_sent) : 0;
    $mail_types      = MAILPN_Data::mailpn_mail_types();
    $type_label      = isset($mail_types[$mail_type]) ? $mail_types[$mail_type] : $mail_type;

    $sent_count = count(get_posts([
      'fields'      => 'ids',
      'numberposts' => -1,
      'post_type'   => 'mailpn_rec',
      'post_status' => ['any'],
      'meta_key'    => 'mailpn_rec_mail_id',
      'meta_value'  => $post_id,
    ]));

    $queue_data    = get_option('mailpn_queue');
    $queue_pending = !empty($queue_data[$post_id]) ? count($queue_data[$post_id]) : 0;

    ob_start();
    ?>
    <div class="mailpn-status-popup-inner" style="min-width:380px;">
      <h3 style="margin:0 0 16px;display:flex;align-items:center;gap:8px;">
        <i class="material-icons-outlined">mail</i>
        <?php echo esc_html(get_the_title($post_id)); ?>
      </h3>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
        <div style="background:#f0f6fc;border-radius:8px;padding:10px;text-align:center;">
          <div style="font-size:11px;color:#50575e;text-transform:uppercase;"><?php esc_html_e('Type', 'mailpn'); ?></div>
          <div style="font-size:14px;font-weight:600;color:#2271b1;"><?php echo esc_html($type_label); ?></div>
        </div>
        <div style="background:#f0f6fc;border-radius:8px;padding:10px;text-align:center;">
          <div style="font-size:11px;color:#50575e;text-transform:uppercase;"><?php esc_html_e('Total sent', 'mailpn'); ?></div>
          <div style="font-size:22px;font-weight:600;color:#2271b1;"><?php echo esc_html($sent_count); ?></div>
        </div>
      </div>

      <?php if ($queue_pending > 0): ?>
      <div style="background:#fef8ee;border-radius:8px;padding:10px;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
        <i class="material-icons-outlined" style="color:#dba617;">hourglass_top</i>
        <span><?php echo esc_html(sprintf(__('%d emails pending in queue', 'mailpn'), $queue_pending)); ?></span>
      </div>
      <?php endif; ?>

      <?php if (!empty($last_sent)): ?>
      <div style="margin-bottom:12px;color:#50575e;">
        <i class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">schedule</i>
        <?php echo esc_html(sprintf(__('Last sent: %s', 'mailpn'), date_i18n(get_option('date_format') . ' H:i', $last_sent))); ?>
      </div>
      <?php endif; ?>

      <?php
      // Upcoming sends for periodic emails
      if ($mail_type === 'email_periodic' && $wp_post_status === 'publish') {
        $period   = get_post_meta($post_id, 'mailpn_periodic_period', true);
        $interval = !empty($period) ? MAILPN_Cron::mailpn_periodic_interval_seconds_static($period) : 0;
        $period_labels = [
          'hourly'  => __('Each hour', 'mailpn'),
          'daily'   => __('Each day', 'mailpn'),
          'weekly'  => __('Each week', 'mailpn'),
          'monthly' => __('Each month', 'mailpn'),
          'yearly'  => __('Each year', 'mailpn'),
        ];
        $period_label = isset($period_labels[$period]) ? $period_labels[$period] : $period;

        if ($interval > 0) {
          $base_time = !empty($last_sent) ? $last_sent : current_time('timestamp');
          $now       = current_time('timestamp');
          $next      = $base_time + $interval;
          while ($next < $now) {
            $next += $interval;
          }
          ?>
          <h4 style="margin:12px 0 8px;font-size:14px;display:flex;align-items:center;gap:6px;">
            <i class="material-icons-outlined" style="font-size:18px;">event_repeat</i>
            <?php esc_html_e('Upcoming sends', 'mailpn'); ?>
            <span style="font-weight:400;color:#787c82;">(<?php echo esc_html($period_label); ?>)</span>
          </h4>
          <table class="mailpn-emails-table" style="width:100%;">
            <tbody>
              <?php for ($i = 0; $i < 5; $i++):
                $send_time = $next + ($i * $interval);
                $diff = $send_time - $now;
                if ($diff < HOUR_IN_SECONDS) {
                  $remaining = sprintf(__('%d min', 'mailpn'), max(1, intval($diff / 60)));
                } elseif ($diff < DAY_IN_SECONDS) {
                  $remaining = sprintf(__('%d hours', 'mailpn'), intval($diff / HOUR_IN_SECONDS));
                } else {
                  $remaining = sprintf(__('%d days', 'mailpn'), intval($diff / DAY_IN_SECONDS));
                }
              ?>
              <tr>
                <td style="padding:5px 8px;">
                  <i class="material-icons-outlined" style="font-size:16px;vertical-align:middle;color:#2271b1;">schedule</i>
                  <?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', $send_time)); ?>
                </td>
                <td style="padding:5px 8px;color:#787c82;text-align:right;"><?php echo esc_html($remaining); ?></td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
          <?php
        }
      }

      // Send history
      if (!empty($timestamps_sent) && is_array($timestamps_sent) && count($timestamps_sent) > 0) {
        ?>
        <h4 style="margin:14px 0 8px;font-size:14px;display:flex;align-items:center;gap:6px;">
          <i class="material-icons-outlined" style="font-size:18px;">history</i>
          <?php esc_html_e('Send history', 'mailpn'); ?>
        </h4>
        <table class="mailpn-emails-table" style="width:100%;">
          <tbody>
            <?php
            $reversed = array_reverse($timestamps_sent);
            $shown = array_slice($reversed, 0, 10);
            foreach ($shown as $idx => $ts): ?>
            <tr>
              <td style="padding:5px 8px;">
                <i class="material-icons-outlined" style="font-size:16px;vertical-align:middle;color:#00a32a;">check_circle</i>
                <?php echo esc_html(date_i18n(get_option('date_format') . ' H:i:s', $ts)); ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (count($timestamps_sent) > 10): ?>
            <tr>
              <td style="padding:5px 8px;color:#787c82;font-style:italic;">
                <?php echo esc_html(sprintf(__('... and %d more', 'mailpn'), count($timestamps_sent) - 10)); ?>
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
        <?php
      }

      // Resend info
      if ($mail_type === 'email_periodic') {
        ?>
        <div style="margin-top:14px;padding:10px;background:#f0faf0;border-radius:8px;display:flex;align-items:center;gap:8px;">
          <i class="material-icons-outlined" style="color:#00a32a;">autorenew</i>
          <span style="font-size:13px;"><?php esc_html_e('This is a recurring email. It will be sent again automatically based on the configured period.', 'mailpn'); ?></span>
        </div>
        <?php
      } elseif (in_array($mail_type, ['email_welcome', 'newsletter_welcome', 'email_published_content', 'email_coded', 'email_woocommerce_purchase', 'email_woocommerce_abandoned_cart'])) {
        ?>
        <div style="margin-top:14px;padding:10px;background:#f0f6fc;border-radius:8px;display:flex;align-items:center;gap:8px;">
          <i class="material-icons-outlined" style="color:#2271b1;">bolt</i>
          <span style="font-size:13px;"><?php esc_html_e('This email is triggered automatically by events (new users, purchases, etc.).', 'mailpn'); ?></span>
        </div>
        <?php
      } elseif ($mail_type === 'email_one_time' && $mailpn_status === 'sent') {
        ?>
        <div style="margin-top:14px;padding:10px;background:#f0f6fc;border-radius:8px;display:flex;align-items:center;gap:8px;">
          <i class="material-icons-outlined" style="color:#2271b1;">info</i>
          <span style="font-size:13px;"><?php esc_html_e('This is a one-time email. Use "Resend All" from the tools to send it again.', 'mailpn'); ?></span>
        </div>
        <?php
      }
      ?>
    </div>
    <?php
    return ob_get_clean();
  }
}