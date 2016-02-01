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
