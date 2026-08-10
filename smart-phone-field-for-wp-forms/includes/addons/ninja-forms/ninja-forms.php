<?php

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('PCAFE_SPF_Ninja_Forms')) {

    class PCAFE_SPF_Ninja_Forms {

        public function __construct() {
            add_action('plugins_loaded', [$this, 'load_spf_ninja_forms'], 9);
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
                        '<strong>Smart Phone Field for Ninja Forms</strong>',
                        '<strong>Ninja Forms</strong>',
                        '<a href="' . esc_url(admin_url('plugin-install.php?tab=search&s=ninja+forms')) . '">',
                        '</a>'
                    );
                    ?>
                </p>
            </div>
<?php
        }

        public function load_spf_ninja_forms() {
            if (class_exists('Ninja_Forms') && class_exists('NF_Abstracts_Input')) {
                require_once PCAFE_SPF_PATH . 'includes/addons/ninja-forms/field.php';

                add_filter('ninja_forms_register_fields', [$this, 'register_field']);
                add_filter('ninja_forms_field_template_file_paths', [$this, 'register_template_path']);
                add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
                add_action('nf_admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
            } else {
                add_action('admin_notices', [$this, 'inject_dependency']);
            }
        }

        public function register_field($fields) {
            $fields['smart_phone'] = new SPF_Ninja_Forms_Field();

            return $fields;
        }

        public function register_template_path($paths) {
            $paths[] = PCAFE_SPF_PATH . 'includes/addons/ninja-forms/templates/';

            return $paths;
        }

        public function enqueue_scripts() {
            if (! wp_style_is('pcafe_spf_intl', 'enqueued')) {
                wp_enqueue_style('spf-ninja-forms-field', PCAFE_SPF_URL . 'assets/css/intlTelInput2.css', array(), PCAFE_SPF_VERSION, 'all');
            }

            if (! wp_script_is('pcafe_spf_intl', 'enqueued')) {
                wp_enqueue_script('pcafe_spf_intl', PCAFE_SPF_URL . 'assets/js/intlTelInputWithUtils.min.js', array(), PCAFE_SPF_VERSION, false);
            }

            wp_enqueue_script('spf-ninja-forms', PCAFE_SPF_URL . 'includes/addons/ninja-forms/js/spf_ninja_forms.js', ['jquery', 'pcafe_spf_intl'], PCAFE_SPF_VERSION, true);
        }

        public function enqueue_admin_scripts() {
            wp_enqueue_style('spf-ninja-forms-admin', PCAFE_SPF_URL . 'includes/addons/ninja-forms/css/spf_ninja_forms_admin.css', array('nf-builder'), PCAFE_SPF_VERSION, 'all');
        }
    }

    new PCAFE_SPF_Ninja_Forms();
}
