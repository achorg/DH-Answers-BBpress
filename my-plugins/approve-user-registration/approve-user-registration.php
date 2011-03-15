<?php
/*
Plugin Name: Approve User Registration
Plugin URI: http://www.adityanaik.com/projects/plugins/approve-user-registration/
Description: Holds user registration for approval from the administration
Author: Aditya Naik
Author URI: http://www.adityanaik.com/
Version: 0.3
*/
if (!function_exists('bb_new_user')) :
function bb_new_user( $user_login, $user_email, $user_url ) {
	global $bbdb;
	$user_login = sanitize_user( $user_login, true );
	$user_email = bb_verify_email( $user_email );
	
	if ( !$user_login || !$user_email )
		return false;
	
	$user_nicename = $_user_nicename = bb_user_nicename_sanitize( $user_login );
	if ( strlen( $_user_nicename ) < 1 )
		return false;

	while ( is_numeric($user_nicename) || $existing_user = bb_get_user_by_nicename( $user_nicename ) )
		$user_nicename = bb_slug_increment($_user_nicename, $existing_user->user_nicename, 50);
	
	$user_url = bb_fix_link( $user_url );
	$user_registered = bb_current_time('mysql');
	$password = wp_generate_password();
	$user_pass = wp_hash_password( $password );

	$bbdb->insert( $bbdb->users,
		compact( 'user_login', 'user_pass', 'user_nicename', 'user_email', 'user_url', 'user_registered' )
	);
	
	$user_id = $bbdb->insert_id;
	$options = bb_get_option('approve_user_registration_options');
	if ( defined( 'BB_INSTALLING' ) ) {
		bb_update_usermeta( $user_id, $bbdb->prefix . 'capabilities', array('keymaster' => true) );
	} else {		
		bb_update_usermeta( $user_id, $bbdb->prefix . 'capabilities', array('waitingapproval' => true, 'member' => true) );
		approve_user_registration_send_pass( $user_id, $password );
	}

	do_action('bb_new_user', $user_id, $password);
	return $user_id;
}
endif;

if (!function_exists('bb_check_login')) :
function bb_check_login($user, $pass, $already_md5 = false) {
	global $bbdb;
	$user = sanitize_user( $user );
	if ($user == '') {
		return false;
	}
	$user = bb_get_user_by_name( $user );
	
	$test_user = new BB_User($user->ID);
	
	if ($test_user->has_cap('waitingapproval')) return false;
	
	if ( !wp_check_password($pass, $user->user_pass, $user->ID) ) {
		return false;
	}
	
	return $user;
}
endif;

function approve_user_registration_send_pass( $user, $pass ) {
	if ( !$user = bb_get_user( $user ) )
		return false;
	$options = bb_get_option('approve_user_registration_options');
	$passtext = ($options['send_password_with'] != 'A') ? sprintf("\nYour password is: %1\$s ",$pass) : '';
	$message = __("Your username is: %1\$s $passtext\nYour registration is being held for approval by the administrator. Once it is approved you can log on here: %2\$s \n\nEnjoy!");

	return bb_mail(
		bb_get_user_email( $user->ID ),
		bb_get_option('name') . ': ' . __('User Registration'),
		sprintf( $message, $user->user_login, bb_get_option('uri') )
	);
}

if (!BB_IS_ADMIN) {
	return;
}

add_action( 'bb_admin_menu_generator', 'approve_user_registration_add_admin_page' );
function approve_user_registration_add_admin_page() {
	bb_admin_add_submenu(__('Registration Queue'), 'moderate', 'approve_user_registration_admin_page', 'users.php');
	bb_admin_add_submenu(__('Registration Settings'), 'moderate', 'approve_user_registration_settings_page', 'options-general.php');
}

function approve_user_registration_settings_page() {
	$options = bb_get_option('approve_user_registration_options');
	?>
	<h2>Registration Settings</h2>
	<form class="options" method="post">
		<fieldset>
			<label for="">
				Send password with 
			</label>
			<div>
				Registration Mail <input <?php if($options['send_password_with'] != 'A') echo ' checked="checked" ' ?> type="radio" name="approve_user_registration[send_password_with]" id="send_password_with_registration" value="R"/>
				Approval Mail <input <?php if($options['send_password_with'] == 'A') echo ' checked="checked" ' ?> type="radio" name="approve_user_registration[send_password_with]" id="send_password_with_approval" value="A"/>
			</div>
	       	<p class="submit">
	          <input type="submit" name="approve_user_registration_button_options" value="Update Options" />
	        </p>
	        </fieldset>
	</form>
	<?php
}

