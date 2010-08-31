<?php
/*
Plugin Name: Blocklist
Plugin URI:  http://bbpress.org/plugins/topic/blocklist
Description:  blocks posts based on a list of words or IP addresses (like WordPress) by immediately marking them as spam
Version: 0.0.4
Author: _ck_
Author URI: http://bbshowcase.org
*/

add_filter( 'bb_insert_post', 'blocklist_check', 8);

if ((defined('BB_IS_ADMIN') && BB_IS_ADMIN) || !(strpos($_SERVER['REQUEST_URI'],"/bb-admin/")===false)) { // "stub" only load functions if in admin 
	if (isset($_GET['plugin']) && ($_GET['plugin']=="blocklist_admin" || strpos($_GET['plugin'],"blocklist.php"))) {require_once("blocklist-admin.php");} 
	elseif (defined('BACKPRESS_PATH') && strpos($_SERVER['REQUEST_URI'],'bb-admin/content.php')) {header('Location: '.str_replace('content.php','',$_SERVER['REQUEST_URI'])); exit;}
	add_action('bb_admin_menu_generator', 'blocklist_add_admin_page');	
	function blocklist_add_admin_page() {
		global $bb_menu;  if (defined('BACKPRESS_PATH') && empty($bb_menu[165]))  {$bb_menu[165] = array( __('Manage'),'moderate','content.php', 'bb-menu-manage' );}
		bb_admin_add_submenu('Blocklist', 'administrate', 'blocklist_admin','content.php');
	} 
} 

function blocklist_initialize() { 
	global $blocklist; 
	if (!isset($blocklist)) {$blocklist = bb_get_option('blocklist'); if (empty($blocklist)) {$blocklist['data']="";$blocklist['email']="";}}
}	
	
function blocklist_check($post_id=0,$wall=false) { 	
	if (bb_current_user_can('moderate') || bb_current_user_can('throttle')) {return;}	
	if ($wall) {$bb_post = user_wall_get_post( $post_id);} else {$bb_post = bb_get_post( $post_id );}
	if (empty($post_id) || empty($bb_post) || !empty($bb_post->post_status)) {return;}
	
	global $blocklist,$bbdb; blocklist_initialize(); 
	if (empty($blocklist['data'])) {return;}
	(array) $data=explode("\r\n",$blocklist['data']);
	$user=bb_get_user($bb_post->poster_id);
	
	foreach ($data as $item) {				
		if (empty($item) || strlen($item)<4 || ord($item)==35)  {continue;}
		if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}/',$item)) {	// is IP		
		if (strpos($bb_post->poster_ip,$item)===0) {$found="IP address"; $bad=$item; break;}
		} else {	 	// is word
			$qitem=preg_quote($item);
			if (preg_match('/\b'.$qitem.'/simU',$user->user_email)) {$found="email"; $bad=$item; break;}
			if (preg_match('/\b'.$qitem.'/simU',$user->user_login)) {$found="username"; $bad=$item; break;}
			if (preg_match('/\b'.$qitem.'/simU',$bb_post->post_text)) {$found="post text"; $bad=$item; break;}
			elseif (!$wall && $bb_post->post_position==1) {
				if (empty($topic)) {$topic = get_topic( $bb_post->topic_id );}				
				if (!empty($topic->topic_title) && preg_match('/\b'.$qitem.'/simU',$topic->topic_title)) {$found="topic title"; $bad=$item; break;}
			}
		}
		if (!empty($bad)) {break;}
	}	
	if (!empty($bad)) {		
		
		if ($wall) {
			user_wall_delete_post( $post_id, 2);
			$uri=bb_get_option('uri') . "bb-admin/admin-base.php?post_status=2&plugin=user_wall_admin&user-wall-recent=1";
		} else {			
			bb_delete_post( $post_id, 2);
			if (empty($topic)) {$topic = get_topic( $bb_post->topic_id );}
			if ( empty($topic->topic_posts) ) {bb_delete_topic( $topic->topic_id, 2 );}	// if no posts in topic, also set topic to spam
			$uri=bb_get_option('uri').'bb-admin/'.(defined('BACKPRESS_PATH') ? '' : 'content-').'posts.php?post_status=2';
		}
		
		if (empty($blocklist['email'])) {return;}
		(array) $email=explode("\r\n",$blocklist['email']);		
		
		$message="The blocklist has been triggered... \r\n\r\n";		
		$message.="Matching entry ".'"'.$bad.'"'." found in $found.\r\n";
		$message.= "$uri\r\n\r\n";		
		$message .= sprintf(__('Username: %s'), stripslashes($user->user_login)) . "\r\n";
		$message .= sprintf(__('Profile: %s'), get_user_profile_link($user->ID)) . "\r\n";
		$message .= sprintf(__('Email: %s'), stripslashes($user->user_email)) . "\r\n";		
		$message .= sprintf(__('IP address: %s'), $_SERVER['REMOTE_ADDR']) . "\r\n";
		$message .= sprintf(__('Agent: %s'), substr(stripslashes($_SERVER["HTTP_USER_AGENT"]),0,255)) . "\r\n\r\n";			

		foreach ($email as $to) {if (empty($to) || strlen($to)<8) {continue;}
			@bb_mail($to , "[".bb_get_option('name')."] blocklist triggered", $message);
		}
	} 	
}

?>