(function ($) {
    var parent = $('#transifex_live_settings_custom_urls'),
            children = $('.custom-urls-settings');
    parent.change(function () {
        children.toggleClass('hide-if-js', !this.checked);
    });
})(jQuery);

(function ($) {
    console.log('checker');
    var parent = $('#transifex_live_settings_add_rewrites_all'),
            children = $('.all_selector');
    parent.change(function () {
        console.log('check all');
        children.prop("checked", this.checked);
    });
})(jQuery);
