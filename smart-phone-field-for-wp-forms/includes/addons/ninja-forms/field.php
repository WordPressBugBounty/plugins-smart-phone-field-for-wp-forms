<?php

if (! defined('ABSPATH')) {
    exit;
}

class SPF_Ninja_Forms_Field extends NF_Abstracts_Input {

    protected $_name = 'smart_phone';

    protected $_section = 'common';

    protected $_icon = 'phone';

    protected $_type = 'smart_phone';

    protected $_templates = 'smartphone';

    protected $_settings = array(
        'spf_configuration',
        'spf_geoip',
        'spf_default_country',
        'spf_frontend_validation',
        'custom_name_attribute',
        'personally_identifiable',
    );

    public function __construct() {
        add_filter('ninja_forms_field_load_settings', [$this, 'add_spf_settings'], 10, 3);

        parent::__construct();

        $this->_nicename = esc_html__('Smart Phone', 'smart-phone-field-for-wp-forms');

        if (isset($this->_settings['custom_name_attribute'])) {
            $this->_settings['custom_name_attribute']['value'] = 'phone';
        }

        if (isset($this->_settings['personally_identifiable'])) {
            $this->_settings['personally_identifiable']['value'] = '1';
        }
    }

    public function add_spf_settings($settings, $field_name, $parent_type) {
        if ($field_name !== $this->_name) {
            return $settings;
        }

        $settings['spf_configuration'] = array(
            'name'    => 'spf_configuration',
            'type'    => 'select',
            'label'   => esc_html__('Configuration', 'smart-phone-field-for-wp-forms'),
            'width'   => 'full',
            'group'   => 'primary',
            'value'   => 'global',
            'help'    => esc_html__('Choose whether this field uses the global Smart Phone Field settings or custom settings.', 'smart-phone-field-for-wp-forms'),
            'options' => array(
                array(
                    'label' => esc_html__('Global', 'smart-phone-field-for-wp-forms'),
                    'value' => 'global',
                ),
                array(
                    'label' => esc_html__('Custom', 'smart-phone-field-for-wp-forms'),
                    'value' => 'custom',
                ),
            ),
        );

        $settings['spf_geoip'] = array(
            'name'  => 'spf_geoip',
            'type'  => 'toggle',
            'label' => esc_html__('Determine by visitor location (GeoIP)', 'smart-phone-field-for-wp-forms'),
            'width' => 'full',
            'group' => 'primary',
            'value' => false,
            'help'  => esc_html__('Automatically select the country from the visitor location when custom settings are used.', 'smart-phone-field-for-wp-forms'),
            'deps'  => array(
                'spf_configuration' => 'custom',
            ),
        );

        $settings['spf_default_country'] = array(
            'name'    => 'spf_default_country',
            'type'    => 'select',
            'label'   => esc_html__('Default Country', 'smart-phone-field-for-wp-forms'),
            'width'   => 'full',
            'group'   => 'primary',
            'value'   => 'us',
            'help'    => esc_html__('Choose default country when custom settings are used.', 'smart-phone-field-for-wp-forms'),
            'deps'    => array(
                'spf_configuration' => 'custom',
            ),
            'options' => $this->get_country_options(),
        );

        $settings['spf_frontend_validation'] = array(
            'name'  => 'spf_frontend_validation',
            'type'  => 'toggle',
            'label' => esc_html__('Enable frontend validation', 'smart-phone-field-for-wp-forms'),
            'width' => 'full',
            'group' => 'primary',
            'value' => false,
            'help'  => esc_html__('Validate the phone number format in the browser when custom settings are used.', 'smart-phone-field-for-wp-forms'),
            'deps'  => array(
                'spf_configuration' => 'custom',
            ),
        );

        return $settings;
    }

    public function get_parent_type() {
        return 'textbox';
    }

    public function localize_settings($settings, $form_id) {
        $settings['parentType'] = 'textbox';

        return $settings;
    }

    private function get_country_options() {
        $options = array();

        foreach (PCafe_SPF_Utils::get_countries() as $code => $name) {
            $options[] = array(
                'label' => $name,
                'value' => strtolower($code),
            );
        }

        return $options;
    }
}
