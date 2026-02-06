; (function ($) {
    'use strict';
    $(document).ready(function () {
        $('.wpcf7-smart_phone_field').each(function () {
            var $this = $(this),
                initCountry = $this.data('init_country'),
                config = $this.data('config'),
                global = pcafe_spf_global_setting,
                options = {},
                inputId = $this.attr('id');

            options.useFullscreenPopup = false;
            options.autoPlaceholder = false;

            options.initialCountry = config == 'global' ? global.spf_default_country : initCountry;
            options.countrySearch = global.spf_country_search ? true : false;

            if (global.spf_restrict_type == 'exclude') {
                options.excludeCountries = global.spf_restrict_country;
            }

            if (global.spf_restrict_type == 'include') {
                options.onlyCountries = global.spf_restrict_country;
            }

            let input = document.querySelector('#' + inputId);

            window.intlTelInput(input, options);
        });
    });
})(jQuery);