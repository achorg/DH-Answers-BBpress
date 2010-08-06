=== Easy Mentions ===
Contributors: Gautam Gupta
Donate link: http://gaut.am/donate/EM/
Tags: easy, mention, username, link, reply, twitter, Gautam
Requires at least: 1.0
Tested up to: 1.1
Stable tag: 0.2

Easy Mentions allows the users to link to other users' profiles in posts (by using @username) and tags (by using #tag), basically like Twitter.

== Description ==

Easy Mentions allows the users to link to other users' profiles in posts (by using `@username`) and tags (by using `#tag`), basically like Twitter.

Just make a new post, write the content with a `@username` (can be any username) in the text. When you submit the post, the plugin will automatically link the usernames (which exist) to their profile. You can also link the tags in the same way by using `#tag`. Note that the plugin doesn't link the usernames or tags with spaces.
You can also disable linking of users or tags by going to the settings page.

The plugin can also add a Reply link below each post, which when clicked, adds the reply text (configurable) in the post textbox. This can be enabled via the settings page, but before that please see #1 question in the [FAQ](http://bbpress.org/plugins/topic/easy-mentions/faq/).

== Other Notes ==

= Translations =
* Hindi Translation by [Gautam](http://gaut.am/) (me)

You can contribute by translating this plugin. Please refer to [this post](http://gaut.am/translating-wordpress-or-bbpress-plugins/) to know how to translate.

= To Do =
Nothing for now.

= License =
GNU General Public License version 3 (GPLv3): http://www.opensource.org/licenses/gpl-3.0.html

= Donate =
You may donate by going [here](http://gaut.am/donate/EM/).

== Installation ==

1. Upload the extracted `easy-mentions` folder to the `/my-plugins/` directory
2. Activate the plugin through the 'Plugins' menu in bbPress
3. Optional - Change the plugin's settings by going to `Settings` -> `Easy Mentions`
4. Enjoy!

== Frequently Asked Questions ==

= 1. How do I show the reply form on each page of topic for the reply feature? =
* Open `topic.php` file of your theme.
* Search for `post_form();` (Tip: Press `Ctrl+F` and then search)
* Replace it with `post_form( array( 'last_page_only' => false ) );`
* The resulting line should look something like this - `<?php post_form( array( 'last_page_only' => false ) ); ?>`
* Save and Upload!

== Screenshots ==

1. Easy Mentions Plugin in Action
2. A Screenshot of the Settings Page

== Changelog ==

= 0.2 (09-02-10) =
* Now the plugin can also link tags (use `#tag`)
* Added option to let the user to choose whether to link tags/users or not
* Added option to choose reply format
* Removed the Javascript file for public section, hard-coded the reply values
* Removed the use of jQuery from public section
* Updated Hindi Translation
* Updated Screenshots

= 0.1.1 (28-01-10) =
* Important bug fix for Reply feature

= 0.1 (28-01-10) =
* Initial Release

== Upgrade Notice ==

= 0.2 (09-02-10) =
If you are upgrading and also using the reply feature, then after upgrading, go to settings page and press "Save Changes" button.

= 0.1.1 (28-01-10) =
Has important bug fix for Reply feature, please upgrade!

= 0.1 (28-01-10) =
Initial Release