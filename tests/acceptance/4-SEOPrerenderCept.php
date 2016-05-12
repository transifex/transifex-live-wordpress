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
$I->see('Success! Transifex Live sidebar enabled.');
$I->see('Success! Advanced SEO settings enabled.');

$I->executeJS('jQuery("#transifex_live_settings_enable_prerender").click()');
$I->executeJS('jQuery("#transifex_live_settings_prerender_url").val("http://192.168.99.100:32769/");');
$I->executeJS('jQuery("#transifex_live_settings_prerender_url").trigger("input");');
$I->executeJS('jQuery("#transifex_live_settings_prerender_enable_response_header").click();');
$I->executeJS('jQuery("#transifex_live_settings_prerender_enable_cookie").click();');
$I->dontSeeElement('#transifex_live_submit', ['disabled' => 'true']);

$I->executeJS('jQuery("input#transifex_live_submit").click();');
$I->waitForText('Your changes to the settings have been saved!', 7);
$I->runShellCommand("curl -A 'slackbot' http://192.168.99.100:32777/2015/12/17/hello-world/");
$I->seeInShellOutput('X-Prerender-Req: TRUE');
$I->seeInShellOutput('Buffer swapped with prerender response.');
$I->runShellCommand("curl -s -A 'slackbot' -D - http://192.168.99.100:32777/2015/12/17/hello-world/ -o /dev/null");
$I->seeInShellOutput('Vary: User-Agent,X-Prerender-Req,Accept-Encoding');
$I->seeInShellOutput('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
$I->seeInShellOutput('Last-Modified: {now} GMT');
$I->seeInShellOutput('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
$I->seeInShellOutput('Set-Cookie: wordpress_test_cookie=WP%2BCookie%2Bcheck;');