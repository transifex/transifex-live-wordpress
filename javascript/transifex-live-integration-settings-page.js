
function transifex_live_integration_mapper(l1) {
    return {
        'caption': l1['tx_name'],
        'name': 'transifex-integration-live-' + l1['code'],
        'id': 'transifex-integration-live-' + l1['code'],
        'type': 'text',
        'value': l1['code']
    };
}

function transifex_live_integration_map_update(tl) {
    if (typeof (tl) != 'undefined') {
        var t = tl;
    } else {
        var t = transifex_languages;
    }
    var h = [];
    var local_lm = [];
    var arr = {};
    // loop through transifex languages
    jQuery.each(t, function (i, s) {
        var l = jQuery('#transifex-integration-live-' + s).val();
        arr[s] = l;
    });
    local_lm.push(arr);
    jQuery('#transifex_live_settings_language_map').val(JSON.stringify(local_lm));
    return true;
}


function transifex_live_integration_convert(l) {
    var r = {"type": "div",
        "id": "transifex-languages"};
    var t = l['translation'];
    var h = [];
    transifex_languages = [];
    language_lookup = [];
    language_map = [];
    var arrr = {};
    jQuery.each(t, function (i, o) {
        h.push(transifex_live_integration_mapper(o));
        transifex_languages.push(o['code']);
        var arr = {};
        arr['tx_name'] = o['tx_name'];
        arr['code'] = o['code'];
        language_lookup.push(arr);
        arrr[o['code']] = o['code'];
    });
    language_map.push(arrr);
    var s = {
        caption: 'Source:' + l['source']['tx_name'],
        name: "transifex-integration-live-source-language",
        id: "transifex-integration-live-[source-language]",
        type: "text",
        value: l['source']['code']
    };
    source_language = l['source']['code'];
    r['source'] = s;
    r['html'] = h;
    return r;
}


function transifexLanguages() {
    var apikey = jQuery('#transifex_live_settings_api_key').val();
    if (apikey != '') {
        jQuery.ajax({
            url: "https://cdn.transifex.com/" + apikey + "/latest/languages.jsonp",
            jsonpCallback: "transifex_languages",
            jsonp: true,
            dataType: "jsonp",
            timeout: 3000
        }).done(function (data) {
            if (data['translation'] != undefined && data['translation'].length > 0) {
                globaldata = data;
                transifex_language_fields = transifex_live_integration_convert(data);
                jQuery('#transifex_live_settings_api_key').trigger('success');
            } else {
                jQuery('#transifex_live_settings_api_key').trigger('notranslation');
            }
        }).fail(function () {
            jQuery('#transifex_live_settings_api_key').trigger('error');
        });
    } else {
        jQuery('#transifex_live_settings_api_key').trigger('blank');
    }
}

function addTransifexLanguages(obj) {

    if (typeof (obj) !== 'undefined' && obj !== null) {
        lm = jQuery.parseJSON(jQuery('#transifex_live_settings_language_map').val());
        globalobj = obj;
        var myName = '';
        var myId = '';
        var tl = JSON.parse(jQuery('#transifex_live_settings_transifex_languages').val());
        jQuery.each(obj, function (i, o) {
            myName = 'transifex-integration-live-' + o.code;
            myId = 'transifex-integration-live-' + o.code;
            jQuery('#transifex_live_language_map_table').append(jQuery('#transifex_live_language_map_template').clone().show().addClass('cloned-language-map').each(function (ii, oo) {
                jQuery(oo).find('span.tx-language').text(o.tx_name);
                jQuery(oo).find('input.tx-code').attr('id', myId).attr('name', myName).val(lm[0][o.code]);
            }));
        });

        jQuery.each(obj, function (i, o) {
            jQuery('#transifex-integration-live-' + o.code).machine({defaultState: {onEnter: function () {
                        transifex_live_integration_map_update(tl);
                    },
                    events: {change: 'defaultState'}}, });
        });


    } else {
        jQuery.each(transifex_language_fields['html'], function (i, o) {
            jQuery('#transifex_live_language_map_table').append(jQuery('#transifex_live_language_map_template').clone().show().addClass('cloned-language-map').each(function () {
                jQuery(this).find('span.tx-language').text(o.caption);
                jQuery(this).find('input.tx-code').attr('id', o.id).attr('name', o.name).val(o.value);
                jQuery(this).machine({defaultState: {onEnter: function () {
                            transifex_live_integration_map_update();
                        },
                        events: {change: 'defaultState'}}, });
            }));
        });
        jQuery('#transifex_live_settings_source_language').val(source_language);
        jQuery('#transifex_live_settings_transifex_languages').val(JSON.stringify(transifex_languages));
        jQuery('#transifex_live_settings_language_lookup').val(JSON.stringify(language_lookup));
        jQuery('#transifex_live_settings_language_map').val(JSON.stringify(language_map));
    }
}

