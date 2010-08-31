=== bb-NoSpamUser ===
Contributors: Nightgunner5
Tags: spam, user, login, protection, admin, registration
Requires at least: 1.0
Tested up to: trunk
Stable tag: 0.8

bb-NoSpamUser blocks potential spammers from registering on your forum.

== Description ==
bb-NoSpamUser blocks potential spammers from registering on your forum by checking with [Stop Forum Spam](http://www.stopforumspam.com/) for IP address, email address, and username.

The spam checks are cached for 7 days to lessen the load on [Stop Forum Spam](http://www.stopforumspam.com/) and your own server. Optionally, a reCAPTCHA can be shown to lesser spammers to allow false positives to be bypassed.

== Installation ==
1. Upload the entire `nospamuser` folder to the `my-plugins` folder at the root of your forums.
2. Activate the plugin through the `Plugins` menu in bbPress admin.
3. Modify the settings in the administration panel.

== Screenshots ==
1. bb-NoSpamUser can block spammers via IP, email, or username.

== Changelog ==

= 0.8 =
* Complete rewrite
* Marking a user as bozo reports them to Stop Forum Spam
* Fixed reporting code (it was closing the socket too early)
* Switched to WP_Http
* A bug where non-spammers could be blocked has been fixed
* Serious logic bug fixed

= 0.7 =
* Minimum frequency setting added

= 0.6 =
* Changed to cURL, removed SimpleXML requirement

= 0.4 =
* Initial release