<?php
/**
 * Plugin Name: Twitter Notification
 * Plugin Description: Sends tweets to a specific Twitter account when a new topic is created.  Based on Post Notification by Thomas Klaiber.
 * Author: Joseph Gilbert
 * Author URI: http://lib.virginia.edu/scholarslab/
 * Plugin URI: http://lib.virginia.edu/scholarslab/
 * Version: 0.1
 */
 
function tweet_new_topic() {
	global $bbdb, $bb_table_prefix, $topic_id, $topic;
	
	$t_title = get_topic_title($topic->ID);
	
	// TODO: can we use a URL shoterner?
	$t_link = $topic_id;
	$id = (int) @$_POST['id'];
	
	//$message = "New topic at DH Q&A: \"".$t_title."\" ".$t_link;
	//$message = "New topic posted at DH Q&A.";
	
	$message = "new #dhqa topic ($t_link): " . $id . $t_title;
	if (strlen($message)>140) {$message = substr($message,0,139) . '…';}

	// Set username and password
	$username = 'adhoqatest';
	$password = 'Answ3rs';

	// do a simple command-line curl with the status
	// TODO: check return status
	// exec("curl -u $username:$password -d status=" . escapeshellarg($message) . " http://api.twitter.com/1/statuses/update.json", $output, $return);


}
add_action('bb_insert_topic', 'tweet_new_topic');
?>
