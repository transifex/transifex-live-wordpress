function transifex_live_integration_mapper(l1) {
    return {
        'caption': l1['tx_name'],
        'name': 'transifex-integration-live-' + l1['code'],
        'id': 'transifex-integration-live-' + l1['code'],
        'hreflangname': 'transifex-integration-live-hreflang-' + l1['code'],
        'hreflangid': 'transifex-integration-live-hreflang-' + l1['code'],
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
    //var h = [];
    var local_lm = [];
    var local_hreflm = [];
    var arr = {};
    var arrr = {};
    var updateOk = true;
    // loop through transifex languages
    jQuery.each(
            t, function (i, s) {
                var l = jQuery('#transifex-integration-live-' + s).val();
                var h = jQuery('#transifex-integration-live-hreflang-' + s).val();
                arrr[s] = h;
                arr[s] = l;
                updateOk = (h) ? true : false;
                updateOk = (updateOk) ? (l) ? true : false : false
            }
    );
    local_lm.push(arr);
    local_hreflm.push(arrr);
    if (updateOk) {
        jQuery('#transifex_live_settings_language_map').val(JSON.stringify(local_lm));
        jQuery('#transifex_live_settings_hreflang_map').val(JSON.stringify(local_hreflm));
    }
    return true;
}

function transifex_live_integration_hreflang_update(tl) {
    if (typeof (tl) != 'undefined') {
        var t = tl;
    } else {
        var t = transifex_languages;
    }
    var h = [];
    var local_lm = [];
    var arr = {};
    var updateOk = true;
    // loop through transifex languages
    jQuery.each(
            t, function (i, s) {
                var l = jQuery('#transifex-integration-live-hreflang-' + s).val();
                updateOk = (l) ? true : false;
                arr[s] = l;
            }
    );
    local_lm.push(arr);
    if (updateOk) {
        jQuery('#transifex_live_settings_hreflang_map').val(JSON.stringify(local_lm));
    }
    return true;
}

function build_cdn_manifest_url(apikey) {
    return "https://cdn.transifex.com/" + apikey + "/latest/manifest.jsonp";
}

function transifex_live_integration_convert(data) {
    var result = {"type": "div",
        "id": "transifex-languages"};
    var translation = data['translation'];
    var source = data['source'];
    var html = [];
    transifex_languages = [];
    language_lookup = [];
    language_map = [];
    hreflang_map = [];
    var language_data = {};
    var language = {};
    var hreflang = {};

    html.push(transifex_live_integration_mapper(source));
    transifex_languages.push(source['code']);
    language_data['tx_name'] = source['tx_name'];
    language_data['code'] = source['code'];
    language_lookup.push(language_data);
    language[source['code']] = source['code'];
    hreflang[source['code']] = source['code'].toLowerCase().replace('_', '-');

    jQuery.each(
            translation, function (i, o) {
                html.push(transifex_live_integration_mapper(o));
                transifex_languages.push(o['code']);
                var language_data = {};
                language_data['tx_name'] = o['tx_name'];
                language_data['code'] = o['code'];
                language_lookup.push(language_data);
                language[o['code']] = o['code'];
                hreflang[o['code']] = o['code'].toLowerCase().replace('_', '-');
            }
    );
    language_map.push(language);
    hreflang_map.push(hreflang);
    var s = {
        caption: 'Source:' + source['tx_name'],
        name: "transifex-integration-live-source-language",
        id: "transifex-integration-live-[source-language]",
        type: "text",
        value: source['code']
    };
    source_language = source['code'];
    result['source'] = s;
    result['html'] = html;
    return result;
}


function transifexLanguages() {
    var apikey = jQuery('#transifex_live_settings_api_key').val();
    if (apikey != '') {
        transifex_settings_params = {
            url: build_cdn_manifest_url(apikey),
            done: function (data) {
                if (data) {
                    updateTransifexSettingsFields(data);
                    var data = window.transifex_languages;
                    if (data['translation'] != undefined && data['translation'].length > 0) {
                        transifex_language_fields = transifex_live_integration_convert(data);
                        jQuery('#transifex_live_settings_url_options').trigger('success');
                    } else {
                        jQuery('#transifex_live_settings_url_options').trigger('notranslation');
                    }
                }
            }
        };
        Transifex.httpGet(transifex_settings_params);
    } else {
      jQuery('#transifex_live_settings_api_key').trigger('blank');
    }
}

function addTransifexLanguages(obj) {
    if (typeof (obj) !== 'undefined' && obj !== null) {
        lm = jQuery.parseJSON(jQuery('#transifex_live_settings_language_map').val());
        hm = jQuery.parseJSON(jQuery('#transifex_live_settings_hreflang_map').val());
        globalobj = obj;
        var myName = '';
        var myId = '';
        var tl = JSON.parse(jQuery('#transifex_live_settings_transifex_languages').val());
        jQuery.each(
                obj, function (i, o) {
                    myName = 'transifex-integration-live-' + o.code;
                    myId = 'transifex-integration-live-' + o.code;
                    myHreflangName = 'transifex-integration-live-hreflang-' + o.code;
                    myHreflangId = 'transifex-integration-live-hreflang-' + o.code;
                    jQuery('#transifex_live_language_map_table').append(
                            jQuery('#transifex_live_language_map_template').clone().show().addClass('cloned-language-map').each(
                            function (ii, oo) {
                                jQuery(oo).find('span.tx-language').text(o.tx_name);
                                if (lm.length > 1) {
                                    var lmval = (typeof lm[i] != 'undefined') ? lm[i][o.code] : '';
                                } else {
                                    var lmval = (typeof lm[0] != 'undefined') ? lm[0][o.code] : '';
                                }
                                jQuery(oo).find('input.tx-code').attr('id', myId).attr('name', myName).val(lmval);
                                if (lm.length > 1) {
                                    var hmval = (typeof hm[i] != 'undefined') ? hm[i][o.code] : '';
                                } else {
                                    var hmval = (typeof hm[0] != 'undefined') ? hm[0][o.code] : '';
                                }
                                jQuery(oo).find('input.tx-hreflang').attr('id', myHreflangId).attr('name', myHreflangName).val(hmval);

                            }
                    )
                            );
                }
        );

        jQuery.each(
                obj, function (i, o) {
                    jQuery('#transifex-integration-live-' + o.code).machine(
                            {defaultState: {onEnter: function () {
                                        transifex_live_integration_map_update(tl);
                                    },
                                    events: {change: 'defaultState'}}, }
                    );
                }
        );
        jQuery.each(
                obj, function (i, o) {
                    jQuery('#transifex-integration-live-hreflang-' + o.code).machine(
                            {defaultState: {onEnter: function () {
                                        transifex_live_integration_hreflang_update(tl);
                                    },
                                    events: {change: 'defaultState'}}, }
                    );
                }
        );

    } else {
        var tlslm = JSON.parse(jQuery('#transifex_live_settings_language_map').val());
        var tlshm = JSON.parse(jQuery('#transifex_live_settings_hreflang_map').val());
        language_map = (tlslm.length < 1) ? language_map : [];
        hreflang_map = (tlshm.length < 1) ? hreflang_map : [];
        var e = {};
        var ee = {};
        jQuery.each(
                transifex_language_fields['html'], function (i, o) {
            jQuery('#transifex_live_language_map_table').append(
                    jQuery('#transifex_live_language_map_template').clone().show().addClass('cloned-language-map').each(
                    function () {
                        jQuery(this).find('span.tx-language').text(o.caption);
                        if (tlslm.length < 1) {
                            jQuery(this).find('input.tx-code').attr('id', o.id).attr('name', o.name).val(o.value);
                            jQuery(this).find('input.tx-hreflang').attr('id', o.hreflangid).attr('name', o.hreflangname).val(o.value.toLowerCase().replace('_', '-'));
                        } else {
                            jQuery(this).find('input.tx-code').attr('id', o.id).attr('name', o.name).val(tlslm[0][o.value]);
                            jQuery(this).find('input.tx-hreflang').attr('id', o.hreflangid).attr('name', o.hreflangname).val(tlshm[0][o.value]);

                            e[o.value] = tlslm[0][o.value];

                            ee[o.value] = tlshm[0][o.value];


                        }
                        jQuery(this).machine(
                                {defaultState: {onEnter: function () {
                                            transifex_live_integration_map_update();
                                            transifex_live_integration_hreflang_update();
                                        },
                                        events: {change: 'defaultState'}}, }
                        );

                    }
            )
                    );
        }
        );
        if (tlslm.length > 0) {
            language_map.push(e);
            hreflang_map.push(ee);
        }
        jQuery('#transifex_live_settings_source_language').val(source_language);
        jQuery('#transifex_live_settings_transifex_languages').val(JSON.stringify(transifex_languages));
        jQuery('#transifex_live_settings_language_lookup').val(JSON.stringify(language_lookup));
        jQuery('#transifex_live_settings_language_map').val(JSON.stringify(language_map));
        jQuery('#transifex_live_settings_hreflang_map').val(JSON.stringify(hreflang_map));
    }
}

function updateTransifexSettingsFields(obj) {
    var env = (jQuery('#transifex_live_settings_enable_staging').prop('checked')) ? 'staging' : 'production';
    if (!obj.languages) {
        jQuery('#transifex_live_settings_url_options').trigger('error');
    } else {
        window.transifex_languages = obj.languages[env];
    }
    jQuery('#transifex_live_transifex_settings_settings').val(
        JSON.stringify(obj.settings)
    );
}

(function ($) {
    $('#transifex_live_languages').machine(
            {
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
            }, {setClass: true}
    );
})(jQuery);


(function ($) {
    var oldapikey = jQuery('#transifex_live_settings_api_key').val();
    $('#transifex_live_settings_api_key_button').machine(
            {
                defaultState: {
                    onEnter: function () {
                        var c_api_key = $('#transifex_live_settings_api_key').val();
                        $.log.debug('transifex_live_settings_api_key_button::defaultState::onEnter');
                        if (c_api_key == oldapikey && oldapikey.length != 0) {
                          this.attr('disabled', true);
                        }
                    },
                    events: {wait: 'wait'}
                },
                wait: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_api_key_button::wait::onEnter');
                        this.attr('disabled', $('#transifex_live_settings_api_key').val() === oldapikey);
                    },
                    events: {wait: 'wait', invalid: 'invalid'}
                },
                invalid: {
                  onEnter: function() {
                    this.attr('disabled', true);
                  },
                  events: {wait: 'wait', invalid: 'invalid'}
                },
                checking: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_api_key_button::checking::onEnter');
                        $('#transifex_live_settings_api_key').trigger('validating');
                        this.attr('disabled', true);
                    },
                    events: {wait: 'wait'}
                },
            }, {setClass: true}
    );
})(jQuery);


