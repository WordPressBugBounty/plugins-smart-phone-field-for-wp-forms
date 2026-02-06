<?php
defined('ABSPATH') || exit;
class WPForms_SPF {

    public function __construct() {
        add_action('wpforms_loaded', [$this, 'loads_field']);
        add_action('admin_notices', [$this, 'inject_dependency']);
    }



    public function inject_dependency() {
        if (!class_exists('WPForms')) {
?>
            <div class="notice notice-error">
                <p>
                    <?php printf(
                        /* translators: 1: plugin name, 2: required plugin name, 3: installation URL  4: tag close */
                        esc_html__('%1$s requires %2$s to be installed and actived. You can install and activate it from %3$s here %4$s', 'smart-phone-field-for-wp-forms'),
                        '<strong>Smart Phone Field for WPForms</strong>',
                        '<strong>WPForms</strong>',
                        '<a href="' . esc_url(admin_url('plugin-install.php?tab=search&s=wpforms-lite')) . '">',
                        '</a>.'
                    ); ?>
                </p>
            </div>
<?php
        } else {
        }
    }

    public function loads_field() {
        include PCAFE_SPF_PATH . "includes/addons/wp-forms/field.php";
    }
}

new WPForms_SPF();