add_action('approve_user_registration_settings_page_pre_head','approve_user_registration_settings_page_process');
function approve_user_registration_settings_page_process() {

	if (isset($_POST['approve_user_registration'])) {
		$options = ($_POST['approve_user_registration']) ? $_POST['approve_user_registration'] : array() ;
		bb_update_option('approve_user_registration_options',$options);
	}
}

add_action('approve_user_registration_admin_page_pre_head','approve_user_registration_admin_page_process');
function approve_user_registration_admin_page_process() {
	class BB_Users_Waiting_Approval extends BB_Users_By_Role {
		var $role = '';
		var $title = '';
	
		function BB_Users_Waiting_Approval($page = '') { // constructor
			$this->role = 'waitingapproval';
			$this->raw_page = ( '' == $page ) ? false : (int) $page;
			$this->page = (int) ( '' == $page ) ? 1 : $page;
	
			$this->prepare_query();
			$this->query();
			$this->do_paging();
		}
		
		function display( $show_search = true, $show_email = false ) {
			global $bb_roles;
			$r = '';
			// Make the user objects
			foreach ( $this->get_results() as $user_id ) {
				$tmp_user = new BB_User($user_id);
				$roles = $tmp_user->roles;
				$role = array_shift($roles);
				$roleclasses[$role][$tmp_user->data->user_login] = $tmp_user;
			}
	
			if ( isset($this->title) )
				$title = $this->title;
			elseif ( $this->is_search() )
				$title = sprintf(__('Users Matching "%s" by Role'), wp_specialchars( $this->search_term ));
			else
				$title = __('User List by Role');
			$r .= "<h2>$title</h2>\n";
	
			if ( $show_search ) {
				$r .= "<form action='' method='get' id='search'>\n\t<p>";
				$r .= "\t\t<input type='text' name='usersearch' id='usersearch' value='" . wp_specialchars( $this->search_term, 1) . "' />\n";
				$r .= "\t\t<input type='submit' value='" . __('Search for users &raquo;') . "' />\n\t</p>\n";
				$r .= "</form>\n\n";
			}
	
			if ( $this->get_results() ) {
				if ( $this->is_search() )
					$r .= "<p>\n\t<a href='users.php'>" . __('&laquo; Back to All Users') . "</a>\n</p>\n\n";
	
				$r .= '<h3>' . sprintf(__('%1$s &#8211; %2$s of %3$s shown below'), $this->first_user + 1, min($this->first_user + $this->users_per_page, $this->total_users_for_query), $this->total_users_for_query) . "</h3>\n";
	
				if ( $this->results_are_paged() )
					$r .= "<div class='user-paging-text'>\n" . $this->paging_text . "</div>\n\n";
	
				foreach($roleclasses as $role => $roleclass) {
					ksort($roleclass);
					if ( !empty($role) )
						$r .= "<h3>{$bb_roles->role_names[$role]}</h3>\n";
					else
						$r .= "<h3><em>" . __('Users with no role in these forums') . "</h3>\n";
					$r .= "<table class='widefat'>\n";
					$r .= "<thead>\n";
					$r .= "\t<tr>\n";
					$r .= "\t\t<th style='width:10%;'>" . __('ID') . "</th>\n";
					if ( $show_email ) {
						$r .= "\t\t<th style='width:30%;'>" . __('Username') . "</th>\n";
						$r .= "\t\t<th style='width:30%;'>" . __('Email') . "</th>\n";
					} else {
						$r .= "\t\t<th style='width:60%;'>" . __('Username') . "</th>\n";
					}
					$r .= "\t\t<th style='width:20%;'>" . __('Registered At') . "</th>\n";
					$r .= "\t</tr>\n";
					$r .= "</thead>\n\n";
	
					$r .= "<tbody id='role-$role'>\n";
					foreach ( (array) $roleclass as $user_object ) {
						//$r .= bb_user_row($user_object->ID, $role, $show_email);
						$user = bb_get_user( $user_object->ID );
						$r .= "\t<tr id='user-$user->ID'" . get_alt_class("user-$role") . ">\n";
						$r .= "\t\t<td><input type='checkbox' value='$user->ID' name='userids[]'/></td>\n";
						$r .= "\t\t<td><a href='" . get_user_profile_link( $user->ID ) . "'>" . get_user_name( $user->ID ) . "</a></td>\n";
						if ( $show_email ) {
							$email = bb_get_user_email( $user->ID );
							$r .= "\t\t<td><a href='mailto:$email'>$email</a></td>\n";
						}
						$r .= "\t\t<td>" . date( 'Y-m-d H:i:s', bb_offset_time( bb_gmtstrtotime( $user->user_registered ) ) ) . "</td>\n";
						$r .= "\n\t</tr>";
					}
					$r .= "</tbody>\n";
					$r .= "</table>\n\n";
				}
	
			 	if ( $this->results_are_paged() )
					$r .= "<div class='user-paging-text'>\n" . $this->paging_text . "</div>\n\n";
			}
			?>
			<form method="post" name="approve_user_registration_form">
			<?php
			echo $r;
			if ($roleclass) {
			?>
	       <p class="submit">
	          <input type="submit" name="approve_user_registration_button_approve" value="Approve" />
	          <input type="submit" name="approve_user_registration_button_reject" value="Reject" />
	        </p>
	        </form>
			<?php
			}
		}
	}
	
	if (isset($_POST['approve_user_registration_button_approve'])) {
		$users = $_POST['userids'];
		if ($users)
		foreach($users as $user) {
			approve_user_registration_approve_user($user);
		}
	} elseif (isset($_POST['approve_user_registration_button_reject'])) {
		$users = $_POST['userids'];
		if ($users)
		foreach($users as $user) {
			approve_user_registration_reject_user($user);
		}
	}
	
}

