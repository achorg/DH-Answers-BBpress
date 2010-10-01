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
	// initiate badge array
	$badges = array();
	
	// Get # of user posts, # of best answers
	$post_count = get_post_count($user);
	$answer_count = get_answer_count($user);
	
	// Assign post count badge
	if($post_count >= 50) {
		$badges["posts"] = "<span title='50+ Posts' class='badge gold-poster ".$post_count."'><!-- 50+ posts! --></span>";
	} elseif($post_count < 50 && $post_count >= 20) {
		$badges["posts"] = "<span title='20+ Posts' class='badge silver-poster ".$post_count."'><!-- 20-49 posts --></span>";
	} elseif($post_count < 20 && $post_count >= 5) {
		$badges["posts"] = "<span title='5+ Posts' class='badge bronze-poster ".$post_count."'><!-- 10-19 posts --></span>";
	} else {
		$badges["posts"] = "<span class='badge ".$post_count."'><!-- fewer than 5 posts --></span>";
	}
	
	// Assign answer count badge
	if($answer_count >= 25) {
		$badges["answers"] = "<span title='25 Best Answers' class='badge gold-answerer ".$answer_count."'><!-- 25+ answers! --></span>";
	} elseif($answer_count < 25 && $answer_count >= 10) {
		$badges["answers"] = "<span title='10+ Best Answers' class='badge silver-answerer ".$answer_count."'><!--10-24 answers --></span>";
	} elseif($answer_count < 10 && $answer_count >= 3) {
		$badges["answers"] = "<span title='3+ Best Answers' class='badge bronze-answerer ".$answer_count."'><!-- 3-9 answers --></span>";
	} else {
		$badges["answers"] = "<span class='badge ".$answer_count."'><!-- fewer than 3 answers --></span>";
	}
	
	// Return array of badges
	return $badges;
}

function get_post_count($user) {
	global $bbdb;
	return $bbdb->query("SELECT * FROM $bbdb->posts WHERE poster_id = $user AND post_status = 0");
}

function get_answer_count($user) {
	global $bbdb;
<<<<<<< HEAD
	return $bbdb->query("SELECT * FROM $bbdb->posts AS posts LEFT JOIN $bbdb->meta AS meta ON posts.topic_id=meta.object_id WHERE object_type = 'bb_topic' AND poster_id = $user AND post_status = 0 AND meta_key = 'best_answer' AND post_position = meta_value");
=======
	return $bbdb->query("SELECT * FROM $bbdb->posts AS posts LEFT JOIN $bbdb->meta AS meta ON posts.topic_id=meta.object_id WHERE object_type = 'bb_topic' AND poster_id = $user AND post_status = 0 AND meta_key = 'best_answer' AND post_id = meta_value");
>>>>>>> 29a604a95344f87ef6e2049b84c61cec32001d76
}

?>
