[![Build Status](https://travis-ci.org/transifex/transifex-live-wordpress.svg?branch=devel)](https://travis-ci.org/transifex/transifex-live-wordpress)

#Transifex Live Translation Plugin


[What is Transifex?](https://www.transifex.com/product/?utm_source=github&utm_medium=web&utm_campaign=tx-live-wp-plugin)



[What is Transifex Live?](https://www.transifex.com/product/transifexlive/)


== Description ==

In order to use Transifex Live, you will need to [sign up here for an account](https://www.transifex.com/signup/?utm_source=github&utm_medium=web&utm_campaign=tx-live-wp-plugin). This plugin also requires a Transifex Live API key.  More information about how to obtain a key can be found in the [plugin documentation here](http://docs.transifex.com/integrations/wordpress/#getting-your-transifex-live-api-key/?utm_source=github&utm_medium=web&utm_campaign=tx-live-wp-plugin) if you don't have a key yet.  

== Features ==

* Simple installation of Transifex Live.
* Automatically identify new or changed content on your site.
* Translate your website in context.
* Easily set unique language or region-specific URLs either by subdomain or subdirectory.
* Integrated language picker 
* Add hreflang tags to your pages and tell search engines what language a page is in.
* Supports using a prerendered server for SEO purposes

== Get Involved ==

Developers can contribute via this repository. Please send a Pull Request.

Translators can contribute new languages to this plugin or our other WordPress plugins through [Transifex](https://www.transifex.com/projects/p/transifex-live/).

== Minimum Requirements ==

* WordPress 3.5.2 or greater
* PHP version 5.4.0 or greater
* MySQL version 5.0 or greater

=== Installation ===

== Automatic Installation ==

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of Transifex Live, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.

In the search field type "Transifex Live" and click Search Plugins. Once you’ve found the plugin you can view details about it such as the point release, rating, and description. Most importantly of course, you can install it by simply clicking Install Now. After clicking that link you will be asked if you’re sure you want to install the plugin. Click "yes" and WordPress will automatically complete the installation.

== Manual Installation ==

Manual installation involves downloading the plugin and uploading it to your webserver via your favorite FTP application.

1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.

== Upgrading ==

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Plugin Translations ==

The WP-Translations project for plugin translations can be found [here](https://www.transifex.com/wp-translations/transifex-live/transifex-live/).

== Changelog ==

= 1.0.0 =
Full relase.  Restructured plugin to follow boilerplate.  Added unit tests.

= 1.0.1 =
Minor edits for documentation

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
Fixed hreflang tag output for subdirectories

= 1.2.3 =
Improved admin UI

= 1.2.4 =
Added picker support that respects locale
Additional admin API key validation

= 1.3.0 =
Added Prerendering capability
Fixed some timing issues with the WP loop

= 1.3.1 =
Patch for Prerender logic

= 1.3.2 =
Added additional Prerender options for caching