(function (Transifex, $) {
    var oldapikey = jQuery('#transifex_live_settings_api_key').val();
    $('#transifex_live_settings_api_key').machine(
            {
                defaultState: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_api_key:defaultState:onEnter');
                        languages_override = false;
                        (this.val() !== '' || this.val() !== oldapikey) ? this.trigger('validating') : this.trigger('wait');
                    },
                    events: {validating: 'validating', wait: 'wait'}
                },
                wait: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_api_key:wait:onEnter');
                    },
                    events: {on: 'validating', validating: 'validating'}
                },
                validating: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_api_key:validating:onEnter');
                        $('#transifex_live_settings_url_options_none').attr('disabled', true);
                        $('#transifex_live_settings_url_options_subdirectory').attr('disabled', true);
                        $('#transifex_live_settings_url_options_subdomain').attr('disabled', true);
                        $('input#transifex_live_submit').trigger('disable');
                        $('#transifex_live_settings_api_key_message_error').toggleClass('hide-if-js', true);
                        $('#transifex_live_settings_api_key_message_missing').toggleClass('hide-if-js', true);

                        transifex_settings_params = {
                            url: build_cdn_manifest_url(this.val()),
                            done: function (data) {
                                if (data) {
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
                    events: {success: 'valid', blank: 'blank', error: 'error', notranslation: 'missing', change: 'validating'}
                },
                valid: {
                    onEnter: function () {
                        $.log.debug('#transifex_live_settings_api_key:valid:onEnter');
                        $('#transifex_live_settings_api_key_button').trigger('wait');
                        $('#transifex_live_settings_url_options').trigger('validating');
                        $('#transifex_live_settings_api_key_message_error').toggleClass('hide-if-js', true);
                        $('#transifex_live_settings_api_key_message_missing').toggleClass('hide-if-js', true);
                        $('input#transifex_live_start').trigger('enable');
                    },
                    events: {success: 'valid', change: 'validating', validating: 'validating'}
                },
                error: {
                    onEnter: function () {
                        $.log.debug('error:onEnter');
                        $('#transifex_live_settings_api_key_button').trigger('fail');
                        $('#transifex_live_settings_api_key_message_validating').toggleClass('hide-if-js', true);
                        $('#transifex_live_settings_api_key_message_error').toggleClass('hide-if-js', false);
                    },
                    events: {change: 'validating', validating: 'validating'}
                },
                blank: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_api_key:blank:onEnter');
                        $('#transifex_live_settings_api_key_button').trigger('wait');
                        $('#transifex_live_settings_api_key_message_validating').toggleClass('hide-if-js', true);
                        $('#transifex_live_settings_api_key_message_valid').toggleClass('hide-if-js', true);
                        $('#transifex_live_settings_api_key_message_error').toggleClass('hide-if-js', true);
                        $('#transifex_live_settings_api_key_message_missing').toggleClass('hide-if-js', true);
                    },
                    events: {change: 'validating', validating: 'validating'}
                },
                missing: {
                    onEnter: function () {
                        $.log.debug('#transifex_live_settings_api_key:missing:onEnter');
                        $('#transifex_live_settings_api_key_button').trigger('fail');
                        $('#transifex_live_settings_api_key_message_validating').toggleClass('hide-if-js', true);
                        $('#transifex_live_settings_api_key_message_missing').toggleClass('hide-if-js', false);
                    },
                    events: {validating: 'validating'}
                }
            }, {setClass: true}
    );
})(window.Transifex, jQuery);

