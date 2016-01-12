<?php 
date_default_timezone_set('America/New_York');

$I = new AcceptanceTester($scenario);
$I->amOnPage('/wp-login.php');
$I->fillField('Username', 'test');
$I->fillField('Password','test');
$I->click('Log In');
$I->see('Dashboard');
$I->click('Settings');
$I->click('Transifex Live');
$I->see('Transifex Live Wordpress Plugin Settings','h2');
$I->seeInFormFields('#settings_form',[ 
	'transifex_live_settings[api_key]' => '',
	'transifex_live_colors[accent]' => '#006f9f',
	'transifex_live_colors[text]' => '#ffffff',
	'transifex_live_colors[background]' => '#000000',
	'transifex_live_colors[menu]' => '#eaf1f7',
	'transifex_live_colors[languages]' => '#666666'
	]);
$I->fillField('transifex_live_settings[api_key]','2699bc66df6546008d0a14acf26732a1');
$I->click('Save Changes');
$I->see('Your changes to the settings have been saved!');
$I->see('Your changes to the colors have been saved!');
$I->seeInFormFields('#settings_form',[ 'transifex_live_settings[add_language_rewrites]' => 'none']);
$I->seeInFormFields('#settings_form',[ 'transifex_live_settings[hreflang]' => '0']);
$I->seeInFormFields('#settings_form',[ 'transifex_live_settings[source_language]' => 'en']);
$I->seeInFormFields('#settings_form',[ 
	'transifex_live_settings[wp_language_fr]' => 'fr',
	'transifex_live_settings[wp_language_de_DE]' => 'de_DE',
	'transifex_live_settings[wp_language_ko]' => 'ko',
	'transifex_live_settings[wp_language_es]' => 'es',
	]);
$I->click('Save Changes');
$I->see('Your changes to the settings have been saved!');
$I->see('Your changes to the colors have been saved!');
$I->amOnPage('/');
$I->seeInSource('<script type="text/javascript" src="//cdn.transifex.com/live.js"></script>');
$I->seeInSource('<script type="text/javascript">window.liveSettings={"api_key":"2699bc66df6546008d0a14acf26732a1","enable_frontend_css":0,"custom_picker_id":""};</script>');




