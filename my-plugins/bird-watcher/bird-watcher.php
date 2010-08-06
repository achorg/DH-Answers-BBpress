<?php
/**
 * Plugin Name: Bird Watcher
 * Plugin Description: Create new questions from tweets with #ask hashtag 
 * Author: Joseph Gilbert
 * Author URI: http://lib.virginia.edu/scholarslab/
 * Plugin URI: http://lib.virginia.edu/scholarslab/
 * Version: 0.1
 */

function get_tweets($hashtag) {
	//retrieve feed via simplexml
	if($xml = simplexml_load_file('http://search.twitter.com/search.rss?q=%23'.$hashtag,'SimpleXMLElement', LIBXML_NOCDATA)) {
		//find title, author, and tweet id with xpath
		$result["title"] = $xml->xpath("/rss/channel/item/title");
		$result["author"] = $xml->xpath("/rss/channel/item/author");
		$result["guid"] = $xml->xpath("/rss/channel/item/guid");
		//assign tweets and attributes to 2d array (e.g., author of first tweet will be $tweet[0]["author"])
		foreach($result as $key => $attribute) {
			$i=0;
	    	foreach($attribute as $element) {
	      		$tweet[$i][$key] = (string)$element;
	      		$i++;
	    	}
	  	}
		return $tweet;
	} else {
	  return false;
	}
}

function show_tweets($hashtag) {
	$tweet = get_tweets($hashtag);
	foreach($tweet as $msg) {
		echo("<p>".$msg["title"]."</p>");
	}
}

?>