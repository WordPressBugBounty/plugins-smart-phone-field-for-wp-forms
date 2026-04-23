<?php
if (! defined('ABSPATH')) {
    exit;
}

class PCAFE_SPF_Admin_Menu {

    public function __construct() {
        add_action('admin_menu', [$this, 'pcafe_spf_add_plugin_page'], 9999);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'), 2);
        add_action('wp_ajax_spf_save_plugins_data', [$this, 'spf_save_plugins_data']);
        add_action('wp_ajax_spf_global_setting', [$this, 'spf_global_setting']);
        add_action('wp_ajax_spf_install_manage_plugin', [$this, 'spf_install_manage_plugin']);

        $this->get_required_files();
    }

    public function pcafe_spf_add_plugin_page() {

        $icon_64 = 'PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQzLjQ2NjIgNjMuMDM5MkM0My4wMzQxIDYzLjQ3MTYgNDIuNDc2MiA2My43NTYxIDQxLjg3MjQgNjMuODUxOUM0MS4yNjg3IDYzLjk0NzcgNDAuNjUwMSA2My44NSA0MC4xMDUzIDYzLjU3MjZDMzUuMDU1NCA2MC45NjkzIDMwLjQ0NTUgNTcuNTg5MiAyNi40NDQxIDUzLjU1NThDMjIuNDEwMSA0OS41NTUgMTkuMDI5OSA0NC45NDUxIDE2LjQyNzQgMzkuODk0NkMxNi4xNSAzOS4zNDk5IDE2LjA1MjIgMzguNzMxMyAxNi4xNDgxIDM4LjEyNzVDMTYuMjQzOSAzNy41MjM4IDE2LjUyODQgMzYuOTY1OSAxNi45NjA4IDM2LjUzMzhMMjQuNTc0NyAyOC45MTgyTDIuNDYxNzkgNi44MDUzQy0zLjc2ODEgMjMuMzg2OCAxLjk0NDM4IDQ1LjQzMjQgMTguMjU2OCA2MS43NDMyQzM0LjU2NzUgNzguMDU1NiA1Ni42MTMyIDgzLjc2OCA3My4xOTQ2IDc3LjUzODJMNTEuMDgxOCA1NS40MjUyTDQzLjQ2NjIgNjMuMDM5MloiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik03OS4xNTMgNzAuNDM2N0w1OS42NTkyIDUwLjk0M0M1OS4zOTA0IDUwLjY3NCA1OS4wNzEyIDUwLjQ2MDcgNTguNzE5OSA1MC4zMTUyQzU4LjM2ODcgNTAuMTY5NiA1Ny45OTIxIDUwLjA5NDcgNTcuNjExOSA1MC4wOTQ3QzU3LjIzMTcgNTAuMDk0NyA1Ni44NTUyIDUwLjE2OTYgNTYuNTAzOSA1MC4zMTUyQzU2LjE1MjYgNTAuNDYwNyA1NS44MzM1IDUwLjY3NCA1NS41NjQ2IDUwLjk0M0w1Mi4yMTUxIDU0LjI5MjZMNzQuODA4NiA3Ni44ODYxQzc2LjEgNzYuMzI5IDc3LjM1NjEgNzUuNjkzNSA3OC41Njk5IDc0Ljk4M0M3OC45NTE3IDc0Ljc1OTEgNzkuMjc3MSA3NC40NTA3IDc5LjUyMTEgNzQuMDgxNUM3OS43NjUxIDczLjcxMjIgNzkuOTIxMiA3My4yOTIgNzkuOTc3NSA3Mi44NTNDODAuMDMzOCA3Mi40MTQgNzkuOTg4OCA3MS45NjggNzkuODQ1OCA3MS41NDkxQzc5LjcwMjkgNzEuMTMwMyA3OS40NjU5IDcwLjc0OTcgNzkuMTUzIDcwLjQzNjdaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMjkuMDU5NCAyNC40MzYyQzI5LjYwMTkgMjMuODkyOSAyOS45MDY1IDIzLjE1NjYgMjkuOTA2NSAyMi4zODg5QzI5LjkwNjUgMjEuNjIxMyAyOS42MDE5IDIwLjg4NDkgMjkuMDU5NCAyMC4zNDE3TDkuNTY0MTIgMC44NDc4OTdDOS4yNTEwOCAwLjUzNDkzNSA4Ljg3MDQ3IDAuMjk3OTAzIDguNDUxNTIgMC4xNTUwMTdDOC4wMzI1NiAwLjAxMjEzMDIgNy41ODY0MyAtMC4wMzI4MDggNy4xNDc0IDAuMDIzNjU1NUM2LjcwODM3IDAuMDgwMTE4OSA2LjI4ODEzIDAuMjM2NDgxIDUuOTE4OTYgMC40ODA3MjZDNS41NDk4IDAuNzI0OTcxIDUuMjQxNTUgMS4wNTA2IDUuMDE3ODkgMS40MzI1OEM0LjMwNzEgMi42NDU2NSAzLjY3MTUyIDMuOTAxMjQgMy4xMTQ3NSA1LjE5MjI2TDI1LjcwODMgMjcuNzg1OEwyOS4wNTk0IDI0LjQzNjJaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNNTcuNTEzMSA0MS4yNDk0QzYxLjIyMzUgNDEuMjQ4OSA2NC44NTA0IDQwLjE0ODIgNjcuOTM1MiAzOC4wODY0QzcxLjAyIDM2LjAyNDYgNzMuNDI0MiAzMy4wOTQ0IDc0Ljg0MzYgMjkuNjY2MkM3Ni4yNjMxIDI2LjIzODEgNzYuNjM0MSAyMi40NjYgNzUuOTA5NyAxOC44MjdDNzUuMTg1NCAxNS4xODggNzMuMzk4MiAxMS44NDU1IDcwLjc3NDIgOS4yMjIyQzY4LjE1MDIgNi41OTg5IDY0LjgwNzIgNC44MTI2MiA2MS4xNjggNC4wODkyNEM1Ny41Mjg5IDMuMzY1ODYgNTMuNzU2OSAzLjczNzg3IDUwLjMyOTEgNS4xNTgyNEM0Ni45MDEzIDYuNTc4NiA0My45NzE3IDguOTgzNTIgNDEuOTEwOCAxMi4wNjg5QzM5Ljg0OTggMTUuMTU0MiAzOC43NSAxOC43ODE0IDM4Ljc1MDUgMjIuNDkxOEMzOC43NTY4IDI3LjQ2NTYgNDAuNzM1OCAzMi4yMzM3IDQ0LjI1MzIgMzUuNzUwMkM0Ny43NzA3IDM5LjI2NjcgNTIuNTM5MyA0MS4yNDQ0IDU3LjUxMzEgNDEuMjQ5NFpNNDcuMjU2MSAyMC43NTI3QzQ3LjU0NTUgMjAuNDYzNCA0Ny45MzggMjAuMzAwOSA0OC4zNDcyIDIwLjMwMDlDNDguNzU2NCAyMC4zMDA5IDQ5LjE0ODkgMjAuNDYzNCA0OS40MzgzIDIwLjc1MjdMNTMuODMxMiAyNS4xNDY4TDY0LjAwMDYgMTQuOTc3NEM2NC4yOTAyIDE0LjY4OTQgNjQuNjgyMiAxNC41Mjc5IDY1LjA5MDcgMTQuNTI4NUM2NS40OTkyIDE0LjUyOTEgNjUuODkwOCAxNC42OTE3IDY2LjE3OTYgMTQuOTgwN0M2Ni40NjgzIDE1LjI2OTYgNjYuNjMwNyAxNS42NjEzIDY2LjYzMTEgMTYuMDY5OEM2Ni42MzE0IDE2LjQ3ODMgNjYuNDY5OCAxNi44NzAyIDY2LjE4MTUgMTcuMTU5N0w1NC45MjIzIDI4LjQxOUM1NC43NzkxIDI4LjU2MjIgNTQuNjA5MSAyOC42NzU4IDU0LjQyMiAyOC43NTMzQzU0LjIzNDkgMjguODMwOCA1NC4wMzQzIDI4Ljg3MDcgNTMuODMxOCAyOC44NzA3QzUzLjYyOTMgMjguODcwNyA1My40Mjg3IDI4LjgzMDggNTMuMjQxNiAyOC43NTMzQzUzLjA1NDUgMjguNjc1OCA1Mi44ODQ1IDI4LjU2MjIgNTIuNzQxMyAyOC40MTlMNDcuMjU2MSAyMi45MzVDNDYuOTY2OCAyMi42NDU1IDQ2LjgwNDMgMjIuMjUzMSA0Ni44MDQzIDIxLjg0MzhDNDYuODA0MyAyMS40MzQ2IDQ2Ljk2NjggMjEuMDQyMiA0Ny4yNTYxIDIwLjc1MjdaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K';
        $icon_data_uri = 'data:image/svg+xml;base64,' . $icon_64;

        add_menu_page(
            __('Smart Phone Field', 'smart-phone-field-for-wp-forms'),
            __('SmartPhone Field', 'smart-phone-field-for-wp-forms'),
            'manage_options',
            'smart-phone-field-pro',
            array($this, 'pcafe_spf_admin_page'),
            $icon_data_uri
        );
    }

