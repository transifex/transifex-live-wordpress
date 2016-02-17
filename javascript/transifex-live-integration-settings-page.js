
function transifex_live_integration_mapper(l1) {
    return {
        'caption': l1['tx_name'],
        'name': 'transifex-integration-live-' + l1['code'],
        'id': 'transifex-integration-live-' + l1['code'],
        'type': 'text',
        'value': l1['code']
    };
}

function transifex_live_integration_convert(l) {
    var r = {"type": "div",
        "id": "transifex-languages"};
    var t = l['translation'];
    var h = [];
    transifex_languages = [];
    language_lookup = [];
    language_map = [];
    jQuery.each(t, function (i, o) {
        h.push(transifex_live_integration_mapper(o));
        transifex_languages.push(o['code']);
        var arr = {};
        arr['tx_name'] = o['tx_name'];
        arr['code'] = o['code'];
        language_lookup.push(arr);
        var arrr = {};
        arrr[o['code']] = o['code'];
        language_map.push(arrr);
    });
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
    console.log('transifexLanguages');
    var apikey = jQuery('#transifex_live_settings_api_key').val();
    if (apikey!='') {
    jQuery.ajax({
        url: "https://cdn.transifex.com/" + apikey + "/latest/languages.jsonp",
        jsonpCallback: "transifex_languages",
        jsonp: true,
        dataType: "jsonp",
        timeout : 3000
    }).done(function (data) {
        console.log('done');
        if (data['translation'] != undefined && data['translation'].length > 0) {
            console.log('success');
        transifex_language_fields = transifex_live_integration_convert(data);
        jQuery('#transifex_live_settings_api_key').trigger('success');
    } else {
        console.log('no translation index');
        jQuery('#transifex_live_settings_api_key').trigger('notranslation');
    }
    }).fail(function() {
        console.log('failed');
        jQuery('#transifex_live_settings_api_key').trigger('error');
    }).always(function(jqXHR, textStatus) { 
        console.log(jqXHR);
        console.log(textStatus);
    });
} else {
    jQuery('#transifex_live_settings_api_key').trigger('blank');
}
}

function addTransifexLanguages(obj) {
    var language_fields = '<table><tr><th scope="row">Language</th><th scope="row">Code</th></tr>';
    if (obj!==null&&obj!==undefined) {
        var lm = jQuery.parseJSON(jQuery('#transifex_live_settings_language_map').val() );
        jQuery.each(obj, function (i, o) {
            language_fields = language_fields + '<tr><td style="padding:0px"><span>'+o.tx_name+'</span></td><td style="padding:0px"><input type="text" style="width:100px" name="transifex-integration-live-'+o.code+'" id="transifex-integration-live-'+o.code+'" value="'+lm[i][o.code]+'" /></td></tr>';
          //  jQuery('#transifex-integration-live-'+o.code).change(function(){console.log(jQuery(this).val());});
        });
    } else {
    jQuery.each(transifex_language_fields['html'], function (i, o) {
        language_fields = language_fields + '<tr><td style="padding:0px"><span>'+o.caption+'</span></td><td style="padding:0px"><input type="'+o.type+'" style="width:100px" name="'+o.name+'" id="'+o.id+'" value="'+o.value+'" /></td></tr>';
//        jQuery('#'+o.id).change(function(){console.log(jQuery(this).val());});
    });
    language_fields = language_fields + '</table>';
    jQuery('#transifex_live_languages').append(language_fields);
    jQuery('#transifex_live_settings_source_language').val(source_language);
    jQuery('#transifex_live_settings_transifex_languages').val(JSON.stringify(transifex_languages));
    jQuery('#transifex_live_settings_language_lookup').val(JSON.stringify(language_lookup));
    jQuery('#transifex_live_settings_language_map').val(JSON.stringify(language_map));
    }
}

(function ($) {
    $('#transifex_live_languages').machine({
        defaultState: {
            onEnter: function () {
                console.log('#transifex_live_languages:defaultState:onEnter');
                ($('#transifex_live_settings_language_lookup').val()!=='')?this.trigger('render'):this.trigger('wait');
               
            },
            events: {render : 'render',wait: 'wait'}
        },
        wait:{  
            onEnter: function () {
                console.log('#transifex_live_languages:wait:onEnter');
            },
            events: {load : 'loadnew'}
        },
        loadnew: {
            onEnter: function () {
                console.log('#transifex_live_languages:load:onEnter');
                $("#transifex_live_languages_message").toggleClass('hide-if-js',true);
                addTransifexLanguages();
            }
        },
        render: {
            onEnter: function () {
                console.log('#transifex_live_languages:render:onEnter');
                $("#transifex_live_languages_message").toggleClass('hide-if-js',true);
                var obj = jQuery.parseJSON(jQuery('#transifex_live_settings_language_lookup').val() );
                myobj = obj;
                console.log(obj);
                addTransifexLanguages(obj);
            }
        }
    }, {setClass: true});
})(jQuery);


