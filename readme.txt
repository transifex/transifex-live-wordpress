=== Plugin Name ===
Name: Transifex Live Translation Plugin
Contributors: txmatthew, ThemeBoy, brooksx
Tags: transifex, translate, translations, localize, localise, localization, localisation, multilingual, t9n, l10n, i18n, language, switcher, live, translation, translator
Requires at least: 3.5.2
Tested up to: 4.4.1
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Translate your WordPress website or blog without the usual complex setups.

== Description ==

Transifex Live makes it easy to translate WordPress sites while making them SEO-friendly. There’s no need to create one language per post, insert language tags, or have multiple WordPress instances. Your site’s content is automatically detected and ready to be saved to the Transifex localization platform, where you can translate with the help of your existing translators, or order professional translations from Transifex partners.

When the translations are done, take them live with the click of a button, just like you would with a blog post. The next time someone visits your WordPress site, they’ll automatically see the latest content in their native language.

In order to use Transifex Live, you will need to [sign up here for an account](https://www.transifex.com/signup/?utm_source=wp-directory&utm_campaign=int-wp). This plugin also requires a Transifex Live API key. More information about how to obtain a key can be found in the [plugin documentation](http://docs.transifex.com/integrations/wordpress/#getting-your-transifex-live-api-key/?utm_source=wp-directory&utm_campaign=int-wp).

Features:

* Simple installation of Transifex Live.
* Automatically identify new or changed content on your site.
* Translate your website in context.
* Easily set unique language or region-specific URLs.
* Add hreflang tags to your pages and tell search engines what language a page is in.

Learn more about the [Transifex Live Translation Plugin](https://www.transifex.com/integrations/wordpress-multilingual-plugin/?utm_source=wp-directory&utm_campaign=int-wp).

Get Involved:

Developers can contribute via the plugin's [GitHub Repository](https://github.com/transifex/transifex-live-wordpress).

Translators can contribute new languages to this plugin or our other WordPress plugins through [Transifex](https://www.transifex.com/wp-translations/transifex-live/?utm_source=wp-directory&utm_campaign=int-wp).

Minimum Requirements:

* WordPress 3.5.2 or greater
* PHP version 5.5.0 or greater
* MySQL version 5.0 or greater

== Installation ==

Automatic

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of Transifex Live, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.

In the search field type "Transifex Live" and click Search Plugins. Once you’ve found the plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking Install Now. After clicking that link you will be asked if you’re sure you want to install the plugin. Click yes and WordPress will automatically complete the installation.

Manual

The manual installation method involves downloading the plugin and uploading it to your webserver via your favorite FTP application.

1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.

Upgrading

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Screenshots ==

1. screenshot-1.jpg
2. screenshot-2.jpg

== Changelog ==

= 1.0.0 =
Full release.  Restructured plugin to follow boilerplate.  Added unit tests.

= 1.0.1 =
Cleaned up readme and notes

= 1.0.2 =
Fixed brittle js ordering and namespace

= 1.0.3 =
Removed settings that can now be controlled in Transifex Live dashboard

= 1.0.4 =
Initial implementation of SEO and lang urls

= 1.0.5 =
SEO and lang urls feature switch set to off

= 1.0.6 =
Removing staging option (use Transifex dashboard to control it)

= 1.1.0 =
SEO and lang urls and HREFLANG enabled
Custom language picker color options removed

= 1.2.0 =
Added support for subdomains
Added reverse lookups for many link types

= 1.2.1 =
Fixed support for PHP 5.4

= 1.2.2 =
Improved admin UI
