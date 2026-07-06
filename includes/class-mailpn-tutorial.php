<?php
/**
 * Tutorial Onboarding Manager.
 *
 * This class handles the interactive tutorial/onboarding experience for new users.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage mailpn/includes
 * @author     Padres en la Nube
 */
class MAILPN_Tutorial
{
  /**
   * Check if tutorial should be displayed
   *
   * @return bool
   */
  public static function should_display()
  {
    return !get_option('mailpn_tutorial_completed', false);
  }

  /**
   * Enqueue tutorial assets
   */
  public static function enqueue_assets()
  {
    if (!self::should_display()) {
      return;
    }

    wp_enqueue_style(
      'mailpn-tutorial',
      MAILPN_URL . 'assets/css/admin/mailpn-tutorial.css',
      [],
      MAILPN_VERSION
    );

    wp_enqueue_script(
      'mailpn-tutorial',
      MAILPN_URL . 'assets/js/admin/mailpn-tutorial.js',
      ['jquery'],
      MAILPN_VERSION,
      true
    );

    wp_localize_script('mailpn-tutorial', 'mailpnTutorial', [
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('mailpn-nonce'),
    ]);
  }

  /**
   * Render tutorial HTML
   */
  public static function render()
  {
    if (!self::should_display()) {
      return;
    }
    ?>
    <!-- Tutorial Onboarding Overlay -->
    <div id="mailpn-tutorial-overlay" class="mailpn-tutorial-overlay">
      <div class="mailpn-tutorial-spotlight"></div>
      <div class="mailpn-tutorial-box">
        <?php self::render_step_welcome(); ?>
        <?php self::render_step_contents(); ?>
        <?php self::render_step_design(); ?>
        <?php self::render_step_smtp(); ?>
        <?php self::render_step_finish(); ?>
      </div>
    </div>
    <?php
  }

  /**
   * Render Step 1: Welcome
   */
  private static function render_step_welcome()
  {
    ?>
    <div class="mailpn-tutorial-step" data-step="1" data-target=".mailpn-options">
      <div class="mailpn-tutorial-header">
        <h2><?php esc_html_e('Welcome to Mailing Manager - PN', 'mailpn'); ?></h2>
        <button type="button" class="mailpn-tutorial-skip" title="<?php esc_attr_e('Skip tutorial', 'mailpn'); ?>">
          <i class="material-icons-outlined">close</i>
        </button>
      </div>
      <div class="mailpn-tutorial-content">
        <p><?php esc_html_e('Let us guide you through the main features of this powerful email management plugin.', 'mailpn'); ?></p>
        <ul class="mailpn-tutorial-features">
          <li><i class="material-icons-outlined">check_circle</i> <?php esc_html_e('Create and manage unlimited email templates', 'mailpn'); ?></li>
          <li><i class="material-icons-outlined">check_circle</i> <?php esc_html_e('Schedule and queue email delivery', 'mailpn'); ?></li>
          <li><i class="material-icons-outlined">check_circle</i> <?php esc_html_e('Track email opens and clicks', 'mailpn'); ?></li>
          <li><i class="material-icons-outlined">check_circle</i> <?php esc_html_e('Full SMTP configuration and customization', 'mailpn'); ?></li>
        </ul>
      </div>
      <div class="mailpn-tutorial-footer">
        <?php self::render_progress(1, 5); ?>
        <button type="button" class="mailpn-btn mailpn-btn-mini mailpn-tutorial-next">
          <?php esc_html_e('Get Started', 'mailpn'); ?> <i class="material-icons-outlined">arrow_forward</i>
        </button>
      </div>
    </div>
    <?php
  }

