<?php
/**
 * Plugin Name: Bird Watcher
 * Plugin Description: Create new questions from tweets with #DHanswers hashtag 
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
	
	$hashtag = 'dhanswers';
	$tweets = bw_get_tweets($hashtag);
	
	//iterate through each tweet
	foreach($tweets as $tweet) {
		
		//determine tweet and twitter user
		$full_tweet = (string)$tweet->title;
		$twitter_user = substr($tweet->author, 0, strpos($tweet->guid,'/'));
		
		//ignore if tweet contains a mention (@whatever)
		//if(!bw_has_mention($full_tweet) && bw_is_user($twitter_user)){
		if(!bw_has_mention($full_tweet)){
			//extract tags and remove from tweet
			$tags = bw_get_tags($full_tweet);
			$full_tweet = preg_replace('/#[\w_-]+/', '', $full_tweet);
		
			//shorten title if need be
			if(strlen($full_tweet) >= 45) {
				$short_title = substr($full_tweet, 0, 44) . '[...]';
			} else {
				$short_title = $full_tweet;
			}
		
			//get tweet guid to help with duplication prevention
			//find string position of last slash then get substring after it
			$tweetId = substr($tweet->guid, strrpos($tweet->guid,'/')+1);
				
			//if topic doesn't already exist (i.e., there are zero posts with that tweetid)
			if (!bw_check_duplicate($tweetId)) {
				//add a new topic by "Twitter User"
				$new_topic = bb_insert_topic(array(
					'topic_title' => str_ireplace('#dhanswers','',$short_title),
					'topic_poster' => 13, // accepts ids bw_get_id_from_user($twitter_user)
					'forum_id' => 'general', // accepts ids or slugs
					'tags' => $tags
				));
				//add the tweet guid to the meta table for duplication
				bb_update_topicmeta($new_topic,'tweetid',$tweetId);
				//add a new post to this topic with the full tweet
				bb_insert_post(array(
					'topic_id' => $new_topic,
					'post_text' => $full_tweet,
					'poster_id' => 13, // accepts ids or names
					'poster_ip' => '127.0.0.1'
				));
			} //end if bw_check_duplicate
		} //end if preg_match mentions
	} //end foreach tweets
}

function bw_check_duplicate($id) {
	global $bbdb;
	//check if Twitter guid exists in bb_meta table
	//note that we aren't checking to see if the post is 
	//"normal" or not to prevent deleted posts from returning
	return($bbdb->query("SELECT * FROM $bbdb->topics AS topics LEFT JOIN $bbdb->meta AS meta ON topics.topic_id=meta.object_id WHERE object_type = 'bb_topic' AND meta_key = 'tweetid' AND meta_value='$id'"));
}

function bw_get_tags($tweet) {
	//extract tags from tweet
	$tagMatches = array();
	preg_match_all('/#[\w_-]+/', $tweet, $tagMatches);
	$tags = array();
	
	$tag_count = 0;
	foreach($tagMatches as $tagMatch) {
		$tags[$tag_count] = str_replace('#', '', $tagMatch);
		$tag_count++;
	}
	return $tags;
}

function bw_has_mention($tweet) {
	return preg_match('/@[\w_-]+/', $tweet);
}

function bw_is_user($user) {
	//return($bbdb->query("SELECT * FROM $bbdb->topics AS topics LEFT JOIN $bbdb->meta AS meta ON topics.topic_id=meta.object_id WHERE object_type = 'bb_topic' AND meta_key = 'tweetid' AND meta_value='$id'"));
}

function bw_get_id_from_user($user) {
	//return($bbdb->query("SELECT * FROM $bbdb->topics AS topics LEFT JOIN $bbdb->meta AS meta ON topics.topic_id=meta.object_id WHERE object_type = 'bb_topic' AND meta_key = 'tweetid' AND meta_value='$id'"));
}

//Check for new tweeted messages every time footer loads.  Better ideas?
add_action('bb_foot', 'bw_add_tweets');

?>