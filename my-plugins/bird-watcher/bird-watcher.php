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

function bw_show_tweets() {
	$tweets = get_tweets('geoinst');
	foreach($tweets as $tweet) {
		echo("<p>" . $tweet->title . "</p>");
	}
}
add_action('post_form','bw_show_tweets');

?>