function approve_user_registration_admin_page() {
	$bb_waiting_users = new BB_Users_Waiting_Approval( $_GET['userspage'] );

	$bb_waiting_users->title = __('These users are waiting for their account to be approved');
	$bb_waiting_users->display( false, bb_current_user_can( 'edit_users' ) );	
}

function approve_user_registration_approve_user($user_id) {
	global $bbdb; 
	
	$user = new BB_User($user_id);
	$user->remove_cap('waitingapproval');
	
	$options = bb_get_option('approve_user_registration_options');

	$user = bb_get_user($user_id);
	
	$password = wp_generate_password();
	$user_pass = wp_hash_password( $password );

	$bbdb->update( $bbdb->users,
		compact( 'user_pass'),
		array('ID' => $user->ID)
	);
	$passtext = ($options['send_password_with'] == 'A') ? sprintf("\nYour password is: %1\$s ",$password) : '';
	$message = __("Your user %1\$s has been approved by the administrator. $passtext\nYou can now log on: %2\$s \n\nEnjoy!");

	return bb_mail(
		bb_get_user_email( $user->ID ),
		bb_get_option('name') . ': ' . __('User Approved'),
		sprintf( $message, $user->user_login, bb_get_option('uri') )
	);
}

function approve_user_registration_reject_user($user_id) {
	global $bbdb; 
	
	$user = new BB_User($user_id);
	$user->remove_cap('waitingapproval');
	$user->remove_cap('member');
	$user->add_cap('blocked');
	
	$user = bb_get_user($user_id);
	
	$message = __("Your user %1\$s has been rejected by the administrator.");

	return bb_mail(
		bb_get_user_email( $user->ID ),
		bb_get_option('name') . ': ' . __('User Rejected'),
		sprintf( $message, $user->user_login )
	);
}

add_action('bb_admin-footer.php','approve_user_registration_dashboard'); 
function approve_user_registration_dashboard() {
	global $page,$bb_current_menu;
	$waiting_user = new BB_Users_By_Role( 'waitingapproval' );
	if($bb_current_menu[0] == 'Dashboard' && $waiting_user->total_users_for_query > 0 ) :
		?>
		<h3><?php _e('Users waiting for approval'); ?></h3>
		<ul>
		 <li><a href="<?php echo bb_get_option('path') . 'bb-admin/' . bb_get_admin_tab_link('approve_user_registration_admin_page') ; ?>"><?php echo $waiting_user->total_users_for_query . (($waiting_user->total_users_for_query == 1) ? ' user' : ' users') . ' waiting for approval'; ?></a> </li>
		</ul>
		<?php
	endif;
}
?>