(function ($) {
    $('#transifex_live_settings_url_options_none').machine(
            {
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
            }, {setClass: true}
    );
})(jQuery);


(function ($) {
    $('#transifex_live_settings_url_options_subdirectory').machine(
            {
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
            }, {setClass: true}
    );
})(jQuery);

(function ($) {
    $('#transifex_live_settings_url_options_subdomain').machine(
            {
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
            }, {setClass: true}
    );
})(jQuery);

(function ($) {
    $('#transifex_live_settings_url_options').machine(
            {
                defaultState: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_url_options::defaultState::onEnter');
                        this.trigger('wait');
                    },
                    events: {wait: 'wait'}
                },
                wait: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_url_options::wait::onEnter');
                    },
                    events: {validating: 'validating'}
                },
                validating: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_url_options::validating::onEnter');
                        $('#transifex_live_settings_api_enable_seo_missing').toggleClass('hide-if-js', true);
                        transifexLanguages();

                    },
                    events: {success: 'valid', error: 'error', notranslation: 'error'}
                },
                valid: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_url_options::valid::onEnter');
                        $('#transifex_live_settings_url_options_none').attr('disabled', false);
                        $('#transifex_live_settings_url_options_subdirectory').attr('disabled', false);
                        $('#transifex_live_settings_url_options_subdomain').attr('disabled', false);
                        $('#transifex_live_settings_api_enable_seo_missing').toggleClass('hide-if-js', true);
                        if (jQuery('#transifex_live_settings_language_map').val() == '[]' || languages_override) {
                            addTransifexLanguages();
                            $('#transifex_live_languages').trigger('load');
                            languages_override = false;
                        }
                        (this.val() === "1") ? this.trigger('none') : (this.val() === "2") ? this.trigger('subdomain') : this.trigger('subdirectory');
                    },
                    events: {none: 'none', subdomain: 'subdomain', subdirectory: 'subdirectory'}
                },
                error: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_url_options::error:onEnter');
                        $('#transifex_live_settings_api_enable_seo_missing').toggleClass('hide-if-js', false);
                    },
                    events: {change: 'validating', validating: 'validating'}
                },
                none: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_url_options::none::onEnter');
                        $('.url-structure-subdirectory').toggleClass('hide-if-js', true);
                        $('.url-structure-subdomain').toggleClass('hide-if-js', true);
                        $('.custom-urls-settings').toggleClass('hide-if-js', true);
                        $('.prerender-options').toggleClass('hide-if-js', true);
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
                        $('.prerender-options').toggleClass('hide-if-js', false);
                        $('#transifex_live_options_all').trigger('activate');
                        $('#transifex_live_settings_url_options_none').prop("checked", false);
                        $('#transifex_live_settings_url_options_subdomain').prop("checked", false);
                        this.val('3');
                        $('input#transifex_live_submit').trigger('enable');
                    },
                    events: {none: 'none', subdomain: 'subdomain', subdirectory: 'subdirectory', validating: 'validating'}
                },
                subdomain: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_url_options::subdomain::onEnter');
                        $('#transifex_live_settings_custom_urls').val("1");
                        $('.url-structure-subdirectory').toggleClass('hide-if-js', true);
                        $('.url-structure-subdomain').toggleClass('hide-if-js', false);
                        $('.custom-urls-settings').toggleClass('hide-if-js', false);
                        $('.prerender-options').toggleClass('hide-if-js', false);
                        $('#transifex_live_options_all').trigger('activate');
                        $('#transifex_live_settings_url_options_subdirectory').prop("checked", false);
                        $('#transifex_live_settings_url_options_none').prop("checked", false);
                        this.val('2');
                        $('input#transifex_live_submit').trigger('enable');
                    },
                    events: {none: 'none', subdomain: 'subdomain', subdirectory: 'subdirectory'}
                }
            }, {setClass: true}
    );
})(jQuery);


