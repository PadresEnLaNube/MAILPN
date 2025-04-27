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
class MAILPN_Post_Type_Mail {
  public function mailpn_get_fields_meta() {
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
    foreach (get_users(['fields' => 'ids', 'number' => -1, 'orderby' => 'display_name', 'order' => 'ASC', ]) as $user_id) {
      $mailpn_user_options[$user_id] = get_user_by('id', $user_id)->user_email . ' (ID#' . $user_id . ') ' . get_user_meta($user_id, 'first_name', true) . ' ' . get_user_meta($user_id, 'last_name', true);
    }

    $mailpn_attachments_options = [];
    foreach (get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'attachment', 'orderby' => 'display_name', 'order' => 'ASC', ]) as $attachment_id) {
      $mailpn_attachments_options[$attachment_id] = get_the_title($attachment_id) . ' (ID#' . get_post($attachment_id)->ID . ')';
    }

    $mailpn_fields_meta = [];
    $mailpn_fields_meta['mailpn_tools'] = [
      'id' => 'mailpn_tools',
      'input' => 'html',
      'html_content' => '[mailpn-tools post_id="' . $post_id . '"]',
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
      $mailpn_fields_meta['mailpn_periodic_period'] = [
        'id' => 'mailpn_periodic_period',
        'class' => 'mailpn-select mailpn-width-100-percent',
        'input' => 'select',
        'options' => ['hourly' => __('Each hour', 'mailpn'), 'daily' => __('Each day', 'mailpn'), 'weekly' => __('Each week', 'mailpn'), 'monthly' => __('Each month', 'mailpn'), 'yearly' => __('Each year', 'mailpn'), ],
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
        'options' => ['email_published_content_new' => __('Each new content', 'mailpn'), 'email_published_content_period' => __('From time to time', 'mailpn'), 'email_published_content_date' => __('In a specific date', 'mailpn'), ],
        'parent' => 'this mailpn_type',
        'parent_option' => 'email_published_content',
        'label' => __('When will it be sent?', 'mailpn'),
        'placeholder' => __('When will it be sent?', 'mailpn'),
      ];
        $mailpn_fields_meta['mailpn_updated_content_period'] = [
          'id' => 'mailpn_updated_content_period',
          'class' => 'mailpn-select mailpn-width-100-percent',
          'input' => 'select',
          'options' => ['hourly' => __('Each hour', 'mailpn'), 'daily' => __('Each day', 'mailpn'), 'weekly' => __('Each week', 'mailpn'), 'monthly' => __('Each month', 'mailpn'), 'yearly' => __('Each year', 'mailpn'), ],
          'parent' => 'mailpn_updated_content',
          'parent_option' => 'email_published_content_period',
          'label' => __('Sending period', 'mailpn'),
          'placeholder' => __('Sending period', 'mailpn'),
        ];
        $mailpn_fields_meta['mailpn_updated_content_date'] = [
          'id' => 'mailpn_updated_content_date',
          'class' => 'mailpn-select mailpn-width-100-percent',
          'input' => 'select',
          'options' => ['in_minute_on_hour' => __('In a specific minute each hour', 'mailpn'), 'in_hour_on_day' => __('In a specific hour each day', 'mailpn'), 'in_day_on_week' => __('In a specific day each week', 'mailpn'), 'in_day_on_month' => __('In a specific day each month', 'mailpn'), 'in_week_on_year' => __('In a specific week each year', 'mailpn'), 'in_month_on_year' => __('In a specific month each year', 'mailpn'), ],
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
    $mailpn_fields_meta['mailpn_distribution'] = [
      'id' => 'mailpn_distribution',
      'class' => 'mailpn-select mailpn-width-100-percent',
      'input' => 'select',
      'options' => 'checkbox',
      'options' => ['public' => __('Everybody will receive', 'mailpn'), 'private_role' => __('Only users with specific role will receive', 'mailpn'), 'private_user' => __('Only specific users will receive', 'mailpn'), ],
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
    $mailpn_fields_meta['ajax_nonce'] = [
      'id' => 'ajax_nonce',
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
      'name'                => _x('Mail templates', 'Post Type general name', 'mailpn'),
      'singular_name'       => _x('Mail template', 'Post Type singular name', 'mailpn'),
      'menu_name'           => esc_html(__('Mail templates', 'mailpn')),
      'parent_item_colon'   => esc_html(__('Parent Mail template', 'mailpn')),
      'all_items'           => esc_html(__('All Mail templates', 'mailpn')),
      'view_item'           => esc_html(__('View Mail template', 'mailpn')),
      'add_new_item'        => esc_html(__('Add new Mail template', 'mailpn')),
      'add_new'             => esc_html(__('Add new Mail template', 'mailpn')),
      'edit_item'           => esc_html(__('Edit Mail template', 'mailpn')),
      'update_item'         => esc_html(__('Update Mail template', 'mailpn')),
      'search_items'        => esc_html(__('Search Mail templates', 'mailpn')),
      'not_found'           => esc_html(__('Not Mail template found', 'mailpn')),
      'not_found_in_trash'  => esc_html(__('Not Mail template found in Trash', 'mailpn')),
    ];

    $args = [
      'labels'              => $labels,
      'rewrite'             => ['slug' => (!empty(get_option('mailpn')) ? get_option('mailpn') : 'mailpn'), 'with_front' => false],
      'label'               => esc_html(__('Mail template', 'mailpn')),
      'description'         => esc_html(__('Mail template description', 'mailpn')),
      'supports'            => ['title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'page-attributes', ],
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
      'taxonomies'          => MAILPN_ROLE_CAPABILITIES,
      'show_in_rest'        => true, /* REST API */
    ];

    register_post_type('mailpn_mail', $args);
    add_theme_support('post-thumbnails', ['page', 'mailpn_mail']);
  }

  /**
   * Add Mail dashboard metabox.
   *
   * @since    1.0.0
   */
  public function mailpn_add_meta_box() {
    add_meta_box('mailpn_meta_box', esc_html(__('Mail template details', 'mailpn')), [$this, 'mailpn_meta_box_function'], 'mailpn_mail', 'normal', 'high', ['__block_editor_compatible_meta_box' => true,]);
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

  public function mailpn_save_post($post_id, $cpt, $update) {
    if($cpt->post_type == 'mailpn_mail'){
      // Always require nonce verification
      if (!array_key_exists('ajax_nonce', $_POST)) {
        echo wp_json_encode([
          'error_key' => 'mailpn_nonce_error',
          'error_content' => esc_html(__('Security check failed: Nonce is required.', 'mailpn')),
        ]);

        exit();
      }

      if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ajax_nonce'])), 'mailpn-nonce')) {
        echo wp_json_encode([
          'error_key' => 'mailpn_nonce_error',
          'error_content' => esc_html(__('Security check failed: Invalid nonce.', 'mailpn')),
        ]);

        exit();
      }
      
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
      
      // ONE TIME
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
  }

  public function mailpn_form_save($element_id, $key_value, $mailpn_form_type, $mailpn_form_subtype) {
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
                    delete_post_meta($post_id, $key);
                  }
                }
              }

              $post_functions = new MAILPN_Functions_Post();
              $mail_id = $post_functions->insert_post(esc_html($mailpn_title), $mailpn_description, '', sanitize_title(esc_html($mailpn_title)), 'mailpn_mail', 'publish', get_current_user_id());

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
              wp_update_post(['ID' => $mail_id, 'post_title' => $mailpn_title, 'post_content' => $mailpn_description,]);

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

  public function save_post($post_id) {
    // If this is an autosave, our form has not been submitted
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Verify nonce
    if (!isset($_POST['mailpn_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mailpn_nonce'])), 'mailpn-nonce')) {
        return $post_id;
    }

    // Check the user's permissions
    $post_type = get_post_type($post_id);
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // Now safe to save data
    if (!empty($this->mailpn_fields)) {
        foreach ($this->mailpn_fields as $mailpn_field) {
            $mailpn_value = array_key_exists($mailpn_field['id'], $_POST) ? 
                MAILPN_Forms::mailpn_sanitizer($_POST[$mailpn_field['id']], $mailpn_field['input'], !empty($mailpn_field['type']) ? $mailpn_field['type'] : '') : '';
            update_post_meta($post_id, $mailpn_field['id'], $mailpn_value);
        }
    }
  }

  public function mailpn_mail_posts_columns($columns) {
    $new_columns = [];
    
    // Add title column first
    if (isset($columns['title'])) {
      $new_columns['title'] = $columns['title'];
    }
    
    // Add our custom column after title
    $new_columns['mailpn_mail_type'] = __('Mail Type', 'mailpn');
    
    // Add remaining columns
    foreach ($columns as $key => $value) {
      if ($key !== 'title') {
        $new_columns[$key] = $value;
      }
    }
    
    return $new_columns;
  }

  public function mailpn_mail_posts_custom_column($column_slug, $post_id) {
    switch ($column_slug) {
      case 'mailpn_mail_type':
        $mail_type = get_post_meta($post_id, 'mailpn_type', true);
        if ($mail_type) {
          ?>
            <p><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-mr-10">mark_email_read</i> <?php echo isset(MAILPN_Data::mailpn_mail_types()[$mail_type]) ? esc_html(MAILPN_Data::mailpn_mail_types()[$mail_type]) : esc_html($mail_type); ?></p>
          <?php
        } else {
          ?>
            <p><i class="material-icons-outlined mailpn-vertical-align-middle mailpn-font-size-20 mailpn-color-red mailpn-mr-10">mark_email_read</i> <?php esc_html_e('Unset email type.', 'mailpn'); ?></p>
          <?php
        }
        break;
    }
  }
}