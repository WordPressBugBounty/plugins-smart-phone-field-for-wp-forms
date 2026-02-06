class PCAFE_SPF_Elementor_Form extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                intlInputId: '.smart_phone_field',
                globalConfig: pcafe_spf_global_setting ? pcafe_spf_global_setting : {}
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $intlInputId: this.$element.find(selectors.intlInputId),
            $globalConfig: selectors.globalConfig
        };
    }

    bindEvents() {
        this.telInput = new Array();

        this.init = {};
        this.getInputIntlData();
        this.initSmartPhoneField();
        this.addCountryCodeInputHandler();
    }

    initSmartPhoneField() {
        this.telInput.forEach(element => {
            this.initTelInput(element);
        });
    }

    initTelInput(element) {
        const inputId = document.getElementById(element.inputId);
        const itiOptions = {
            initialCountry: element.initialCountry,
            countrySearch: element.countrySearch,
            containerClass: 'pcafe_spf_container',
            useFullscreenPopup: false,
            formatAsYouType: false,
            formatOnDisplay: false,
            nationalMode: false,
            autoHideDialCode: false
        };

        if (element.restrictType == 'exclude') {
            itiOptions.excludeCountries = element.dropdwonCoutnries;
        }

        if (element.restrictType == 'include') {
            itiOptions.onlyCountries = element.dropdwonCoutnries;
        }

        if (element.geoIp || element.initialCountry == 'auto') {
            var defaultCountry = element.initialCountry.toString().toLowerCase();
            var api_url = element.ipinfoToken ? `https://ipinfo.io?token=${element.ipinfoToken}` : "https://ipinfo.io/json";
            itiOptions.initialCountry = 'auto';
            itiOptions.geoIpLookup = function (callback) {
                fetch(api_url)
                    .then(r => r.json())
                    .then(data => {
                        const country = (data && data.country) ? data.country.toLowerCase() : defaultCountry;
                        callback(country);
                    })
                    .catch(() => callback(defaultCountry));
            };
        }

        const iti = window.intlTelInput(inputId, itiOptions);

        this.init[element.inputId] = iti;

        inputId.addEventListener('keypress', function (e) {
            var charCode = e.which ? e.which : e.keyCode;
            if (String.fromCharCode(charCode).match(/[^0-9+]/g)) {
                e.preventDefault();
            }
        });

        if (element.frontendValidation) {
            inputId.addEventListener('blur', (e) => {
                this.validateNumber(inputId, iti);
            });

            inputId.addEventListener('keyup', (e) => {
                this.formatValidation(inputId, iti);
            });
        }

    }

    addCountryCodeInputHandler() {
        const itiArr = this.init;

        Object.keys(itiArr).forEach(key => {
            const iti = itiArr[key];
            const inputElement = iti.telInput;

            const handleCountryChange = (event) => {

                const currentCountryData = iti.getSelectedCountryData();
                const currentCode = `+${currentCountryData.dialCode}`;

                this.updateCountryCodeHandler(event.currentTarget, currentCode);
            }

            inputElement.addEventListener('keydown', handleCountryChange);
            inputElement.addEventListener('input', handleCountryChange);
            inputElement.addEventListener('countrychange', handleCountryChange);
        });

    }

    updateCountryCodeHandler(input, currentCode) {
        let value = input.value;

        if (currentCode && '+undefined' === currentCode || ['', '+'].includes(value)) {
            return;
        }

        if (!value.startsWith(currentCode)) {
            value = value.replace(/\+/g, '');
            input.value = currentCode + value;
        }
    }

    validateNumber(inputId, iti) {
        const isValid = iti.isValidNumber();

        if (inputId.value) {
            this.showValidationIcon(inputId, isValid);
        } else {
            this.hideValidationIcon(inputId);
        }
    }

    showValidationIcon(telInput, validity) {
        const containerId = telInput.closest('.pcafe_spf_container');

        if (validity) {
            containerId.classList.remove('invalid');
            containerId.classList.add('valid');
        } else {
            containerId.classList.add('invalid');
            containerId.classList.remove('valid');
        }
    }

    wrongValidation(telInput) {
        const containerId = telInput.closest('.pcafe_spf_container');
        containerId.classList.add('invalid');
        containerId.classList.remove('valid');
    }

    hideValidationIcon(telInput) {
        const containerId = telInput.closest('.pcafe_spf_container');
        containerId.classList.remove('valid');
        containerId.classList.remove('invalid');
    }

    formatValidation(inputId, iti) {
        const isValid = iti.isValidNumber();

        this.hideValidationIcon(inputId);

        if (inputId.value) {
            if (isValid) {
                this.showValidationIcon(inputId, true);
            }
        }
    }

    getInputIntlData() {
        const intlInputElement = this.elements.$intlInputId;

        let globalOptions = this.elements.$globalConfig;

        intlInputElement.each((_, ele) => {
            const options = {};
            const inputId = ele.id;
            const country = jQuery(ele).data('init-country') ? jQuery(ele).data('init-country') : 'us';
            const config = jQuery(ele).data('config') ? jQuery(ele).data('config') : 'global';
            const geoIp = jQuery(ele).data('geoip') ? true : 0;
            const frontendValidation = jQuery(ele).data('fv') ? true : false;

            if (config == 'global') {
                options.initialCountry = globalOptions.spf_default_country;
                options.geoIp = globalOptions.spf_geoip ? true : false;
                options.frontendValidation = globalOptions.spf_frontend_validation ? true : false;
            } else {
                options.frontendValidation = frontendValidation;
                options.geoIp = geoIp;
                options.initialCountry = country;
            }

            options.countrySearch = globalOptions.spf_country_search ? true : false;
            options.restrictType = globalOptions.spf_restrict_type;
            options.dropdwonCoutnries = globalOptions.spf_restrict_country;
            options.ipinfoToken = globalOptions.spf_ipinfo_token;

            this.telInput.push({ inputId, ...options });
        });
    }

}

jQuery(window).on('elementor/frontend/init', () => {

    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(PCAFE_SPF_Elementor_Form, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/form.default', addHandler);
});
