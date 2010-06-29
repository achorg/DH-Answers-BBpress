=== bbPress Topic Icons ===
Contributors: paulhawke
Tags: sticky, closed, busy, topics, skin, skinning, support
Requires at least: 1.0.2
Tested up to: 1.0.2
Stable tag: 1.0.2

== Description ==

This plugin changes the default behavior of bbPress - takes away the words "sticky" and "closed" next to topics and replaces them with small icons - in addition, busy topics and normal topics gain an icon.  New "icon sets" can be added by simply creating a sub-directory and uploading the image files, allowing the forum to be easily "skinned" with new icons.

Users of the "support forum" plugin can activate the optional second plugin - the "topic icons/support forum connector" which makes topic icons aware of support forum statuses, and the topic icon plugin will take over the job of displaying icons, allowing forums using the "support forum" plugin to similarly be "skinned" easily.

== Screenshots ==

== Installation ==

The `bb-topic-icons` directory needs to go into your `my-plugins` directory. If you dont have one, you can create it so that it lives alongside your `bb-plugins` directory. Alternatively, `bb-topic-icons` can be dropped directly in to your `bb-plugins` directory.

Oh, and don’t forget to Activate the plugin!

== Frequently Asked Questions ==

= I still see the word "sticky" next to topics =

Firstly, have you installed and activated the plugin?  See the installation instructions for details.

Secondly, if the plugin is being reported as active, the template itself might be misbehaving, you will need to look for where the `frontpage.php` file is looking at topics - there will be a hard-coded 'sticky' text there.  Swap that text for a call to the built-in bbPress function: `<?php bb_topic_labels(); ?>` that handles topic labelling, and things should work.

= Can I use different icons? =

Sure you can.  In the `bb-topic-icons` directory, there's a `icon-sets` subdirectory.  In future versions of the plugin, different icon sets will be installable by copying them to this directory, or via a plugin API.  For now, take a look in the default icon-set - the four icon files live in that directory.  Upload your own files, and in the `bb-topic-icons.php` file, change the filename constants and icon sizes if you need to.

= Can I change the toolips? =

If you want to change the text that is displayed, `class.default-status-renderer.php` is the file to look in - you will see a map that converts a given status value to the tooltip description.

If you want to change the CSS styling of the tooltips, take a look at `bb-topic-icons.css` for the various styles that are applied.

== Change Log ==

= Version 0.6 =

Created the "support forum connector" to allow support forums to be "skinned" using the topic icon plugin.  Added icon-sets for the default support forum along with a modified "grey-blox" theme for the support forum to show how skinning can take place.

= Version 0.5 =

The long awaited admin screen, detection of additional icon-sets if they have   been installed and general code cleanup.  A second built-in icon set "grey-blox" has been added too.

= Version 0.4 =

Added CSS styled tooltip displays for the various status icons, and made them clickable to display the appropriate topics.  Pulled out the stylesheet into its own file (bb-topic-icons.css) to aid in comprehension.

= Version 0.3 =

Major code cleanup - internal architecture made object-oriented, with a split between determining a given topic's status and secondly rendering that status.  This paves the way for supporting custom statuses (and therefore supporting the 'support forum' plugin) in the future.

= Version 0.2 =

Thanks to 'hpguru' for reporting the bug - downloads from bbPress.org were called "bb-topic-icons" and the code itself was looking for a "topic-icons" directory - quick rename of files to fix the issue.

= Version 0.1 =

Initial release with minimal documentation
