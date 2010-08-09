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
		$tagMatches = array();
		preg_match_all('/#[a-zA-Z0-9_-]+/',$full_tweet,$tagMatches);
		$tag = array();
		$tag_count = 0;
		foreach($tagMatches as $tagMatch) {
			$tag[$tag_count] = str_replace('#','',$tagMatch);
			$full_tweet = str_replace($tagMatch, '', $full_tweet);
			$tag_count++;
		}
		
		//shorten title if need be
		if(strlen($full_tweet) >= 45) {
			$short_title = substr($full_tweet, 0, 44) . '[...]';
		} else {
			$short_title = $full_tweet;
		}
		
		//get tweet guid to help with duplication prevention
		//find string position of last slash then get substring after it
		$tweetId = substr($tweet->guid, strrpos($tweet->guid,'/')+1);
				
		//if topic doesn't already exist
		if (bw_check_duplicate($tweetId) == 0) {
			//add a new topic by "Twitter User"
			$new_topic = bb_insert_topic(array(
				'topic_title' => str_ireplace('#askdh','',$short_title),
				'topic_poster' => 13, // accepts ids
				'topic_poster_name' => 'Twitter User', // accept names
				'topic_last_poster' => 13, // accepts ids
				'topic_last_poster_name' => 'Twitter User', // accept names
				'forum_id' => 'general', // accepts ids or slugs
				'tags' => $tag
			));
			bb_update_topicmeta($new_topic,'tweetid',$tweetId);
		}
	}
}

function bw_check_duplicate($id) {
	global $bbdb;
	//check if Twitter guid exists in bb_meta table
	//note that we aren't checking to see if the post is 
	//"normal" or not to prevent deleted posts from returning
	return($bbdb->query("SELECT * FROM $bbdb->topics AS topics LEFT JOIN $bbdb->meta AS meta ON topics.topic_id=meta.object_id WHERE object_type = 'bb_topic' AND meta_key = 'tweetid' AND meta_value='$id'"));
}

//Check for new tweeted messages every time footer loads.  Better ideas?
add_action('bb_foot', 'bw_add_tweets');

?>