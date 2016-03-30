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
$I->assertTrue($I->executeJS('return (jQuery("#transifex_live_settings_api_key").val()=="2699bc66df6546008d0a14acf26732a1")?true:false;'));
$I->wait(5);
$I->see('Success! Advanced SEO settings enabled.');
$I->checkOption('#transifex_live_settings_url_options_subdirectory');
$I->seeElement('#transifex-integration-live-zh_CN', ['value' => 'zh_CN'] );
$I->seeElement('#transifex-integration-live-de_DE', ['value' => 'de_DE']);
$I->executeJS('jQuery("#transifex-integration-live-zh_CN").val("cn");');
$I->executeJS('jQuery("#transifex-integration-live-zh_CN").trigger("change");');
$I->seeElement('#transifex-integration-live-zh_CN', ['value' => 'cn']);

$I->executeJS('jQuery("#transifex-integration-live-de_DE").val("de");');
$I->executeJS('jQuery("#transifex-integration-live-de_DE").trigger("change");');
$I->seeElement('#transifex-integration-live-de_DE', ['value' => 'de']);

$I->dontSeeElement('#transifex_live_submit', ['disabled' => 'true']);
//$I->dontSeeElement('#submit', ['disabled' => 'true']);
$I->executeJS('jQuery("input#transifex_live_submit").click();');
$I->waitForText('Your changes to the settings have been saved!', 7);
$I->amOnPage('/wp-admin/options-permalink.php');
$I->amOnPage('/');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="cn"');
$I->seeInSource('hreflang="de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "en";}');


$I->amOnPage('/cn/');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="cn"');
$I->seeInSource('hreflang="de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "zh_CN";}');
$I->seeLink('Sample Page','http://192.168.99.100:32777/cn/sample-page/');
$I->seeLink('Hello world!','http://192.168.99.100:32777/cn/2015/12/17/hello-world/');
$I->seeLink('Home','http://192.168.99.100:32777/cn/home/');
$I->seeLink('Blog','http://192.168.99.100:32777/cn/blog/');

$I->amOnPage('/de/');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="cn"');
$I->seeInSource('hreflang="de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "de_DE";}');
$I->seeLink('Sample Page','http://192.168.99.100:32777/de/sample-page/');
$I->seeLink('Hello world!','http://192.168.99.100:32777/de/2015/12/17/hello-world/');
$I->seeLink('Home','http://192.168.99.100:32777/de/home/');
$I->seeLink('Blog','http://192.168.99.100:32777/de/blog/');
