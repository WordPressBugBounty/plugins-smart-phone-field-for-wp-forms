<?php

/** 
 * Plugin Name: Smart Phone Field
 * Plugin URI: https://pluginscafe.com/plugin/smart-phone-field
 * Version: 1.0.7
 * Description: Instruct visitors to choose country code when entering their mobile number to ensure accurate and correctly formatted data submissions.
 * Author: Pluginscafe
 * Author URI: https://pluginscafe.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: smart-phone-field-for-wp-forms
 * Domain Path: /languages/
 */
if (!defined('ABSPATH')) {
    exit;
}

if (function_exists('spf_fs')) {
    spf_fs()->set_basename(false, __FILE__);
} else {
    if (! function_exists('spf_fs')) {
        // Create a helper function for easy SDK access.
        function spf_fs() {
            global $spf_fs;

            if (! isset($spf_fs)) {
                // Include Freemius SDK.
                require_once dirname(__FILE__) . '/vendor/freemius/start.php';

                $spf_fs = fs_dynamic_init(array(
                    'id'                  => '28140',
                    'slug'                => 'smart-phone-field-for-wp-forms',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_1fdc751968a7a371a7a3606db7509',
                    'is_premium'          => false,
                    'has_addons'          => false,
                    'premium_suffix'      => 'Pro',
                    'has_paid_plans'      => true,
                    'menu'                => array(
                        'slug'           => 'smart-phone-field-pro',
                        'first-path'     => 'admin.php?page=smart-phone-field-pro',
                        'support'        => false,
                        'contact'       => false,
                    ),
                    'is_live'        => true,
                ));
            }

            return $spf_fs;
        }

        // Init Freemius.
        spf_fs();
        // Signal that SDK was initiated.
        do_action('spf_fs_loaded');
    }
}

class PCafe_Smart_Phone_Field {
    const version = '1.0.7';
    public function __construct() {
        define('PCAFE_SPF_PATH', plugin_dir_path(__FILE__));
        define('PCAFE_SPF_URL', plugin_dir_url(__FILE__));
        define('PCAFE_SPF_VERSION', self::version);

        add_action('wp_enqueue_scripts', [$this, 'pcafe_spf_enqueue_scripts']);
        add_action('activated_plugin', array($this, 'pcafe_spf_plugin_redirection'));
        register_activation_hook(__FILE__,   [$this, 'pcafe_spf_activation']);
        add_action('admin_init', [$this, 'redirect_after_update']);

        add_action('wp_head', [$this, 'pcafe_spf_global_setting']);
        $this->loads_field();
    }

    public function pcafe_spf_enqueue_scripts() {
        if (! PCafe_SPF_Utils::instance()->active_addon_list()) return;

        wp_enqueue_style('pcafe_spf_intl', PCAFE_SPF_URL . 'assets/css/intlTelInput2.css', array(), PCAFE_SPF_VERSION);
        wp_enqueue_style('pcafe_spf_style', PCAFE_SPF_URL . 'assets/css/spf_style.css', array(), PCAFE_SPF_VERSION);

        wp_enqueue_script('pcafe_spf_intl', PCAFE_SPF_URL . 'assets/js/intlTelInputWithUtils.min.js', array(), PCAFE_SPF_VERSION, false);
    }

    public function pcafe_spf_global_setting() {
        if (! PCafe_SPF_Utils::instance()->active_addon_list()) return;
?>
        <script>
            const pcafe_spf_global_setting = <?php echo wp_json_encode(PCafe_SPF_Utils::instance()->get_settings()); ?>
        </script>
<?php
    }

    public function loads_field() {
        include PCAFE_SPF_PATH . "includes/admin/dashboard.php";
    }

    public function pcafe_spf_plugin_redirection($plugin) {
        if ($plugin == plugin_basename(__FILE__)) {
            wp_safe_redirect(esc_url(admin_url('admin.php?page=smart-phone-field-pro')));
            exit;
        }
    }

    public function pcafe_spf_activation() {
        $saved_addon    = get_option('pcafe_spf_plugin_list');
        $saved_settings = get_option('pcafe_spf_global_setting');
        $installed = get_option('pcafe_spf_installed');

        update_option('pcafe_spf_version', PCAFE_SPF_VERSION);

        if (!$installed) {
            update_option('pcafe_spf_installed', time());
        }

        if (! $saved_addon) {
            $addon = ['contact-form-7'];
            update_option('pcafe_spf_plugin_list', $addon);
        }

        if (! $saved_settings) {
            $settings = ['spf_geoip' => 'on', 'spf_default_country' => 'US', 'spf_country_search' => 'on'];
            update_option('pcafe_spf_global_setting', $settings);
        }
    }

    public function redirect_after_update() {
        if (! is_admin() || ! current_user_can('manage_options')) {
            return;
        }

        $saved_version   = get_option('pcafe_spf_version');
        $current_version = PCAFE_SPF_VERSION;
        $should_redirect = false;

        if ($saved_version === false) {
            $should_redirect = true;
        } elseif (version_compare($saved_version, $current_version, '<')) {
            $should_redirect = true;
        }

        if ($should_redirect) {
            update_option('pcafe_spf_version', $current_version);
            $redirect_url = admin_url('admin.php?page=smart-phone-field-pro');
            wp_safe_redirect($redirect_url);
            exit;
        }
    }
}

new PCafe_Smart_Phone_Field();