function updateTransifexSettingsFields(obj) {
    jQuery('#transifex_live_transifex_settings_settings').val(JSON.stringify(obj));
}

(function ($) {
    $('#transifex_live_languages').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('#transifex_live_languages:defaultState:onEnter');
                ($('#transifex_live_settings_language_lookup').val() !== '') ? this.trigger('render') : this.trigger('wait');

            },
            events: {render: 'render', wait: 'wait'}
        },
        wait: {
            onEnter: function () {
                $.log.debug('#transifex_live_languages:wait:onEnter');
            },
            events: {load: 'loadnew'}
        },
        loadnew: {
            onEnter: function () {
                $.log.debug('#transifex_live_languages:load:onEnter');
                $("#transifex_live_languages_message").toggleClass('hide-if-js', true);
                $(".cloned-language-map").remove();
                addTransifexLanguages();
            },
            events: {load: 'loadnew'}
        },
        render: {
            onEnter: function () {
                $.log.debug('#transifex_live_languages:render:onEnter');
                $("#transifex_live_languages_message").toggleClass('hide-if-js', true);
                var obj = jQuery.parseJSON(jQuery('#transifex_live_settings_language_lookup').val());
                myobj = obj;
                addTransifexLanguages(obj);
            },
            events: {load: 'loadnew'}
        }
    }, {setClass: true});
})(jQuery);


(function ($) {
    $('#transifex_live_settings_api_key_button').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_api_key_button::defaultState::onEnter');
                ($('#transifex_live_settings_api_key').val() !== '') ? this.trigger('hidden') : this.trigger('wait');
            },
            events: {wait: 'wait', hidden: 'hidden'}
        },
        wait: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_api_key_button::wait::onEnter');
                this.show();
                this.attr('disabled', false);
            },
            events: {click: 'checking'}
        },
        checking: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_api_key_button::checking::onEnter');
                $('#transifex_live_settings_api_key').trigger('validating');
                this.attr('disabled', true);
            },
            events: {wait: 'wait', hidden: 'hidden'}
        },
        hidden: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_api_key_button::hidden::onEnter');
                this.hide();
            },
            events: {wait: 'wait'}
        }
    }, {setClass: true});
})(jQuery);


(function (Transifex, $) {
    $('#transifex_live_settings_api_key').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_api_key:defaultState:onEnter');
                languages_override = false;
                this.trigger('validating');
            },
            events: {validating: 'validating'}
        },
        validating: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_api_key:validating:onEnter');
                $('#transifex_live_settings_api_key_message').text('Checking key...');
                $('#transifex_live_settings_url_options_none').attr('disabled', true);
                $('#transifex_live_settings_url_options_subdirectory').attr('disabled', true);
                $('#transifex_live_settings_url_options_subdomain').attr('disabled', true);
                $('input#transifex_live_submit').trigger('disable');
                transifexLanguages();
                transifex_settings_params = {
                    url: "https://cdn.transifex.com/" + this.val() + "/latest/settings.jsonp",
                    done: function (data) {
                        if (data) {
                        console.log(data);
                        updateTransifexSettingsFields(data);
                        $('#transifex_live_settings_api_key').trigger('success');
                    } else {
                        $('#transifex_live_settings_api_key').trigger('error');
                    }
                    },
                    fail: function () {
                        $('#transifex_live_settings_api_key').trigger('error');
                    }
                };

                Transifex.httpGet(transifex_settings_params);
            },
            events: {success: 'validating2', blank: 'blank', error: 'error', notranslation: 'missing', change: 'validating'}
        },
        validating2: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_api_key:validating2:onEnter');
            },
            events: {success: 'valid', blank: 'blank', error: 'error', notranslation: 'missing', change: 'validating'}
        },
        valid: {
            onEnter: function () {
                $.log.debug('#transifex_live_settings_api_key:valid:onEnter');
                $('#transifex_live_settings_api_key_button').trigger('hidden');
                $('#transifex_live_settings_url_options_none').attr('disabled', false);
                $('#transifex_live_settings_url_options_subdirectory').attr('disabled', false);
                $('#transifex_live_settings_url_options_subdomain').attr('disabled', false);
                if (jQuery('#transifex_live_settings_language_map').val() == '' || languages_override) {
                    $('#transifex_live_languages').trigger('load');
                    languages_override = false;
                }
                $('#transifex_live_settings_api_key_message').text('Success! Advanced SEO settings enabled.');
                $('input#transifex_live_submit').trigger('enable');
            },
            events: {success: 'valid', change: 'validating', validating: 'validating'}
        },
        error: {
            onEnter: function () {
                $.log.debug('error:onEnter');
                $('#transifex_live_settings_api_key_button').trigger('wait');
                $('#transifex_live_settings_api_key_message').text(' Oops! Please make sure you’ve entered a valid API key.');
            },
            events: {change: 'validating', validating: 'validating'}
        },
        blank: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_api_key:blank:onEnter');
                $('#transifex_live_settings_api_key_button').trigger('wait');
                $('#transifex_live_settings_api_key_message').text('');
            },
            events: {change: 'validating', validating: 'validating'}
        },
        missing: {
            onEnter: function () {
                $.log.debug('#transifex_live_settings_api_key:missing:onEnter');
                $('#transifex_live_settings_api_key_button').trigger('wait');
                $('#transifex_live_settings_api_key_message').text('D’oh! No languages have been published from Transifex Live yet.');
            },
            events: {validating: 'validating'}
        }
    }, {setClass: true});
})(window.Transifex, jQuery);

