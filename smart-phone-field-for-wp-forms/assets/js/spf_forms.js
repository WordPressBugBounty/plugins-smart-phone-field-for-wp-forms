; (function ($) {
'use strict';

    $(document).ready(function () { 
        $('.custom_config').hide();

        $(document).on('change', '#config', function () {
            let configOption = $(this).val();

            if( configOption != 'global' ) {
                $('.custom_config').show();
            } else {
                $('.custom_config').hide();
            }
        });

        // let wpforms_config = $('.wpforms-field-option-row-configuration select').val();

        // if( wpforms_config == 'global') {
        //     $('.wpforms-field-option-row-geoip, .wpforms-field-option-row-default_country, .wpforms-field-option-row-front_validation').hide();
        // } else {
        //     $('.wpforms-field-option-row-geoip, .wpforms-field-option-row-default_country, .wpforms-field-option-row-front_validation').show();
        // }
        
        // $(document).on('change', '.wpforms-field-option-row-configuration select', function() {
        //     let config = $(this).val();

        //     if( config == 'global') {
        //         $('.wpforms-field-option-row-geoip, .wpforms-field-option-row-default_country, .wpforms-field-option-row-front_validation').hide();
        //     } else {
        //         $('.wpforms-field-option-row-geoip, .wpforms-field-option-row-default_country, .wpforms-field-option-row-front_validation').show();
        //     }
        // });


        jQuery('.wpforms-field-option-spf_phone').each( function(i, e) {

            let main_btn = jQuery(this).find('.wpforms-field-option-row-configuration select'),
                main_btn_value = main_btn.val(),
                geoip_box = jQuery(this).find('.wpforms-field-option-row-geoip'),
                deCou_box = jQuery(this).find('.wpforms-field-option-row-default_country'),
                front_box = jQuery(this).find('.wpforms-field-option-row-front_validation');

            showHide(main_btn_value);

            jQuery(main_btn).on('change', function() {
                let config = jQuery(this).val();
                showHide(config);
            });

            function showHide( config ) {
                if( config == 'global') {
                    geoip_box.hide();
                    deCou_box.hide();
                    front_box.hide();
                } else {
                    geoip_box.show();
                    deCou_box.show();
                    front_box.show();
                }
            }
        });

    });

})(jQuery);