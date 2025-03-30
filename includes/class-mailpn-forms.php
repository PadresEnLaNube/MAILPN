<?php
/**
 * Fired from activate() function.
 *
 * This class defines all post types necessary to run during the plugin's life cycle.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Forms {
	/**
	 * Plaform forms.
	 *
	 * @since    1.0.0
	 */

  public static function input_builder($mailpn_input, $mailpn_type, $mailpn_id = 0, $disabled = 0, $mailpn_meta_array = 0, $mailpn_array_index = 0) {
    // MAILPN_Forms::input_builder($mailpn_input, $mailpn_type, $mailpn_id = 0, $disabled = 0, $mailpn_meta_array = 0, $mailpn_array_index = 0)
    if ($mailpn_meta_array) {
      switch ($mailpn_type) {
        case 'user':
          $user_meta = get_user_meta($mailpn_id, $mailpn_input['id'], true);

          if (is_array($user_meta) && array_key_exists($mailpn_array_index, $user_meta) && !empty($user_meta[$mailpn_array_index])) {
            $mailpn_value = $user_meta[$mailpn_array_index];
          }else{
            if (array_key_exists('value', $mailpn_input)) {
              $mailpn_value = $mailpn_input['value'];
            }else{
              $mailpn_value = '';
            }
          }
          break;
        case 'post':
          $post_meta = get_post_meta($mailpn_id, $mailpn_input['id'], true);

          if (is_array($post_meta) && array_key_exists($mailpn_array_index, $post_meta) && !empty($post_meta[$mailpn_array_index])) {
            $mailpn_value = $post_meta[$mailpn_array_index];
          }else{
            if (array_key_exists('value', $mailpn_input)) {
              $mailpn_value = $mailpn_input['value'];
            }else{
              $mailpn_value = '';
            }
          }
          break;
        case 'option':
          $option = get_option($mailpn_input['id']);

          if (is_array($option) && array_key_exists($mailpn_array_index, $option) && !empty($option[$mailpn_array_index])) {
            $mailpn_value = $option[$mailpn_array_index];
          }else{
            if (array_key_exists('value', $mailpn_input)) {
              $mailpn_value = $mailpn_input['value'];
            }else{
              $mailpn_value = '';
            }
          }
          break;
      }
    }else{
      switch ($mailpn_type) {
        case 'user':
          $user_meta = get_user_meta($mailpn_id, $mailpn_input['id'], true);

          if ($user_meta != '') {
            $mailpn_value = $user_meta;
          }else{
            if (array_key_exists('value', $mailpn_input)) {
              $mailpn_value = $mailpn_input['value'];
            }else{
              $mailpn_value = '';
            }
          }
          break;
        case 'post':
          $post_meta = get_post_meta($mailpn_id, $mailpn_input['id'], true);

          if ($post_meta != '') {
            $mailpn_value = $post_meta;
          }else{
            if (array_key_exists('value', $mailpn_input)) {
              $mailpn_value = $mailpn_input['value'];
            }else{
              $mailpn_value = '';
            }
          }
          break;
        case 'option':
          $option = get_option($mailpn_input['id']);

          if ($option != '') {
            $mailpn_value = $option;
          }else{
            if (array_key_exists('value', $mailpn_input)) {
              $mailpn_value = $mailpn_input['value'];
            }else{
              $mailpn_value = '';
            }
          }
          break;
      }
    }

    $mailpn_parent_block = (!empty($mailpn_input['parent']) ? 'data-mailpn-parent="' . $mailpn_input['parent'] . '"' : '') . ' ' . (!empty($mailpn_input['parent_option']) ? 'data-mailpn-parent-option="' . $mailpn_input['parent_option'] . '"' : '');

    switch ($mailpn_input['input']) {
      case 'input':
        switch ($mailpn_input['type']) {
          case 'file':
            ?>
              <?php if (empty($mailpn_value)): ?>
                <p class="mailpn-m-10"><?php esc_html_e('No file found', 'mailpn'); ?></p>
              <?php else: ?>
                <p class="mailpn-m-10">
                  <a href="<?php echo esc_url(get_post_meta($mailpn_id, $mailpn_input['id'], true)['url']); ?>" target="_blank"><?php echo esc_html(basename(get_post_meta($mailpn_id, $mailpn_input['id'], true)['url'])); ?></a>
                </p>
              <?php endif ?>
            <?php
            break;
          case 'checkbox':
            ?>
              <label class="mailpn-switch">
                <input id="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? '[]' : ''); ?>" class="<?php echo array_key_exists('class', $mailpn_input) ? esc_attr($mailpn_input['class']) : ''; ?> mailpn-checkbox mailpn-checkbox-switch mailpn-field" type="<?php echo esc_attr($mailpn_input['type']); ?>" <?php echo $mailpn_value == 'on' ? 'checked="checked"' : ''; ?> <?php echo (((array_key_exists('disabled', $mailpn_input) && $mailpn_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo ((array_key_exists('required', $mailpn_input) && $mailpn_input['required'] == true) ? 'required' : ''); ?> <?php echo wp_kses_post($mailpn_parent_block); ?>>
                <span class="mailpn-slider mailpn-round"></span>
              </label>
            <?php
            break;
          case 'range':
            ?>
              <div class="mailpn-input-range-wrapper">
                <div class="mailpn-width-100-percent">
                  <?php if (!empty($mailpn_input['mailpn_label_min'])): ?>
                    <p class="mailpn-input-range-label-min"><?php echo esc_html($mailpn_input['mailpn_label_min']); ?></p>
                  <?php endif ?>

                  <?php if (!empty($mailpn_input['mailpn_label_max'])): ?>
                    <p class="mailpn-input-range-label-max"><?php echo esc_html($mailpn_input['mailpn_label_max']); ?></p>
                  <?php endif ?>
                </div>

                <input type="<?php echo esc_attr($mailpn_input['type']); ?>" id="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($mailpn_input['id']); ?>" class="mailpn-input-range <?php echo array_key_exists('class', $mailpn_input) ? esc_attr($mailpn_input['class']) : ''; ?>" <?php echo ((array_key_exists('required', $mailpn_input) && $mailpn_input['required'] == true) ? 'required' : ''); ?> <?php echo (((array_key_exists('disabled', $mailpn_input) && $mailpn_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo (isset($mailpn_input['mailpn_max']) ? 'max=' . esc_attr($mailpn_input['mailpn_max']) : ''); ?> <?php echo (isset($mailpn_input['mailpn_min']) ? 'min=' . esc_attr($mailpn_input['mailpn_min']) : ''); ?> <?php echo (((array_key_exists('step', $mailpn_input) && $mailpn_input['step'] != '')) ? 'step="' . esc_attr($mailpn_input['step']) . '"' : ''); ?> <?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple'] ? 'multiple' : ''); ?> value="<?php echo (!empty($mailpn_input['button_text']) ? esc_html($mailpn_input['button_text']) : esc_html($mailpn_value)); ?>"/>
                <h3 class="mailpn-input-range-output"></h3>
              </div>
            <?php
            break;
          case 'stars':
            $mailpn_stars = !empty($mailpn_input['stars_number']) ? $mailpn_input['stars_number'] : 5;
            ?>
              <div class="mailpn-input-stars-wrapper">
                <div class="mailpn-width-100-percent">
                  <?php if (!empty($mailpn_input['mailpn_label_min'])): ?>
                    <p class="mailpn-input-stars-label-min"><?php echo esc_html($mailpn_input['mailpn_label_min']); ?></p>
                  <?php endif ?>

                  <?php if (!empty($mailpn_input['mailpn_label_max'])): ?>
                    <p class="mailpn-input-stars-label-max"><?php echo esc_html($mailpn_input['mailpn_label_max']); ?></p>
                  <?php endif ?>
                </div>

                <div class="mailpn-input-stars mailpn-text-align-center mailpn-pt-20">
                  <?php foreach (range(1, $mailpn_stars) as $index => $star): ?>
                    <i class="material-icons-outlined mailpn-input-star">star_outlined</i>
                  <?php endforeach ?>
                </div>

                <input type="number" <?php echo ((array_key_exists('required', $mailpn_input) && $mailpn_input['required'] == true) ? 'required' : ''); ?> <?php echo ((array_key_exists('disabled', $mailpn_input) && $mailpn_input['disabled'] == 'true') ? 'disabled' : ''); ?> id="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($mailpn_input['id']); ?>" class="mailpn-input-hidden-stars <?php echo array_key_exists('class', $mailpn_input) ? esc_attr($mailpn_input['class']) : ''; ?>" min="1" max="<?php echo esc_attr($mailpn_stars) ?>">
              </div>
            <?php
            break;
          case 'submit':
            ?>
              <div class="mailpn-text-align-right">
                <input type="submit" value="<?php echo esc_attr($mailpn_input['value']); ?>" name="<?php echo esc_attr($mailpn_input['id']); ?>" id="<?php echo esc_attr($mailpn_input['id']); ?>" class="mailpn-btn" data-mailpn-type="<?php echo esc_attr($mailpn_type); ?>" data-mailpn-subtype="<?php echo ((array_key_exists('subtype', $mailpn_input)) ? esc_attr($mailpn_input['subtype']) : ''); ?>" data-mailpn-user-id="<?php echo esc_attr($mailpn_id); ?>" data-mailpn-post-id="<?php echo esc_attr(get_the_ID()); ?>"/><?php echo esc_html(MAILPN_Data::loader()); ?>
              </div>
            <?php
            break;
          case 'hidden':
            ?>
              <input type="hidden" id="<?php echo esc_attr($mailpn_input['id']); ?>" name="<?php echo esc_attr($mailpn_input['id']); ?>" value="<?php echo esc_attr($mailpn_value); ?>">
            <?php
            break;
          case 'nonce':
            ?>
              <input type="hidden" id="<?php echo esc_attr($mailpn_input['id']); ?>" name="<?php echo esc_attr($mailpn_input['id']); ?>" value="<?php echo esc_attr(wp_create_nonce('mailpn-nonce')); ?>">
            <?php
            break;
          case 'password':
            ?>
              <div class="mailpn-password-checker">
                <div class="mailpn-password-input mailpn-position-relative">
                  <input id="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple'] == 'true') ? '[]' : ''); ?>" name="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple'] == 'true') ? '[]' : ''); ?>" <?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple'] == 'true' ? 'multiple' : ''); ?> class="mailpn-field mailpn-password-strength <?php echo array_key_exists('class', $mailpn_input) ? esc_attr($mailpn_input['class']) : ''; ?>" type="<?php echo esc_attr($mailpn_input['type']); ?>" <?php echo ((array_key_exists('required', $mailpn_input) && $mailpn_input['required'] == 'true') ? 'required' : ''); ?> <?php echo ((array_key_exists('disabled', $mailpn_input) && $mailpn_input['disabled'] == 'true') ? 'disabled' : ''); ?> value="<?php echo (!empty($mailpn_input['button_text']) ? esc_html($mailpn_input['button_text']) : esc_attr($mailpn_value)); ?>" placeholder="<?php echo (array_key_exists('placeholder', $mailpn_input) ? esc_attr($mailpn_input['placeholder']) : ''); ?>" <?php echo wp_kses_post($mailpn_parent_block); ?>/>

                  <a href="#" class="mailpn-show-pass mailpn-cursor-pointer mailpn-display-none-soft">
                    <i class="material-icons-outlined mailpn-font-size-20 mailpn-vertical-align-middle">visibility</i>
                  </a>
                </div>

                <div id="mailpn-popover-pass" class="mailpn-display-none-soft">
                  <div class="mailpn-progress-bar-wrapper">
                    <div class="mailpn-password-strength-bar"></div>
                  </div>

                  <h3 class="mailpn-mt-20"><?php esc_html_e('Password strength checker', 'mailpn'); ?> <i class="material-icons-outlined mailpn-cursor-pointer mailpn-close-icon mailpn-mt-30">close</i></h3>
                  <ul class="mailpn-list-style-none">
                    <li class="low-upper-case">
                      <i class="material-icons-outlined mailpn-font-size-20 mailpn-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Lowercase & Uppercase', 'mailpn'); ?></span>
                    </li>
                    <li class="one-number">
                      <i class="material-icons-outlined mailpn-font-size-20 mailpn-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Number (0-9)', 'mailpn'); ?></span>
                    </li>
                    <li class="one-special-char">
                      <i class="material-icons-outlined mailpn-font-size-20 mailpn-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Special Character (!@#$%^&*)', 'mailpn'); ?></span>
                    </li>
                    <li class="eight-character">
                      <i class="material-icons-outlined mailpn-font-size-20 mailpn-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Atleast 8 Character', 'mailpn'); ?></span>
                    </li>
                  </ul>
                </div>
              </div>
            <?php
            break;
          default:
            ?>
              <input id="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? '[]' : ''); ?>" <?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple'] ? 'multiple' : ''); ?> class="mailpn-field <?php echo array_key_exists('class', $mailpn_input) ? esc_attr($mailpn_input['class']) : ''; ?>" type="<?php echo esc_attr($mailpn_input['type']); ?>" <?php echo ((array_key_exists('required', $mailpn_input) && $mailpn_input['required'] == true) ? 'required' : ''); ?> <?php echo (((array_key_exists('disabled', $mailpn_input) && $mailpn_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo (((array_key_exists('step', $mailpn_input) && $mailpn_input['step'] != '')) ? 'step="' . esc_attr($mailpn_input['step']) . '"' : ''); ?> <?php echo (isset($mailpn_input['max']) ? 'max=' . esc_attr($mailpn_input['max']) : ''); ?> <?php echo (isset($mailpn_input['min']) ? 'min=' . esc_attr($mailpn_input['min']) : ''); ?> <?php echo (isset($mailpn_input['pattern']) ? 'pattern=' . esc_attr($mailpn_input['pattern']) : ''); ?> value="<?php echo (!empty($mailpn_input['button_text']) ? esc_html($mailpn_input['button_text']) : esc_html($mailpn_value)); ?>" placeholder="<?php echo (array_key_exists('placeholder', $mailpn_input) ? esc_html($mailpn_input['placeholder']) : ''); ?>" <?php echo wp_kses_post($mailpn_parent_block); ?>/>
            <?php
            break;
        }
        break;
      case 'select':
        ?>
          <select <?php echo ((array_key_exists('required', $mailpn_input) && $mailpn_input['required'] == true) ? 'required' : ''); ?> <?php echo (((array_key_exists('disabled', $mailpn_input) && $mailpn_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple'] ? 'multiple' : ''); ?> id="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? '[]' : ''); ?>" class="mailpn-field <?php echo array_key_exists('class', $mailpn_input) ? esc_attr($mailpn_input['class']) : ''; ?>" placeholder="<?php echo (array_key_exists('placeholder', $mailpn_input) ? esc_attr($mailpn_input['placeholder']) : ''); ?>" data-placeholder="<?php echo (array_key_exists('placeholder', $mailpn_input) ? esc_attr($mailpn_input['placeholder']) : ''); ?>" <?php echo wp_kses_post($mailpn_parent_block); ?>>

            <?php if (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']): ?>
              <?php 
                switch ($mailpn_type) {
                  case 'user':
                    $mailpn_selected_values = !empty(get_user_meta($mailpn_id, $mailpn_input['id'], true)) ? get_user_meta($mailpn_id, $mailpn_input['id'], true) : [];
                    break;
                  case 'post':
                    $mailpn_selected_values = !empty(get_post_meta($mailpn_id, $mailpn_input['id'], true)) ? get_post_meta($mailpn_id, $mailpn_input['id'], true) : [];
                    break;
                  case 'option':
                    $mailpn_selected_values = !empty(get_option($mailpn_input['id'])) ? get_option($mailpn_input['id']) : [];
                    break;
                }
              ?>
              
              <?php foreach ($mailpn_input['options'] as $mailpn_input_option_key => $mailpn_input_option_value): ?>
                <option value="<?php echo esc_attr($mailpn_input_option_key); ?>" <?php echo ((array_key_exists('all_selected', $mailpn_input) && $mailpn_input['all_selected'] == 'true') || (is_array($mailpn_selected_values) && in_array($mailpn_input_option_key, $mailpn_selected_values)) ? 'selected' : ''); ?>><?php echo esc_html($mailpn_input_option_value) ?></option>
              <?php endforeach ?>
            <?php else: ?>
              <option value="" <?php echo $mailpn_value == '' ? 'selected' : '';?>><?php esc_html_e('Select an option', 'mailpn'); ?></option>
              
              <?php foreach ($mailpn_input['options'] as $mailpn_input_option_key => $mailpn_input_option_value): ?>
                <option value="<?php echo esc_attr($mailpn_input_option_key); ?>" <?php echo ((array_key_exists('all_selected', $mailpn_input) && $mailpn_input['all_selected'] == 'true') || ($mailpn_value != '' && $mailpn_input_option_key == $mailpn_value) ? 'selected' : ''); ?>><?php echo esc_html($mailpn_input_option_value); ?></option>
              <?php endforeach ?>
            <?php endif ?>
          </select>
        <?php
        break;
      case 'textarea':
        ?>
          <textarea id="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($mailpn_input['id']) . ((array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? '[]' : ''); ?>" <?php echo wp_kses_post($mailpn_parent_block); ?> class="mailpn-field <?php echo array_key_exists('class', $mailpn_input) ? esc_attr($mailpn_input['class']) : ''; ?>" <?php echo ((array_key_exists('required', $mailpn_input) && $mailpn_input['required'] == true) ? 'required' : ''); ?> <?php echo (((array_key_exists('disabled', $mailpn_input) && $mailpn_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple'] ? 'multiple' : ''); ?> placeholder="<?php echo (array_key_exists('placeholder', $mailpn_input) ? esc_attr($mailpn_input['placeholder']) : ''); ?>"><?php echo esc_html($mailpn_value); ?></textarea>
        <?php
        break;
      case 'image':
        ?>
          <div class="mailpn-field mailpn-images-block" <?php echo wp_kses_post($mailpn_parent_block); ?> data-mailpn-multiple="<?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? 'true' : 'false'; ?>">
            <?php if (!empty($mailpn_value)): ?>
              <div class="mailpn-images">
                <?php foreach (explode(',', $mailpn_value) as $mailpn_image): ?>
                  <?php echo wp_get_attachment_image($mailpn_image, 'medium'); ?>
                <?php endforeach ?>
              </div>

              <div class="mailpn-text-align-center mailpn-position-relative"><a href="#" class="mailpn-btn mailpn-btn-mini mailpn-image-btn"><?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? esc_html(__('Edit images', 'mailpn')) : esc_html(__('Edit image', 'mailpn')); ?></a></div>
            <?php else: ?>
              <div class="mailpn-images"></div>

              <div class="mailpn-text-align-center mailpn-position-relative"><a href="#" class="mailpn-btn mailpn-btn-mini mailpn-image-btn"><?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? esc_html(__('Add images', 'mailpn')) : esc_html(__('Add image', 'mailpn')); ?></a></div>
            <?php endif ?>

            <input name="<?php echo esc_attr($mailpn_input['id']); ?>" id="<?php echo esc_attr($mailpn_input['id']); ?>" class="mailpn-display-none mailpn-image-input" type="text" value="<?php echo esc_attr($mailpn_value); ?>"/>
          </div>
        <?php
        break;
      case 'video':
        ?>
        <div class="mailpn-field mailpn-videos-block" <?php echo wp_kses_post($mailpn_parent_block); ?>>
            <?php if (!empty($mailpn_value)): ?>
              <div class="mailpn-videos">
                <?php foreach (explode(',', $mailpn_value) as $mailpn_video): ?>
                  <div class="mailpn-video mailpn-tooltip" title="<?php echo esc_html(get_the_title($mailpn_video)); ?>"><i class="dashicons dashicons-media-video"></i></div>
                <?php endforeach ?>
              </div>

              <div class="mailpn-text-align-center mailpn-position-relative"><a href="#" class="mailpn-btn mailpn-video-btn"><?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? esc_html(__('Edit videos', 'mailpn')) : esc_html(__('Edit video', 'mailpn')); ?></a></div>
            <?php else: ?>
              <div class="mailpn-videos"></div>

              <div class="mailpn-text-align-center mailpn-position-relative"><a href="#" class="mailpn-btn mailpn-video-btn"><?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? esc_html(__('Add videos', 'mailpn')) : esc_html(__('Add video', 'mailpn')); ?></a></div>
            <?php endif ?>

            <input name="<?php echo esc_attr($mailpn_input['id']); ?>" id="<?php echo esc_attr($mailpn_input['id']); ?>" class="mailpn-display-none mailpn-video-input" type="text" value="<?php echo esc_attr($mailpn_value); ?>"/>
          </div>
        <?php
        break;
      case 'audio':
        ?>
          <div class="mailpn-field mailpn-audios-block" <?php echo wp_kses_post($mailpn_parent_block); ?>>
            <?php if (!empty($mailpn_value)): ?>
              <div class="mailpn-audios">
                <?php foreach (explode(',', $mailpn_value) as $mailpn_audio): ?>
                  <div class="mailpn-audio mailpn-tooltip" title="<?php echo esc_html(get_the_title($mailpn_audio)); ?>"><i class="dashicons dashicons-media-audio"></i></div>
                <?php endforeach ?>
              </div>

              <div class="mailpn-text-align-center mailpn-position-relative"><a href="#" class="mailpn-btn mailpn-btn-mini mailpn-audio-btn"><?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? esc_html(__('Edit audios', 'mailpn')) : esc_html(__('Edit audio', 'mailpn')); ?></a></div>
            <?php else: ?>
              <div class="mailpn-audios"></div>

              <div class="mailpn-text-align-center mailpn-position-relative"><a href="#" class="mailpn-btn mailpn-btn-mini mailpn-audio-btn"><?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? esc_html(__('Add audios', 'mailpn')) : esc_html(__('Add audio', 'mailpn')); ?></a></div>
            <?php endif ?>

            <input name="<?php echo esc_attr($mailpn_input['id']); ?>" id="<?php echo esc_attr($mailpn_input['id']); ?>" class="mailpn-display-none mailpn-audio-input" type="text" value="<?php echo esc_attr($mailpn_value); ?>"/>
          </div>
        <?php
        break;
      case 'file':
        ?>
          <div class="mailpn-field mailpn-files-block" <?php echo wp_kses_post($mailpn_parent_block); ?>>
            <?php if (!empty($mailpn_value)): ?>
              <div class="mailpn-files mailpn-text-align-center">
                <?php foreach (explode(',', $mailpn_value) as $mailpn_file): ?>
                  <embed src="<?php echo esc_url(wp_get_attachment_url($mailpn_file)); ?>" type="application/pdf" class="mailpn-embed-file"/>
                <?php endforeach ?>
              </div>

              <div class="mailpn-text-align-center mailpn-position-relative"><a href="#" class="mailpn-btn mailpn-btn-mini mailpn-file-btn"><?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? esc_html(__('Edit files', 'mailpn')) : esc_html(__('Edit file', 'mailpn')); ?></a></div>
            <?php else: ?>
              <div class="mailpn-files"></div>

              <div class="mailpn-text-align-center mailpn-position-relative"><a href="#" class="mailpn-btn mailpn-btn-mini mailpn-btn-mini mailpn-file-btn"><?php echo (array_key_exists('multiple', $mailpn_input) && $mailpn_input['multiple']) ? esc_html(__('Add files', 'mailpn')) : esc_html(__('Add file', 'mailpn')); ?></a></div>
            <?php endif ?>

            <input name="<?php echo esc_attr($mailpn_input['id']); ?>" id="<?php echo esc_attr($mailpn_input['id']); ?>" class="mailpn-display-none mailpn-file-input mailpn-btn-mini" type="text" value="<?php echo esc_attr($mailpn_value); ?>"/>
          </div>
        <?php
        break;
      case 'editor':
        ?>
          <div class="mailpn-field" <?php echo wp_kses_post($mailpn_parent_block); ?>>
            <textarea id="<?php echo esc_attr($mailpn_input['id']); ?>" name="<?php echo esc_attr($mailpn_input['id']); ?>" class="mailpn-input mailpn-width-100-percent mailpn-wysiwyg"><?php echo ((empty($mailpn_value)) ? (array_key_exists('placeholder', $mailpn_input) ? esc_attr($mailpn_input['placeholder']) : '') : esc_html($mailpn_value)); ?></textarea>
          </div>
        <?php
        break;
      case 'html':
        ?>
          <div class="mailpn-field" <?php echo wp_kses_post($mailpn_parent_block); ?>>
            <?php echo !empty($mailpn_input['html_content']) ? wp_kses(do_shortcode($mailpn_input['html_content']), MAILPN_KSES) : ''; ?>
          </div>
        <?php
        break;
      case 'html_multi':
        switch ($mailpn_type) {
          case 'user':
            $html_multi_fields_length = !empty(get_user_meta($mailpn_id, $mailpn_input['html_multi_fields'][0]['id'], true)) ? count(get_user_meta($mailpn_id, $mailpn_input['html_multi_fields'][0]['id'], true)) : 0;
            break;
          case 'post':
            $html_multi_fields_length = !empty(get_post_meta($mailpn_id, $mailpn_input['html_multi_fields'][0]['id'], true)) ? count(get_post_meta($mailpn_id, $mailpn_input['html_multi_fields'][0]['id'], true)) : 0;
            break;
          case 'option':
            $html_multi_fields_length = !empty(get_option($mailpn_input['html_multi_fields'][0]['id'])) ? count(get_option($mailpn_input['html_multi_fields'][0]['id'])) : 0;
        }

        ?>
          <div class="mailpn-html-multi-wrapper mailpn-mb-50" <?php echo wp_kses_post($mailpn_parent_block); ?>>
            <?php if ($html_multi_fields_length): ?>
              <?php foreach (range(0, ($html_multi_fields_length - 1)) as $length_index): ?>
                <div class="mailpn-html-multi-group mailpn-display-table mailpn-width-100-percent mailpn-mb-30">
                  <div class="mailpn-display-inline-table mailpn-width-90-percent">
                    <?php foreach ($mailpn_input['html_multi_fields'] as $index => $html_multi_field): ?>
                      <?php self::input_builder($html_multi_field, $mailpn_type, $mailpn_id, false, true, $length_index); ?>
                    <?php endforeach ?>
                  </div>
                  <div class="mailpn-display-inline-table mailpn-width-10-percent mailpn-text-align-center">
                    <i class="material-icons-outlined mailpn-cursor-move mailpn-multi-sorting mailpn-vertical-align-super mailpn-tooltip" title="<?php esc_html_e('Order element', 'mailpn'); ?>">drag_handle</i>
                  </div>

                  <div class="mailpn-text-align-right">
                    <a href="#" class="mailpn-html-multi-remove-btn"><i class="material-icons-outlined mailpn-cursor-pointer mailpn-tooltip" title="<?php esc_html_e('Remove element', 'mailpn'); ?>">remove</i></a>
                  </div>
                </div>
              <?php endforeach ?>
            <?php else: ?>
              <div class="mailpn-html-multi-group mailpn-mb-50">
                <div class="mailpn-display-inline-table mailpn-width-90-percent">
                  <?php foreach ($mailpn_input['html_multi_fields'] as $html_multi_field): ?>
                    <?php self::input_builder($html_multi_field, $mailpn_type); ?>
                  <?php endforeach ?>
                </div>
                <div class="mailpn-display-inline-table mailpn-width-10-percent mailpn-text-align-center">
                  <i class="material-icons-outlined mailpn-cursor-move mailpn-multi-sorting mailpn-vertical-align-super mailpn-tooltip" title="<?php esc_html_e('Order element', 'mailpn'); ?>">drag_handle</i>
                </div>

                <div class="mailpn-text-align-right">
                  <a href="#" class="mailpn-html-multi-remove-btn mailpn-tooltip" title="<?php esc_html_e('Remove element', 'mailpn'); ?>"><i class="material-icons-outlined mailpn-cursor-pointer">remove</i></a>
                </div>
              </div>
            <?php endif ?>

            <div class="mailpn-text-align-right">
              <a href="#" class="mailpn-html-multi-add-btn mailpn-tooltip" title="<?php esc_html_e('Add element', 'mailpn'); ?>"><i class="material-icons-outlined mailpn-cursor-pointer mailpn-font-size-40">add</i></a>
            </div>
          </div>
        <?php
        break;
    }
  }

  public static function input_wrapper_builder($input_array, $type, $mailpn_id = 0, $disabled = 0, $mailpn_format = 'half'){
    // MAILPN_Forms::input_wrapper_builder($input_array, $type, $mailpn_id = 0, $disabled = 0, $mailpn_format = 'half')
    ?>
      <?php if (array_key_exists('section', $input_array) && !empty($input_array['section'])): ?>      
        <?php if ($input_array['section'] == 'start'): ?>
          <div class="mailpn-toggle-wrapper mailpn-section-wrapper mailpn-position-relative mailpn-mb-30 <?php echo array_key_exists('class', $input_array) ? esc_attr($input_array['class']) : ''; ?>" id="<?php echo array_key_exists('id', $input_array) ? esc_attr($input_array['id']) : ''; ?>">
            <?php if (array_key_exists('description', $input_array) && !empty($input_array['description'])): ?>
              <i class="material-icons-outlined mailpn-section-helper mailpn-color-main-0 mailpn-tooltip" title="<?php echo wp_kses_post($input_array['description']); ?>">help</i>
            <?php endif ?>

            <a href="#" class="mailpn-toggle mailpn-width-100-percent mailpn-text-decoration-none">
              <div class="mailpn-display-table mailpn-width-100-percent mailpn-mb-20">
                <div class="mailpn-display-inline-table mailpn-width-90-percent">
                  <label class="mailpn-cursor-pointer mailpn-mb-20 mailpn-color-main-0"><?php echo wp_kses_post($input_array['label']); ?></label>
                </div>
                <div class="mailpn-display-inline-table mailpn-width-10-percent mailpn-text-align-right">
                  <i class="material-icons-outlined mailpn-cursor-pointer mailpn-color-main-0">add</i>
                </div>
              </div>
            </a>

            <div class="mailpn-content mailpn-pl-10 mailpn-toggle-content mailpn-mb-20 mailpn-display-none-soft">
        <?php elseif ($input_array['section'] == 'end'): ?>
            </div>
          </div>
        <?php endif ?>
      <?php else: ?>
        <div class="mailpn-input-wrapper <?php echo esc_attr($input_array['id']); ?> <?php echo !empty($input_array['tabs']) ? 'mailpn-input-tabbed' : ''; ?> mailpn-input-field-<?php echo esc_attr($input_array['input']); ?> <?php echo (!empty($input_array['required']) && $input_array['required'] == true) ? 'mailpn-input-field-required' : ''; ?> <?php echo ($disabled) ? 'mailpn-input-field-disabled' : ''; ?>">
          <?php if (array_key_exists('label', $input_array) && !empty($input_array['label'])): ?>
            <div class="mailpn-display-inline-table <?php echo (($mailpn_format == 'half' && !(array_key_exists('type', $input_array) && $input_array['type'] == 'submit')) ? 'mailpn-width-40-percent' : 'mailpn-width-100-percent'); ?> mailpn-tablet-display-block mailpn-tablet-width-100-percent mailpn-vertical-align-top">
              <div class="mailpn-p-10 <?php echo (array_key_exists('parent', $input_array) && !empty($input_array['parent']) && $input_array['parent'] != 'this') ? 'mailpn-pl-30' : ''; ?>">
                <label class="mailpn-vertical-align-middle mailpn-display-block <?php echo (array_key_exists('description', $input_array) && !empty($input_array['description'])) ? 'mailpn-toggle' : ''; ?>" for="<?php echo esc_attr($input_array['id']); ?>"><?php echo esc_attr($input_array['label']); ?> <?php echo (array_key_exists('required', $input_array) && !empty($input_array['required']) && $input_array['required'] == true) ? '<span class="mailpn-tooltip" title="' . esc_html(__('Required field', 'mailpn')) . '">*</span>' : ''; ?><?php echo (array_key_exists('description', $input_array) && !empty($input_array['description'])) ? '<i class="material-icons-outlined mailpn-cursor-pointer mailpn-float-right">add</i>' : ''; ?></label>

                <?php if (array_key_exists('description', $input_array) && !empty($input_array['description'])): ?>
                  <div class="mailpn-toggle-content mailpn-display-none-soft">
                    <small><?php echo wp_kses_post(wp_specialchars_decode($input_array['description'])); ?></small>
                  </div>
                <?php endif ?>
              </div>
            </div>
          <?php endif ?>

          <div class="mailpn-display-inline-table <?php echo ((array_key_exists('label', $input_array) && empty($input_array['label'])) ? 'mailpn-width-100-percent' : (($mailpn_format == 'half' && !(array_key_exists('type', $input_array) && $input_array['type'] == 'submit')) ? 'mailpn-width-60-percent' : 'mailpn-width-100-percent')); ?> mailpn-tablet-display-block mailpn-tablet-width-100-percent mailpn-vertical-align-top">
            <div class="mailpn-p-10 <?php echo (array_key_exists('parent', $input_array) && !empty($input_array['parent']) && $input_array['parent'] != 'this') ? 'mailpn-pl-30' : ''; ?>">
              <div class="mailpn-input-field"><?php self::input_builder($input_array, $type, $mailpn_id, $disabled); ?></div>
            </div>
          </div>
        </div>
      <?php endif ?>
    <?php
  }

  public static function sanitizer($value, $node = '', $type = '') {
    // MAILPN_Forms::sanitizer($value, $node = '', $type = '')
    switch (strtolower($node)) {
      case 'input':
        switch (strtolower($type)) {
          case 'text':
            return sanitize_text_field($value);
          case 'email':
            return sanitize_email($value);
          case 'url':
            return sanitize_url($value);
          case 'color':
            return sanitize_hex_color($value);
          default:
            return sanitize_text_field($value);
        }
      case 'select':
        switch ($type) {
          case 'select-multiple':
            foreach ($value as $key => $values) {
              $value[$key] = sanitize_key($values);
            }

            return $value;
          default:
            return sanitize_key($value);
        }
      case 'textarea':
        return wp_kses_post($value);
      case 'editor':
        return wp_kses_post($value);
      default:
        return sanitize_text_field($value);
    }
  }
}