(function ($) {
    $('#transifex_live_settings_api_key_button').machine({
        defaultState: {
            onEnter: function () {
                console.log('transifex_live_settings_api_key_button::defaultState::onEnter');
                ($('#transifex_live_settings_api_key').val()!=='')?this.trigger('hiddencheck'):this.trigger('waitforclick');
            },
            events: {waitforclick: 'wait', hiddencheck:'hidden'}
        },
        wait: {
            onEnter: function () {
                console.log('transifex_live_settings_api_key_button::wait::onEnter');
                $('#transifex_live_settings_api_key').trigger('validating');
            },
             events: {click: 'checking'}
        },
        checking: {
            onEnter: function () {
                console.log('transifex_live_settings_api_key_button::checking::onEnter');
                $('#transifex_live_settings_api_key').trigger('validating');
            },
             events: {click: 'checking'}
        },
        hidden: {
            onEnter: function () {
                console.log('transifex_live_settings_api_key_button::hidden::onEnter');
            },
            events: {wait: 'wait'}
        }
    }, {setClass: true});
})(jQuery);


(function ($) {
    $('#transifex_live_settings_api_key').machine({
        defaultState: {
            onEnter: function () {
                console.log('transifex_live_settings_api_key:defaultState:onEnter');
                this.trigger('validating');
            },
        events: {validating: 'validating'}
        },
        validating: {
            onEnter: function () {
                console.log('transifex_live_settings_api_key:validating:onEnter');
                $('#transifex_live_settings_api_key_button').prop('disabled', true);
                $('#transifex_live_settings_api_key_message').text('Checking Key');
                transifexLanguages();
            },
            events: {success: 'valid', blank:'blank', error: 'error', notranslation: 'missing',change: 'validating'}
        },
        valid: {
            onEnter: function () {
                console.log('valid:onEnter');
                $('#transifex_live_settings_api_key_button').hide();
                $('#transifex_live_settings_url_options_none').attr('disabled',false);
                $('#transifex_live_settings_url_options_subdirectory').attr('disabled',false);
                $('#transifex_live_settings_url_options_subdomain').attr('disabled',false);
                $('#transifex_live_languages').trigger('load');
                $('#transifex_live_settings_api_key_message').text('Valid Key - Enabling Advanced SEO');
                $('#transifex_live_settings_api_key_button').prop('disabled', true);
            },
            events: {success: 'valid', change: 'validating'}
        },
        error: {
            onEnter: function () {
                console.log('error:onEnter');
                $('#transifex_live_settings_api_key_button').prop('disabled', false);
                $('#transifex_live_settings_api_key_message').text('Error Checking Key - Please Correct Key');
            },
            events: {change: 'validating', validating: 'validating'}
        },
        blank: {
            onEnter: function () {
                console.log('transifex_live_settings_api_key:blank:onEnter');
                $('#transifex_live_settings_api_key_button').trigger('wait');
                $('#transifex_live_settings_api_key_message').text('');
                $('#transifex_live_settings_api_key_button').prop('disabled', false);
            },
            events: {change: 'validating', validating: 'validating'}
        },
        missing: {
            onEnter: function () {
                console.log('missing:onEnter');
                $('#transifex_live_settings_api_key_button').prop('disabled', false);
                $('#transifex_live_settings_api_key_message').text('Error No Languages have been Published.');
            },
            events: {validating: 'validating'}
        },
    }, {setClass: true});
})(jQuery);

(function ($) {
    $('#transifex_live_settings_url_options_none').machine({
        defaultState: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options_none::defaultState::onEnter');
            },
            events: {click:'on'}
        },
        on: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options_none::on::onEnter');
                $('#transifex_live_settings_url_options').trigger('none');
            },
            events: {click:'on'}
        },  
    }, {setClass: true});
})(jQuery);

(function ($) {
    $('#transifex_live_settings_url_options_subdirectory').machine({
        defaultState: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options_subdirectory::defaultState::onEnter');
            },
            events: {click:'on'}
        },
        on: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options_subdirectory::on::onEnter');
                $('#transifex_live_settings_url_options').trigger('subdirectory');
            },
            events: {click:'on'}
        },  
    }, {setClass: true});
})(jQuery);

