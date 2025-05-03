<?php
/**
 * Class MAILPN_Popups
 * Handles popup functionality for the MAILPN plugin
 */
class MAILPN_Popups {
    /**
     * The single instance of the class
     */
    protected static $_instance = null;

    /**
     * Main MAILPN_Popups Instance
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Open a popup
     */
    public static function open($content, $options = array()) {
        $defaults = array(
            'id' => uniqid('mailpn-popup-'),
            'class' => '',
            'closeButton' => true,
            'overlayClose' => true,
            'escClose' => true
        );

        $options = wp_parse_args($options, $defaults);

        ob_start();
        ?>
        <div id="<?php echo esc_attr($options['id']); ?>" class="mailpn-popup <?php echo esc_attr($options['class']); ?>" style="display: none;">
            <div class="mailpn-popup-overlay"></div>
            <div class="mailpn-popup-content">
                <?php if ($options['closeButton']) : ?>
                    <button type="button" class="mailpn-popup-close"><i class="material-icons-outlined">close</i></button>
                <?php endif; ?>
                <?php echo wp_kses_post($content); ?>
            </div>
        </div>
        <?php
        $html = ob_get_clean();

        return $html;
    }

    /**
     * Close a popup
     */
    public static function close($id = null) {
        if ($id) {
            return "<script>jQuery('#" . esc_js($id) . "').remove();</script>";
        }
        return "<script>jQuery('.mailpn-popup').remove();</script>";
    }
} 