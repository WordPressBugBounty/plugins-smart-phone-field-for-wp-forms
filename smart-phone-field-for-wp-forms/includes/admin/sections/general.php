<?php
if (! defined('ABSPATH')) {
    exit;
}
?>

<div class="pcafe_spf_container">
    <div class="pcafe_spf_header">
        <div class="pcafe_spf_box">
            <div class="pcafe_spf_logo">
                <img src="<?php echo esc_url(PCAFE_SPF_URL . 'assets/img/SPF.svg'); ?>" alt="logo">
                <span>Smart Phone Field</span>
            </div>
            <div class="pcafe_menu">
                <ul class="pcafe_tab_menu">
                    <li class="tab_list" data-tabscrollnavi="general">
                        <a href="#general" class="tab_item"><?php esc_html_e('General', 'smart-phone-field-for-wp-forms'); ?></a>
                    </li>
                    <li class="tab_list" data-tabscrollnavi="settings">
                        <a href="#settings" class="tab_item"><?php esc_html_e('Settings', 'smart-phone-field-for-wp-forms'); ?></a>
                    </li>
                    <li class="tab_list" data-tabscrollnavi="features">
                        <a href="#features" class="tab_item"><?php esc_html_e('Free Vs Pro', 'smart-phone-field-for-wp-forms'); ?></a>
                    </li>
                    <li class="tab_list" data-tabscrollnavi="help">
                        <a href="#help" class="tab_item"><?php esc_html_e('Help', 'smart-phone-field-for-wp-forms'); ?></a>
                    </li>
                </ul>
                <div class="pcafe_upgrade">
                    <?php if (spf_fs()->is_not_paying()) : ?>
                        <a href="<?php echo esc_url(spf_fs()->get_upgrade_url()); ?>">Upgrade Now</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="pcafe_body_wrapper">
        <div class="pcafe_spf_content">
            <div class="pcafe_spf_tabs">
                <div id="addons" class="spf_content" data-tabscroll="general" style="display: none;">
                    <?php include plugin_dir_path(__FILE__) . 'addons_list.php'; ?>
                </div>

                <div data-tabscroll="settings" style="display: none;">
                    <?php include plugin_dir_path(__FILE__) . 'settings.php'; ?>
                </div>

                <div data-tabscroll="features" style="display: none;">
                    <?php include plugin_dir_path(__FILE__) . 'features.php'; ?>
                </div>

                <div data-tabscroll="help" style="display: none;">
                    <?php include plugin_dir_path(__FILE__) . 'help.php'; ?>
                </div>
            </div>
            <div class="pcafe_spf_sidebar_wrap">
                <div class="pcafe_spf_sidebar_content">
                    <h3 class="pcafe_spf_sidebar_title">
                        <?php esc_html_e('Power up your website', 'smart-phone-field-for-wp-forms'); ?>
                    </h3>
                    <ul class="pcafe_spf_plugin_list">
                        <?php
                        $pcafe_spf_other_products = (array) PCafe_SPF_Utils::instance()->featured_plugins();
                        foreach ($pcafe_spf_other_products as $item) :
                            $plugin_path = $item['slug'] . '/' . $item['file_name'] . '.php';
                            $installed = file_exists(WP_PLUGIN_DIR . '/' . $plugin_path);
                            $activated = $installed && is_plugin_active($plugin_path);
                        ?>
                            <li class="pcafe_spf_plugin_item">
                                <div class="pcafe_spf_plugin_img">
                                    <img src="<?php echo esc_url(PCAFE_SPF_URL . 'assets/img/' . $item['icon']); ?>" />
                                </div>
                                <div class="pcafe_spf_plugin_content">
                                    <div class="pcafe_spf_plugin_label"><?php echo esc_html($item['name']) ?></div>
                                    <p><?php echo esc_html($item['desc']) ?></p>
                                </div>
                                <div class="pcafe_spf_plugin_btn_wrap">
                                    <?php if (!$installed): ?>
                                        <button class="pcafe_spf_install_btn" data-action="install" data-plugin="<?php echo esc_attr($item['slug']); ?>" data-filename="<?php echo esc_attr($item['file_name']); ?>">
                                            <?php esc_html_e('Install', 'smart-phone-field-for-wp-forms'); ?>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.8335 5.83331H14.1668M14.1668 5.83331V14.1666M14.1668 5.83331L5.8335 14.1666" stroke="#0077FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                            <span class="loader"></span>
                                        </button>
                                    <?php elseif (!$activated): ?>
                                        <button class="pcafe_spf_install_btn activate" data-action="activate" data-plugin="<?php echo esc_attr($item['slug']); ?>" data-filename="<?php echo esc_attr($item['file_name']); ?>">
                                            Activate <span class="loader"></span>
                                        </button>
                                    <?php else: ?>
                                        <span class="pcafe_spf_install_btn plugin_status active">Activated</span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="spf_save_notification">
            <div class="spf_notification_content">
                <p><?php esc_html_e('Your changes have been saved sucessfully.', 'smart-phone-field-for-wp-forms'); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('#spf_default_country').select2();
        $('#spf_restrict_type, #spf_flag_option').select2({
            minimumResultsForSearch: Infinity
        });

        $('#spf_restrict_country').select2({
            placeholder: 'Select Country'
        });
    });
</script>