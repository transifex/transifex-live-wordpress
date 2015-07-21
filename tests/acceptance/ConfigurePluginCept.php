<?php 
date_default_timezone_set('America/New_York');

$I = new AcceptanceTester($scenario);
$I->amOnPage('/wp-login.php');
$I->fillField('Username', 'user');
$I->fillField('Password','bitnami');
$I->click('Log In');
$I->see('Dashboard');
$I->click('Settings');
$I->click('Transifex Live');
$I->see('Transifex Live Wordpress Plugin Settings','h2');
$I->selectOption('input[name="transifex_live_settings[picker]"]', 'top-left');
$I->selectOption('input[name="transifex_live_settings[picker]"]', 'bottom-left');
$I->selectOption('input[name="transifex_live_settings[picker]"]', 'bottom-right');
$I->selectOption('input[name="transifex_live_settings[picker]"]', 'custom id');
$I->selectOption('input[name="transifex_live_settings[picker]"]', 'top-right');

$I->seeInFormFields('#settings_form',[ 
	'transifex_live_settings[api_key]' => '',
	'transifex_live_settings[detectlang]' => true,
	'transifex_live_settings[autocollect]' => true,
	'transifex_live_settings[dynamic]' => true,
	'transifex_live_settings[staging]' => false,
	'transifex_live_settings[parse_attr]' => '',
	'transifex_live_settings[ignore_tags]' => '',
	'transifex_live_settings[ignore_class]' => '',
	'transifex_live_settings[picker]' => 'top-right',
	'transifex_live_settings[custom_picker_id]' => '',
	'transifex_live_colors[accent]' => '#006f9f',
	'transifex_live_colors[text]' => '#ffffff',
	'transifex_live_colors[background]' => '#000000',
	'transifex_live_colors[menu]' => '#eaf1f7',
	'transifex_live_colors[languages]' => '#666666'
	]);
$I->click('Save Changes');
$I->see('Your changes to the settings have been saved!');
$I->see('Your changes to the colors have been saved!');



