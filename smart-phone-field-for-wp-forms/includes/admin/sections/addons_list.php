<?php
if (! defined('ABSPATH')) exit;

?>
<form method="POST" class="spf_addon_form">
    <?php wp_nonce_field('spf_plugin_addon', 'spf_plugin_addon'); ?>
    <div class="addons_wrapper">
        <?php

        $checked_plugin = get_option('pcafe_spf_plugin_list', []);

        foreach (PCafe_SPF_Utils::instance()->addon_list() as $addon) :

            $checked = in_array($addon['slug'], $checked_plugin) ? 'checked' : '';
            $is_pro = (isset($addon['is_pro']) && $addon['is_pro']) ? 'pro' : 'free';
        ?>
            <div class="spf__addon_item <?php echo esc_attr($is_pro); ?>">
                <div class="spf__addon_head">
                    <h4 class="spf_addon_name"><?php echo esc_html($addon['name']); ?></h4>

                    <div class="spf__input_switch">
                        <input type="checkbox" name="addon_list[]" id="spf__<?php echo esc_html($addon['slug']); ?>" value="<?php echo esc_html($addon['slug']); ?>" <?php echo esc_attr($checked); ?>>
                        <label for="spf__<?php echo esc_html($addon['slug']); ?>"></label>
                    </div>
                </div>
                <div class="spf__addon_footer">
                    <div class="addon_info">
                        <?php if (isset($addon['demo']) && !empty($addon['demo'])) : ?>
                            <a href="<?php echo esc_html($addon['demo']); ?>" class="single_info demo" target="_blank">
                                <?php if ($addon['slug'] == 'woo-commerce'): ?>
                                    <?php esc_html_e('Demo', 'smart-phone-field-for-wp-forms'); ?>
                                <?php else : ?>
                                    <?php esc_html_e('Pro Demo', 'smart-phone-field-for-wp-forms'); ?>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                        <?php if (isset($addon['doc']) && !empty($addon['doc'])) : ?>
                            <a href="<?php echo esc_html($addon['doc']); ?>" class="single_info doc" target="_blank">
                                <?php esc_html_e('Docs', 'smart-phone-field-for-wp-forms'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="spf_submit_wrap">
        <button class="spf_submit"><?php esc_html_e('Save Settings', 'smart-phone-field-for-wp-forms'); ?><span class="loader"></span></button>
    </div>
</form>