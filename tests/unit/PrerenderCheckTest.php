<?php

include_once __DIR__ .'/BaseTestCase.php';

class PrerenderCheckTest extends BaseTestCase
{

    private $data;

    protected function setUp(): void
    {
        include_once './includes/common/plugin-debug.php';
        include_once './includes/transifex-live-integration-util.php';
        include_once './includes/transifex-live-integration-defaults.php';
        $settings = Transifex_Live_Integration_Defaults::settings();
        $whitelist = $settings['whitelist_crawlers'];
        $bot_types = $settings['generic_bot_types'];
        $this->data = [[ // Standard Chrome Agent
        'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36',
        'fragment' => false,
        'bot_types' => $bot_types,
        'whitelist' => $whitelist,
        'result' => false
        ],[ // Standard Firefox Agent
        'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:46.0) Gecko/20100101 Firefox/46.0',
        'fragment' => false,
        'bot_types' => $bot_types,
        'whitelist' => $whitelist,
        'result' => false
        ], [ // Prerender added
        'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36 Prerender (+https://github.com/prerender/prerender)',
        'fragment' => false,
        'bot_types' => $bot_types,
        'whitelist' => $whitelist,
        'result' => false
        ], [ // Standard Googlebot
        'agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'fragment' => false,
        'bot_types' => $bot_types,
        'whitelist' => $whitelist,
        'result' => true
        ], [ // Googlebot with prerender added
        'agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html) Prerender (+https://github.com/prerender/prerender)',
        'fragment' => false,
        'bot_types' => $bot_types,
        'whitelist' => $whitelist,
        'result' => true
        ], [ // Slackbot with
        'agent' => 'Slackbot-LinkExpanding 1.0 (+https://api.slack.com/robots)',
        'fragment' => false,
        'bot_types' => $bot_types,
        'whitelist' => $whitelist,
        'result' => true
        ],[ // Slackbot with
        'agent' => 'slackbot',
        'fragment' => false,
        'bot_types' => $bot_types,
        'whitelist' => $whitelist,
        'result' => true
        ],[ // some fragment
        'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36',
        'fragment' => true,
        'bot_types' => $bot_types,
        'whitelist' => $whitelist,
        'result' => true
        ]
        ];
    }

    public function testMe()
    {
        foreach ($this->data as $i) {
            $result = Transifex_Live_Integration_Util::prerender_check($i['agent'], $i['fragment'], $i['bot_types'], $i['whitelist']);

            //eval(\Psy\sh());
            $this->assertEquals($i['result'], $result);
        }
    }

}
