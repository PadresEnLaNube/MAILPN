<?php
/**
 * Plugin menus manager.
 *
 * This class defines plugin menus, both in dashboard or in front-end.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Notifications {
  public function mailpn_wp_body_open() {
    $mailpn_action = !empty($_GET['mailpn_action']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_GET['mailpn_action'])) : '';
    $mailpn_notice = !empty($_GET['mailpn_notice']) ? MAILPN_Forms::mailpn_sanitizer(wp_unslash($_GET['mailpn_notice'])) : '';

    ?>
      <?php if (!empty($mailpn_notice)): ?>
        <?php if (!wp_script_is('mailpn-notifications', 'enqueued')): ?>
          <?php wp_enqueue_script('mailpn-notifications', MAILPN_URL . 'assets/js/mailpn-notifications.js', ['jquery'], MAILPN_VERSION, false, ['in_footer' => true, 'strategy' => 'defer']); ?>
        <?php endif ?>

        <div id="mailpn-popup-notice" class="mailpn-popup mailpn-popup-size-small mailpn-display-none-soft">
          <button class="mailpn-popup-close-wrapper">
            <i class="material-icons-outlined">close</i>
          </button>

          <div class="mailpn-popup-content mailpn-text-align-center">
            <div class="mailpn-p-30">
              <?php
                switch ($mailpn_notice) {
                  case 'subscription-unsubscribe-success':
                    ?>
                      <p class="mailpn-alert mailpn-alert-success"><?php esc_html_e('All done! You have been unsubscribed.', 'mailpn'); ?></p>

                        <?php if (class_exists('USERSPN')): ?>
                          <a href="#" class="userspn-profile-popup-btn userspn-btn userspn-btn-mini" data-userspn-action="notifications"><?php esc_html_e('Subscribe again', 'mailpn'); ?></a>
                        <?php endif ?>
                    <?php
                    break;
                  case 'subscription-unsubscribe-error':
                    ?>
                      <p class="mailpn-alert mailpn-alert-error"><?php esc_html_e('Oppps! We are not able to unsubscribe your account.', 'mailpn'); ?></p>

                      <?php if (is_user_logged_in()): ?>
                        <p class="mailpn-mb-20"><?php esc_html_e('It looks like the link followed has expired. Please, set your notification preferences.', 'mailpn'); ?></p>

                        <?php if (class_exists('USERSPN')): ?>
                          <a href="#" class="userspn-profile-popup-btn userspn-btn userspn-btn-mini" data-userspn-action="notifications"><?php esc_html_e('Notifications', 'mailpn'); ?></a>
                        <?php endif ?>
                      <?php else: ?>
                        <p class="mailpn-mb-20"><?php esc_html_e('Please, login to edit your preferences.', 'mailpn'); ?></p>

                        <?php if (class_exists('USERSPN')): ?>
                          <a href="#" class="userspn-profile-popup-btn userspn-btn userspn-btn-mini" data-userspn-action="login"><?php esc_html_e('Login', 'mailpn'); ?></a>
                        <?php endif ?>
                      <?php endif ?>
                    <?php
                    break;
                }
              ?>
            </div>
          </div>
        </div>
      <?php endif ?>
    <?php
  }
}