(function ($) {
    $('#transifex_live_settings_url_options_none').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options_none::defaultState::onEnter');
            },
            events: {click: 'on'}
        },
        on: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options_none::on::onEnter');
                $('#transifex_live_settings_url_options').trigger('none');
            },
            events: {click: 'on'}
        },
    }, {setClass: true});
})(jQuery);

(function ($) {
    $('#transifex_live_settings_url_options_subdirectory').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options_subdirectory::defaultState::onEnter');
            },
            events: {click: 'on'}
        },
        on: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options_subdirectory::on::onEnter');
                $('#transifex_live_settings_url_options').trigger('subdirectory');
            },
            events: {click: 'on'}
        },
    }, {setClass: true});
})(jQuery);

(function ($) {
    $('#transifex_live_settings_url_options_subdomain').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options_subdomain::defaultState::onEnter');
            },
            events: {click: 'on'}
        },
        on: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options_subdomain::on::onEnter');
                $('#transifex_live_settings_url_options').trigger('subdomain');
            },
            events: {click: 'on'}
        },
    }, {setClass: true});
})(jQuery);

(function ($) {
    $('#transifex_live_settings_url_options').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options::defaultState::onEnter');
                (this.val() === "1") ? this.trigger('none') : (this.val() === "2") ? this.trigger('subdomain') : this.trigger('subdirectory');
            },
            events: {none: 'none', subdomain: 'subdomain', subdirectory: 'subdirectory'}
        },
        none: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options::none::onEnter');
                $('.url-structure-subdirectory').toggleClass('hide-if-js', true);
                $('.url-structure-subdomain').toggleClass('hide-if-js', true);
                $('.custom-urls-settings').toggleClass('hide-if-js', true);
                $('#transifex_live_settings_url_options_subdirectory').prop("checked", false);
                $('#transifex_live_settings_url_options_subdomain').prop("checked", false);
                this.val('1');
                $('input#transifex_live_submit').trigger('enable');
            },
            events: {none: 'none', subdomain: 'subdomain', subdirectory: 'subdirectory'}
        },
        subdirectory: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options::subdirectory::onEnter');
                $('#transifex_live_settings_custom_urls').val("1");
                $('.url-structure-subdirectory').toggleClass('hide-if-js', false);
                $('.url-structure-subdomain').toggleClass('hide-if-js', true);
                $('.custom-urls-settings').toggleClass('hide-if-js', false);
                $('#transifex_live_options_all').trigger('activate');
                $('#transifex_live_settings_url_options_none').prop("checked", false);
                $('#transifex_live_settings_url_options_subdomain').prop("checked", false);
                this.val('3');
                $('input#transifex_live_submit').trigger('enable');
            },
            events: {none: 'none', subdomain: 'subdomain', subdirectory: 'subdirectory'}
        },
        subdomain: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options::subdomain::onEnter');
                $('#transifex_live_settings_custom_urls').val("1");
                $('.url-structure-subdirectory').toggleClass('hide-if-js', true);
                $('.url-structure-subdomain').toggleClass('hide-if-js', false);
                $('.custom-urls-settings').toggleClass('hide-if-js', false);
                $('#transifex_live_options_all').trigger('activate');
                $('#transifex_live_settings_url_options_subdirectory').prop("checked", false);
                $('#transifex_live_settings_url_options_none').prop("checked", false);
                this.val('2');
                $('input#transifex_live_submit').trigger('enable');
            },
            events: {none: 'none', subdomain: 'subdomain', subdirectory: 'subdirectory'}
        }
    }, {setClass: true});
})(jQuery);


