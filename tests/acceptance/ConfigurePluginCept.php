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
	'transifex_live_settings[staging]' => false,
	'transifex_live_colors[accent]' => '#006f9f',
	'transifex_live_colors[text]' => '#ffffff',
	'transifex_live_colors[background]' => '#000000',
	'transifex_live_colors[menu]' => '#eaf1f7',
	'transifex_live_colors[languages]' => '#666666'
	]);
$I->click('Save Changes');
$I->see('Your changes to the settings have been saved!');
$I->see('Your changes to the colors have been saved!');



