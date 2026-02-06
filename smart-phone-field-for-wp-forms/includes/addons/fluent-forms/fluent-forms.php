<?php
if (! defined('ABSPATH')) {
    exit;
}

class PCAFE_SPF_Fluent_Forms {
    public function boot() {
        if (!defined('FLUENTFORM')) {
            return $this->injectDependency();
        }

        $this->includeFiles();
        new SPF_Fluent_Forms_Field();
    }

    function includeFiles() {
        include PCAFE_SPF_PATH . 'includes/addons/fluent-forms/field.php';
    }

    protected function injectDependency() {
        add_action('admin_notices', function () {
            $pluginInfo = $this->getFluentFormInstallationDetails();

            $class = 'notice notice-error';

            $install_url_text = __('Click Here to Install the Plugin', 'smart-phone-field-for-wp-forms');

            if ($pluginInfo->action == 'activate') {
                $install_url_text = __('Click Here to Activate the Plugin', 'smart-phone-field-for-wp-forms');
            }

            printf(
                /* translators: 1: class, 2: plugin url, 3: installation text */
                '<div class="%1$s"><p>Smart Phone Field For Fluent Forms Requires Fluent Forms Plugin, <b><a href="%2$s">%3$s</a></b></p></div>',
                esc_attr($class),
                esc_url($pluginInfo->url),
                esc_attr($install_url_text)
            );
        });
    }

    protected function getFluentFormInstallationDetails() {
        $activation = (object)[
            'action' => 'install',
            'url'    => ''
        ];

        $allPlugins = get_plugins();

        if (isset($allPlugins['fluentform/fluentform.php'])) {
            $url = wp_nonce_url(
                self_admin_url('plugins.php?action=activate&plugin=fluentform/fluentform.php'),
                'activate-plugin_fluentform/fluentform.php'
            );

            $activation->action = 'activate';
        } else {
            $api = (object)[
                'slug' => 'fluentform'
            ];

            $url = wp_nonce_url(
                self_admin_url('update.php?action=install-plugin&plugin=' . $api->slug),
                'install-plugin_' . $api->slug
            );
        }

        $activation->url = $url;

        return $activation;
    }
}

add_action('plugins_loaded', function () {
    (new PCAFE_SPF_Fluent_Forms())->boot();
});