(function ($) {
    $('#transifex_live_settings_url_options_subdomain').machine({
        defaultState: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options_subdomain::defaultState::onEnter');
            },
            events: {click:'on'}
        },
        on: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options_subdomain::on::onEnter');
                $('#transifex_live_settings_url_options').trigger('subdomain');
            },
            events: {click:'on'}
        },  
    }, {setClass: true});
})(jQuery);

(function ($) {
    $('#transifex_live_settings_url_options').machine({
        defaultState: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options::defaultState::onEnter');
                (this.val() === "1")?this.trigger('none'):(this.val() === "2")?this.trigger('subdomain'):this.trigger('subdirectory');
            },
            events: {none:'none',subdomain:'subdomain',subdirectory:'subdirectory'}
        },
        none: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options::none::onEnter');
                $('.url-structure-subdirectory').toggleClass('hide-if-js',true);
                $('.url-structure-subdomain').toggleClass('hide-if-js',true);
                $('.custom-urls-settings').toggleClass('hide-if-js',true);
                $('#transifex_live_settings_url_options_subdirectory').prop( "checked", false );
                $('#transifex_live_settings_url_options_subdomain').prop( "checked", false );
                this.val('1');
            },
            events: {none:'none',subdomain:'subdomain',subdirectory:'subdirectory'}
        },
        subdirectory: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options::subdirectory::onEnter');
                $('#transifex_live_settings_custom_urls').val("1");
                $('.url-structure-subdirectory').toggleClass('hide-if-js',false);
                $('.url-structure-subdomain').toggleClass('hide-if-js',true);
                $('.custom-urls-settings').toggleClass('hide-if-js',false);
                $('#transifex_live_options_all').trigger('activate');
                $('#transifex_live_settings_url_options_none').prop( "checked", false );
                $('#transifex_live_settings_url_options_subdomain').prop( "checked", false );
                this.val(2);
            },
            events: {none:'none',subdomain:'subdomain',subdirectory:'subdirectory'}
        },
        subdomain: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options::subdomain::onEnter');
                $('#transifex_live_settings_custom_urls').val("1");
                $('.url-structure-subdirectory').toggleClass('hide-if-js',true);
                $('.url-structure-subdomain').toggleClass('hide-if-js',false);
                $('.custom-urls-settings').toggleClass('hide-if-js',false);
                $('#transifex_live_options_all').trigger('activate');
                $('#transifex_live_settings_url_options_subdirectory').prop( "checked", false );
                $('#transifex_live_settings_url_options_none').prop( "checked", false );
                this.val('3');
            },
            events: {none:'none',subdomain:'subdomain',subdirectory:'subdirectory'}
        }
    }, {setClass: true});
})(jQuery);


(function ($) {
    $('#transifex_live_settings_rewrite_option_all').machine({
        defaultState: {
            onEnter: function () {
                console.log('transifex_live_settings_rewrite_option_all::defaultState::onEnter');
                if (this.prop('checked')) {
                    this.trigger('seton');
                } else {
                    this.trigger('setoff');
                }
            },
            events: {seton: 'on',setoff: 'off'}
        },
        on: {
            onEnter: function () {
                console.log('transifex_live_settings_rewrite_option_all::on::onEnter');
                this.prop('checked', true);
                $('.all_selector').trigger('on');
            },
             events: {click: 'off',off: 'off',singleoff:'singleoff'}
        },
        off: {
            onEnter: function () {
                console.log('transifex_live_settings_rewrite_option_all::off::onEnter');
                this.prop('checked', false);
                $('.all_selector').trigger('off');
            },
           events: {click: 'on'} 
        },
        singleoff: {
            onEnter: function () {
                console.log('transifex_live_settings_rewrite_option_all::singleoff::onEnter');
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
                console.log('all_selector::defaultState::onEnter');
                if (this.prop('checked')) {
                    this.trigger('seton');
                } else {
                    this.trigger('setoff');
                }
            },
            events: {seton: 'on',setoff: 'off'}
        },
        on: {
            onEnter: function () {
                console.log('all_selector::on::onEnter');
                this.prop("checked", true);
            },
             events: {click: 'off', off: 'off'}
        },
        off: {
            onEnter: function () {
                console.log('all_selector::off::onEnter');
                this.prop("checked", false);
                $('#transifex_live_settings_rewrite_option_all').trigger('singleoff');
            },
           events: {click: 'on', on: 'on'} 
        }
    }, {setClass: true});
})(jQuery);