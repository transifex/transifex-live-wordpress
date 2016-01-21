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
        children.toggleClass('hide-if-js',this.value!=='3');
    });
})(jQuery);

(function ($) {
    var parent = $('#transifex_live_settings_add_rewrites_all'),
            children = $('.all_selector');
    parent.change(function () {
        children.prop("checked", this.checked);
    });
})(jQuery);
