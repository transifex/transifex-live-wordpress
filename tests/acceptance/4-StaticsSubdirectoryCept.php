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
$I->see('Transifex Live Translation Plugin Settings','h2');
$I->assertTrue($I->executeJS('return (jQuery("#transifex_live_settings_api_key").val()=="2699bc66df6546008d0a14acf26732a1")?true:false;'));

$I->wait(5);

$I->checkOption('#transifex_live_settings_url_options_subdirectory');
//$I->executeJS('jQuery("#transifex-integration-live-zh_CN").val("cn");');
//$I->executeJS('jQuery("#transifex-integration-live-zh_CN").trigger("change");');
$I->seeElement('#transifex-integration-live-zh_CN', ['value' => 'cn']);

//$I->executeJS('jQuery("#transifex-integration-live-de_DE").val("de");');
//$I->executeJS('jQuery("#transifex-integration-live-de_DE").trigger("change");');
$I->seeElement('#transifex-integration-live-de_DE', ['value' => 'de']);

$I->dontSeeElement('#transifex_live_submit', ['disabled' => 'true']);
//$I->dontSeeElement('#submit', ['disabled' => 'true']);
//$I->executeJS('jQuery("input#transifex_live_submit").click();');
//$I->waitForText('Your changes to the settings have been saved!', 7);
$I->amOnPage('/wp-admin/options-reading.php');
$I->executeJS("jQuery('input:radio[name=show_on_front][value=page]').prop('checked', true);");

$I->executeJS('jQuery("#page_on_front").prop("disabled",false);');
$I->executeJS('jQuery("#page_on_front").val("1064");');

$I->executeJS('jQuery("#page_for_posts").prop("disabled",false);');
$I->executeJS('jQuery("#page_for_posts").val("1066");');

$I->assertTrue($I->executeJS('return (jQuery("#page_on_front").val() === "1064");'));
$I->assertTrue($I->executeJS('return (jQuery("#page_for_posts").val() === "1066");'));

$I->executeJS('jQuery("#submit").click();');
$I->wait(5);
$I->see('Settings saved.');

$I->amOnPage('/wp-admin/options-permalink.php');
$I->executeJS('jQuery("#custom_selection").prop("checked",true);');
$I->executeJS('jQuery("#permalink_structure").val("/blog/%postname%/");');

$I->assertTrue($I->executeJS('return (jQuery("#permalink_structure").val() === "/blog/%postname%/");'));
$I->executeJS('jQuery("#submit").click();');
$I->wait(5);
$I->see('Permalink structure updated.');


$I->amOnPage('/');
$I->see('Home');
$I->see('Note that you will probably want to test this page in conjunction with the Blog page.');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh-cn"');
$I->seeInSource('hreflang="de-de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "en";}');

$I->amOnPage('/cn/');
$I->see('首页'); //Home
$I->see('Note that you will probably want to test this page in conjunction with the Blog page.');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh-cn"');
$I->seeInSource('hreflang="de-de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "zh_CN";}');
$I->seeLink('Sample Page','http://192.168.99.100:32777/cn/sample-page/');
$I->seeLink('Hello world!','http://192.168.99.100:32777/cn/blog/hello-world/');
$I->seeLink('首页','http://192.168.99.100:32777/cn/');
$I->seeLink('博客','http://192.168.99.100:32777/cn/blog/'); //Blog

$I->amOnPage('/de/');
$I->see('Haus'); // Home
$I->see('Note that you will probably want to test this page in conjunction with the Blog page.');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh-cn"');
$I->seeInSource('hreflang="de-de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "de_DE";}');
$I->seeLink('Sample Page','http://192.168.99.100:32777/de/sample-page/');
$I->seeLink('Hello world!','http://192.168.99.100:32777/de/blog/hello-world/');
$I->seeLink('Home','http://192.168.99.100:32777/de/home/');
$I->seeLink('Blog','http://192.168.99.100:32777/de/blog/');

$I->amOnPage('/blog/');
$I->see('Sticky');
$I->seeInSource('href="http://192.168.99.100:32777/blog/"');
$I->seeInSource('hreflang="en"');
$I->seeInSource('href="http://192.168.99.100:32777/cn/blog/"');
$I->seeInSource('hreflang="zh-cn"');
$I->seeInSource('href="http://192.168.99.100:32777/de/blog/"');
$I->seeInSource('hreflang="de-de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "en";}');

$I->amOnPage('/de/blog/');
$I->see('Klebrige'); // Sticky
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh-cn"');
$I->seeInSource('hreflang="de-de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "de_DE";}');

$I->amOnPage('/cn/blog/');
$I->see('粘性'); // Sticky
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh-cn"');
$I->seeInSource('hreflang="de-de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "zh_CN";}');

$I->amOnPage('/wp-admin/options-reading.php');
$I->executeJS('jQuery("#page_for_posts").val("0");');
$I->assertTrue($I->executeJS('return (jQuery("#page_for_posts").val() === "0");'));
$I->executeJS('jQuery("#submit").click();');

$I->wait(5);
$I->see('Settings saved.');

$I->amOnPage('/blog/');
$I->see('Blog');
$I->see('Note that you will probably want to test this page in conjunction with the Home page.');
$I->seeInSource('href="http://192.168.99.100:32777/blog/"');
$I->seeInSource('hreflang="en"');
$I->seeInSource('href="http://192.168.99.100:32777/cn/blog/"');
$I->seeInSource('hreflang="zh-cn"');
$I->seeInSource('href="http://192.168.99.100:32777/de/blog/"');
$I->seeInSource('hreflang="de-de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "en";}');

$I->amOnPage('/de/blog/');
$I->see('Blog');
$I->see('Note that you will probably want to test this page in conjunction with the Home page.');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh-cn"');
$I->seeInSource('hreflang="de-de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "de_DE";}');

$I->amOnPage('/cn/blog/');
$I->see('博客'); //Blog
$I->see('Note that you will probably want to test this page in conjunction with the Home page.');
$I->seeInSource('hreflang="en"');
$I->seeInSource('hreflang="zh-cn"');
$I->seeInSource('hreflang="de-de"');
$I->seeInSource('src="//cdn.transifex.com/live.js"');
$I->seeInSource('window.liveSettings');
$I->seeInSource('"api_key":"2699bc66df6546008d0a14acf26732a1"');
$I->seeInSource('"detectlang":function() { return "zh_CN";}');

$I->amOnPage('/wp-admin/options-reading.php');
$I->executeJS("jQuery('input:radio[name=show_on_front][value=posts]').prop('checked', true);");
$I->executeJS('jQuery("#submit").click();');

$I->wait(5);
$I->see('Settings saved.');