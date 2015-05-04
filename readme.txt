=== Plugin Name ===
Contributors: ThemeBoy, brianmiyaji, Transifex, @mjjacko
Donate link: http://docs.transifex.com/developer/integrations/wordpress
Tags: transifex, translate, translations, localize, localise, localization, localisation, l10n, i18n, language, switcher, live, translation, translator
Requires at least: 3.0
Tested up to: 4.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== What is Transifex? ==

Localization can be a real hassle – all the searching, coding, tagging, organizing every time you need to update something. Our localization management platform changes all of that with a sophisticated, yet simple, cloud-based approach to collecting, translating and delivering your digital products and supporting content.


== What is Transifex Live? ==

Transifex Live eliminates system integration headaches you run into when setting up a multilingual site – you drop in some JavaScript and you’re done. It’s like installing Google Analytics. For a content heavy site, Transifex Live makes a lot of sense.

== Description ==

Requires a Transifex Live API key (https://www.transifex.com/live/) if you don't have one yet. If you need to obtain one, please see [the documentation]() for setting up this plugin.

== Features ==

* Integrates Transifex Live into your WordPress site using your API key.
* Display language selector in top left, top right, bottom left, or bottom right corner of your site.
* Auto-detect the browser locale and translate the page.
* Automatically identify new strings when page content changes.
* Customize the language switcher by choosing your own color scheme.

== Get Involved ==

Developers can contribute via the plugin's [GitHub Repository](https://github.com/transifex/transifex-live-wordpress).

Translators can contribute new languages to this plugin or our other WordPress plugins through [Transifex](https://www.transifex.com/projects/p/transifex-live/).

== Minimum Requirements ==

* WordPress 3.0 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

=== Installation ===

== Automatic Installation ==

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of Transifex Live, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.

In the search field type "Transifex Live" and click Search Plugins. Once you’ve found the plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking Install Now. After clicking that link you will be asked if you’re sure you want to install the plugin. Click yes and WordPress will automatically complete the installation.

== Manual Installation ==

The manual installation method involves downloading the plugin and uploading it to your webserver via your favorite FTP application.

1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.

== Upgrading ==

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Changelog ==

= 1.0.0 =
Restructured plugin to follow boilerplate.  Added unit tests.

= 0.9.2 =
* Updated options - Add extra option for Tx Live.

= 0.9.1 =
* Documentation - Add link to translation project.

= 0.9 =
* Beta release.
