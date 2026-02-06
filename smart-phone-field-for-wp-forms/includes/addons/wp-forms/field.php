<?php
if (!defined('ABSPATH')) {
    exit;
}

class SPF_WPForms_Field extends WPForms_Field {

    public function init() {
        $this->name  = 'Smart Phone';
        $this->type  = 'spf_phone';
        $this->icon  = 'fa-phone';
        $this->order = 550;
        $this->group = 'standard';

        add_filter('wpforms_field_properties_spf_phone', [$this, 'field_properties'], 5, 3);
        add_action('wpforms_frontend_css', [$this, 'frontend_css']);
        add_action('wpforms_frontend_js', [$this, 'frontend_js']);
    }

    public function frontend_css($forms) {
        if (! wpforms()->frontend->assets_global() && true !== wpforms_has_field_type('spf_phone', $forms, true)) {
            return;
        }

        wp_enqueue_style('spf-field', PCAFE_SPF_URL . 'assets/css/intlTelInput.css', array(), PCAFE_SPF_VERSION, 'all');
    }

    public function frontend_js($forms) {
        if (! wpforms()->frontend->assets_global() && true !== wpforms_has_field_type('spf_phone', $forms, true)) {
            return;
        }
        wp_enqueue_script('spf_wpforms', plugin_dir_url(__FILE__) . 'js/spf_wpforms.js', [], PCAFE_SPF_VERSION, true);
    }

    public function field_properties($properties, $field, $form_data) {

        $properties['inputs']['primary']['class'][]     = 'pcafe_sphone_field';
        $properties['inputs']['primary']['data']['dc']  = $field['default_country'];

        if (!empty($field['configuration'])) {
            $properties['inputs']['primary']['data']['config']  = $field['configuration'];
        } else {
            $properties['inputs']['primary']['data']['config']  = 'custom';
        }

        if (!empty($field['front_validation'])) {
            $properties['inputs']['primary']['data']['validation']  = $field['front_validation'];
        }

        if (!empty($field['geoip'])) {
            $properties['inputs']['primary']['data']['geoip']  = $field['geoip'];
        }
        return $properties;
    }

    public function field_display($field, $field_atts, $form_data) {
        // Define data.
        $primary = $field['properties']['inputs']['primary'];

        // Allow input type to be changed for this particular field.
        $type = 'tel';

        echo '<div class="wpforms-pcafe-smart-phone-field">';
        // Primary field.
        printf(
            '<input type="%s" %s %s>',
            esc_attr($type),
            wpforms_html_attributes($primary['id'], $primary['class'], $primary['data'], $primary['attr']),
            esc_attr($primary['required'])
        );

        echo '</div>';
    }


    public function field_options($field) {
        // Options open markup.
        $this->field_option(
            'basic-options',
            $field,
            [
                'markup' => 'open',
            ]
        );

        // Label.
        $this->field_option('label', $field);

        // Description.
        $this->field_option('description', $field);

        // Required toggle.
        $this->field_option('required', $field);

        // Default country
        $config_lavel = $this->field_element(
            'label',
            $field,
            [
                'slug'      => 'configuration',
                'value'     => esc_html__('Configuration', 'smart-phone-field-for-wp-forms'),
                'tooltip'   => esc_html__('Choose configuration type', 'smart-phone-field-for-wp-forms')
            ],
            false
        );

        $config_option = $this->field_element(
            'select',
            $field,
            [
                'slug'      => 'configuration',
                'value'     => !empty($field['configuration']) ? esc_attr($field['configuration']) : 'custom',
                'options'   => [
                    'global'    => esc_html__('Global', 'smart-phone-field-for-wp-forms'),
                    'custom'    => esc_html__('Custom', 'smart-phone-field-for-wp-forms'),
                ]
            ],
            false
        );

        $args = [
            'slug'      => 'configuration',
            'content'   => $config_lavel . $config_option,
        ];

        $this->field_element('row', $field, $args);

        //GeoIP 
        $geoip = $this->field_element(
            'toggle',
            $field,
            [
                'slug'      => 'geoip',
                'value'     => ! empty($field['geoip']) ? '1' : '0',
                'desc'      => esc_html__('Determine by visitor location (GeoIP)', 'smart-phone-field-for-wp-forms'),
                'tooltip'   =>    esc_html__('Click here to enable frontend validation.', 'smart-phone-field-for-wp-forms')
            ],
            false
        );

        $args = [
            'slug'    => 'geoip',
            'content' => $geoip,
        ];

        $this->field_element('row', $field, $args);

        // Default country
        $dc_lavel = $this->field_element(
            'label',
            $field,
            [
                'slug'      => 'default_country',
                'value'     => esc_html__('Default Country', 'smart-phone-field-for-wp-forms'),
                'tooltip'   => esc_html__('Choose default country', 'smart-phone-field-for-wp-forms')
            ],
            false
        );

        $dc_option = $this->field_element(
            'select',
            $field,
            [
                'slug'      => 'default_country',
                'value'     => !empty($field['default_country']) ? esc_attr($field['default_country']) : 'US',
                'options'   => wpforms_countries()
            ],
            false
        );

        $args = [
            'slug'      => 'default_country',
            'content'   => $dc_lavel . $dc_option,
        ];

        $this->field_element('row', $field, $args);

        //Frontend Validation
        $front_validation = $this->field_element(
            'toggle',
            $field,
            [
                'slug'      => 'front_validation',
                'value'     => ! empty($field['front_validation']) ? '1' : '0',
                'desc'      => esc_html__('Enable frontend validation', 'smart-phone-field-for-wp-forms'),
                'tooltip'   =>    esc_html__('Click here to enable frontend validation.', 'smart-phone-field-for-wp-forms')
            ],
            false
        );

        $args = [
            'slug'    => 'front_validation',
            'content' => $front_validation,
        ];

        $this->field_element('row', $field, $args);

        // Options close markup.
        $this->field_option(
            'basic-options',
            $field,
            [
                'markup' => 'close',
            ]
        );

        // Options open markup.
        $args = [
            'markup' => 'open',
        ];

        $this->field_option('advanced-options', $field, $args);

        // Size.
        $this->field_option('size', $field);

        // Placeholder.
        $this->field_option('placeholder', $field);

        // Default value.
        $this->field_option('default_value', $field);

        // Custom CSS classes.
        $this->field_option('css', $field);

        // Hide Label.
        $this->field_option('label_hide', $field);

        // Options close markup.
        $args = [
            'markup' => 'close',
        ];

        $this->field_option('advanced-options', $field, $args);
    }

    public function field_preview($field) {
        // Define data.
        $placeholder   = ! empty($field['placeholder']) ? $field['placeholder'] : '';
        $default_value = ! empty($field['default_value']) ? $field['default_value'] : '';

        // Label.
        $this->field_preview_option('label', $field);

        // Primary input.
        echo '<input type="text" placeholder="' . esc_attr($placeholder) . '" value="' . esc_attr($default_value) . '" class="primary-input" readonly>';

        // Description.
        $this->field_preview_option('description', $field);
    }
}

new SPF_WPForms_Field();
