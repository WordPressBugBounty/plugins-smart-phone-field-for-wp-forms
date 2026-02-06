<?php
defined('ABSPATH') || exit;

class WooCommerce_SPF {
    public function __construct() {
        add_action('before_woocommerce_init', [$this, 'declare_compatibility']);

        $is_shipping_phone_enabled = get_option('pcafe_spf_woo_shipping_phone', 'no') === 'yes';
        add_filter('woocommerce_settings_tabs_array', [$this, 'add_settings_tab'], 50);
        add_action('woocommerce_settings_tabs_smart_phone_field', [$this, 'settings_tab_content']);
        add_action('woocommerce_update_options_smart_phone_field', [$this, 'update_settings']);

        if ($is_shipping_phone_enabled) {
            add_filter('woocommerce_checkout_fields', [$this, 'add_shipping_phone_field']);
            add_action('woocommerce_checkout_update_order_meta', [$this, 'save_shipping_phone_field']);
        }

        add_action('wp_enqueue_scripts', [$this, 'woo_enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_js']);
    }

    /**
     * Declare compatibility with WooCommerce features (like HPOS).
     */
    public function declare_compatibility() {

        if (! class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
            return;
        }

        // Declare HPOS compatibility
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true
        );
    }

    public function woo_enqueue_scripts() {
        if (class_exists('WooCommerce') && is_checkout()) {

            wp_enqueue_script(
                'pcafe_spf_woo_script',
                plugin_dir_url(__FILE__) . 'js/woo_spf.js',
                [],
                PCAFE_SPF_VERSION,
                true
            );

            wp_localize_script('pcafe_spf_woo_script', 'PCAFE_SPF_WOO_DATA', [
                'configuration'         => get_option('pcafe_spf_woo_configuration_type', 'global'),
                'geoCountry'            => get_option('pcafe_spf_woo_geo_country', 'no'),
                'defaultCountry'        => get_option('pcafe_spf_woo_default_country', 'US'),
                'frontendValidation'    => get_option('pcafe_spf_woo_frontend_validation', 'no'),
                'countrySearch'         => get_option('pcafe_spf_woo_country_search', 'no'),
                'enableShipping'        => get_option('pcafe_spf_woo_shipping_phone', 'no'),
            ]);
        }
    }

    public function enqueue_admin_js($hook) {
        if ($hook !== 'woocommerce_page_wc-settings') {
            return;
        }

        // Check if we are on *your* tab
        $current_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : '';
        if ($current_tab === 'smart_phone_field') {
            wp_enqueue_script(
                'pcafe_spf_woo_admin_script',
                plugin_dir_url(__FILE__) . 'js/woo_admin.js',
                ['jquery'],
                PCAFE_SPF_VERSION,
                true
            );
        }
    }

    /**
     * Add custom settings tab
     */
    public function add_settings_tab($tabs) {
        $tabs['smart_phone_field'] = __('Smart Phone Field', 'smart-phone-field-for-wp-forms');
        return $tabs;
    }

    /**
     * Get settings fields
     */
    public function get_settings() {
        $settings = array(
            'pcafe_spf_section_title' => array(
                'name' => esc_html__('Smart Phone Field Settings', 'smart-phone-field-for-wp-forms'),
                'type' => 'title',
                'desc' => esc_html__('A modern phone input with country flags and automatic dial codes for global users.', 'smart-phone-field-for-wp-forms'),
                'id'   => 'pcafe_spf_section_title'
            ),
            'configuration' => array(
                'name'    => esc_html__('Configuration Type', 'smart-phone-field-for-wp-forms'),
                'type'    => 'select',
                'id'      => 'pcafe_spf_woo_configuration_type',
                'options' => array(
                    'global' => esc_html__('Global', 'smart-phone-field-for-wp-forms'),
                    'custom' => esc_html__('Custom', 'smart-phone-field-for-wp-forms'),
                ),
                'default' => 'global',
            ),
            'geo_country' => array(
                'name' => esc_html__('Visitor Location By IP', 'smart-phone-field-for-wp-forms'),
                'id'      => 'pcafe_spf_woo_geo_country',
                'type' => 'checkbox',
                'desc' => esc_html__('Enable determine by visitor location (GeoIP)', 'smart-phone-field-for-wp-forms'),
                'default' => 'no',
                'class' => 'pcafe_spf_geoip_field'
            ),
            'default_country' => array(
                'name'     => esc_html__('Default Country', 'smart-phone-field-for-wp-forms'),
                'id'      => 'pcafe_spf_woo_default_country',
                'type'     => 'select',
                'default'  => 'US',
                'options'  => PCafe_SPF_Utils::instance()->get_countries(),
                'desc'     => esc_html__('Choose the default country to be used for the country code selector when GeoIP is disabled.', 'smart-phone-field-for-wp-forms'),
                'class' => 'pcafe_spf_default_country_field'
            ),
            'frontend_validation' => array(
                'name' => esc_html__('Frontend Validation', 'smart-phone-field-for-wp-forms'),
                'id'      => 'pcafe_spf_woo_frontend_validation',
                'type' => 'checkbox',
                'desc' => esc_html__('Enable Frontend Validation', 'smart-phone-field-for-wp-forms'),
                'default' => 'no',
                'class' => 'pcafe_spf_validation_field'
            ),
            'shipping_phone' => array(
                'name'      => esc_html__('Shipping Phone Number', 'smart-phone-field-for-wp-forms'),
                'id'        => 'pcafe_spf_woo_shipping_phone',
                'type'      => 'checkbox',
                'desc'      => esc_html__('Enable On Shipping Phone Field', 'smart-phone-field-for-wp-forms'),
                'default'   => 'no',
            ),
            'spf_section_end' => array(
                'type' => 'sectionend',
                'id'   => 'pcafe_sfp_settings_section_end',
            ),
        );

        return apply_filters('pcafe_spf_woocommerce_tab_settings', $settings);
    }

    /**
     * Output settings fields
     */
    public function settings_tab_content() {
        woocommerce_admin_fields($this->get_settings());
    }

    /**
     * Save settings
     */
    public function update_settings() {
        woocommerce_update_options($this->get_settings());
    }

    public function add_shipping_phone_field($fields) {

        if (isset($fields['shipping']['shipping_phone'])) {
            // Already exists — don’t add again
            return $fields;
        }

        $fields['shipping']['shipping_phone'] = array(
            'label'       => __('Phone', 'smart-phone-field-for-wp-forms'),
            'placeholder' => __('Phone', 'smart-phone-field-for-wp-forms'),
            'required'    => false,
            'class'       => array('form-row-wide'),
            'priority'    => 120,
        );

        return $fields;
    }

    public function save_shipping_phone_field($order_id) {
        if (!empty($_POST['shipping_phone'])) {
            update_post_meta($order_id, '_shipping_phone', sanitize_text_field(wp_unslash($_POST['shipping_phone'])));
        }
    }
}

new WooCommerce_SPF();
