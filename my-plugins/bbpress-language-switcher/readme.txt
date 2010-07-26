=== bbPress Language Switcher  ===
Tags: _ck_, language, i18n, L10n, internationalization, translate, translation, mo, po, poedit
Contributors: _ck_
Requires at least: 0.9
Tested up to: 0.9
Stable tag: trunk
Donate link: http://bbshowcase.org/donate/

Allows any user (guest or member) to select a different bbPress language for templates.

== Description ==

Allows any user (guest or member) to select a different bbPress language for templates.

== Installation ==

* Place dropdown anywhere you'd like in templates via:  `<?php do_action('bb_language_switcher',''); ?>`

* If you do not already have an alternate language set,
you MUST change in your bb-config.php :  `define('BB_LANG', ' ');` 
note the space between the quotes

* bbpress 0.9 - put any .mo language files into  `bb-includes/languages/`  

* bbPress 1.0 - put any .mo language files into  `my-languages/` 

* Copy `bb-language-switcher/` directory to `my-plugins/`  and activate plugin

* To rebuild the list of languages, either deactivate/reactivate the plugin or add to your URL `?bb_language_switcher_update`

== Frequently Asked Questions ==

* Users must have cookies enabled for language switch to work

* You can define your own custom path to .mo files with:  `define('BB_LANG_DIR', '/your-custom-path/');`  

* To rebuild the list of languages in the dropdown you must deactivate/reactivate the plugin

* To see a plain list of processed languages, add to your URL `?bb_language_switcher_debug`

* You can force or disable file scan by editing near the top of the plugin and changing 
 define('BB_LANG_USE_FILE_META',false);	
 change to true to scan inside of .mo files for language name, 
 set to 'only' or 'force' to ONLY use file meta

== License ==

* CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

== Donate ==

* http://bbshowcase.org/donate/

== Changelog ==

= Version 0.0.1 (2009-03-27) =

*   first public alpha release

= Version 0.0.2 (2009-03-28) =

*   sort languages alphabetically
*   show 2-letter country code next to language name if available
*   optional URL to rebuild language list in addition to activation

= Version 0.0.3 (2009-03-29) =

*   use list of language codes as primary lookup (iso639)
*   debug mode for admin
*   externalized admin functions

= Version 0.0.4 (2009-03-30) =

*   update to BB_LANG from BBLANG (deprecated) and some code cleanup

= Version 0.0.5 (2009-05-09) =

*   attempt to fix bug for supporting English option on non-english defaults  (will need to rebuild via  ?bb_language_switcher_update )

= Version 0.0.6 (2010-07-22) =

*   workaround for bbPress 1.1 bug where textdomains are loaded and cached before plugins are loaded
