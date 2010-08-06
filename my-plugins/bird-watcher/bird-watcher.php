<?php
/**
 * Plugin Name: Bird Watcher
 * Plugin Description: Create new questions from tweets with #askDH hashtag 
 * Author: Joseph Gilbert
 * Author URI: http://lib.virginia.edu/scholarslab/
 * Plugin URI: http://lib.virginia.edu/scholarslab/
 * Version: 0.1
 */

function get_tweets($hashtag) {
	$tweets = array();
	$feed = simplexml_load_file('http://search.twitter.com/search.rss?q=%23askdh'); 

    foreach($feed->channel->item as $tweet) {
		//publish unless it's already in the db
		echo('<p>'.$tweet->guid.'</p>');
    }
}

?>