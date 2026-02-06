<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class PCAFE_SPF_Elementor_Form {
    public function __construct() {
        add_action('admin_notices', array($this, 'pcafe_spf_elementor_form_notice'));
        add_action('elementor_pro/forms/fields/register', [$this, 'spf_field_register'], 10, 1);
    }


    function spf_field_register($form_fields_registrar) {

        require_once __DIR__ . '/field.php';

        $form_fields_registrar->register(new \SPF_Elementor_Form_Field());
    }

    public function pcafe_spf_elementor_form_notice() {
        if (!$this->has_elementor_pro()) {
?>
            <div class="notice notice-error">
                <p>
                    <?php printf(
                        /* translators: 1: plugin name, 2: required plugin name, 3: installation URL  4: tag close */
                        esc_html__('%1$s requires %2$s to be installed and actived.', 'smart-phone-field-for-wp-forms'),
                        '<strong>Smart Phone Field for Elementor Form</strong>',
                        '<strong>Elementor Pro</strong>'
                    ); ?>
                </p>
            </div>
<?php
        }
    }

    public function has_elementor_pro() {
        return class_exists('ElementorPro\Plugin');
    }
}


new PCAFE_SPF_Elementor_Form();
