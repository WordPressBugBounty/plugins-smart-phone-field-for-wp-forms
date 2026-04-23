<?php
if (! defined('ABSPATH')) exit;

$features = [
    [
        'feature'   => __('Live/Frontend validation', 'smart-phone-field-for-wp-forms'),
        'pro'       => 0
    ],
    [
        'feature'   => __('Determine by visitor location - GeoIP', 'smart-phone-field-for-wp-forms'),
        'pro'       => 0
    ],
    [
        'feature'   => __('Default country selection', 'smart-phone-field-for-wp-forms'),
        'pro'       => 0
    ],
    [
        'feature'   => __('Country search', 'smart-phone-field-for-wp-forms'),
        'pro'       => 0
    ],
    [
        'feature'   => __('Prevent submit form with wrong validation', 'smart-phone-field-for-wp-forms'),
        'pro'       => true
    ],
    [
        'feature'   => __('Custom validation message', 'smart-phone-field-for-wp-forms'),
        'pro'       => true
    ],
    [
        'feature'   => __('Phone number format with typing', 'smart-phone-field-for-wp-forms'),
        'pro'       => true
    ],
    [
        'feature'   => __('Phone number format in 4 different types. Ex: E.164 and more.', 'smart-phone-field-for-wp-forms'),
        'pro'       => true
    ],
    [
        'feature'   => __('Three flag option', 'smart-phone-field-for-wp-forms'),
        'pro'       => true
    ],
    [
        'feature'   => __('Strict mode', 'smart-phone-field-for-wp-forms'),
        'pro'       => true
    ],
    [
        'feature'   => __('Translation support (upcomming)', 'smart-phone-field-for-wp-forms'),
        'pro'       => true
    ],
    [
        'feature'   => __('RTL support (upcomming)', 'smart-phone-field-for-wp-forms'),
        'pro'       => true
    ],
];

?>
<div id="pro" class="pro_introduction tab_item">
    <div class="content_heading">
        <h2><?php esc_html_e('Unlock the full power of Smart Phone Field', 'smart-phone-field-for-wp-forms'); ?></h2>
        <p><?php esc_html_e('The amazing PRO features will make your Smart Phone Field even more efficient.', 'smart-phone-field-for-wp-forms'); ?></p>
    </div>

    <div class="content_heading free_vs_pro">
        <h2>
            <span><?php esc_html_e('Free', 'smart-phone-field-for-wp-forms'); ?></span>
            <?php esc_html_e('vs', 'smart-phone-field-for-wp-forms'); ?>
            <span><?php esc_html_e('Pro', 'smart-phone-field-for-wp-forms'); ?></span>
        </h2>
    </div>

    <div class="features_list">
        <div class="list_header">
            <div class="feature_title"><?php esc_html_e('Feature List', 'smart-phone-field-for-wp-forms'); ?></div>
            <div class="feature_free"><?php esc_html_e('Free', 'smart-phone-field-for-wp-forms'); ?></div>
            <div class="feature_pro"><?php esc_html_e('Pro', 'smart-phone-field-for-wp-forms'); ?></div>
        </div>
        <?php foreach ($features as $feature) : ?>
            <div class="feature">
                <div class="feature_title"><?php echo esc_html($feature['feature']); ?></div>
                <div class="feature_free">
                    <?php if ($feature['pro']) : ?>
                        <i class="dashicons dashicons-no-alt"></i>
                    <?php else : ?>
                        <i class="dashicons dashicons-saved"></i>
                    <?php endif; ?>
                </div>
                <div class="feature_pro">
                    <i class="dashicons dashicons-saved"></i>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>