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
	
	$t_link = get_topic_link($topic_id);
	//shorten URL
	exec("curl http://is.gd/api.php?longurl=" . $t_link, $shorturl);
	
	$message = "new #dhqa topic ($shorturl[0]): " . $t_title;
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

// call preview_tweet() in template to display preview of new topic tweet
function preview_tweet() {
	echo '<p id="twitter_topic_preview" style="display: none;">' . _e('This question will be posted to Twitter as follows: ') . '<span id="tweet_preview"></span>
		<input type="hidden" name="tweet" />
		<script type="text/javascript">
			jQuery("#topic").keyup(function() {
				if (jQuery("#twitter_topic_preview").is(":hidden")) {
					jQuery("#twitter_topic_preview").show();
				}
				var tweet = "@dhanswers new topic: " + jQuery("#topic").val();
				if (tweet.length > 140) { // useless for now since topic is limited to 50 chars
					tweet = tweet.substring("0,139") + "…";
				}
				jQuery("#tweet_preview").html(tweet);
				jQuery("#tweet").val(tweet); // we could use this value server side
			})
		</script>
	</p>';
}

add_action('bb_insert_topic', 'tweet_new_topic', 10, 2);
?>
