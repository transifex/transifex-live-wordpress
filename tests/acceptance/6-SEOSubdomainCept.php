<?php 
date_default_timezone_set('America/New_York');

//Live project
//https://www.transifex.com/test-organization-4/wordpress-test-project/wptransifexdevnet-1/
$I = new AcceptanceTester($scenario);
$I->assertTrue(true);
$I->amOnPage('/wp-login.php');
$I->fillField('Username', 'admin');
$I->fillField('Password','admin');
$I->click('Log In');
$I->see('Dashboard');
$I->amOnPage('/wp-admin/options-general.php?page=transifex-live-integration');
$I->see('International SEO by Transifex','h2');
$I->assertTrue($I->executeJS('return (jQuery("#transifex_live_settings_api_key").val()=="2699bc66df6546008d0a14acf26732a1")?true:false;'));

$I->wait(5);

$I->executeJS('jQuery("input#transifex_live_settings_url_options_subdomain").click();');
$I->seeElement('#transifex-integration-live-zh_CN', ['value' => 'cn'] );
$I->seeElement('#transifex-integration-live-de_DE', ['value' => 'de']);

$I->dontSeeElement('#transifex_live_submit', ['disabled' => 'true']);
//$I->dontSeeElement('#submit', ['disabled' => 'true']);
$I->executeJS('jQuery("input#transifex_live_submit").click();');
$I->waitForText('Your changes to the settings have been saved!', 7);
$I->amOnPage('/wp-admin/options-permalink.php');
$I->amOnPage('/');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh-cn"');
$I->seeInSource('href="http://cn.168.99.100:32777/"');
$I->seeInSource('hreflang="de-de"');
$I->seeInSource('href="http://de.168.99.100:32777/"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');

