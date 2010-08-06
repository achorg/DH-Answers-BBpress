<?php
/**
 * Plugin Name: Bird Watcher
 * Plugin Description: Create new questions from tweets with #ask hashtag 
 * Author: Joseph Gilbert
 * Author URI: http://lib.virginia.edu/scholarslab/
 * Plugin URI: http://lib.virginia.edu/scholarslab/
 * Version: 0.1
 */

function bw_get_tweets($hashtag) {
	//retrieve feed via simplexml
	if($xml = simplexml_load_file('http://search.twitter.com/search.rss?q=%23'.$hashtag,'SimpleXMLElement', LIBXML_NOCDATA)) {
		//find title, author, and tweet id with xpath
		$tweets = $xml->xpath("/rss/channel/item");
		return $tweets;
	} else {
		return false;
	}
}

function bw_add_tweets() {
	global $bbdb;
	$hashtag = 'askdh';
	$tweets = get_tweets($hashtag);
	foreach($tweets as $tweet) {
		bb_new_topic($tweet->title, 5, 'from-twitter');
	}
}

add_action('bb_foot', 'bw_add_tweets');

?>