=== Best Answer ===
Tags: best answer, vote, _ck_
Contributors: _ck_
Requires at least: 0.9
Tested up to: 0.9
Stable tag: trunk
Donate link: http://bbshowcase.org/donate/

Allows the topic starter or moderators to select which reply is a "Best Answer" to the original post. 

== Description ==

Allows the topic starter or moderators to select which reply is a "Best Answer" to the original post. Helpful for support forums, etc.

== Installation ==

* Add the `best-answer/` directory to bbPress' `my-plugins/` directory and activate.

* There are a few options you can edit at the top of the plugin including allowing multiple best answers 
and if the best answer(s) should be displayed first when viewing the topic.

* because of lack of foresight in the bbPress output functions you have to edit the topic.php template to change post colors
 change: 
 `<?php foreach ($posts as $bb_post) : $del_class = post_del_class(); ?>`
 to:  
  `<?php foreach ($posts as $bb_post) : $del_class = apply_filters('best_answer_class',post_del_class()); ?>`

== Frequently Asked Questions ==

 = How can other users vote?  =

* this is a simplified version of "best answer" and doesn't allow voting by other users right now

== License ==

* CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

== Donate ==

* http://bbshowcase.org/donate/

== Changelog ==

= Version 0.0.2 (2009-05-23) =

* first public release

= Version 0.0.3 (2009-08-03) =

* support for best answers across multiple page topics, moved to first post, jump to proper post
* css has changed slightly to allow optional use of graphic icons instead of font symbols

= Version 0.0.4 (2009-08-04) =

* forum specific support to only activate on specific forum id #'s

= Version 0.0.5 (2009-08-05) =

* replaced text star with sprite image for better cross browser support
* optional views to show topics with best-answer or not

== To Do ==

* admin menu ?
