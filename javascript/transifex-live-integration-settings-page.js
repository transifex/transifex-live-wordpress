
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
  //  h.push(s);
    r['html'] = h;
    return r;
}

function transifexLanguages() {
    jQuery.ajax({
        url: "https://cdn.transifex.com/" + jQuery('#transifex_live_settings_api_key').val() + "/latest/languages.jsonp",
        jsonpCallback: "transifex_languages",
        jsonp: true,
        dataType: "jsonp"
    }).done(function (data) {
        if (data['translation'] != undefined) {
        transifex_language_fields = transifex_live_integration_convert(data);
        jQuery('#transifex_live_settings_api_key').trigger('success');
    } else {
        jQuery('#transifex_live_settings_api_key').trigger('notranslation');
    }
    }).fail(function() {
        jQuery('#transifex_live_settings_api_key').trigger('error');
    });
}

function addTransifexLanguages(obj) {
    if (obj!==null&&obj!==undefined) {
        var lm = jQuery.parseJSON(jQuery('#transifex_live_settings_language_map').val() );
        jQuery.each(obj, function (i, o) {
            jQuery('#transifex_live_languages').append('<input disabled="true" type="text" style="width:200px" name="dummy" value="'+o.tx_name+'" />');
            jQuery('#transifex_live_languages').append('<input type="text" style="width:100px" name="transifex-integration-live-'+o.code+'" id="transifex-integration-live-'+o.code+'" value="'+lm[i][o.code]+'" />');
      
            jQuery('#transifex_live_languages').append('<br/>');
            jQuery('#transifex-integration-live-'+o.code).change(function(){console.log(jQuery(this).val());});
        });
    } else {
    jQuery.each(transifex_language_fields['html'], function (i, o) {
        jQuery('#transifex_live_languages').append('<input disabled="true" type="'+o.type+'" style="width:200px" name="dummy" value="'+o.caption+'" />');
        jQuery('#transifex_live_languages').append('<input type="'+o.type+'" style="width:100px" name="'+o.name+'" id="'+o.id+'" value="'+o.value+'" />');
        jQuery('#transifex_live_languages').append('<br/>');
        jQuery('#'+o.id).change(function(){console.log(jQuery(this).val());});
    });
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
            },
            events: {click: 'checking'}
        },
        checking: {
            onEnter: function () {
                console.log('transifex_live_settings_api_key_button::checking::onEnter');
                $('#transifex_live_settings_api_key').trigger('validating');
            },
             events: {save: 'save',click: 'checking'}
        },
        save: {
            onEnter: function () {
                console.log('transifex_live_settings_api_key_button::save::onEnter');
                this.prop('type','submit').prop('value', 'Save');
            },
        }
    }, {setClass: true});
})(jQuery);


(function ($) {
    $('#transifex_live_settings_api_key').machine({
        defaultState: {
            onEnter: function () {
                console.log('defaultState:onEnter');
            },
            events: {change: 'validating',validating: 'validating'}
        },
        validating: {
            onEnter: function () {
                console.log('validating:onEnter');
                $('input#submit').prop('disabled', true);
                $('#transifex_live_settings_api_key_message').text('Checking Key');
                transifexLanguages();
            },
            events: {success: 'valid', error: 'error', notranslation: 'missing'}
        },
        valid: {
            onEnter: function () {
                console.log('valid:onEnter');
                $('#transifex_live_settings_api_key_button').trigger('save');
                $('#transifex_live_settings_url_options').prop('disabled',false);
                $('#transifex_live_languages').trigger('load');
                $('#transifex_live_settings_api_key_message').text('Valid Key - Enabling Advanced SEO');
                
            },
            events: {success: 'valid', change: 'validating'}
        },
        error: {
            onEnter: function () {
                console.log('error:onEnter');
                $('input#submit').prop('disabled', true);
                $('#transifex_live_settings_api_key_message').text('Error Checking Key - Please Correct Key');
            },
            events: {change: 'validating', validating: 'validating'}
        },
        missing: {
            onEnter: function () {
                console.log('missing:onEnter');
                $('input#submit').prop('disabled', true);
                $('#transifex_live_settings_api_key_message').text('Error No Languages have been Published.<a href="">Learn more</a>');
            },
            events: {validating: 'validating'}
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
            },
            events: {change: function() { return (this.val() === "3")?'subdirectory':'subdomain'}}
        },
        subdirectory: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options::subdirectory::onEnter');
                $('#transifex_live_settings_custom_urls').val("1");
                $('.url-structure-subdirectory').toggleClass('hide-if-js',false);
                $('.url-structure-subdomain').toggleClass('hide-if-js',true);
                $('.custom-urls-settings').toggleClass('hide-if-js',false);
            },
            events: {change: function() { return (this.val() === "2")?'subdomain':'none'}}
        },
        subdomain: {
            onEnter: function () {
                console.log('transifex_live_settings_url_options::subdomain::onEnter');
                $('#transifex_live_settings_custom_urls').val("1");
                $('.url-structure-subdirectory').toggleClass('hide-if-js',true);
                $('.url-structure-subdomain').toggleClass('hide-if-js',false);
                $('.custom-urls-settings').toggleClass('hide-if-js',false);
            },
            events: {change: function() { return (this.val() === "3")?'subdirectory':'none'}}
        }
    }, {setClass: true});
})(jQuery);

/*
 (function ($) {
 var parent = $('#transifex_live_settings_custom_urls'),
 children = $('.custom-urls-settings');
 parent.change(function () {
 children.toggleClass('hide-if-js', !this.checked);
 });
 })(jQuery);
 
 (function ($) {
 var parent = $('#transifex_live_settings_url_options'),
 children = $('.adds-rewrites');
 parent.change(function () {
 children.toggleClass('hide-if-js');
 });
 })(jQuery);
 
 (function ($) {
 var parent = $('#transifex_live_settings_url_options'),
 children = $('.adds-rewrites-subdomain');
 parent.change(function () {
 children.toggleClass('hide-if-js');
 });
 })(jQuery);
 
 (function ($) {
 var parent = $('#transifex_live_options_all'),
 children = $('.all_selector');
 parent.change(function () {
 children.prop("checked", this.checked);
 });
 })(jQuery);
 */
