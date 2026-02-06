<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use \FluentForm\Framework\Helpers\ArrayHelper;

class SPF_Fluent_Forms_Field extends \FluentForm\App\Services\FormBuilder\BaseFieldManager {

    public function __construct() {
        parent::__construct(
            'smart_phone_field',
            'Smart Phone',
            ['phone', 'smart', 'mobile'],
            'general'
        );

        add_action('wp_enqueue_scripts', [$this, 'spf_enqueueScripts'], 11);
    }

    public function spf_enqueueScripts() {

        wp_enqueue_script('spf_fluent_forms', plugin_dir_url(__FILE__) . 'js/spf_ff.js', [], PCAFE_SPF_VERSION, true);
    }

    public function getComponent() {
        return [
            'index'          => 16,
            'element'        => $this->key,
            'attributes'     => [
                'name'        => $this->key,
                'class'       => '',
                'value'       => '',
                'type'        => 'tel'
            ],
            'settings'       => [
                'container_class'       => '',
                'placeholder'           => '',
                'config'                => true,
                'ip_country'            => false,
                'frontend_validation'   => false,
                'label'                 => $this->title,
                'label_placement'       => '',
                'help_message'          => '',
                'validate_number'       => false,
                'default_country'       => 'us',
                'error_message'         => __('Phone number is not valid', 'smart-phone-field-for-wp-forms'),
                'validation_rules'    => [
                    'required'      => [
                        'value'     => false,
                        'message'   => __('This field is required', 'smart-phone-field-for-wp-forms'),
                    ],
                    'validate_number' => [
                        'value'   => false,
                        'message' => __('Phone number is not valid', 'smart-phone-field-for-wp-forms')
                    ]
                ],
                'conditional_logics'  => []
            ],
            'editor_options' => [
                'title'      => $this->title . ' Field',
                'icon_class' => 'el-icon-phone-outline',
                'template'   => 'inputText'
            ],
        ];
    }

    public function getGeneralEditorElements() {
        return [
            'label',
            'placeholder',
            'label_placement',
            'validation_rules',
            'config',
            'ip_country',
            'error_message',
            'frontend_validation'
        ];
    }

    public function generalEditorElement() {
        return [
            'config' => [
                'template' => 'radioButton',
                'label'    => __('Configuration', 'smart-phone-field-for-wp-forms'),
                'help_text'  => __('If you enable this, The country will be selected based on user\'s ip address. ipinfo.io service will be used here', 'smart-phone-field-for-wp-forms'),
                'options'  => [
                    [
                        'value' => true,
                        'label' => __('Global', 'smart-phone-field-for-wp-forms'),
                    ],
                    [
                        'value' => false,
                        'label' => __('Custom', 'smart-phone-field-for-wp-forms'),
                    ]
                ]
            ],
            'ip_country' => [
                'template' => 'radioButton',
                'label'    => __('Enable Auto Country Select', 'smart-phone-field-for-wp-forms'),
                'help_text'  => __('If you enable this, The country will be selected based on user\'s ip address. ipinfo.io service will be used here', 'smart-phone-field-for-wp-forms'),
                'options'  => [
                    [
                        'value' => true,
                        'label' => __('Yes', 'smart-phone-field-for-wp-forms'),
                    ],
                    [
                        'value' => false,
                        'label' => __('No', 'smart-phone-field-for-wp-forms'),
                    ]
                ],
                'dependency'    => [
                    'depends_on'    => 'settings/config',
                    'value'         => false,
                    'operator'      => '=='
                ]
            ],
            'default_country' => [
                'template'      => 'select',
                'label'         => __('Default Country', 'smart-phone-field-for-wp-forms'),
                'options'       => PCafe_SPF_Utils::get_ff_countries(),
                'dependency'    => [
                    'depends_on'    => 'settings/config',
                    'value'         => false,
                    'operator'      => '=='
                ]
            ],
            'frontend_validation' => [
                'template' => 'radioButton',
                'label'    => __('Enable frontend validation', 'smart-phone-field-for-wp-forms'),
                'help_text'  => __('If you enable this, The country will be selected based on user\'s ip address. ipinfo.io service will be used here', 'smart-phone-field-for-wp-forms'),
                'options'  => [
                    [
                        'value' => true,
                        'label' => __('Yes', 'smart-phone-field-for-wp-forms'),
                    ],
                    [
                        'value' => false,
                        'label' => __('No', 'smart-phone-field-for-wp-forms'),
                    ]
                ],
                'dependency'    => [
                    'depends_on'    => 'settings/config',
                    'value'         => false,
                    'operator'      => '=='
                ]
            ]
        ];
    }



    public function render($data, $form) {
        $elementName = $data['element'];
        $data = apply_filters('fluentform/rendering_field_data_' . $elementName, $data, $form);

        if ($tabIndex = \FluentForm\App\Helpers\Helper::getNextTabIndex()) {
            $data['attributes']['tabindex'] = $tabIndex;
        }

        $data['attributes']['class'] = @trim('ff-el-form-control ff-el-spf-input ' . $data['attributes']['class']);
        $data['attributes']['id'] = $this->makeElementId($data, $form);

        $this->pushScripts($data, $form);

        $ariaRequired = 'false';
        if (ArrayHelper::get($data, 'settings.validation_rules.required.value')) {
            $ariaRequired = 'true';
        }

        $elMarkup = "<div class='ff-el-spf--content'><span class='spf-phone error-msg hide'></span><span class='spf-phone valid-msg hide'></span><input " . $this->buildAttributes($data['attributes'], $form) . " aria-invalid='false' aria-required={$ariaRequired} /></div>";

        $html = $this->buildElementMarkup($elMarkup, $data, $form);

        echo apply_filters('fluentform/rendering_field_html_' . $elementName, $html, $data, $form);
    }

    private function pushScripts($data, $form) {
        add_action('wp_footer', function () use ($data, $form) {

            $options = $data['settings'];

            $removed_items = ['container_class', 'validation_rules', 'label', 'label_placement', 'help_message', 'conditional_logics', 'placeholder'];
            foreach ($removed_items as $key) {
                unset($options[$key]);
            }

            $options['name'] = ArrayHelper::get($data, 'attributes.name');
?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    let globalOptions = pcafe_spf_global_setting;
                    let options = {
                        inputId: '<?php echo esc_attr($data['attributes']['id']); ?>',
                        config: '<?php echo esc_attr($data['settings']['config']); ?>',
                        initialCountry: '<?php echo esc_attr($data['settings']['default_country']); ?>',
                        geoIp: '<?php echo esc_attr($data['settings']['ip_country']); ?>',
                        validation: '<?php echo esc_attr($data['settings']['frontend_validation']); ?>',
                        name: '<?php echo esc_attr($options['name']); ?>',
                    };

                    new PCAFE_SPF_FF(options, globalOptions);
                });
            </script>
<?php
        });
    }
}
