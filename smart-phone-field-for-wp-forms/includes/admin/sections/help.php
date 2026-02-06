<?php
if (! defined('ABSPATH')) {
    exit;
}
?>

<div class="pcafe_spf_contianer">
    <div class="section_heading">
        <h2><?php echo esc_html__('Need Help?', 'smart-phone-field-for-wp-forms'); ?></h2>
        <p><?php echo esc_html__('Read our knowledge base documentation or you can contact us.', 'smart-phone-field-for-wp-forms'); ?></p>
    </div>
    <div class="helps_wrapper">
        <?php
        $help_items = PCafe_SPF_Utils::instance()->help_items();
        if ($help_items) :
            foreach ($help_items as $key => $item) :
                $img_url = $item['path'] ? $item['path'] . $item['icon'] : PCAFE_SPF_URL . 'assets/img/' . $item['icon'];
        ?>
                <div class="helps_box">
                    <div class="help_img">
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                    </div>
                    <div class="helps_content">
                        <h3><?php echo esc_html($item['name']); ?></h3>
                        <p><?php echo esc_html($item['desc']); ?></p>
                        <div class="buttons">
                            <?php if (!empty($item['url'])): ?>
                                <a href="<?php echo esc_url($item['url']); ?>" class="spf_button_solid" target="_blank">
                                    <?php echo esc_html($item['btn_text']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

        <?php endforeach;
        endif; ?>
    </div>
</div>