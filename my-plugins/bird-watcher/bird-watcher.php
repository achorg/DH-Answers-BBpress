<?php
/**
 * Plugin Name: Bird Watcher
 * Plugin Description: Create new questions from tweets with #DHanswers hashtag 
 * Author: Joseph Gilbert
 * Author URI: http://lib.virginia.edu/scholarslab/
 * Plugin URI: http://lib.virginia.edu/scholarslab/
 * Version: 0.2
 */

function bw_get_tweets( $hashtag ) {
	//retrieve feed via SimpleXML
	if($xml = simplexml_load_file( 'http://search.twitter.com/search.rss?q=%23'.$hashtag, 'SimpleXMLElement', LIBXML_NOCDATA ) ) {
		//find title, author, and tweet id with xpath
		$tweets = $xml->xpath( "/rss/channel/item" );
		return $tweets;
	} else {
		return false;
	}
}

function bw_add_tweets() {
	//set hashtag for query
	$hashtag = 'dhanswers';
	//retrieve tweets
	$tweets = bw_get_tweets( $hashtag );
	
	//iterate through each tweet
	foreach( $tweets as $tweet ) {
		
		//determine tweet text, author, and id
		$tweet_text = bw_get_tweet_text( $tweet );
		$tweet_user = bw_get_tweet_user( $tweet );		
		$tweet_id = bw_get_tweet_id( $tweet );
		
		//ignore if tweet is already a topic, contains a mention (@whatever),
		//or if the tweeter is not a DHAnswers user.
		if(!bw_check_duplicate( $tweet_id ) && !bw_has_mention( $tweet_text ) && bw_is_user( $tweet_user )){
			
			//extract tags
			$tags = bw_get_tags( $tweet_text );
			
			//get short title
			$topic_title = bw_get_title( $tweet_text );
			
			//new topic and post from tweet
			bw_insert_tweet( $twitter_user,$tweet_id,$short_title,$full_tweet,$tags );
		
		} //end if
	} //end foreach
}
//Check for new tweeted messages every time footer loads.  Better ideas?
add_action( 'bb_foot', 'bw_add_tweets' );

function bw_get_tweet_text( $tweet ) {
	//return tweet title
	return (string)$tweet->title;
}

function bw_get_tweet_user( $tweet ) {
	//strip '@twitter.com' from author and return
	return substr( $tweet->author, 0, strpos( $tweet->author, '@' ));
}

function bw_get_tweet_id( $tweet ) {
	//retrieve unique tweet id from tweet url
	//find string position of last slash then get following characters
	return (int)substr( $tweet->guid, strrpos( $tweet->guid, '/' ) + 1 );
}

function bw_check_duplicate( $id ) {
	global $bbdb;
	//check if Twitter guid exists in bb_meta table
	//note that we aren't checking to see if the post is 
	//"normal" or not to prevent deleted posts from returning
	return( $bbdb->query( $bbdb-prepare( "SELECT * FROM $bbdb->topics AS topics LEFT JOIN $bbdb->meta AS meta ON topics.topic_id=meta.object_id WHERE object_type = 'bb_topic' AND meta_key = 'tweetid' AND meta_value='$id'" ) ) );
}

function bw_get_tags( $tweet ) {
	//extract tags from tweet
	$tagMatches = array();
	preg_match_all( '/#[\w_-]+/', $tweet, $tagMatches );
	$tags = array();
	
	$tag_count = 0;
	foreach( $tagMatches as $tagMatch ) {
		$tags[$tag_count] = str_replace( '#', '', $tagMatch );
		$tag_count++;
	}
	$tags_str = implode( ",", $tags );
	return $tags_str;
}

function bw_get_title( $full_tweet ) {
	//shorten title if need be
	if( strlen( $full_tweet ) >= 45) {
		$short_title = substr( $full_tweet, 0, 44 ) . '[...]';
	} else {
		$short_title = $full_tweet;
	}
	
	return $short_title;
}

function bw_insert_tweet( $user, $id, $title, $tweet, $tags ) {
		
		//add a new topic by "Twitter User"
		$new_topic = bb_insert_topic( array(
			'topic_title' => str_ireplace( '#dhanswers', '', $title ),
			'topic_poster' => bw_get_id_from_user( $user ),
			'forum_id' => 'general',
			'tags' => $tags
		));
		
		//add the tweet guid to the meta table for duplication
		bb_update_topicmeta( $new_topic, 'tweetid', $id );
		
		//add a new post to this topic with the full tweet
		bb_insert_post( array(
			'topic_id' => $new_topic,
			'post_text' => $tweet,
			'poster_id' => bw_get_id_from_user( $user ),
			'poster_ip' => '127.0.0.1'
		));
		
}

function bw_has_mention( $tweet ) {
	//true if tweet contains @user style mention
	return preg_match( '/@[\w_-]+/', $tweet );
}

function bw_is_user( $user ) {
	//true if Twitter username is found in a DHAnswers user profile
	global $bbdb;
	return( $bbdb->query( $bbdb->prepare( "SELECT * FROM $bbdb->usermeta WHERE meta_key = 'twitter' AND meta_value='$user'" ) ) );
}

function bw_get_id_from_user($user) {
	//returns ID of DHAnswers user with specified Twitter username
	global $bbdb;
	return( (int)( $bbdb->get_var( $bbdb->prepare( "SELECT user_id FROM $bbdb->usermeta WHERE meta_key = 'twitter' AND meta_value='$user'" ) ) ) );
}

?>