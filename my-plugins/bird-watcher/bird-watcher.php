<?php
/**
 * Plugin Name: Bird Watcher
 * Plugin Description: Create new questions from tweets with #askDH hashtag 
 * Author: Joseph Gilbert
 * Author URI: http://lib.virginia.edu/scholarslab/
 * Plugin URI: http://lib.virginia.edu/scholarslab/
 * Version: 0.1
 */

function bw_get_tweets($hashtag) {
	//retrieve feed via SimpleXML
	if($xml = simplexml_load_file('http://search.twitter.com/search.rss?q=%23'.$hashtag,'SimpleXMLElement', LIBXML_NOCDATA)) {
		//find title, author, and tweet id with xpath
		$tweets = $xml->xpath("/rss/channel/item");
		return $tweets;
	} else {
		return false;
	}
}

function bw_add_tweets() {
	
	$hashtag = 'askdh';
	$tweets = bw_get_tweets($hashtag);
	foreach($tweets as $tweet) {
		preg_match('/\/([0-9]+)$/', $tweet->guid, $idMatch);
		//if topic doesn't already exist
		if (!get_topic($idMatch[1])) {
			bb_insert_topic(array(
				'topic_id' => $idMatch[1],
				'topic_title' => $tweet->title,
				'topic_slug' => '',
				'topic_poster' => 1, // accepts ids
				'topic_poster_name' => 'Twitter User', // accept names
				'topic_last_poster' => 1, // accepts ids
				'topic_last_poster_name' => 'Twitter User', // accept names
				'topic_open' => 1,
				'forum_id' => 'general' // accepts ids or slugs
			));
		}
	}
}

//Check for new tweeted messages every time footer loads.  Better ideas?
add_action('bb_foot', 'bw_add_tweets');

?>