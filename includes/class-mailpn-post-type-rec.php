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
class MAILPN_Post_Type_Rec {
  public function mailpn_get_fields_meta() {
    $mailpn_user_options = [];
    foreach (get_users(['fields' => 'ids', 'number' => -1, 'orderby' => 'display_name', 'order' => 'ASC', ]) as $user_id) {
      $mailpn_user_options[$user_id] = get_user_by('id', $user_id)->user_email . ' (ID#' . $user_id . ') ' . get_user_meta($user_id, 'first_name', true) . ' ' . get_user_meta($user_id, 'last_name', true);
    }

    $mailpn_attachments_options = [];
    foreach (get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'attachment', 'orderby' => 'display_name', 'order' => 'ASC', ]) as $attachment_id) {
      $mailpn_attachments_options[$attachment_id] = get_the_title($attachment_id) . ' (ID#' . get_post($attachment_id)->ID . ')';
    }

    $mailpn_fields_meta = [];
    $mailpn_fields_meta['mailpn_rec_type'] = [
      'id' => 'mailpn_rec_type',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'disabled' => 'true',
      'required' => 'true',
      'input' => 'select',
      'options' => MAILPN_Data::mailpn_mail_types(),
      'label' => __('Email type', 'mailpn'),
      'placeholder' => __('Email type', 'mailpn'),
    ];

    // New fields for additional email information
    $mailpn_fields_meta['mailpn_rec_subject'] = [
      'id' => 'mailpn_rec_subject',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'disabled' => 'true',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Email Subject', 'mailpn'),
      'placeholder' => __('Email subject line', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_rec_content_html'] = [
      'id' => 'mailpn_rec_content_html',
      'class' => 'mailpn-input mailpn-width-100-percent mailpn-height-300',
      'disabled' => 'true',
      'input' => 'textarea',
      'label' => __('Email HTML Content', 'mailpn'),
      'placeholder' => __('Email HTML content', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_rec_content_text'] = [
      'id' => 'mailpn_rec_content_text',
      'class' => 'mailpn-input mailpn-width-100-percent mailpn-height-300',
      'disabled' => 'true',
      'input' => 'textarea',
      'label' => __('Email Text Content', 'mailpn'),
      'placeholder' => __('Email plain text content', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_rec_headers'] = [
      'id' => 'mailpn_rec_headers',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'disabled' => 'true',
      'input' => 'textarea',
      'label' => __('Email Headers', 'mailpn'),
      'placeholder' => __('Email headers (From, Reply-To, CC, BCC)', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_rec_error'] = [
      'id' => 'mailpn_rec_error',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'disabled' => 'true',
      'input' => 'textarea',
      'label' => __('Error Message', 'mailpn'),
      'placeholder' => __('Error message if email failed to send', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_rec_server_ip'] = [
      'id' => 'mailpn_rec_server_ip',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'disabled' => 'true',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Server IP Address', 'mailpn'),
      'placeholder' => __('IP address of originating server', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_rec_sent_datetime'] = [
      'id' => 'mailpn_rec_sent_datetime',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'disabled' => 'true',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Date and Time Sent', 'mailpn'),
      'placeholder' => __('Date and time when email was sent', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_rec_to_email'] = [
      'id' => 'mailpn_rec_to_email',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'disabled' => 'true',
      'input' => 'input',
      'type' => 'email',
      'label' => __('Recipient Email', 'mailpn'),
      'placeholder' => __('Recipient email address', 'mailpn'),
    ];

    $mailpn_fields_meta['mailpn_rec_post_id'] = [
      'id' => 'mailpn_rec_post_id',
      'class' => 'mailpn-input mailpn-width-100-percent',
      'disabled' => 'true',
      'input' => 'input',
      'type' => 'number',
      'label' => __('Source post/page ID', 'mailpn'),
      'placeholder' => __('ID of the post/page where the email was sent from', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_rec_to'] = [
      'id' => 'mailpn_rec_to',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'disabled' => 'true',
      'input' => 'select',
      'options' => $mailpn_user_options,
      'label' => __('Mail addressee', 'mailpn'),
      'placeholder' => __('Mail addressee', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_rec_attachments'] = [
      'id' => 'mailpn_rec_attachments',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'disabled' => 'true',
      'multiple' => 'true',
      'input' => 'select',
      'options' => $mailpn_attachments_options,
      'label' => __('Mail attachments', 'mailpn'),
      'placeholder' => __('Mail attachments', 'mailpn'),
    ];
    $mailpn_fields_meta['mailpn_rec_content'] = [
      'id' => 'mailpn_rec_content',
      'class' => 'mailpn-input mailpn-width-100-percent mailpn-height-300',
      'disabled' => 'true',
      'input' => 'textarea',
      'label' => __('Mail HTML content', 'mailpn'),
      'placeholder' => __('Mail HTML content', 'mailpn'),
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
  public function mailpn_register_post_type() {
    $labels = [
      'name'                => _x('Mail records', 'Post Type general name', 'mailpn'),
      'singular_name'       => _x('Mail record', 'Post Type singular name', 'mailpn'),
      'menu_name'           => esc_html(__('Mail records', 'mailpn')),
      'parent_item_colon'   => esc_html(__('Parent Mail record', 'mailpn')),
      'all_items'           => esc_html(__('All Mail records', 'mailpn')),
      'view_item'           => esc_html(__('View Mail record', 'mailpn')),
      'add_new_item'        => esc_html(__('Add new Mail record', 'mailpn')),
      'add_new'             => esc_html(__('Add new Mail record', 'mailpn')),
      'edit_item'           => esc_html(__('Edit Mail record', 'mailpn')),
      'update_item'         => esc_html(__('Update Mail record', 'mailpn')),
      'search_items'        => esc_html(__('Search Mail records', 'mailpn')),
      'not_found'           => esc_html(__('Not Mail record found', 'mailpn')),
      'not_found_in_trash'  => esc_html(__('Not Mail record found in Trash', 'mailpn')),
    ];

    $args = [
      'labels'              => $labels,
      'rewrite'             => ['slug' => 'mail-record', 'with_front' => false],
      'label'               => esc_html(__('Mail record', 'mailpn')),
      'description'         => esc_html(__('Mail record description', 'mailpn')),
      'supports'            => ['title', 'editor', 'author', 'thumbnail', 'revisions', ],
      'hierarchical'        => false,
      'public'              => false,
      'show_ui'             => true,
      'show_in_menu'        => false,
      'show_in_nav_menus'   => false,
      'show_in_admin_bar'   => false,
      'can_export'          => false,
      'has_archive'         => false,
      'exclude_from_search' => true,
      'publicly_queryable'  => false,
      'capability_type'     => 'page',
      'taxonomies'          => ['mailpn_rec_category'],
      'show_in_rest'        => true,
    ];

    register_post_type('mailpn_rec', $args);
    add_theme_support('post-thumbnails', ['page', 'mailpn_rec']);
  }

  /**
   * Block REST API access for mailpn_rec post type while keeping Gutenberg editor
   *
   * @param WP_Error|null|bool $errors WP_Error if authentication error, null if authentication method wasn't used, true if authentication succeeded.
   * @return WP_Error|null|bool
   */
  public function mailpn_rec_block_rest_api_access($errors) {
    // Get the current REST route
    $current_route = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
    
    // Check if this is a request to the mailpn_rec endpoint
    if (strpos($current_route, '/wp-json/wp/v2/mailpn_rec') !== false) {
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
  public function mailpn_add_meta_box() {
    add_meta_box('mailpn_meta_box', esc_html(__('Mail details', 'mailpn')), [$this, 'mailpn_meta_box_function'], 'mailpn_rec', 'normal', 'high', ['__block_editor_compatible_meta_box' => true,]);
  }

  /**
   * Defines Mail dashboard contents.
   *
   * @since    1.0.0
   */
  public function mailpn_meta_box_function($post) {
    foreach ($this->mailpn_get_fields_meta() as $mailpn_field) {
      MAILPN_Forms::mailpn_input_wrapper_builder($mailpn_field, 'post', $post->ID);
    }
  }

  /**
   * Save post metadata when a post is saved.
   *
   * @param int $post_id The post ID.
   * @return int|void
   */
  public function mailpn_save_post($post_id, $cpt, $update) {
    if($cpt->post_type == 'mailpn_rec' && array_key_exists('mailpn_rec_type', $_POST)){
      // Always require nonce verification
      if (!array_key_exists('mailpn_ajax_nonce', $_POST)) {
        echo wp_json_encode([
          'error_key' => 'mailpn_rec_nonce_error_required',
          'error_content' => esc_html(__('Security check failed: Nonce is required.', 'mailpn')),
        ]);

        exit();
      }

      if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mailpn_ajax_nonce'])), 'mailpn-nonce')) {
        echo wp_json_encode([
          'error_key' => 'mailpn_rec_nonce_error_invalid',
          'error_content' => esc_html(__('Security check failed: Invalid nonce.', 'mailpn')),
        ]);

        exit();
      }

      // Check the user's permissions
      if (!current_user_can('edit_post', $post_id)) {
          echo wp_json_encode([
            'error_key' => 'mailpn_permission_error',
            'error_content' => esc_html(__('You are not allowed to edit this post.', 'mailpn')),
          ]);
  
          exit();
      }

      // Now safe to save data
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
                    }else{
                      update_post_meta($post_id, $mailpn_field['id'], '');
                    }
                  }else{
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
                  }else{
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
                      }else{
                        update_post_meta($post_id, $mailpn_multi_field['id'], '');
                      }
                    }
                  }

                  break;
                default:
                  update_post_meta($post_id, $mailpn_field['id'], $mailpn_value);
                  break;
              }
            }
          }else{
            update_post_meta($post_id, $mailpn_field['id'], '');
          }
        }
      }
    }
  }

  public function mailpn_form_save($element_id, $key_value, $mailpn_form_type, $mailpn_form_subtype) {
    $post_type = !empty(get_post_type($element_id)) ? get_post_type($element_id) : 'mailpn_rec';

    if ($post_type == 'mailpn_rec') {
      switch ($mailpn_form_type) {
        case 'post':
          switch ($mailpn_form_subtype) {
            case 'post_new':
              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  if (strpos($key, 'mailpn_') !== false) {
                    ${$key} = $value;
                    delete_post_meta($post_id, $key);
                  }
                }
              }

              $post_functions = new MAILPN_Functions_Post();
              $mail_id = $post_functions->mailpn_insert_post(esc_html($rec_), $mailpn_description, '', sanitize_title(esc_html($rec_)), 'mailpn_rec', 'publish', get_current_user_id());

              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  update_post_meta($mail_id, $key, $value);
                }
              }

              break;
            case 'post_edit':
              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  if (strpos($key, 'mailpn_') !== false) {
                    ${$key} = $value;
                    delete_post_meta($post_id, $key);
                  }
                }
              }

              $mail_id = $element_id;
              wp_update_post(['ID' => $mail_id, 'post_title' => $rec_, 'post_content' => $mailpn_description,]);

              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  update_post_meta($mail_id, $key, $value);
                }
              }

              break;
            case 'post_check':
              self::mailpn_history_add($element_id);
              break;
            case 'post_uncheck':
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
      }
    }
  }

  public function mailpn_rec_posts_columns($columns) {
    unset($columns['author']);
    unset($columns['date']);

    $columns['mailpn_rec_date_sent'] = __('Date sent', 'mailpn');
    $columns['mailpn_rec_mail_template'] = __('Mail Template', 'mailpn');
    $columns['mailpn_rec_to'] = __('Recipient', 'mailpn');
    $columns['mailpn_rec_mail_result'] = __('Result', 'mailpn');
    $columns['mailpn_rec_opened'] = __('Opened', 'mailpn');

    return $columns;
  }

  public function mailpn_rec_posts_custom_column($column_slug, $post_id) {
    switch ($column_slug) {
      case 'mailpn_rec_mail_template':
        $mail_id = get_post_meta($post_id, 'mailpn_rec_mail_id', true);
        $mail_type = get_post_meta($post_id, 'mailpn_rec_type', true);

        ?>
          <?php if ($mail_type): ?>
            <p><a href="<?php echo esc_url(admin_url('post.php?post=' . $mail_id . '&action=edit')); ?>" class="mailpn-color-main-0 mailpn-font-weight-bold mailpn-mr-10" target="_blank"><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-mr-10">mark_email_read</i> #<?php echo esc_html($mail_id) ?> <?php echo isset(MAILPN_Data::mailpn_mail_types()[$mail_type]) ? esc_html(MAILPN_Data::mailpn_mail_types()[$mail_type]) : esc_html($mail_type); ?></a></p>
          <?php else: ?>
            <p><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-red mailpn-mr-10">mark_email_read</i> <?php esc_html_e('Unset email type.', 'mailpn'); ?></p>
          <?php endif ?>
        <?php
        break;
      case 'mailpn_rec_to':
        $user_id = get_post_meta($post_id, 'mailpn_rec_to', true);

        if (get_userdata($user_id) !== false) {
          $user_info = get_userdata($user_id);
          ?>
            <p><a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $user_id)); ?>" class="mailpn-color-main-0 mailpn-font-weight-bold mailpn-mr-10" target="_blank"><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-main-0">person</i> #<?php echo esc_html($user_id); ?> <?php echo esc_html($user_info->first_name) . ' ' . esc_html($user_info->last_name); ?></a> (<a href="mailto:<?php echo esc_html($user_info->user_email); ?>" target="_blank"><?php echo esc_html($user_info->user_email); ?></a>)</p>
          <?php
        }else{
          ?>
            <p><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-red mailpn-mr-10">person_off</i> <?php esc_html_e('User removed or email sent by email address.', 'mailpn'); ?></p>
          <?php
        }
        break;
      case 'mailpn_rec_date_sent':
        $date_sent = strtotime(get_post($post_id)->post_date);
        ?>
          <p><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-main-0 mailpn-mr-10">calendar_today</i> <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $date_sent)); ?></p>
        <?php
        break;
      case 'mailpn_rec_mail_result':
        $mailpn_rec_mail_result = get_post_meta($post_id, 'mailpn_rec_mail_result', true);
        $mailpn_rec_post_id = get_post_meta($post_id, 'mailpn_rec_post_id', true);
        ?>
          <?php if ($mailpn_rec_mail_result): ?>
            <p><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-green mailpn-mr-10">check</i> <?php esc_html_e('Successfully sent.', 'mailpn'); ?></p>
            
            <?php if (!empty($mailpn_rec_post_id) && get_post($mailpn_rec_post_id)): ?>
              <small><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-main-0 mailpn-mr-10">article</i> <?php esc_html_e('Sent from:', 'mailpn'); ?> <a href="<?php echo esc_url(get_edit_post_link($mailpn_rec_post_id)); ?>" class="mailpn-color-main-0 mailpn-font-weight-bold" target="_blank">#<?php echo esc_html($mailpn_rec_post_id); ?> <?php echo esc_html(get_the_title($mailpn_rec_post_id)); ?></a></small>
            <?php endif ?>
          <?php else: ?>
            <p><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-red mailpn-mr-10">block</i> <?php esc_html_e('Email not sent. Errors have been found.', 'mailpn'); ?></p>
          <?php endif ?>
        <?php
        break;
      case 'mailpn_rec_opened':
        $opened = get_post_meta($post_id, 'mailpn_rec_opened', true);
        $opened_at = get_post_meta($post_id, 'mailpn_rec_opened_at', true);
        $clicks = get_post_meta($post_id, 'mailpn_rec_clicks', true);
        $mail_id = get_post_meta($post_id, 'mailpn_rec_mail_id', true);
        ?>
          <?php if ($opened): ?>
            <p>
              <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-green mailpn-mr-10">visibility</i>
              <?php esc_html_e('Opened', 'mailpn'); ?>
              <?php if ($opened_at): ?>
                <br>
                <small>
                  <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($opened_at))); ?>
                </small>
              <?php endif; ?>
            </p>

            <?php if (is_array($clicks) && !empty($clicks)): ?>
              <a href="#" class="mailpn-popup-open" data-mailpn-popup-id="mailpn-click-stats-<?php echo esc_attr($post_id); ?>">
                  <i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-16 mailpn-mr-5">link</i>
                  <?php 
                    $total_clicks = count($clicks);
                    
                    printf(
                      _n('%d click', '%d clicks', $total_clicks, 'mailpn'),
                      $total_clicks
                    ); 
                  ?>
              </a>
              
              <div class="mailpn-popup mailpn-popup-size-medium" id="mailpn-click-stats-<?php echo esc_attr($post_id); ?>">
                <div class="mailpn-popup-content">
                  <div class="mailpn-p-30">
                    <h3><?php esc_html_e('Click Statistics', 'mailpn'); ?></h3>
                  
                    <div class="mailpn-click-stats-data">
                      <?php
                        $unique_urls = [];
                        $clicks_by_url = [];
                        foreach ($clicks as $click) {
                          $url = $click['url'];
                          if (!isset($clicks_by_url[$url])) {
                            $clicks_by_url[$url] = 0;
                            $unique_urls[] = $url;
                          }
                          $clicks_by_url[$url]++;
                        }
                      ?>

                      <p><strong><?php esc_html_e('Total Clicks:', 'mailpn'); ?></strong> <?php echo esc_html($total_clicks); ?></p>
                      <p><strong><?php esc_html_e('Unique URLs:', 'mailpn'); ?></strong> <?php echo esc_html(count($unique_urls)); ?></p>
                      
                      <?php if (!empty($clicks_by_url)): ?>
                        <table class="widefat">
                          <thead>
                            <tr>
                              <th><?php esc_html_e('URL', 'mailpn'); ?></th>
                              <th><?php esc_html_e('Clicks', 'mailpn'); ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($clicks_by_url as $url => $count): ?>
                              <tr>
                                <td><?php echo esc_html($url); ?></td>
                                <td><?php echo esc_html($count); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php else: ?>
            <p><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-red mailpn-mr-10">visibility_off</i> <?php esc_html_e('Not opened', 'mailpn'); ?></p>
          <?php endif; ?>
        <?php
        break;
    }
  }

  public function mailpn_rec_filter_dropdown() {
    global $typenow;
    if ($typenow == 'mailpn_rec') {
      // Mail Type Filter
      $selected_type = isset($_GET['mailpn_type_filter']) ? sanitize_text_field($_GET['mailpn_type_filter']) : '';
      ?>
        <select name="mailpn_type_filter">
          <option value=""><?php echo esc_html__('All mail types', 'mailpn'); ?></option>
          <?php foreach (MAILPN_Data::mailpn_mail_types() as $type_key => $type_label): ?>
            <option value="<?php echo esc_attr($type_key); ?>" <?php echo selected((string)$selected_type, (string)$type_key, false); ?>>
              <?php echo esc_html($type_label); ?>
            </option>
          <?php endforeach; ?>
        </select>
        
      <?php
        // Mail Template Filter
        $selected_template = isset($_GET['mailpn_template_filter']) ? sanitize_text_field($_GET['mailpn_template_filter']) : '';
        
        $args = [
          'fields' => 'ids',
          'post_type' => 'mailpn_mail',
          'posts_per_page' => -1,
          'orderby' => 'ID',
          'order' => 'ASC',
          'post_status' => 'publish',
        ];

        if (class_exists('Polylang')) {
          $args['lang'] = pll_current_language('slug');
        }
        
        $templates = get_posts($args);
      ?>
        <select name="mailpn_template_filter">
          <option value=""><?php echo esc_html__('All mail templates', 'mailpn'); ?></option>
          
          <?php if (!empty($templates)) { ?>
            <?php foreach ($templates as $template): ?>
              <?php $template = get_post($template); ?>
              <option value="<?php echo esc_attr($template->ID); ?>" <?php selected((string)$selected_template, (string)$template->ID, false); ?>>
                #<?php echo esc_html($template->ID); ?> <?php echo esc_html($template->post_title); ?>
              </option>
            <?php endforeach; ?>
          <?php } else { ?> 
            <?php error_log('No mail templates found'); ?>
          <?php } ?>
        </select>
        
      <?php
        // Recipient Filter
        $selected_recipient = isset($_GET['mailpn_recipient_filter']) ? sanitize_text_field($_GET['mailpn_recipient_filter']) : '';
        
        $recipients = get_users([
          'fields' => ['ID', 'user_email'],
          'orderby' => 'ID',
          'order' => 'ASC'
        ]);
      ?>
        <select name="mailpn_recipient_filter">
          <option value=""><?php echo esc_html__('All users', 'mailpn'); ?></option>

          <?php foreach ($recipients as $recipient): 
            $first_name = get_user_meta($recipient->ID, 'first_name', true);
            $last_name = get_user_meta($recipient->ID, 'last_name', true);
          ?>
            <option value="<?php echo esc_attr($recipient->ID); ?>" <?php echo selected((string)$selected_recipient, (string)$recipient->ID, false); ?>>
              #<?php echo esc_html($recipient->ID); ?> <?php echo esc_html($first_name); ?> <?php echo esc_html($last_name); ?> (<?php echo esc_html($recipient->user_email); ?>)
            </option>
          <?php endforeach; ?>
        </select>
      <?php
    }
  }

  public function mailpn_rec_filter_query($query) {
    global $pagenow, $typenow;
    
    if (is_admin() && $pagenow == 'edit.php' && $typenow == 'mailpn_rec') {
      $meta_query = [];

      // Filter by recipient if set and not empty
      if (isset($_GET['mailpn_recipient_filter']) && $_GET['mailpn_recipient_filter'] !== '') {
        $meta_query[] = [
          'key' => 'mailpn_rec_to',
          'value' => sanitize_text_field($_GET['mailpn_recipient_filter'])
        ];
      }

      // Filter by mail type if set and not empty
      if (isset($_GET['mailpn_type_filter']) && $_GET['mailpn_type_filter'] !== '') {
        $meta_query[] = [
          'key' => 'mailpn_rec_type',
          'value' => sanitize_text_field($_GET['mailpn_type_filter'])
        ];
      }

      // Filter by mail template if set and not empty
      if (isset($_GET['mailpn_template_filter']) && $_GET['mailpn_template_filter'] !== '') {
        $meta_query[] = [
          'key' => 'mailpn_rec_mail_id',
          'value' => sanitize_text_field($_GET['mailpn_template_filter']),
          'compare' => '='
        ];
      }

      // Apply meta query if we have any filters
      if (!empty($meta_query)) {
        $meta_query['relation'] = 'AND';
        $query->set('meta_query', $meta_query);
      }

      // Debug output
      error_log('Meta Query: ' . print_r($meta_query, true));
    }
  }
}