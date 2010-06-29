=== Quote ===
Tags: quote, reply, post
Contributors: Michael Nolan
Requires at least: 0.8
Tested up to: 0.8.1
Stable Tag: 0.2

Allows quoting of existing messages when quoting.

== Description ==

Adds two functions to allow quoting of messages.

== Installation ==

Add `quote.php` to your `/my-plugins/` directory.

Modify your post.php template to include the link, outputs "Quote" by default:

`<?php bb_quote_link(); ?>`

And modify post-form.php to include the quote text in textarea:

`<textarea name="post_content" cols="50" rows="8" id="post_content" tabindex="3"><?php bb_quote_message(); ?></textarea>`

== Configuration ==

None necessary.

== Change Log ==

 * Fix problem with Swedish characters
 * Add argument to change link text
 * Strip out paragraph tags
 