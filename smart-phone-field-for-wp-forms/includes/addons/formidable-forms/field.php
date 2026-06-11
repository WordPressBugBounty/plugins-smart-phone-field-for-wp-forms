<?php

defined('ABSPATH') || exit;

class FrmSmartPhoneField extends FrmFieldType {

    protected $type = 'spf-phone';
    protected $has_input = true;

    protected function field_settings_for_type() {
        $settings = parent::field_settings_for_type();

        // Keep if you truly want this field to be read-only in the builder/settings.
        $settings['read_only'] = true;

        return $settings;
    }

    protected function extra_field_opts() {
        // These are the BASE option names (Formidable will often store/retrieve as optname_{field_id} in the admin UI).
        return array(
            'spf_config'              => 'global',
            'spf_default_country'     => 'us',
            'spf_geoip'               => 0,
            'spf_frontend_validation' => 0,
        );
    }

    /**
     * Tell Formidable this field has "choices", so it shows the accordion area.
     */
    protected function has_field_choices($field) {
        return true;
    }

    /**
     * Helper: read option value regardless of whether it's stored as opt or opt_{id}.
     */
    protected function get_opt($field, $base_key, $default = '') {
        $id_key = $base_key . '_' . (isset($field['id']) ? $field['id'] : '');

        if (isset($field[$id_key])) {
            return $field[$id_key];
        }
        if (isset($field[$base_key])) {
            return $field[$base_key];
        }
        return $default;
    }

    public function show_extra_field_choices($args) {
        $field = $args['field'];

        $config              = $this->get_opt($field, 'spf_config', 'global');
        $default_country     = $this->get_opt($field, 'spf_default_country', 'us');
        $geoip               = (int) $this->get_opt($field, 'spf_geoip', 0);
        $frontend_validation = (int) $this->get_opt($field, 'spf_frontend_validation', 0);

        $field_id = (int) $field['id'];
?>
        <p class="frm_form_field">
            <label for="spf_config_<?php echo esc_attr($field_id); ?>">
                <?php esc_html_e('Configuration', 'smart-phone-field-for-wp-forms'); ?>
                <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e('Choose configuration type', 'smart-phone-field-for-wp-forms'); ?>"></span>
            </label>

            <select name="field_options[spf_config_<?php echo esc_attr($field_id); ?>]" id="spf_config_<?php echo esc_attr($field_id); ?>">
                <option value="global" <?php selected($config, 'global'); ?>>
                    <?php esc_html_e('Global', 'smart-phone-field-for-wp-forms'); ?>
                </option>
                <option value="custom" <?php selected($config, 'custom'); ?>>
                    <?php esc_html_e('Manual', 'smart-phone-field-for-wp-forms'); ?>
                </option>
            </select>
        </p>

        <p class="frm_form_field">
            <label class="frm_toggle frm_toggle_block" for="spf_geoip_<?php echo esc_attr($field_id); ?>">
                <input type="checkbox"
                    name="field_options[spf_geoip_<?php echo esc_attr($field_id); ?>]"
                    id="spf_geoip_<?php echo esc_attr($field_id); ?>"
                    value="1" <?php checked($geoip, 1); ?> />

                <span class="frm_toggle" tabindex="0" role="switch" aria-checked="<?php echo $geoip ? 'true' : 'false'; ?>">
                    <span class="frm_toggle_slider"></span>
                </span>

                <span class="frm_toggle_label"><?php esc_html_e('Determine by visitor location (GeoIP)', 'smart-phone-field-for-wp-forms'); ?></span>
            </label>
        </p>

        <p class="frm_form_field">
            <label for="spf_default_country_<?php echo esc_attr($field_id); ?>">
                <?php esc_html_e('Default Country', 'smart-phone-field-for-wp-forms'); ?>
                <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e('Choose default country', 'smart-phone-field-for-wp-forms'); ?>"></span>
            </label>

            <select name="field_options[spf_default_country_<?php echo esc_attr($field_id); ?>]" id="spf_default_country_<?php echo esc_attr($field_id); ?>">
                <?php foreach (PCafe_SPF_Utils::get_countries() as $code => $name) : ?>
                    <option value="<?php echo esc_attr(strtolower($code)); ?>" <?php selected(strtolower($default_country), strtolower($code)); ?>>
                        <?php echo esc_html($name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p class="frm_form_field">
            <label class="frm_toggle frm_toggle_block" for="spf_frontend_validation_<?php echo esc_attr($field_id); ?>">
                <input type="checkbox"
                    name="field_options[spf_frontend_validation_<?php echo esc_attr($field_id); ?>]"
                    id="spf_frontend_validation_<?php echo esc_attr($field_id); ?>"
                    value="1" <?php checked($frontend_validation, 1); ?> />

                <span class="frm_toggle" tabindex="0" role="switch" aria-checked="<?php echo $frontend_validation ? 'true' : 'false'; ?>">
                    <span class="frm_toggle_slider"></span>
                </span>

                <span class="frm_toggle_label"><?php esc_html_e('Enable frontend validation', 'smart-phone-field-for-wp-forms'); ?></span>
            </label>
        </p>
<?php
    }

    public function displayed_field_type($field) {
        return array(
            $this->type => true,
        );
    }

    public function front_field_input($args, $shortcode_atts) {
        $field_name = $args['field_name'];
        $html_id    = $args['html_id'];

        // Prefer current value (editing an entry) then field default.
        $value = '';
        if (isset($args['value'])) {
            $value = $args['value'];
        } elseif (isset($this->field['default_value'])) {
            $value = $this->field['default_value'];
        }

        $config              = $this->get_opt($this->field, 'spf_config', 'global');
        $default_country     = $this->get_opt($this->field, 'spf_default_country', 'us');
        $geoip               = (int) $this->get_opt($this->field, 'spf_geoip', 0);
        $frontend_validation = (int) $this->get_opt($this->field, 'spf_frontend_validation', 0);

        $attributes = ' class="frm_spf_input pcafe_spf_input" data-config="' . esc_attr($config) . '"';

        if ('custom' === $config) {
            $attributes .= ' data-init_country="' . esc_attr(strtolower($default_country)) . '"';

            if ($geoip) {
                $attributes .= ' data-geoip="1"';
            }

            if ($frontend_validation) {
                $attributes .= ' data-validation="1"';
            }
        }

        return sprintf(
            '<input type="text" id="%s" name="%s" value="%s"%s />',
            esc_attr($html_id),
            esc_attr($field_name),
            esc_attr($value),
            $attributes
        );
    }
}
