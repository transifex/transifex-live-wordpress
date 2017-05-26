window.Transifex = window.Transifex || {};

(function (exports, $) {
    function self() {
    }

    exports.httpGet = function (params) {
        $.ajax(
            {
                url: params.url || "BADURL",
                jsonpCallback: params.jsonpCallback || "transifex_manifest",
                jsonp: params.jsonp || true,
                dataType: params.dataType || "jsonp",
                timeout: params.timeout || 3000
            }
        ).done(
                function (data) {
                    params.done(self.validate(data));
                }
        ).fail(
                function () {
                    params.fail();
                }
        );
    };


    self.validate = function (obj) {
        var ret = false;
        var expected_keys = ['picker', 'domain'];
        var env = ($('#transifex_live_settings_enable_staging').prop('checked'))?'staging':'production';
        var keys = Object.keys(obj.settings[env]);
        var diff = [];
        $.grep(
            expected_keys, function (e) {
                if ($.inArray(e, keys) === -1) {
                    diff.push(e); }
            }
        );

        if (diff.length === 0) {
            ret = obj;
        }
        return ret;
    };

})(Transifex, jQuery);
