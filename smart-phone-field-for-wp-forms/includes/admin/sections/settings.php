<?php
if (! defined('ABSPATH')) {
    exit;
}

$spf_settings = get_option('pcafe_spf_global_setting', []);
$frontend_validation = isset($spf_settings['spf_frontend_validation']) ? 'checked' : '';
$country_search = isset($spf_settings['spf_country_search']) ? 'checked' : '';
$default_country = isset($spf_settings['spf_default_country']) ? $spf_settings['spf_default_country'] : '';
$spf_geoip = isset($spf_settings['spf_geoip']) ? 'checked' : '';
$restrict_countries = isset($spf_settings['spf_restrict_country']) ? $spf_settings['spf_restrict_country'] : [];
$restrict_type = isset($spf_settings['spf_restrict_type']) ? $spf_settings['spf_restrict_type'] : 'all';
$flag_option = isset($spf_settings['spf_flag_option']) ? $spf_settings['spf_flag_option'] : 'flag';
$ipinfo_token = isset($spf_settings['spf_ipinfo_token']) ? $spf_settings['spf_ipinfo_token'] : '';
?>

<form class="spf_settings_page" method="post">
    <?php wp_nonce_field('spf_setting_nonce', 'spf_setting_nonce'); ?>
    <div class="spf_setting_wapper">
        <div class="single_setting">
            <div class="setting_title">
                <h3><?php esc_html_e('Determine by visitor location (GeoIP)', 'smart-phone-field-for-wp-forms'); ?></h3>
                <p><?php esc_html_e('Enable this option to automatically detect the user’s country based on their IP address.', 'smart-phone-field-for-wp-forms'); ?></p>
            </div>
            <div class="setting_input">
                <input type="checkbox" name="spf_geoip" id="spf_geoip" <?php echo esc_attr($spf_geoip); ?>>
                <label for="spf_geoip"></label>
            </div>
        </div>
        <div class="single_setting ipinfo_token">
            <div class="setting_title">
                <h3><?php esc_html_e('Ipinfo token (Optional)', 'smart-phone-field-for-wp-forms'); ?></h3>
                <p><?php
                    printf(
                        wp_kses_post(
                            /* translators: %s: URL to the ipinfo signup page */
                            __('Please add an Ipinfo token if GeoIP is not functioning as expected. Please <a href="%s" target="_blank">click here</a> to create an Ipinfo account. It’s free for 50,000 requests per month.', 'smart-phone-field-for-wp-forms')
                        ),
                        esc_url('https://ipinfo.io/dashboard')
                    );
                    ?></p>
            </div>
            <div class="setting_input">
                <input type="text" name="spf_ipinfo_token" id="spf_ipinfo_token" value="<?php echo esc_attr($ipinfo_token); ?>">
            </div>
        </div>
        <div class="single_setting">
            <div class="setting_title">
                <h3><?php esc_html_e('Default Country', 'smart-phone-field-for-wp-forms'); ?></h3>
                <p><?php esc_html_e('Choose the default country to be used for the country code selector when GeoIP is disabled.', 'smart-phone-field-for-wp-forms'); ?></p>
            </div>
            <div class="setting_input">
                <select name="spf_default_country" id="spf_default_country">
                    <?php foreach (PCafe_SPF_Utils::get_countries() as $key => $country) : ?>
                        <option value="<?php echo esc_html($key); ?>" <?php echo esc_html($key == $default_country ? 'selected' : ''); ?>><?php echo esc_html($country); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="single_setting">
            <div class="setting_title">
                <h3><?php esc_html_e('Dropdown Countries', 'smart-phone-field-for-wp-forms'); ?></h3>
                <p><?php esc_html_e('Select the format for displaying countries in the dropdown menu.', 'smart-phone-field-for-wp-forms'); ?></p>
            </div>
            <div class="setting_input">
                <select name="spf_restrict_type" id="spf_restrict_type">
                    <option value="all" <?php echo esc_html($restrict_type == 'all' ? 'selected' : ''); ?>>Include all countries</option>
                    <option value="exclude" <?php echo esc_html($restrict_type == 'exclude' ? 'selected' : ''); ?>>Exclude the following countries</option>
                    <option value="include" <?php echo esc_html($restrict_type == 'include' ? 'selected' : ''); ?>>Only include the following countries</option>
                </select>
            </div>
            <div class="setting_input dep_on_restrict">
                <select name="spf_restrict_country[]" id="spf_restrict_country" multiple="multiple">
                    <?php foreach (PCafe_SPF_Utils::get_countries() as $key => $country) : ?>
                        <option value="<?php echo esc_html($key); ?>" <?php echo esc_html(in_array($key, $restrict_countries) ? 'selected' : ''); ?>><?php echo esc_html($country); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="single_setting">
            <div class="setting_title">
                <h3><?php esc_html_e('Frontend Validation', 'smart-phone-field-for-wp-forms'); ?></h3>
                <p><?php esc_html_e('Enable this option to display frontend validation on the input field as the user types.', 'smart-phone-field-for-wp-forms'); ?></p>
            </div>
            <div class="setting_input">
                <input type="checkbox" name="spf_frontend_validation" id="spf_frontend_validation" <?php echo esc_attr($frontend_validation); ?>>
                <label for="spf_frontend_validation"></label>
            </div>
        </div>
        <div class="single_setting">
            <div class="setting_title">
                <h3><?php esc_html_e('Country Search', 'smart-phone-field-for-wp-forms'); ?></h3>
                <p><?php esc_html_e('Enable this option to include a search input at the top of the dropdown, allowing users to filter the list of displayed countries.', 'smart-phone-field-for-wp-forms'); ?></p>
            </div>
            <div class="setting_input">
                <input type="checkbox" name="spf_country_search" id="spf_country_search" <?php echo esc_attr($country_search); ?>>
                <label for="spf_country_search"></label>
            </div>
        </div>
        <?php do_action('spf_global_settings', $spf_settings); ?>
    </div>
    <div class="spf_submit_wrap">
        <button class="spf_submit"><?php esc_html_e('Save Settings', 'smart-phone-field-for-wp-forms'); ?><span class="loader"></span></button>
    </div>
</form>