  /**
   * Render Step 2: Email Contents Configuration
   */
  private static function render_step_contents()
  {
    ?>
    <div class="mailpn-tutorial-step" data-step="2" data-target="#mailpn_section_contents_start">
      <div class="mailpn-tutorial-header">
        <h2><?php esc_html_e('Configure Email Contents', 'mailpn'); ?></h2>
        <button type="button" class="mailpn-tutorial-skip" title="<?php esc_attr_e('Skip tutorial', 'mailpn'); ?>">
          <i class="material-icons-outlined">close</i>
        </button>
      </div>
      <div class="mailpn-tutorial-content">
        <p><?php esc_html_e('Set up your email sender information, header and footer images, and legal details.', 'mailpn'); ?></p>
        <ul class="mailpn-tutorial-features">
          <li><i class="material-icons-outlined">person</i> <?php esc_html_e('Configure sender name and email address', 'mailpn'); ?></li>
          <li><i class="material-icons-outlined">image</i> <?php esc_html_e('Upload header and footer images for branding', 'mailpn'); ?></li>
          <li><i class="material-icons-outlined">business</i> <?php esc_html_e('Add legal information and company details', 'mailpn'); ?></li>
        </ul>
      </div>
      <div class="mailpn-tutorial-footer">
        <?php self::render_progress(2, 5); ?>
        <?php self::render_navigation(); ?>
      </div>
    </div>
    <?php
  }

  /**
   * Render Step 3: Email Design
   */
  private static function render_step_design()
  {
    ?>
    <div class="mailpn-tutorial-step" data-step="3" data-target="#mailpn_section_design_start">
      <div class="mailpn-tutorial-header">
        <h2><?php esc_html_e('Customize Email Design', 'mailpn'); ?></h2>
        <button type="button" class="mailpn-tutorial-skip" title="<?php esc_attr_e('Skip tutorial', 'mailpn'); ?>">
          <i class="material-icons-outlined">close</i>
        </button>
      </div>
      <div class="mailpn-tutorial-content">
        <p><?php esc_html_e('Personalize the appearance of your emails with fonts, colors, and styles. See changes in real-time with the live preview.', 'mailpn'); ?></p>
        <p class="mailpn-tutorial-tip">
          <i class="material-icons-outlined">palette</i>
          <span>
            <strong><?php esc_html_e('Live Preview:', 'mailpn'); ?></strong>
            <?php esc_html_e('All design changes are reflected instantly in both desktop and mobile views.', 'mailpn'); ?>
          </span>
        </p>
      </div>
      <div class="mailpn-tutorial-footer">
        <?php self::render_progress(3, 5); ?>
        <?php self::render_navigation(); ?>
      </div>
    </div>
    <?php
  }

  /**
   * Render Step 4: SMTP Configuration
   */
  private static function render_step_smtp()
  {
    ?>
    <div class="mailpn-tutorial-step" data-step="4" data-target="#mailpn_section_smtp_start">
      <div class="mailpn-tutorial-header">
        <h2><?php esc_html_e('SMTP Configuration', 'mailpn'); ?></h2>
        <button type="button" class="mailpn-tutorial-skip" title="<?php esc_attr_e('Skip tutorial', 'mailpn'); ?>">
          <i class="material-icons-outlined">close</i>
        </button>
      </div>
      <div class="mailpn-tutorial-content">
        <p><?php esc_html_e('Configure SMTP settings to ensure reliable email delivery. Connect to Gmail, Outlook, or any custom SMTP server.', 'mailpn'); ?></p>
        <ul class="mailpn-tutorial-features">
          <li><i class="material-icons-outlined">settings</i> <?php esc_html_e('Configure SMTP host, port, and security', 'mailpn'); ?></li>
          <li><i class="material-icons-outlined">verified_user</i> <?php esc_html_e('Set authentication credentials', 'mailpn'); ?></li>
          <li><i class="material-icons-outlined">speed</i> <?php esc_html_e('Control sending rates and daily limits', 'mailpn'); ?></li>
        </ul>
        <p class="mailpn-tutorial-tip">
          <i class="material-icons-outlined">lightbulb</i>
          <span>
            <strong><?php esc_html_e('Tip:', 'mailpn'); ?></strong>
            <?php esc_html_e('Use the test email feature to verify your SMTP configuration before sending to users.', 'mailpn'); ?>
          </span>
        </p>
      </div>
      <div class="mailpn-tutorial-footer">
        <?php self::render_progress(4, 5); ?>
        <?php self::render_navigation(); ?>
      </div>
    </div>
    <?php
  }

