<?php

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('PCAFE_SPF_Formidable_Forms')) {

    class PCAFE_SPF_Formidable_Forms {

        public function __construct() {
            add_action('plugins_loaded', [$this, 'load_spf_formidable'], 9);
        }

        public function inject_dependency() {
?>
            <div class="notice notice-error">
                <p>
                    <?php
                    printf(
                        /* translators: 1: plugin name, 2: required plugin name, 3: installation URL 4: close tag. */
                        esc_html__(
                            '%1$s requires %2$s to be installed and actived. You can install and activate it from %3$s here %4$s.',
                            'smart-phone-field-for-wp-forms'
                        ),
                        '<strong>Smart Phone Field for Formidable Forms</strong>',
                        '<strong>Formidable Forms</strong>',
                        '<a href="' . esc_url(admin_url('plugin-install.php?tab=search&s=formidable+forms')) . '">',
                        '</a>'
                    );
                    ?>
                </p>
            </div>
<?php
        }

        public function load_spf_formidable() {
            // Check if Formidable Forms is active by checking for its main class
            if (class_exists('FrmAppHelper')) {

                add_filter('frm_available_fields', [$this, 'add_new_field']);
                add_filter('frm_get_field_type_class', [$this, 'get_field_type_class'], 10, 2);

                add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
                require_once 'field.php';
            } else {
                add_action('admin_notices', [$this, 'inject_dependency']);
            }
        }

        public function add_new_field($fields) {
            $fields['spf-phone'] = array(
                'name' => 'Smart Phone',
                'icon' => 'frm_icon_font frm_phone_icon',
            );
            return $fields;
        }

        public function get_field_type_class($class, $field_type) {

            if ('spf-phone' === $field_type) {
                $class = 'FrmSmartPhoneField';
            }
            return $class;
        }

        public function enqueue_scripts() {
            wp_enqueue_script('spf-formidable-forms', plugin_dir_url(__FILE__) . 'js/spf_formidable.js', ['jquery'], PCAFE_SPF_VERSION, true);
        }
    }

    new PCAFE_SPF_Formidable_Forms;
}
