=== Thanks ===
Contributors: paulhawke
Tags: thanks, like, voting, ajax
Requires at least: 1.0.2
Tested up to: 1.0.2
Stable tag: 1.0.2

== Description ==

The "Thanks" plugin allows logged in users to add a vote of thanks for posts in the forum, and report how many "thank you" votes posts have received.  All text is fully configurable via an admin page (so users can "like" a given post, or the plugin could report the post has "15 sparkling vampires" if you really want it to) and has the ability to fully uninstall its data from the database at the click of a button.

Votes are cast using AJAX, and a given user's vote only counts once for any given post, no matter how many times they click the "vote" link!

== Screenshots ==

== Installation ==

The `thanks` directory needs to go into your `my-plugins` directory. If you dont have one, you can create it so that it lives alongside your `bb-plugins` directory. Alternatively, `thanks` can be dropped directly in to your `bb-plugins` directory.

Oh, and don’t forget to Activate the plugin!

== Frequently Asked Questions ==

= I dont like where the plugin puts its text =

Take a look in the admin area of your forum - you can opt to have the plugin output its report of the number of votes of thanks and the voting link either before, or after, each post.  You can also edit the text that is used by the plugin.

= I dont like the margins and styling used by the plugin, can I change them? =

Have at it!  The plugin wraps its output in a `div` with a class of `thanks-output` - if you want to get into the code to really customize the output, look in `thanks.php` at the `thanks_output()` method.

= Can I get a list of who said thanks for a post? =

Yes - in the admin area, there is an option for listing all the "thanks" voters.  By default the option is set to "no".  If you want the plugin to list all the people, set that to "yes".

== Change Log ==

= Version 0.7 =

As requested (by `bingsterloot`) the plugin now immediately refreshes the count of votes for a given post in response to a user clicking the voting link, rather than displaying a static "thanks" message.

= Version 0.6 =

Thanks to feedback and testing from `chandersbs` and `bingsterloot` the plugin was modified to remove the voting link, if you've already voted, and give the option to list out all the users who have said thanks.  Thanks also to `citizenkeith` for testing v0.9.0.6 compatibility (or rather, lack thereof).

= Version 0.5 =

Initial release.