  /**
   * Render Step 5: Finish
   */
  private static function render_step_finish()
  {
    ?>
    <div class="mailpn-tutorial-step" data-step="5" data-target=".mailpn-settings-footer">
      <div class="mailpn-tutorial-header">
        <h2><?php esc_html_e('You\'re All Set!', 'mailpn'); ?></h2>
      </div>
      <div class="mailpn-tutorial-content">
        <p><?php esc_html_e('You now know the basics of Mailing Manager - PN. Start by creating your first email template or configuring your SMTP settings.', 'mailpn'); ?></p>
        <ul class="mailpn-tutorial-features">
          <li><i class="material-icons-outlined">mail</i> <?php esc_html_e('Create email templates under "Mail" menu', 'mailpn'); ?></li>
          <li><i class="material-icons-outlined">dashboard</i> <?php esc_html_e('Monitor email activity in the Dashboard', 'mailpn'); ?></li>
          <li><i class="material-icons-outlined">analytics</i> <?php esc_html_e('Track opens and clicks in email records', 'mailpn'); ?></li>
        </ul>
        <p class="mailpn-tutorial-tip">
          <i class="material-icons-outlined">help_outline</i>
          <span>
            <strong><?php esc_html_e('Need help?', 'mailpn'); ?></strong>
            <?php esc_html_e('Check our documentation or use the tooltips throughout the interface for guidance.', 'mailpn'); ?>
          </span>
        </p>
      </div>
      <div class="mailpn-tutorial-footer">
        <?php self::render_progress(5, 5); ?>
        <div class="mailpn-tutorial-navigation">
          <button type="button" class="mailpn-btn mailpn-btn-mini mailpn-btn-transparent mailpn-tutorial-prev">
            <i class="material-icons-outlined">arrow_back</i> <?php esc_html_e('Back', 'mailpn'); ?>
          </button>
          <button type="button" class="mailpn-btn mailpn-btn-mini mailpn-tutorial-finish">
            <?php esc_html_e('Finish Tutorial', 'mailpn'); ?> <i class="material-icons-outlined">done</i>
          </button>
        </div>
      </div>
    </div>
    <?php
  }

  /**
   * Render progress indicator
   *
   * @param int $current Current step number
   * @param int $total Total number of steps
   */
  private static function render_progress($current, $total)
  {
    $percentage = ($current / $total) * 100;
    ?>
    <div class="mailpn-tutorial-progress">
      <span class="mailpn-tutorial-progress-text">
        <?php echo esc_html(sprintf(__('Step %d of %d', 'mailpn'), $current, $total)); ?>
      </span>
      <div class="mailpn-tutorial-progress-bar">
        <div class="mailpn-tutorial-progress-fill" style="width: <?php echo esc_attr($percentage); ?>%;"></div>
      </div>
    </div>
    <?php
  }

  /**
   * Render navigation buttons (back/next)
   */
  private static function render_navigation()
  {
    ?>
    <div class="mailpn-tutorial-navigation">
      <button type="button" class="mailpn-btn mailpn-btn-mini mailpn-btn-transparent mailpn-tutorial-prev">
        <i class="material-icons-outlined">arrow_back</i> <?php esc_html_e('Back', 'mailpn'); ?>
      </button>
      <button type="button" class="mailpn-btn mailpn-btn-mini mailpn-tutorial-next">
        <?php esc_html_e('Next', 'mailpn'); ?> <i class="material-icons-outlined">arrow_forward</i>
      </button>
    </div>
    <?php
  }

  /**
   * Mark tutorial as completed
   *
   * @param bool $completed Whether tutorial was completed or skipped
   */
  public static function mark_completed($completed = true)
  {
    update_option('mailpn_tutorial_completed', $completed);
  }

  /**
   * Reset tutorial (for testing or re-showing)
   */
  public static function reset()
  {
    delete_option('mailpn_tutorial_completed');
  }
}
