#Template Development Kit

The Transifex Live Integration Plugin now features a number of shortcodes that can aid in the development of templates and themes that are language specific.

This TDK uses the built in WordPress shortcode interface for more information see [the WordPress codex](https://codex.wordpress.org/Shortcode).  In addition to calling shortcodes using square bracket notation...they can also be called directly as PHP functions using the 'do_shortcode' function.  For example: ```do_shortcode('[get_language_url]');```

Currently there are 3 shortcode functions:
- get_language_url
- detect_language
- is_language


get_language_url - returns the localized url given a param 'url'.  Expects the full url and will not work with relative urls.  If no url is specificied, it defaults to the home_url.

detect_language - returns the current locale code used by the plugin.

is_language - expects a 'language' param with the locale code to check for. returns a boolean if the current locale matches.  if no language is given it will return 'true'.


== Examples ==
For a page current using subdirectories in French

```[get_language_url url=http://www.mysite.com/mypost]```

>>http://www.mysite.com/fr/mypost


For a page currently using French (Canadian)

```[detect_language]```

>>fr_CA


For a page currently using English (US)

```[is_language language=en_US]```

>>1
