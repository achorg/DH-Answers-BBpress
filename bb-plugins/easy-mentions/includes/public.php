<?php

/**
 * @package Easy Mentions
 * @subpackage Public Section
 * @author Gautam Gupta (www.gaut.am)
 * @link http://gaut.am/bbpress/plugins/easy-mentions/
 */

/**
 * Links the users & tags in posts
 *
 * @see bp_activity_at_name_filter() BuddyPress Function
 * 
 * @param $content The content to be parsed
 */
function em_do_linking( $content ){
	global $em_plugopts;
	
	if ( $em_plugopts['link-tags'] == 1 ){
		preg_match_all( '/[#]+([A-Za-z0-9-_]+)/', $content, $tags );
		$tags = $tags[1];
	
		foreach( (array)$tags as $tag ) {
			if ( !$t = bb_get_tag( $tag ) )
				continue;
			
			if ( $link = bb_get_tag_link( $t ) )
				$content = str_replace( "#$tag", "#<a href='" . $link . "'>$tag</a>", $content ); //should we add rel='nofollow'?
		}
	}
	
	if ( $em_plugopts['link-users'] == 1 ){
		preg_match_all( '/[@]+([A-Za-z0-9-_]+)/', $content, $usernames );
	
		$usernames = $usernames[1];
	
		foreach( (array)$usernames as $username ) {
			if ( !$user = bb_get_user( $username, array( 'by' => 'login' ) ) ){ //check #1, by username
				if ( !$user = bb_get_user( $username, array( 'by' => 'nicename' ) ) ) //check #2, by nicename
					continue;
			}
	
			/* Increase the number of new @ mentions for the user - maybe later */
			/*$new_mention_count = (int)bb_get_usermeta( $user_id, 'em_mention_count' );
			bb_update_usermeta( $user_id, 'em_mention_count', $new_mention_count + 1 );*/
			
			if ( 'website' == $em_plugopts['link-user-to'] ) {
				if ( !$link = $user->user_url )
					$link = get_user_profile_link( $user->ID );
			} else {
				$link = get_user_profile_link( $user->ID );
			}
			
			if ( $link )
				$content = str_replace( "@$username", "@<a href='" . $link . "'>$username</a>", $content ); //should we add rel='nofollow'?
		}
	}

        return $content;
}

/**
 * Add reply link below each post
 *
 * @param $post_links Array of the links
 * @param $args Array of args
 */
function em_reply_link( $post_links = array(), $args = array() ) {
	global $em_plugopts;
	
	if ( $em_plugopts['reply-link'] == 1 && $em_plugopts['reply-text'] && bb_is_topic() && topic_is_open() && ( bb_is_user_logged_in() || ( function_exists( 'bb_is_login_required' ) && !bb_is_login_required() ) ) ){ /* Check if link is needed */
		$text		= str_replace( "%%POSTLINK%%", get_post_link(), str_replace( "%%USERNAME%%", get_post_author(), $em_plugopts['reply-text'] ) );
		$js		= "var ema=document.getElementById('post_content');var emb=ema.value;if(emb!='')emb+='\\n\\n';ema.value=emb+'" . $text . "\\n\\n';ema.focus();void(0);";
		$post_links[]	= $args['before_each'] . '<a class="reply_link" style="cursor:pointer" onclick="' . $js . '">' . __( 'Reply', 'easy-mentions' ) . '</a>' . $args['after_each'];
	}
	
        return $post_links;
}

add_filter( 'post_text', 'em_do_linking', -999, 1 ); /* Do Linking */
add_filter( 'bb_post_admin', 'em_reply_link', 11, 2 ); /* Add reply link */