    public function pcafe_spf_admin_page() {
        include plugin_dir_path(__FILE__) . 'sections/general.php';
    }

    public function get_required_files() {
        include PCAFE_SPF_PATH . 'includes/admin/utils.php';
        include PCAFE_SPF_PATH . 'includes/addons/addons.php';
    }

    public function enqueue_admin_scripts($screen) {

        $current_screen = get_current_screen();

        if (strpos($current_screen->base, 'smart-phone-field') !== false) {
            wp_enqueue_style('pcafe_spf_select2', PCAFE_SPF_URL . 'assets/css/select2.min.css', array(), PCAFE_SPF_VERSION);
            wp_enqueue_style('pcafe_spf_admin_style', PCAFE_SPF_URL . 'assets/css/admin_style.css', array(), PCAFE_SPF_VERSION);

            wp_enqueue_script('pcafe_spf_select2', PCAFE_SPF_URL . '/assets/js/select2.min.js', array(), PCAFE_SPF_VERSION, true);
            wp_enqueue_script('pcafe_spf_admin', PCAFE_SPF_URL . '/assets/js/admin.js', array(), PCAFE_SPF_VERSION, true);
            wp_localize_script('pcafe_spf_admin', 'pcafe_spf_admin', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('pcafe_spf_nonce'),
            ));
        }

