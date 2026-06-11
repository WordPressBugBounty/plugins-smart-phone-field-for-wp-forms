<?php

if (! defined('ABSPATH')) {
    exit;
}

class SPF_Contact_Form_7_Field {
    /**
     * Constructor Function
     */
    public function __construct() {
        add_action('wpcf7_init', [$this, 'add_shortcodes']);
        add_action('admin_init', [$this, 'tag_generator']);
        add_filter('wpcf7_validate_smart_phone_field', [$this, 'smart_phone_field_validation_filter'], 10, 2);
        add_filter('wpcf7_validate_smart_phone_field*', [$this, 'smart_phone_field_validation_filter'], 10, 2);
        add_action("wpcf7_before_send_mail", array($this, 'send_data'));

        add_action('wp_enqueue_scripts', [$this, 'spf_enqueue_script']);
    }

    public function spf_enqueue_script() {
        wp_enqueue_script('spf-contact-form-7', plugin_dir_url(__FILE__) . 'js/spf_cf7.js', [], PCAFE_SPF_VERSION, true);
    }

    public function send_data($cf7) {
        // get the contact form object
        $wpcf = WPCF7_Submission::get_instance();

        // Check if submission instance exists
        if (! $wpcf) {
            return;
        }

        $posted_data = $wpcf->get_posted_data();
        $id = $cf7->id();
    }

    /**
     * add form tag
     */
    public function add_shortcodes() {
        wpcf7_add_form_tag(
            ['smart_phone_field', 'smart_phone_field*'],
            [$this, 'spf_tag_handler_callback'],
            ['name-attr' => true]
        );
    }

    public function spf_tag_handler_callback($tag) {
        if (empty($tag->name)) {
            return '';
        }

        $validation_error = wpcf7_get_validation_error($tag->name);

        $config = ! empty($tag->get_option('config', '', true)) ? $tag->get_option('config', '', true) : 'global';
        $initCounry = ! empty($tag->get_option('init_country', '', true)) ? $tag->get_option('init_country', '', true) : 'us';

        $class = wpcf7_form_controls_class($tag->type);

        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = [];

        $atts['type'] = 'tel';

        $class .= ' spf_validation';

        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->name;
        $atts['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $atts['name'] = $tag->name;

        if ($tag->is_required()) {
            $atts['aria-required'] = 'true';
        }

        if ($tag->has_option('config')) {
            $atts['data-config'] = $config;
        }

        if ($tag->has_option('init_country')) {
            $atts['data-init_country'] = $initCounry;
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        if ($tag->has_option('frontend_validation')) {
            $atts['data-fv'] = 'true';
        }

        $value = (string) reset($tag->values);

        $value = $tag->get_default_option($value);

        $value = wpcf7_get_hangover($tag->name, $value);

        $atts['value'] = $value;

        $atts = wpcf7_format_atts($atts);

        $html = sprintf(
            '<span class="wpcf7-form-control-wrap spf_wrap" data-name="%1$s"><input %2$s />%3$s</span>',
            sanitize_html_class($tag->name),
            $atts,
            $validation_error
        );

        return $html;
    }

    public function smart_phone_field_validation_filter($result, $tag) {
        $name = $tag->name;

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $value = isset($_POST[$name]) ? sanitize_text_field(wp_unslash($_POST[$name])) : '';

        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, wpcf7_get_message('invalid_required'));
        }

        return $result;
    }

    /**
     * Tag Generator
     */
    public function tag_generator() {

        $tag_generator = WPCF7_TagGenerator::get_instance();

        $tag_generator->add(
            'smart_phone_field',
            __('Smart Phone Field', 'smart-phone-field-for-wp-forms'),
            [
                $this,
                'spf_panel'
            ],
            array('version' => '2')
        );
    }

    public function spf_panel($cf, $options) {

        $field_types = array(
            'smart_phone_field' => array(
                'display_name' => __('Smart Phone Field', 'smart-phone-field-for-wp-forms'),
                'heading' => __('Smart Phone Field', 'smart-phone-field-for-wp-forms'),
                'description' => ''
            ),
        );

        $tgg = new WPCF7_TagGeneratorGenerator($options['content']);
?>
        <header class="description-box">
            <h3><?php echo esc_html($field_types['smart_phone_field']['heading']); ?></h3>
            <p><?php
                wp_kses(
                    $field_types['smart_phone_field']['description'],
                    array(
                        'a' => array('href' => true),
                        'strong' => array(),
                    ),
                    array('http', 'https')
                );

                ?></p>
            <div class="pcafe_spf_notice">
                <?php
                printf(
                    /* translators: 1: Documentation link 2: close tag */
                    esc_html__('Confused? Check our Documentation on  %1$s Smart Phone Field %2$s.', 'smart-phone-field-for-wp-forms'),
                    '<a href="https://pluginscafe.com/docs/smart-phone-field/#contact-form-7" target="_blank">',
                    '</a>'
                ); ?>
            </div>
        </header>
        <div class="control-box uacf7-control-box">

            <?php

            $tgg->print('field_type', array(
                'with_required' => true,
                'select_options' => array(
                    'smart_phone_field' => $field_types['smart_phone_field']['display_name'],
                ),
            ));

            $tgg->print('field_name');

            ?>
            <fieldset>
                <legend>
                    <?php echo esc_html__('Configuration', 'smart-phone-field-for-wp-forms'); ?>
                </legend>
                <select id="config" data-tag-part="option" data-tag-option="config:">
                    <option value="global">
                        <?php echo esc_html__('Global', 'smart-phone-field-for-wp-forms'); ?>
                    </option>
                    <option value="custom">
                        <?php echo esc_html__('Custom', 'smart-phone-field-for-wp-forms'); ?>
                    </option>
                </select>
                <p>
                    <?php
                    printf(
                        /* translators: 1: Plugin global setting page 2: close tag */
                        esc_html__('Choose to use the configuration as a %1$s Global Setting %2$s or for a custom setting for different configurations.', 'smart-phone-field-for-wp-forms'),
                        '<a href="' . esc_url(admin_url('admin.php?page=smart-phone-field#settings')) . '" target="_blank">',
                        '</a>'
                    ); ?>
                </p>
            </fieldset>

            <fieldset class="pcafe-spf-wrapper custom_config">
                <legend>
                    <?php echo esc_html(__('Initial Country', 'smart-phone-field-for-wp-forms')); ?>
                </legend>

                <input type="text" data-tag-part="option" data-tag-option="init_country:" placeholder="eg: us" />
                <p><?php echo esc_html__('Enter the country code using only 2 letters (e.g., us - for the United States). To automatically select the country based on the visitor\'s location, Type - auto.', 'smart-phone-field-for-wp-forms'); ?></p>
            </fieldset>

            <fieldset class="pcafe-spf-wrapper custom_config">
                <legend>
                    <?php echo esc_html(__('Frontend Validation', 'smart-phone-field-for-wp-forms')); ?>
                </legend>
                <input id="frontend_validation" type="checkbox" data-tag-part="option" data-tag-option="frontend_validation" />
                <label for="frontend_validation"><?php echo esc_html__("Enable frontend validation", 'smart-phone-field-for-wp-forms'); ?></label>
            </fieldset>

            <footer class="insert-box">
                <?php
                $tgg->print('insert_box_content');

                $tgg->print('mail_tag_tip');
                ?>
            </footer>
    <?php
    }
}

new SPF_Contact_Form_7_Field;
