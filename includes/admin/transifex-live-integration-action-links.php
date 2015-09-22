<?php

class Transifex_Live_Integration_Action_Links {

    static function action_links($links) {
        Plugin_Debug::logTrace();
        $settings_href = add_query_arg(array('page' => TRANSIFEX_LIVE_INTEGRATION_NAME), admin_url('options-general.php'));
        $settings_text = __('Settings', TRANSIFEX_LIVE_INTEGRATION_TEXT_DOMAIN);
        $settings_link = <<<SETTINGS
<a href="$settings_href">$settings_text</a>
SETTINGS;
        return array_merge([$settings_link], $links);
    }

}
