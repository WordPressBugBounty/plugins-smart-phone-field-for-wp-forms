<?php

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('PCAFE_SPF_Contact_Form_7')) {

    class PCAFE_SPF_Contact_Form_7 {

        public function __construct() {
            add_action('init', [$this, 'load_spf_cf7'], 9);
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
                        '<strong>Smart Phone Field for Contact Form 7</strong>',
                        '<strong>Contact Form 7</strong>',
                        '<a href="' . esc_url(admin_url('plugin-install.php?tab=search&s=contact+form+7')) . '">',
                        '</a>'
                    );
                    ?>
                </p>
            </div>
<?php
        }

        public function load_spf_cf7() {
            if (class_exists('WPCF7')) {
                require_once 'field.php';
            } else {
                add_action('admin_notices', [$this, 'inject_dependency']);
            }
        }
    }

    new PCAFE_SPF_Contact_Form_7;
}
