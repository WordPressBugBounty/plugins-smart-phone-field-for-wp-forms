; (function ($) {
    'use strict';

    $(document).ready(function () {
        $('.custom_config').hide();

        $(document).on('change', '#config', function () {
            let configOption = $(this).val();

            if (configOption != 'global') {
                $('.custom_config').show();
            } else {
                $('.custom_config').hide();
            }
        });


        function updateFor($wrap) {
            const $select   = $wrap.find('.wpforms-field-option-row-configuration select');
            const config    = $select.val();
            const isGlobal  = (config === 'global');
            $wrap.find('.wpforms-field-option-row-geoip').toggle(!isGlobal);
            $wrap.find('.wpforms-field-option-row-default_country').toggle(!isGlobal);
            $wrap.find('.wpforms-field-option-row-front_validation').toggle(!isGlobal);
        }
        // initial pass (in case already present)
        $('.wpforms-field-option-spf_phone').each(function () {
            updateFor($(this));
        });
        // delegated change handler (works even if select is created/replaced later)
        $(document).on(
            'change',
            '.wpforms-field-option-spf_phone .wpforms-field-option-row-configuration select',
            function () {
            updateFor($(this).closest('.wpforms-field-option-spf_phone'));
            }
        );

        /* Formidable Forms */
        function applySpfConditionByConfigSelect($configSelect) {
            const configId = $configSelect.attr('id') || '';
            const suffix = configId.replace('spf_config_', ''); // field_id part
            if (!suffix) return;
            const isGlobal = ($configSelect.val() === 'global');
            // Find the <p> wrappers that contain the related controls
            const $geoipRow   = $('#spf_geoip_' + suffix).closest('p.frm_form_field');
            const $countryRow = $('#spf_default_country_' + suffix).closest('p.frm_form_field');
            const $frontRow   = $('#spf_frontend_validation_' + suffix).closest('p.frm_form_field');
            // Hide when global, show when custom
            $geoipRow.toggle(!isGlobal);
            $countryRow.toggle(!isGlobal);
            $frontRow.toggle(!isGlobal);
        }
        // Initial run for all existing fields
        $('select[id^="spf_config_"]').each(function () {
            applySpfConditionByConfigSelect($(this));
        });
        // Change handler (delegated so it works if rows are injected/replaced later)
        $(document).on('change', 'select[id^="spf_config_"]', function () {
            applySpfConditionByConfigSelect($(this));
        });


    });

})(jQuery);