        wp_enqueue_script('pcafe_spf_forms', PCAFE_SPF_URL . '/assets/js/spf_forms.js', array(), PCAFE_SPF_VERSION, true);
    }

    public function spf_save_plugins_data() {
        if (!isset($_POST['spf_plugin_addon']) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['spf_plugin_addon'])), 'spf_plugin_addon')) {
            return;
        }

        if (isset($_POST['addon_list'])) {
            $sanitized_addon_list = is_array($_POST['addon_list'])
                ? array_map('sanitize_text_field', wp_unslash($_POST['addon_list']))
                : sanitize_text_field(wp_unslash($_POST['addon_list']));

            update_option('pcafe_spf_plugin_list', $sanitized_addon_list);
        } else {
            $sanitized_addon_list = [];
            update_option('pcafe_spf_plugin_list', $sanitized_addon_list);
        }
        wp_die();
    }

    public function spf_global_setting() {
        if (!isset($_POST['spf_setting_nonce']) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['spf_setting_nonce'])), 'spf_setting_nonce')) {
            return;
        }

        if (isset($_POST['action'])) {
            $data = $_POST;
            unset($data['spf_setting_nonce']);
            unset($data['_wp_http_referer']);
            unset($data['action']);

            $sanitized_settings = map_deep($data, 'sanitize_text_field');

            update_option('pcafe_spf_global_setting', $sanitized_settings);
        }
        wp_die();
    }

    public function spf_install_manage_plugin() {
        check_ajax_referer('pcafe_spf_nonce', 'security');

        if (!current_user_can('install_plugins')) {
            wp_send_json_error('You do not have permission to perform this action.');
        }

        $plugin_slug     = isset($_POST['plugin_slug']) ? sanitize_text_field(wp_unslash($_POST['plugin_slug'])) : '';
        $filename = isset($_POST['filename']) ? sanitize_text_field(wp_unslash($_POST['filename'])) : '';
        $plugin_action   = isset($_POST['plugin_action']) ? sanitize_text_field(wp_unslash($_POST['plugin_action'])) : '';


        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        if ($plugin_action === 'install') {
            $api = plugins_api('plugin_information', ['slug' => $plugin_slug]);

            if (is_wp_error($api)) {
                wp_send_json_error($api->get_error_message());
            }

            $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());
            $install_result = $upgrader->install($api->download_link);

            if (is_wp_error($install_result)) {
                wp_send_json_error($install_result->get_error_message());
            }

            wp_send_json_success(['message' => 'Installed successfully.']);
        }

        if ($plugin_action === 'activate') {
            $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_slug . '/' . $filename . '.php';

            if (!file_exists($plugin_path)) {
                wp_send_json_error('Plugin file not found.');
            }

            $activate_result = activate_plugin($plugin_path);

            if (is_wp_error($activate_result)) {
                wp_send_json_error($activate_result->get_error_message());
            }

            wp_send_json_success(['message' => 'Activated successfully.']);
        }

        wp_send_json_error('Invalid action.');
    }
}

new PCAFE_SPF_Admin_Menu();
