<?php
if (! defined('ABSPATH')) {
    exit;
}

class PCafe_SPF_Utils {

    private static $instance = null;

    public function addon_list() {
        return apply_filters('pcafe_spf_addon_list', [
            'wp-forms' => [
                'name'      => 'WPForms',
                'slug'      => 'wp-forms',
                'path'      => '',
                'demo'      => 'https://demo.pluginscafe.com/smart-phone-field/',
                'doc'       => 'https://pluginscafe.com/docs/smart-phone-field/',
            ],
            'fluent-forms' => [
                'name'      => 'Fluent Forms',
                'slug'      => 'fluent-forms',
                'path'      => '',
                'demo'      => 'https://demo.pluginscafe.com/smart-phone-field/',
                'doc'       => 'https://pluginscafe.com/docs/smart-phone-field/',
            ],
            'elementor-form' => [
                'name'      => 'Elementor Form',
                'slug'      => 'elementor-form',
                'path'      => '',
                'demo'      => 'https://demo.pluginscafe.com/smart-phone-field/',
                'doc'       => 'https://pluginscafe.com/docs/smart-phone-field/',
            ],
            'contact-form-7' => [
                'name'      => 'Contact Form 7',
                'slug'      => 'contact-form-7',
                'path'      => '',
                'demo'      => 'https://demo.pluginscafe.com/smart-phone-field/',
                'doc'       => 'https://pluginscafe.com/docs/smart-phone-field/',
            ],
            'woo-commerce' => [
                'name'      => 'WooCommerce',
                'slug'      => 'woo-commerce',
                'path'      => '',
                'demo'      => 'https://demo.pluginscafe.com/checkout',
                'doc'       => 'https://pluginscafe.com/docs/smart-phone-field/',
            ]
        ]);
    }

    public function help_items() {
        return apply_filters('pcafe_spf_help_items', [
            'documentation' => [
                'name'      => __('Documentation', 'smart-phone-field-for-wp-forms'),
                'desc'      => __('Check out our detailed online documentation and video tutorials to find out more about what you can do.', 'smart-phone-field-for-wp-forms'),
                'icon'      => 'documentation.svg',
                'path'      => '',
                'url'       => 'https://pluginscafe.com/docs/smart-phone-field/',
                'btn_text'  => __('Documentation', 'smart-phone-field-for-wp-forms')
            ],
            'support' => [
                'name'      => __('Support', 'smart-phone-field-for-wp-forms'),
                'desc'      => __('We have dedicated support team to provide you fast, friendly & top-notch customer support.', 'smart-phone-field-for-wp-forms'),
                'icon'      => 'support.svg',
                'path'      => '',
                'url'       => 'https://wordpress.org/support/plugin/smart-phone-field-for-wp-forms/',
                'btn_text'  => __('Support', 'smart-phone-field-for-wp-forms')
            ]
        ]);
    }

    public function featured_plugins() {
        return apply_filters('pcafe_spf_featured_plugins', [
            'alphagf' => [
                'name'      => __('Alpha Addons', 'smart-phone-field-for-wp-forms'),
                'desc'      => __('Enhance your Gravity Forms with 14+ powerful addons', 'smart-phone-field-for-wp-forms'),
                'icon'      => 'alpha.png',
                'slug'      => 'alpha-addons-for-gravity-forms',
                'file_name' => 'alphagf-addons'
            ],
            'image_picker' => [
                'name'      => __('Image Picker', 'smart-phone-field-for-wp-forms'),
                'desc'      => __('Redefine product, dropdown and more fields in Gravity Forms', 'smart-phone-field-for-wp-forms'),
                'icon'      => 'image_picker.png',
                'slug'      => 'image-choices-for-gravity-forms',
                'file_name' => 'gf-img-choices'
            ],
            'range_slider' => [
                'name'      => __('Range Slider', 'smart-phone-field-for-wp-forms'),
                'desc'      => __('Smooth, lightweight, customizable range slider for Gravity Forms', 'smart-phone-field-for-wp-forms'),
                'icon'      => 'range_slider.png',
                'slug'      => 'range-slider-addon-for-gravity-forms',
                'file_name' => 'gf-range-slider'
            ],
            'restrict-dates' => [
                'name'      => __('Restrict Dates', 'smart-phone-field-for-wp-forms'),
                'desc'      => __('Restrict which dates are not selectable in Gravity Forms', 'smart-phone-field-for-wp-forms'),
                'icon'      => 'restrict-dates.png',
                'slug'      => 'restrict-dates-add-on-for-gravity-forms',
                'file_name' => 'restrict-dates'
            ]
        ]);
    }

    public function active_addon_list() {
        $active = get_option('pcafe_spf_plugin_list', []);

        $filtered = array_intersect_key($this->addon_list(), array_flip($active));
        return $filtered;
    }

    public function save_settings($lists) {
        update_option('pcafe_spf_global_setting', $lists);
    }

    public function get_settings($option = '') {
        $value = get_option('pcafe_spf_global_setting');

        if (empty($option)) {
            return $value;
        }

        if (isset($value[$option])) {
            return $value[$option];
        } else {
            return false;
        }
    }

    public static function get_countries() {
        return [
            'AF' => 'Afghanistan',
            'AX' => 'Åland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AC' => 'Ascension Island',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia and Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'VG' => 'British Virgin Islands',
            'BN' => 'Brunei',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'IC' => 'Canary Islands',
            'CV' => 'Cape Verde',
            'BQ' => 'Caribbean Netherlands',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'EA' => 'Ceuta and Melilla',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CP' => 'Clipperton Island',
            'CC' => 'Cocos Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CD' => 'Congo',
            'CG' => 'Congo',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Côte d’Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CW' => 'Curaçao',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DG' => 'Diego Garcia',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Islas Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland (Suomi)',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana (Gaana)',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard & McDonald Islands',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland (Ísland)',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'XK' => 'Kosovo',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Laos',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macau',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'KP' => 'North Korea',
            'NO' => 'Norway (Norge)',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestine',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru (Perú)',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn Islands',
            'PL' => 'Poland (Polska)',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Réunion',
            'RO' => 'Romania',
            'RU' => 'Russia',
            'RW' => 'Rwanda',
            'BL' => 'Saint Barthélemy',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'MF' => 'Saint Martin',
            'PM' => 'Saint Pierre and Miquelon',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'São Tomé and Príncipe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SX' => 'Sint Maarten',
            'SK' => 'Slovakia (Slovensko)',
            'SI' => 'Slovenia (Slovenija)',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia & South Sandwich Islands',
            'KR' => 'South Korea',
            'SS' => 'South Sudan',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'VC' => 'St. Vincent & Grenadines',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard and Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden (Sverige)',
            'CH' => 'Switzerland (Schweiz)',
            'SY' => 'Syria',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TA' => 'Tristan da Cunha',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands',
            'TV' => 'Tuvalu',
            'UM' => 'U.S. Outlying Islands',
            'VI' => 'U.S. Virgin Islands',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VA' => 'Vatican City',
            'VE' => 'Venezuela',
            'VN' => 'Vietnam',
            'WF' => 'Wallis and Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        ];
    }

    public static function convertArray($inputArray) {
        $result = [];
        foreach ($inputArray as $key => $value) {
            $result[] = ['label' => $value, 'value' => strtolower($key)];
        }
        return $result;
    }

    public static function get_ff_countries() {
        return self::convertArray(self::get_countries());
    }

    public static function instance() {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
