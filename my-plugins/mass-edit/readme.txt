=== Mass Edit - Moderate Posts ===
Tags: _ck_, administration, moderation, mass edit, bulk, bulk edit
Contributors: _ck_
Requires at least: 0.8.2
Tested up to: 0.9
Stable tag: trunk
Donate link: http://bbshowcase.org/donate/

Adds a "mass edit" feature to bbPress admin panel, similar to WordPress, for easily moderating posts in bulk.

== Description ==

With this plugin keymaster/administrators may view the most recent posts or search 
for posts by any author with any specific text and moderate the posts with delete/spam 
and undelete options. It's very similar to the "mass edit" feature built into WordPress.

== Installation ==

Add the `mass-edit.php` file to bbPress' `my-plugins/` directory.
Activate and check under "content" admin submenu for "Mass Edit".

== Frequently Asked Questions ==

* I am looking for feedback on more intuitive design colors, placement and options

* bbPress sometimes will generate an error when you delete a single post via the direct link - that is internal and I can't fix that, sorry

== License ==

* CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

== Donate ==

* http://bbshowcase.org/donate/

== Changelog ==

= Version 1.00 (2008-02-17) =

* first public release

= Version 1.10 (2008-02-18) =

* user adjustable options including column order and CSS editing

= Version 1.1.1 (2008-04-16) =

* only load mass-edit core if in admin menu and on mass-edit (saves 20k per instance)

= Version 1.1.3 (2009-04-10) =

* bug fix on profile links
* use view=all mode when trying to see deleted post in topic (link is still not calculated correctly due to bbPress limitations)

== To Do ==

* more language translation hooks