(function ($) {
    $('#transifex_live_settings_rewrite_option_all').machine(
            {
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
            }, {setClass: true}
    );
})(jQuery);

(function ($) {
    $('.all_selector').machine(
            {
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
            }, {setClass: true}
    );
})(jQuery);

(function ($) {
    $('input#transifex_live_submit').machine(
            {
                defaultState: {
                    onEnter: function () {
                        $.log.debug('input#transifex_live_submit::defaultState::onEnter');
                        this.trigger('disable');
                    },
                    events: {disable: 'disable'}
                },
                enable: {
                    onEnter: function () {
                        $.log.debug('input#transifex_live_submit::enable::onEnter');
                        this.attr('disabled', false);
                        if (jQuery('#transifex_live_settings_url_options').data('state') == 'subdirectory') {
                            var checkOptions = false;
                            $.each(
                                    $('.all_selector'), function (i, o) {
                                if (!checkOptions) {
                                    checkOptions = ($(o).prop('checked')) ? true : false;
                                }
                            }
                            );
                            if (!checkOptions) {
                                this.trigger('disable');
                            }
                        }
                    },
                    events: {disable: 'disable', enable: 'enable'}
                },
                disable: {
                    onEnter: function () {
                        $.log.debug('input#transifex_live_submit::disable::onEnter');
                        this.attr('disabled', true);
                    },
                    events: {enable: 'enable'}
                },
            }, {setClass: true}
    );
})(jQuery);

