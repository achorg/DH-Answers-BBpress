<?php
/*
Plugin Name: bbPress Tweets
Plugin URI:  http://shuttlex.blogdns.net
Description:  Lets the user choose to show his or hers latest Tweet on their profile page and/or posts
Version: 0.3
Author: RuneG
Author URI: http://shuttlex.blogdns.net

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

Donate: http://www.amazon.com/gp/registry/wishlist/1K51U8VX047NY/ref=wl_web

Instructions:   install, activate, tinker with settings in admin menu


Version History:
0.1 	: First public release
0.2		: Fixed problem avatars did not show. This happend if bb-avatars where not in use.
0.3		: Added the possibility to show of the latest tweet under every post from the user
*/

function fetch_user_twitter($user_id) {
	$user = bb_get_user( $user_id );  
	$twitter=$user->twitter;
	if ($twitter) {return $twitter;}  else  {return "";}
	
	
}

function add_twitter_to_profile_edit() {
global $user_id, $bb_current_user,$bb_twitter;		
if (bb_current_user_can( 'edit_profile', $user->ID )  &&  bb_is_user_logged_in() ) :
	$twitter = fetch_user_twitter($user_id);
	$user = bb_get_user( $user_id );
	$tweets_on = $user->twitter_on;
	$tweets_on_post = $user->twitter_on_post;
	
?><fieldset>
<legend><?php  _e('Twitter')?></legend>
<table border=0>
<tr>
<td>Twitter username : </td><td><input type="text" name="twitter" value="<?php echo $twitter;?> " size="25"/></td>
</tr>
<?php if (false){ ?>
	<tr>
	<td>Show your latest <em>tweet</em> in your profile?</td><td>
	<input name="show_tweets" value="tweets_on" type="checkbox" checked="checked"/></td>
	<input name="show_tweets" value="tweets_on" type="checkbox"/></td>
	</tr>
	<tr>
	<td>Show your latest <em>tweet</em> under each post?</td><td>
	<input name="show_tweets_post" value="tweets_on_post" type="checkbox" checked="checked"/></td>
	<input name="show_tweets_post" value="tweets_on_post" type="checkbox"/></td>
<?php } ?>
</tr>
</table>
</fieldset>
<?php 
	endif;
}
add_action('extra_profile_info', 'add_twitter_to_profile_edit');

function update_user_twitter() {
	global $user_id, $bb_twitter;
	$twitter = $_POST['twitter'];
	if ($_POST['show_tweets']){
	$tweets_on = "yes";
	} else {
	$tweets_on = "no";
	}
	if ($_POST['show_tweets_post']){
	$tweets_on_post = "yes";
	} else {
	$tweets_on_post = "no";
	}
	bb_update_usermeta($user_id, "twitter",$twitter);
	bb_update_usermeta($user_id, "twitter_on",$tweets_on);
	bb_update_usermeta($user_id, "twitter_on_post",$tweets_on_post);
	
	
	
}
add_action('profile_edited', 'update_user_twitter');


function bb_show_tweets() {
 
}

function tweet_on_post($text) {

}

function tweet_start(){
 
}	


?>