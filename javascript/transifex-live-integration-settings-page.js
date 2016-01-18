(function ($) {
    console.log('do js hide');
    var parent = $('#transifex_live_settings_custom_urls'),
            children = $('.custom-urls-settings');
    parent.change(function () {
        children.toggleClass('hide-if-js', !this.checked);
        console.log('toggle hide');
    });
})(jQuery);
