[![Build Status](https://travis-ci.org/transifex/transifex-live-wordpress.svg?branch=devel)](https://travis-ci.org/transifex/transifex-live-wordpress)

#International SEO by Transifex


[What is Transifex?](https://www.transifex.com/product/?utm_source=github&utm_medium=web&utm_campaign=tx-live-wp-plugin)



== Description ==

The Transifex International SEO is a WordPress plugin designed to be used with Transifex.  In order to use Transifex, you will need to [sign up here for an account](https://www.transifex.com/signup/?utm_source=github&utm_medium=web&utm_campaign=tx-live-wp-plugin). Please note that a Transifex Live API key is required in order to use the plugin.  More information about how to obtain a key can be found in the [plugin documentation here](http://docs.transifex.com/integrations/wordpress/#getting-your-transifex-live-api-key/?utm_source=github&utm_medium=web&utm_campaign=tx-live-wp-plugin) if you don't have a key yet.

== Features ==

* Integrates Transifex into WordPress
* Adds support for localized language URLs either by subdomain or subdirectory.
* Adds support rewriting all URLs on the page
* Automatically adds hreflang tags to your pages.
* Adds supports for using an external prerendered server for SEO purposes
* Works with WordPress multisite

== Get Involved ==

Developers can contribute via this repository. Please send a Pull Request.

Translators can contribute new languages to this plugin or our other WordPress plugins through [Transifex](https://www.transifex.com/projects/p/transifex-live/).

== Minimum Requirements ==

* WordPress 3.5.2 or greater
* PHP version 5.4.0 or greater
* MySQL version 5.0 or greater

=== Installation ===

== Automatic Installation ==

To do an automatic install of Transifex Live, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.

In the search field type "Transifex Live" and click Search Plugins. Once you’ve found the plugin you can view details about it such as the point release, rating, and description. You can install it by simply clicking Install Now. After clicking that link you will be asked if you’re sure you want to install the plugin. Click "yes" and WordPress will automatically complete the installation.
After installation a new menu setting option will appear called 'Transifex Live'.  You will need to complete the admin form before the plugin will become active.

== Manual Installation ==

Manual installation involves downloading the plugin and uploading it to your webserver via your favorite FTP application.

1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.
After installation a new menu setting option will appear called 'Transifex Live'.  You will need to complete the admin form before the plugin will become active.

== Upgrading ==

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Plugin Translations ==

The WP-Translations project for plugin translations can be found [here](https://www.transifex.com/wp-translations/transifex-live/transifex-live/).

== Tips for developers ==

* The Transifex Live plugin uses Wordpress hooks to manipulate the links found in your website's content, so they always point to the appropriate language. If you use custom post types (or one of your plugins does) that emits the 'the_content' filter, our code might not be triggered.

For those cases, it is recommended to manually trigger our custom filter 'tx_link' before you return your content, as seen in the example below:

Ex. $updated_content = apply_filters('tx_link', $original_content);

* It is also recommended  to use [widgets](https://codex.wordpress.org/Widgets_API) in your theme instead of custom code, since this allows you to make your integration more future proof against incompatibilities with 3rd party modules.

== Changelog ==

= 1.3.21 =
Update the latest tested WordPress version (5.4)

= 1.3.20 =
Update the latest tested WordPress version (5.3.2)

= 1.3.19 =
Fix subfolder url rewritting for external domains

= 1.3.18 =
Update the latest tested Wordpress version
Add metrics for Wordpress versions used by the plugin

= 1.3.17 =
Add missing prerender value to Transifex live settings

= 1.3.16 =
Add link filter hook for custom type blocks

= 1.3.15 =
Add filter for custom rewrite rules

= 1.3.14 =
Add hook for handling content's url rewrites

= 1.3.13 =
Support X-Transifex-Lang header to set correct language for prerender request
Minor fixes in the way prerender url is handled

= 1.3.12 =
Minor fixes

= 1.3.11 =
Support Live's manifest.jsonp file

= 1.3.10 =
Update keywords

= 1.3.9 =
Cosmetic changes to plugin copy and WordPress.org assets

= 1.3.8 =
Added staging checkbox to admin page

= 1.3.7 =
Fix to allow custom hreflang code and enhanced subdomain language detection

= 1.3.6 =
Patch release for rewrite fix when locale is in url

= 1.3.5 =
Patch release for improved static front page support

= 1.3.4 =
Minor patch release, cleared up some minor warning issues

= 1.3.3 =
A few minor fixes.  Revised admin UI

= 1.3.2 =
Added additional Prerender options for caching

= 1.3.1 =
Patch for Prerender logic

= 1.3.0 =
Fixed support for PHP 5.4
Fixed hreflang tag output for subdirectories
Improved admin UI
Added picker support that respects locale
Additional admin API key validation
Added Prerendering capability
Fixed some timing issues with the WP loop

= 1.2.0 =
Added support for subdomains
Added reverse lookups for many link types

= 1.1.0 =
Cleaned up readme and notes
Fixed brittle js ordering and namespace
Removed settings that can now be controlled in Transifex Live dashboard
Initial implementation of SEO and lang urls
SEO and lang urls feature switch set to off
Removing staging option (use Transifex dashboard to control it)
SEO and lang urls and HREFLANG enabled
Custom language picker color options removed

= 1.0.0 =
Full release.  Restructured plugin to follow boilerplate.  Added unit tests.
