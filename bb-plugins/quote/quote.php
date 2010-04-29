<?php
/*
Plugin Name: Quote
Description: Quote message when replying
Author: Michael Nolan
Author URI: http://www.michaelnolan.co.uk/
Version: 0.2
*/

function bb_quote_link($link_text = 'Quote') {
	global $bb, $page, $topic, $forum, $bb_post;
	$add = topic_pages_add();
	if (!topic_is_open( $bb_post->topic_id ) || !bb_is_user_logged_in()) return;
	$post_id = get_post_id();
	$last_page = get_page_number( $topic->topic_posts + $add );
	echo '<a href="'.get_topic_link( 0, $last_page ).'&quote='.$post_id.'#postform" id="quote_'.$post_id.'">'.__($link_text).'</a>';
}

function bb_quote_message() {
	global $bbdb, $topic;
	$post_id = (int)$_GET['quote'];
	if ($post_id) {
		$row = $bbdb->get_row("SELECT * FROM $bbdb->posts WHERE post_id={$post_id} AND topic_id={$topic->topic_id} AND post_status=0");
		$row->post_text = preg_replace( '(<p>|</p>)', '', $row->post_text );
		if ($row) echo htmlentities('<blockquote>'.$row->post_text.'</blockquote>', ENT_COMPAT, 'UTF-8');
	}
}

?>