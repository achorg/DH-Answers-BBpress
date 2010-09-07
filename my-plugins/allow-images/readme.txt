=== Allow Images ===
Tags: images, html
Contributors: mdawaffe, qayqay12
Requires at least: 0.8.4
Tested up to: 1.0.2
Stable Tag: 0.9

Allow users to include &lt;img /&gt; tags in their posts.

== Description ==

With this simple plugin, users may include image tags in their posts.  

== Installation ==

1. Add the `allow-images.php` file to bbPress' `my-plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in bbPress.

== Frequently Asked Questions ==

= How to prevent big images to break the forum's layout ? =
Add this to your CSS:
#thread .post img {max-width:450px;} 
= Is there any restriction on the image url or file extension ? =
nop. Not anymore.
= Why isn't &lt;img /&gt; allowed by default in bbpress ? =
Maybe because people may add huge images or p0rn or other unwanted content that would bothers other users and slow down your website. Who knows ? 
= How big is this plugin ? =
5 short lines :-)
= What is the average velocity of an unladen swallow? =
What you mean? an African or European swallow? 

== Changelog ==

= 0.9 =
* Fix the compatibilty issue with bbpress 1.0.2
* No more check on file extension. 