(function ($) {
    $('input#transifex_live_sync').machine(
            {
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
                        this.trigger('refresh');
                        //this.trigger('wait');
                    },
                    events: {refresh: 'refresh', wait: 'wait'}
                },
                refresh: {
                    onEnter: function () {
                        $.log.debug('input#transifex_live_sync::refresh::onEnter');
                        languages_override = true;
                        jQuery('#transifex_live_settings_url_options').trigger('validating');
                        jQuery('#transifex_live_settings_language_map').val('[]');
                        jQuery('#transifex_live_settings_hreflang_map').val('[]');
                        this.trigger('wait');
                    },
                    events: {wait: 'wait'}
                },
            }, {setClass: true}
    );
})(jQuery);
(function ($) {
    $('#transifex_live_settings_enable_prerender').machine(
            {
                defaultState: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_enable_prerender::defaultState::onEnter');
                        if (this.prop('checked')) {
                            this.trigger('enable');
                        } else {
                            this.trigger('disable');
                        }
                    },
                    events: {enable: 'enable', disable: 'disable'}
                },
                enable: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_enable_prerender::enable::onEnter');
                        $('.prerender-enable-options').toggleClass('hide-if-js', false);
                        $('input#transifex_live_submit').trigger('disable');
                    },
                    events: {click: 'disable'}
                },
                disable: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_enable_prerender::disable::onEnter');
                        $('.prerender-enable-options').toggleClass('hide-if-js', true);
                        $('#transifex_live_settings_prerender_url').trigger('reset');
                    },
                    events: {click: 'enable'}
                },
            }, {setClass: true}
    );
})(jQuery);

(function ($) {
    $('#transifex_live_start').machine(
            {
                defaultState: {
                    onEnter: function () {
                        $.log.debug('transifex_live_start::defaultState::onEnter');
                    },
                    events: {enable: 'enable'}
                },
                enable: {
                    onEnter: function () {
                        $.log.debug('transifex_live_start::enable::onEnter');
                        this.prop('disabled', false);
                    },
                    events: {click: 'activate'}
                },
                activate: {
                    onEnter: function () {
                        $.log.debug('transifex_live_start::activate::onEnter');
                        var url = $('a#start_link').prop('href');
                        window.open(url, '_blank');
                        this.trigger('enable');
                    },
                    events: {enable: 'enable'}
                }
            }
    )
})(jQuery);



(function ($) {
    $('#transifex_live_settings_prerender_url').machine(
            {
                defaultState: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_prerender_url::defaultState::onEnter');
                    },
                    events: {input: 'edited'}
                },
                edited: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_prerender_url::edited::onEnter');
                        $('input#transifex_live_submit').trigger('enable');
                    },
                    events: {reset: 'reset'}
                },
                reset: {
                    onEnter: function () {
                        $.log.debug('transifex_live_settings_prerender_url::reset::onEnter');
                        this.val('');
                    },
                    events: {input: 'edited'}
                },
            }, {setClass: true}
    );
})(jQuery);
