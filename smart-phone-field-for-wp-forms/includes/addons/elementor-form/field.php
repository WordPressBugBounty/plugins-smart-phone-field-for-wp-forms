<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use ElementorPro\Plugin;
use ElementorPro\Modules\Forms\Classes;
use Elementor\Controls_Manager;

class SPF_Elementor_Form_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {
    /**
     * Get field type.
     *
     * Retrieve local-tel field unique ID.
     *
     * @since 1.0.0
     * @access public
     * @return string Field type.
     */
    public function get_type() {
        return 'spf-tel';
    }

    /**
     * Get field name.
     *
     * Retrieve local-tel field label.
     *
     * @since 1.0.0
     * @access public
     * @return string Field name.
     */
    public function get_name() {
        return esc_html__('Smart Phone Field', 'smart-phone-field-for-wp-forms');
    }

    /**
     * Render field output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access public
     * @param mixed $item
     * @param mixed $item_index
     * @param mixed $form
     * @return void
     */
    public function render($item, $item_index, $form) {

        $form->add_render_attribute(
            'input' . $item_index,
            [
                'size' => '1',
                'type' => 'tel',
                'class' => 'smart_phone_field elementor-field-textual',
            ]
        );

        if (isset($item['spf_configuration_type'])) {
            $form->add_render_attribute('input' . $item_index, 'data-config', esc_attr($item['spf_configuration_type']));
        }

        if (isset($item['spf_auto_country']) && $item['spf_auto_country'] == 'yes') {
            $form->add_render_attribute('input' . $item_index, 'data-geoip', esc_attr($item['spf_auto_country']));
        }

        if (isset($item['spf_init_country'])) {
            $form->add_render_attribute('input' . $item_index, 'data-init-country', esc_attr($item['spf_init_country']));
        }

        if (isset($item['spf_frontend_validation']) && $item['spf_frontend_validation'] == 'yes') {
            $form->add_render_attribute('input' . $item_index, 'data-fv', esc_attr($item['spf_frontend_validation']));
        }

        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
    }


    /**
     * @param Widget_Base $widget
     */

    public function update_controls($widget) {
        $elementor = Plugin::elementor();

        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');

        if (is_wp_error($control_data)) {
            return;
        }

        $field_controls = [
            'spf_configuration_type'   => [
                'name'          => 'spf_configuration_type',
                'label'         => esc_html__('Configuration', 'smart-phone-field-for-wp-forms'),
                'type'          => Controls_Manager::SELECT,
                'label_block'   => false,
                'options'       => [
                    'global'    => esc_html__('Global', 'smart-phone-field-for-wp-forms'),
                    'custom'    => esc_html__('Custom', 'smart-phone-field-for-wp-forms'),
                ],
                'default'       => 'global',
                'condition'     => [
                    'field_type'        => $this->get_type(),
                ],
                'tab'           => 'content',
                'inner_tab'     => 'form_fields_content_tab',
                'tabs_wrapper'  => 'form_fields_tabs',
            ],
            'spf_auto_country'  => [
                'name'          => 'spf_auto_country',
                'label'         => esc_html__('Determine by visitor location (GeoIP)', 'smart-phone-field-for-wp-forms'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Yes', 'smart-phone-field-for-wp-forms'),
                'label_off'     => esc_html__('No', 'smart-phone-field-for-wp-forms'),
                'return_value'  => 'yes',
                'default'       => 'yes',
                'condition'     => [
                    'field_type'    => $this->get_type(),
                    'spf_configuration_type' => 'custom',
                ],
                'tab'           => 'content',
                'inner_tab'     => 'form_fields_content_tab',
                'tabs_wrapper'  => 'form_fields_tabs',
            ],
            'spf_init_country'  => [
                'name'          => 'spf_init_country',
                'label'         => esc_html__('Initial Country', 'smart-phone-field-for-wp-forms'),
                'type'          => Controls_Manager::SELECT2,
                'label_block'   => false,
                'multiple'      => false,
                'options'       => PCafe_SPF_Utils::get_countries(),
                'default'       => 'US',
                'condition'     => [
                    'field_type'        => $this->get_type(),
                    'spf_configuration_type'     => 'custom',
                ],
                'tab'           => 'content',
                'inner_tab'     => 'form_fields_content_tab',
                'tabs_wrapper'  => 'form_fields_tabs',
            ],
            'spf_frontend_validation'  => [
                'name'          => 'spf_frontend_validation',
                'label'         => esc_html__('Frontend Validation', 'smart-phone-field-for-wp-forms'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Yes', 'smart-phone-field-for-wp-forms'),
                'label_off'     => esc_html__('No', 'smart-phone-field-for-wp-forms'),
                'return_value'  => 'yes',
                'default'       => 'yes',
                'condition'     => [
                    'field_type'    => $this->get_type(),
                    'spf_configuration_type' => ['custom'],
                ],
                'tab'           => 'content',
                'inner_tab'     => 'form_fields_content_tab',
                'tabs_wrapper'  => 'form_fields_tabs',
            ],
        ];

        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);

        $widget->update_control('form_fields', $control_data);
    }

    /**
     * Field constructor.
     *
     * Used to add a script to the Elementor editor preview.
     */
    public function __construct() {
        parent::__construct();
        add_action('elementor/preview/init', [$this, 'editor_preview_footer']);
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'elementor_js']);
    }

    function elementor_js() {
        wp_enqueue_script('spf_eleform', plugin_dir_url(__FILE__) . 'js/spf_ef.js', ['elementor-frontend'], PCAFE_SPF_VERSION, true);
    }

    /**
     * Elementor editor preview.
     *
     * Add a script to the footer of the editor preview screen.
     */
    public function editor_preview_footer() {
        add_action('wp_footer', [$this, 'content_template_script']);
    }


    public function content_template_script() {
?>
        <script>
            jQuery(document).ready(() => {

                elementor.hooks.addFilter(
                    'elementor_pro/forms/content_template/field/<?php echo esc_html($this->get_type()); ?>',
                    function(inputField, item, i) {

                        const fieldId = `form_field_${i}`;
                        const fieldClass = `elementor-field-textual smart_phone_field elementor-field ${item.css_classes}`;
                        const size = '1';
                        const initCountry = item['spf_init_country'] ? item['spf_init_country'] : '';
                        const autoCountry = item['spf_auto_country'] ? item['spf_auto_country'] : '';
                        const frontValidation = item['spf_frontend_validation'] ? item['spf_frontend_validation'] : '';
                        const configType = item['spf_configuration_type'] ? item['spf_configuration_type'] : 'global';

                        return `<input id="${fieldId}" class="${fieldClass}" size="${size}" data-fv="${frontValidation}" data-geoip="${autoCountry}" data-config="${configType}" data-init-country="${initCountry}">`;
                    }, 10, 3
                );

            });
        </script>
<?php
    }
}
