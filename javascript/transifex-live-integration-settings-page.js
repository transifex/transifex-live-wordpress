
function transifex_live_integration_mapper(l1) {
    return {
        'caption': l1['tx_name'],
        'name': 'transifex-integration-live-' + l1['code'],
        'id': 'transifex-integration-live-[' + l1['code'] + ']',
        'type': 'text',
        'value': l1['code']
    };
}

function transifex_live_integration_convert(l) {
    var r = {"type": "div",
        "id": "transifex-languages"};
    var t = l['translation'];
    var h = [];
    jQuery.each(t, function (i, o) {
        h.push(transifex_live_integration_mapper(o));
    });
    var s = {
        caption: 'Source:' + l['source']['tx_name'],
        name: "transifex-integration-live-source-language",
        id: "transifex-integration-live-[source-language]",
        type: "text",
        value: l['source']['code']
    };
    h.push(s);
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
        console.log(data);
        transifex_language_fields = transifex_live_integration_convert(data);
        jQuery('#transifex_live_settings_api_key').trigger('success');
        jQuery('#transifex_live_settings_api_key').trigger('new');
    });
}

function addTransifexLanguages() {
    jQuery.each(transifex_language_fields['html'], function (i, o) {
        jQuery('#transifex_live_languages').append('<input disabled="true" type="'+o.type+'" class="regular-text" style="width:200px" name="dummy" value="'+o.caption+'" />');
        jQuery('#transifex_live_languages').append('<input type="'+o.type+'" name="'+o.name+'" id="'+o.id+'" value="'+o.value+'" class="regular-text" />');
    });

}

(function ($) {
    $('#transifex_live_languages').machine({
        defaultState: {
            onEnter: function () {
                console.log('#transifex_live_languages:defaultState:onEnter');
            },
            events: {new : 'create'}
        },
        create: {
            onEnter: function () {
                console.log('#transifex_live_languages:create:onEnter');
                $("transifex_live_languages_message").addClass('hide-if-js');
                addTransifexLanguages();
            }
        }
    }, {setClass: true});
})(jQuery);


(function ($) {
    $('#transifex_live_settings_api_key').machine({
        defaultState: {
            onEnter: function () {
                console.log('defaultState:onEnter');
            },
            events: {change: 'validating'}
        },
        validating: {
            onEnter: function () {
                console.log('validating:onEnter');
                $('input#submit').prop('disabled', true);
                transifexLanguages();
            },
            events: {success: 'valid'}
        },
        valid: {
            onEnter: function () {
                console.log('valid:onEnter');
            }
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
