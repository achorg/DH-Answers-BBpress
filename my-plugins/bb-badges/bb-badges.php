<?php
/*
 * Plugin Name: DHAnswers Badges
 * Plugin Description: Display "badges" for users meeting certain criteria
 * Author: Joseph Gilbert
 * Author URI: http://lib.virginia.edu/scholarslab/
 * Plugin URI: http://lib.virginia.edu/scholarslab/
 * Version: 0.1
 */

function show_badges() {
	$badges = get_badges(get_post_author_id());
	// Print array of badges
	foreach($badges as $badge) {
		echo($badge);
	}
}

function get_badges($user) {
	$badges = array();
	// Get # of user posts, # of best answers
	$post_count = get_post_count($user);
	// answer_count = get_answer_count($user);
	// Assign badges based on numbers
	if($post_count >= 50) {
		$badges["posts"] = "<span title='50 Posts' class='badge gold-poster ".$post_count."'><!-- 50+ posts! --></span>";
	} elseif($post_count < 50 && $post_count >= 20) {
		$badges["posts"] = "<span title='20 Posts' class='badge silver-poster ".$post_count."'><!-- 20-49 posts --></span>";
	} elseif($post_count < 20 && $post_count >= 10) {
		$badges["posts"] = "<span title='10 Posts' class='badge bronze-poster ".$post_count."'><!-- 10-19 posts --></span>";
	} else {
		$badges["posts"] = "<span title='&lt; 10 Posts' class='badge ".$post_count."'><!-- less than 10 posts --></span>";
	}
	
	// Return array of badges
	return $badges;
}

function get_post_count($user) {
	global $bbdb;
	return $bbdb->query("SELECT * FROM $bbdb->posts WHERE poster_id = $user AND post_status = 0");
}

?>