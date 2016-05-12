<?php 
date_default_timezone_set('America/New_York');

$I = new AcceptanceTester($scenario);
$I->assertTrue(true);
$I->amOnPage('/wp-login.php');
$I->fillField('Username', 'admin');
$I->fillField('Password','admin');
$I->click('Log In');
$I->see('Dashboard');
$I->amOnPage('/wp-admin/options-general.php?page=transifex-live-integration');
$I->see('Transifex Live Translation Plugin Settings','h2');
$I->assertTrue($I->executeJS('return (jQuery("#transifex_live_settings_api_key").val()=="")?true:false;'));
$I->executeJS('jQuery("#transifex_live_settings_api_key").val("2699bc66df6546008d0a14acf26732a1");');
$I->executeJS('jQuery("#transifex_live_settings_api_key_button").click();');

$I->wait(5);
$I->see('Success! Transifex Live sidebar enabled.');

$I->executeJS('jQuery("#transifex_live_settings_enable_seo").trigger("click");');
$I->executeJS('jQuery("#transifex_live_settings_url_options_subdirectory").trigger("click");');
$I->seeElement('#transifex-integration-live-zh_CN');
$I->seeElement('#transifex-integration-live-de_DE');
$I->seeElement('#transifex_live_submit', ['disabled' => 'true']);
$I->executeJS('jQuery("#transifex_live_settings_rewrite_option_all").trigger("click");');
$I->dontSeeElement('#transifex_live_submit', ['disabled' => 'true']);
$I->executeJS('jQuery("input#transifex_live_submit").click();');

$I->waitForText('Your changes to the settings have been saved!', 7);
$I->amOnPage('/wp-admin/options-permalink.php');
$I->amOnPage('/');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh_CN"');
$I->seeInSource('hreflang="de_DE"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "en";}');


$I->amOnPage('/zh_CN/');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh_CN"');
$I->seeInSource('hreflang="de_DE"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "zh_CN";}');
$I->seeLink('Sample Page','http://192.168.99.100:32777/zh_CN/sample-page/');
$I->seeLink('Hello world!','http://192.168.99.100:32777/zh_CN/2015/12/17/hello-world/');
$I->seeLink('Home','http://192.168.99.100:32777/zh_CN/home/');
$I->seeLink('Blog','http://192.168.99.100:32777/zh_CN/blog/');

$I->amOnPage('/de_DE/');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh_CN"');
$I->seeInSource('hreflang="de_DE"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "de_DE";}');
$I->seeLink('Sample Page','http://192.168.99.100:32777/de_DE/sample-page/');
$I->seeLink('Hello world!','http://192.168.99.100:32777/de_DE/2015/12/17/hello-world/');
$I->seeLink('Home','http://192.168.99.100:32777/de_DE/home/');
$I->seeLink('Blog','http://192.168.99.100:32777/de_DE/blog/');


