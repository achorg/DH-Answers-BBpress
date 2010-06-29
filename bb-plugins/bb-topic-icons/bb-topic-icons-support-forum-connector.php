<?
/*
Plugin Name: bb Topic Icons/Support Forum Connector
Plugin URI: http://devt.caffeinatedbliss.com/bbpress/topic-icons
Description: Enhances the basic "Topic Icons" plugin with knowledge of status values used in the "Support Forum" plugin allowing the user to use custom icons for support forum topics.
Author: Paul Hawke
Author URI: http://paul.caffeinatedbliss.com/
Version: 0.6
*/

require( 'class.support-forum-status-interpreter.php' );
require( 'class.support-forum-status-renderer.php' );

function topic_icons_support_forum_connector_turn_off_labels() {
	global $support_forum;
	
	if (isset($support_forum)) {
		remove_filter('bb_topic_labels', array(&$support_forum, 'modifyTopicLabelClosed'), 20);		
		remove_filter('bb_topic_labels', array(&$support_forum, 'modifyTopicLabelSticky'), 30);
		remove_filter('bb_topic_labels', array(&$support_forum, 'modifyTopicLabelStatus'), 10);
	}
}

function topic_icons_support_forum_connector_init( ) {
	add_action('bb_head', 'topic_icons_support_forum_connector_turn_off_labels');
	add_action('bb_admin-header.php', 'topic_icons_support_forum_connector_turn_off_labels');
	
	topic_icons_register_status_interpreter('support-forum', new SupportForumStatusInterpreter());
	topic_icons_register_status_renderer('support-forum', new SupportForumStatusRenderer());
}

topic_icons_support_forum_connector_init();

?>
