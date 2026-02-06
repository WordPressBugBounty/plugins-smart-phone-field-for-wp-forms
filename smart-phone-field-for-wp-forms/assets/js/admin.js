; (function ($) {

    // Sets up the page
    function setUpPage() {

        // finds all anchor tabs within the data-tabscrollnavcontainer
        jQuerytabscroll_anchors = jQuery("[data-tabscrollnavcontainer]").find("a");

        // adds the active class to the first tab-navigation
        jQuery(jQuerytabscroll_anchors[0]).parent().addClass("active");

        for (jQueryi = 0; jQueryi < jQuerytabscroll_anchors.length; jQueryi++) {

            // targets each and every link's href-attribute found within the tabscrollnavcontainer
            var jQueryeachAnchor = jQuery(jQuerytabscroll_anchors[jQueryi]).attr("href");

            // adds the navigational data-attribute to each anchor tag's parent
            jQuery(jQuerytabscroll_anchors[jQueryi]).parent().attr("data-tabscrollnavi", jQueryeachAnchor.substring(1))

            // we then use this anchor to find each element, section, etc. that has the 
            // same ID as the anchor tag we found.

            // sets a custom data-tabscroll attribute to each section that correspons
            // with the link in the navigation, stripping off the # (substring)
            jQuery(jQueryeachAnchor).attr("data-tabscroll", jQueryeachAnchor.substring(1));
        }
    }


    jQuery(function () {
        // setup the page
        setUpPage();

        // remove each id tag of an data-tabscroll element
        jQuery("[data-tabscroll]").removeAttr('id');

        // hiding all sections initially except the one specified.
        jQuery("[data-tabscroll]:first-of-type").siblings("[data-tabscroll]").hide();

        // on any hashfragement click within the tabscrollnavi Navigation
        // we may not really need this whole section ...  
        jQuery('[data-tabscrollnavi] [href^="#"]').click(function (event) {
            // read the href tag of the tag clicked
            var jQuerytabscrolltab = jQuery(this).attr("href");

            // not sure if we really need this. Also some old code that didn't work...
            // writing the hashtag into the history
            //        if(history.pushState) {
            //         history.pushState(null, null, jQuerytabscrolltab);
            //        }
            //        else {
            location.hash = jQuerytabscrolltab;
            //        }  
        });



        // this ACTUALLY triggers the change in the tabs 
        // onhashchange because of IE, had onpopstate before
        jQuery(window).on('hashchange', function (event) {
            // writing the URL that raised the event into a string
            var jQuerylocation = String(document.location);

            // stripping off everything before the hash
            jQuerylocation = jQuerylocation = jQuerylocation.split("#")[1];

            // if there is no hash, basically...
            if (jQuerylocation === undefined) {
                // show only the first section
                jQuery("[data-tabscroll]:first-of-type").show();
            }
            // if there is a hash-link active
            else {
                //hide all tabs
                jQuery("[data-tabscroll]").hide();
                // fade in only the tab with the data-tabscroll attribute corresponding
                // to the link that was clicked.
                // Why are we not using the ID? Why did we remove the ID?
                // I did this to prevent the anchor-scroll-back-to-the-top, which seems 
                // not preventable on a window.popstate or hashchange
                jQuery("[data-tabscroll='" + jQuerylocation + "']").show()

                // removes any active navi class from natigation
                jQuery("[data-tabscrollnavi]").removeClass("active");
                // and sets one only on the link's parent that was clicked.
                jQuery("[data-tabscrollnavi='" + jQuerylocation + "']").addClass("active");
            }
            // triggers the hashchange manually on pageload. Adapted from http://stackoverflow.com/questions/20652020/the-hashchange-event-of-jquery-doesnt-work-if-i-open-a-page-with-hash-directly
        }).trigger('hashchange');

        setTimeout(() => {
            const activeLi = $('.pcafe_tab_menu li.active');
            if (!activeLi.length) {
                jQuery('.pcafe_tab_menu li:first-child').addClass('active');
            }
        }, 200);

    });

    $(document).ready(function () {

        $('.spf_addon_form').on('submit', function (e) {
            e.preventDefault();

            var spinner = $(this).find('.loader'),
                toaster = $(this).parent().parent().parent().parent().find('.spf_save_notification'),
                task = $(this).serialize();
            task += "&action=spf_save_plugins_data";

            $.ajax({
                type: 'POST',
                url: pcafe_spf_admin.ajaxurl,
                data: task,
                beforeSend: function () {
                    spinner.addClass('active');
                },
                success: function (response) {
                    toaster.addClass('open');
                },
                complete: function () {
                    spinner.removeClass('active');
                    setTimeout(() => {
                        toaster.removeClass('open');
                    }, 2000);
                }
            });
        });

        $('.spf_settings_page').on('submit', function (e) {
            e.preventDefault();

            var spinner = $(this).find('.loader'),
                toaster = $(this).parent().parent().parent().parent().find('.spf_save_notification'),
                task = $(this).serialize();
            task += "&action=spf_global_setting";

            $.ajax({
                type: 'POST',
                url: pcafe_spf_admin.ajaxurl,
                data: task,
                beforeSend: function () {
                    spinner.addClass('active');
                },
                success: function (response) {
                    toaster.addClass('open');
                },
                complete: function () {
                    spinner.removeClass('active');
                    setTimeout(() => {
                        toaster.removeClass('open');
                    }, 2000);
                }
            });

        });

        let country_restricted = $('#spf_restrict_type').val();

        if (country_restricted != 'all') {
            $('.dep_on_restrict').show();
        } else {
            $('.dep_on_restrict').hide();
        }

        $('#spf_restrict_type').on('change', function () {
            let type = $(this).val();

            if (type != 'all') {
                $('.dep_on_restrict').show();
            } else {
                $('.dep_on_restrict').hide();
            }
        });

        function toggleIpinfoToken() {
            if ($('#spf_geoip').is(':checked')) {
                $('.ipinfo_token').show();
            } else {
                $('.ipinfo_token').hide();
            }
        }

        toggleIpinfoToken(); // on page load

        $('#spf_geoip').on('change', toggleIpinfoToken);

        $('.pcafe_spf_install_btn').not('active').on('click', function (e) {
            e.preventDefault();

            const $btn = $(this);
            const action = $btn.data("action");
            const slug = $btn.data("plugin");
            const filename = $btn.data("filename");

            if (!action || !slug) return;

            const $loader = $btn.find(".loader");
            const originalText = $btn.clone().children().remove().end().text().trim();

            const setText = (text) => $btn.contents().first().replaceWith(text);
            const resetBtn = () => {
                setText(originalText);
                $btn.removeClass("loading").prop("disabled", false);
                $loader.removeClass('active');
            };

            setText(action === "install" ? "Installing.." : "Activating..");
            $btn.addClass("loading").prop("disabled", true);
            $loader.addClass('active');

            $.ajax({
                type: 'POST',
                url: pcafe_spf_admin.ajaxurl,
                data: {
                    action: "spf_install_manage_plugin",
                    security: pcafe_spf_admin.nonce,
                    plugin_action: action,
                    plugin_slug: slug,
                    filename: filename
                },
                beforeSend: function () {
                    $loader.addClass('active');
                    $btn.find('svg').hide();
                },
                success: function (response) {
                    resetBtn();
                    console.log(response);
                    if (action === "install") {
                        setText("Activate");
                        $btn.data("action", "activate")
                            .removeClass("install")
                            .addClass("activate");
                    } else if (action === "activate") {
                        $btn.replaceWith(
                            '<span class="pcafe_spf_install_btn plugin_status active">Activated</span>'
                        );
                    }
                },
                error: function () {
                    resetBtn();
                    alert("An error occurred. Please try again.");
                }
            });

        });

    });

})(jQuery);