(function ($) {
    $('#transifex_live_settings_rewrite_option_all').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_rewrite_option_all::defaultState::onEnter');
                if (this.prop('checked')) {
                    this.trigger('seton');
                } else {
                    this.trigger('setoff');
                }
            },
            events: {seton: 'on', setoff: 'off'}
        },
        on: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_rewrite_option_all::on::onEnter');
                this.prop('checked', true);
                $('.all_selector').trigger('on');
            },
            events: {click: 'off', off: 'off', singleoff: 'singleoff'}
        },
        off: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_rewrite_option_all::off::onEnter');
                this.prop('checked', false);
                $('.all_selector').trigger('off');
                $('input#transifex_live_submit').trigger('disable');
            },
            events: {click: 'on'}
        },
        singleoff: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_rewrite_option_all::singleoff::onEnter');
                this.prop("checked", false);
            },
            events: {click: 'on'}
        }
    }, {setClass: true});
})(jQuery);

(function ($) {
    $('.all_selector').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('all_selector::defaultState::onEnter');
                if (this.prop('checked')) {
                    this.trigger('seton');
                } else {
                    this.trigger('setoff');
                }
            },
            events: {seton: 'on', setoff: 'off'}
        },
        on: {
            onEnter: function () {
                $.log.debug('all_selector::on::onEnter');
                this.prop("checked", true);
                $('input#transifex_live_submit').trigger('enable');
            },
            events: {click: 'off', off: 'off'}
        },
        off: {
            onEnter: function () {
                $.log.debug('all_selector::off::onEnter');
                this.prop("checked", false);
                $('#transifex_live_settings_rewrite_option_all').trigger('singleoff');
                $('input#transifex_live_submit').trigger('enable');
            },
            events: {click: 'on', on: 'on'}
        }
    }, {setClass: true});
})(jQuery);

(function ($) {
    $('input#transifex_live_submit').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options_subdomain::input#transifex_live_submit::onEnter');
                this.trigger('disable');
            },
            events: {disable: 'disable'}
        },
        enable: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options_subdomain::enable::onEnter');
                this.attr('disabled', false);
                if (jQuery('#transifex_live_settings_url_options').data('state') == 'subdirectory') {
                    var checkOptions = false;
                    $.each($('.all_selector'), function (i, o) {
                        if (!checkOptions) {
                            checkOptions = ($(o).prop('checked')) ? true : false;
                        }
                    });
                    if (!checkOptions) {
                        this.trigger('disable');
                    }
                }
            },
            events: {disable: 'disable', enable: 'enable'}
        },
        disable: {
            onEnter: function () {
                $.log.debug('transifex_live_settings_url_options_subdomain::enable::onEnter');
                this.attr('disabled', true);
            },
            events: {enable: 'enable'}
        },
    }, {setClass: true});
})(jQuery);

(function ($) {
    $('input#transifex_live_sync').machine({
        defaultState: {
            onEnter: function () {
                $.log.debug('input#transifex_live_sync::defaultState::onEnter');
                this.trigger('wait');
            },
            events: {wait: 'wait'}
        },
        wait: {
            onEnter: function () {
                $.log.debug('input#transifex_live_sync::wait::onEnter');
            },
            events: {click: 'confirm'}
        },
        confirm: {
            onEnter: function () {
                $.log.debug('input#transifex_live_sync::confirm::onEnter');
                (confirm('Refreshing languages will replace your current codes with those from Transifex Live. Continue?')) ? this.trigger('refresh') : this.trigger('wait');
            },
            events: {refresh: 'refresh', wait: 'wait'}
        },
        refresh: {
            onEnter: function () {
                $.log.debug('input#transifex_live_sync::refresh::onEnter');
                languages_override = true;
                jQuery('#transifex_live_settings_api_key').trigger('validating');

                this.trigger('wait');
            },
            events: {wait: 'wait'}
        },
    }, {setClass: true});
})(jQuery);