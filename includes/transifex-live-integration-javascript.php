<?php

class Transifex_Live_Integration_Javascript {

    private $live_settings = array();

    public function __construct($live_settings) {
        Plugin_Debug::logTrace();
        $this->live_settings = $live_settings;
    }

    function render() {
        Plugin_Debug::logTrace();
        $live_settings_string = json_encode($this->live_settings);
        Plugin_Debug::logTrace($live_settings_string);
        echo <<<LIVE
<script type="text/javascript">window.liveSettings=$live_settings_string;</script>
<script type="text/javascript" src="//cdn.transifex.com/live.js"></script>
LIVE;
    }

}