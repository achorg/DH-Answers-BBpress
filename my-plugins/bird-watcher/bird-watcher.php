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
		
		//extract tags from tweet
		$full_tweet = (string)$tweet->title;
		preg_match_all('/#[a-zA-Z0-9_-]+/',$full_tweet,$tagMatches);
		$tag = array();
		$tag_count = 0;
		foreach($tagMatches as $tagMatch) {
			$tag[$tag_count] = str_replace('#','',$tagMatch);
			$full_tweet = str_replace($tagMatch, '', $full_tweet);
		}
		
		//shorten title if need be
		if(strlen($full_tweet) >= 45) {
			$short_title = substr($full_tweet, 0, 44) . '[...]';
		} else {
			$short_title = $full_tweet;
		}
		
		//get tweet guid to help with duplication prevention
		preg_match('/\/([0-9]+)$/', $tweet->guid, $idMatch);
		$tweetId = (int)$idMatch[1];
		
		//if topic doesn't already exist
		if (!bw_check_duplicate($tweetId)) {
			//add a new topic by "Twitter User"
			$new_topic = bb_insert_topic(array(
				'topic_title' => str_replace('#askdh','',$short_title),
				'topic_poster' => 1, // accepts ids
				'topic_poster_name' => 'Twitter User', // accept names
				'topic_last_poster' => 1, // accepts ids
				'topic_last_poster_name' => 'Twitter User', // accept names
				'forum_id' => 'general', // accepts ids or slugs
				'tags' => $tag
			));
			bb_update_topicmeta($new_topic,'tweetid',$tweetId);
		}
	}
}

function bw_check_duplicate($id) {
	return($bbdb->get_var("SELECT COUNT(*) FROM $bbdb->topics AS topics INNER JOIN $bbdb->meta AS meta ON topics.topic_id=meta.object_id WHERE object_type = 'bb_topic' AND meta_key = 'twitterid' AND meta_value = $id") > 0);
}

//Check for new tweeted messages every time footer loads.  Better ideas?
add_action('bb_foot', 'bw_add_tweets');

?>