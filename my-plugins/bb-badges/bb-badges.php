<?php
/*
 * Plugin Name: DHAnswers Badges
 * Plugin Description: Display "badges" for users meeting certain criteria
 * Author: Joseph Gilbert
 * Author URI: http://lib.virginia.edu/scholarslab/
 * Plugin URI: http://lib.virginia.edu/scholarslab/
 * Version: 0.1
 */

/**
 * Display the array of badges a user has amassed
 *
 * @return void
 */
function show_badges() {
	$badges = get_badges(get_post_author_id());
	// Print array of badges
	foreach($badges as $badge) {
		echo($badge);
	}
}

/**
 * Calculate the social badges earned by a specific user
 *
 * @param $user integer ID of the user to look up.
 * @return $badges array Array of social badges
 */
function get_badges($user) {
	// initiate badge array
	$badges = array(2);
	
	// Get # of user posts, # of best answers
	$post_count = get_post_count($user);
	$answer_count = get_answer_count($user);
	
	// Assign post count badge
	switch($post_count)
	{
	    case($post_count >= 50):
	        $badges["posts"] = "<span title='50+ Posts' class='badge gold-poster ".$post_count."'><!-- 50+ posts! --></span>";
	        break;
	    case($post_count >= 20):
	       	$badges["posts"] = "<span title='20+ Posts' class='badge silver-poster ".$post_count."'><!-- 20-49 posts --></span>";
	       	break;
	    case($post_count >= 5):
	        $badges["posts"] = "<span title='5+ Posts' class='badge bronze-poster ".$post_count."'><!-- 10-19 posts --></span>";
	        break;
	    default:
	        $badges["posts"] = "<span class='badge ".$post_count."'><!-- fewer than 5 posts --></span>";
	}

	// Assign answer count badge
	switch($answer_count)
	{
	    case($answer_count >= 25):
	        $badges["answers"] = "<span title='25 Best Answers' class='badge gold-answerer ".$answer_count."'><!-- 25+ answers! --></span>";
	        break;
	    case($answer_count >= 10):
	        $badges["answers"] = "<span title='10+ Best Answers' class='badge silver-answerer ".$answer_count."'><!--10-24 answers --></span>";
	        break;
	    case($answer_count >= 3):
	        $badges["answers"] = "<span title='3+ Best Answers' class='badge bronze-answerer ".$answer_count."'><!-- 3-9 answers --></span>";
	        break;
	    default:
	        $badges["answers"] = "<span class='badge ".$answer_count."'><!-- fewer than 3 answers --></span>";
	}
	
	// Return array of badges
	return $badges;
}


/**
 * Returns the number of posts for a user
 * @param $user integer user
 */
function get_post_count($user) {
	global $bbdb;
	return $bbdb->query(sprintf("SELECT COUNT(*) FROM $bbdb->posts WHERE poster_id = '%d' AND post_status = 0", $user));
}

/**
 * Return the number of best answers for a user
 * @param $user integer 
 */
function get_answer_count($user) {
	global $bbdb;

	
	$sql = <<<SQL
	SELECT COUNT(*) 
	FROM $bbdb->posts AS p
	LEFT JOIN $bbdb->meta AS m
	    ON p.topic_id = m.object_id
	WHERE m.object_type = 'bb_topic' 
	AND m.meta_key = 'best_answer'
	AND p.post_status = 0
	AND p.post_id = m.meta_value
	AND p.poster_id = '%d'
SQL;

	return $bbdb->query(sprintf($sql), $user));

}

?>
