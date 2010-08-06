<?php
/**
 * Plugin Name: Twitter Notification
 * Plugin Description: Sends tweets to a specific Twitter account when a new topic is created.  Based on Post Notification by Thomas Klaiber.
 * Author: Joseph Gilbert
 * Author URI: http://lib.virginia.edu/scholarslab/
 * Plugin URI: http://lib.virginia.edu/scholarslab/
 * Version: 0.1
 */
 
function tweet_new_topic($topic_id, $topic_title) {
	global $bb_post;
	if($bb_post){
	// post already exists
	}
	else {
	$t_title = get_topic_title($topic_id);
	
	// TODO: can we use a URL shoterner?
	$t_link = get_topic_link($topic_id);
	
	$message = "new #dhqa topic ($t_link): " . $t_title;
	if (strlen($message)>140) {$message = substr($message,0,139) . '…';}

	// Set username and password
	$username = '';
	$password = '';

	// do a simple command-line curl with the status
	// TODO: check return status
	 exec("curl -u $username:$password -d status=" . escapeshellarg($message) . " http://api.twitter.com/1/statuses/update.json", $output, $return);
	}
	return $topic_title;
}
add_action('bb_insert_topic', 'tweet_new_topic', 10, 2);
?>
