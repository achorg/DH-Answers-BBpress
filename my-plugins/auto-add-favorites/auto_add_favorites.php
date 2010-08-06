<?php
/**
 * Plugin Name: Auto Add Favorites
 * Plugin Description: Subscribe to thread whenever you have posted a comment
 * Author: Olaf Lederer
 * Author URI: http://www.finalwebsites.com/portal
 * Plugin URI: http://www.finalwebsites.com/bbpress/auto-add-member-favorites.php
 * Version: 1.0
 */
 
 
function auto_add_favorit() {
	global $topic_id, $bb_current_user;
	
	if (!empty($bb_current_user->data->auto_add_favorit)) {
		if (!empty($_POST['add_to_my_favorites'])) {
			if (is_user_favorite($bb_current_user->ID, $topic_id)) {
				return;
			} else {
				bb_add_user_favorite($bb_current_user->ID, $topic_id);
				return;
			}
		} else {
			return;
		}
	} else {
		return;
	}
}
add_action('bb_new_post', 'auto_add_favorit');


function post_form_auto_add_checkbox() {
	global $topic_id, $bb_current_user;
	
	if (is_user_favorite($bb_current_user->ID, $topic_id)) {
		return;
	} else {
		$checked = (!empty($bb_current_user->data->auto_add_favorit)) ? ' checked="checked"' : '';
		echo '
		<p>
			<input type="checkbox" name="add_to_my_favorites" id="add_to_my_favorites" value="1"'.$checked.' />
			<label for="add_to_my_favorites" style="display:inline;">Add this thread to my favorites </label>
			<span> (change your default <a href="'.attribute_escape(get_profile_tab_link($bb_current_user->ID, 'edit')).'">here</a>)</span>
		</p>';
	}
}
add_action('post_form', 'post_form_auto_add_checkbox');


function auto_add_favorit_profile() {
	global $user_id;
	
	if (bb_is_user_logged_in()) {
		$checked = "";
		$user = bb_get_user($user_id);
		if (!empty($user->auto_add_favorit)) {
			$checked = ' checked="checked"';
		}
		echo '
			<fieldset>
				<legend>Add my threads automatically to my favorites</legend>
				<p>Check this option to add all your threads or threads where you have posted something to your favorite list.</p>
				<table width="100%">
					<tr>
						<th width="21%" scope="row">Activate:</th>
						<td width="79%">
							<input name="edit_auto_add_favorit" id="edit_auto_add_favorit" type="checkbox" value="1"'.$checked.' />
						</td>
					</tr>
				</table>
			</fieldset>';
	}
}
add_action('extra_profile_info', 'auto_add_favorit_profile');


function auto_add_favorit_profile_edit() {
	global $user_id;
	
	bb_update_usermeta($user_id, "auto_add_favorit", $_POST['edit_auto_add_favorit']);
}

add_action('profile_edited', 'auto_add_favorit_profile_edit'); 


function enable_for_new_members() {
	global $bb_current_user;
	if (!isset($bb_current_user->data->auto_add_favorit)) {
		bb_update_usermeta($bb_current_user->ID, "auto_add_favorit", 1);
	}
}
add_action('bb_set_current_user', 'enable_for_new_members'); 

?>