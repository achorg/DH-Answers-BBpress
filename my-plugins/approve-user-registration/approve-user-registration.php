<?php
/*
Plugin Name: Approve User Registration
Plugin URI: http://www.adityanaik.com/projects/plugins/approve-user-registration/
Description: Holds user registration for approval from the administration
Author: Various.
Author URI: http://www.adityanaik.com/
Version: 0.5

Originally composed by Aditya Naik (http://www.adityanaik.com/), then fixed by _ck_ (http://bbpress.org/forums/profile/_ck_) to work with bbPress version 1.0, and various edits made by Tom de Bruin (www.deadlyhifi.com). What a wonderful sharing world.

v-0.4:
- ammended emails sent out.
- added column on approve page stating how long ago they registered.
0.4.1:
- updated bb_new_user function to return correct error messages.
- Set correct display name upon registration.
0.4.2:
- check registration offset by hours rather than seconds.
- auto checkbox if registered over 15 hours ago.
0.5:
- updated user list table to display registrants for bbP v1.0
- corrected some styling issues.
*/
if (!function_exists('bb_new_user')) :
function bb_new_user( $user_login, $user_email, $user_url, $user_status = 0 ) {
	global $wp_users_object, $bbdb;

	// is_email check + dns
	if ( !$user_email = bb_verify_email( $user_email ) )
		return new WP_Error( 'user_email', __( 'Invalid email address' ), $user_email );

	if ( !$user_login = sanitize_user( $user_login, true ) )
		return new WP_Error( 'user_login', __( 'Invalid username' ), $user_login );
	
	// user_status = 1 means the user has not yet been verified
	$user_status = is_numeric($user_status) ? (int) $user_status : 0;
	
	$user_nicename = $_user_nicename = bb_user_nicename_sanitize( $user_login );
	if ( strlen( $_user_nicename ) < 1 )
		return new WP_Error( 'user_login', __( 'Invalid username' ), $user_login );

	while ( is_numeric($user_nicename) || $existing_user = bb_get_user_by_nicename( $user_nicename ) )
		$user_nicename = bb_slug_increment($_user_nicename, $existing_user->user_nicename, 50);
	
	$user_url = bb_fix_link( $user_url );
	$user_registered = bb_current_time('mysql');
	$password = wp_generate_password();
	$user_pass = wp_hash_password( $password );

	$user = $wp_users_object->new_user( compact( 'user_login', 'user_email', 'user_url', 'user_nicename', 'user_status', 'user_pass' ) );
	
	if ( is_wp_error($user) ) {
		if ( 'user_nicename' == $user->get_error_code() )
			return new WP_Error( 'user_login', $user->get_error_message() );
		return $user;
	}
	
	$user_id = $bbdb->insert_id;
	$options = bb_get_option('approve_user_registration_options');
		bb_update_usermeta( $user_id, $bbdb->prefix . 'capabilities', array('waitingapproval' => true, 'member' => true) );
		approve_user_registration_send_pass( $user_id, $password );

	do_action('bb_new_user', $user['ID'], $user['plain_pass']);
	return $user['ID'];
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
	
	$test_user = defined('BACKPRESS_PATH') ? new BP_User($user->ID) : new BB_User($user->ID);
	
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
	$message = __("Your username is: %1\$s $passtext\n\nYour registration is being held for approval. Once it is approved you can log on here: %2\$s.");

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
		global $wp_roles;

		$r = '';
		$now = date('Y-m-d H:i:s');

		if ( isset($this->title) )
			$title = $this->title;
		elseif ( $this->is_search() )
			$title = sprintf(__('Users Matching "%s" by Role'), esc_html( $this->search_term ));

		$h2_role   = $this->roles[0];

		$roles = $wp_roles->get_names();
		if ( in_array( $h2_role, array_keys( $roles ) ) ) {
			$h2_role = $roles[$h2_role];
		}

		$h2_span = apply_filters( 'bb_user_search_description', sprintf( __( '%1$s%2$s' ), $h2_search, $h2_role ), $h2_search, $h2_role, $this );

		echo "<h2 class=\"first left\">" . apply_filters( 'bb_user_search_title', __('Users Waiting for Approval') ) . $h2_span . "</h2>\n";
		do_action( 'bb_admin_notices' );

		if ( $this->get_results() ) {
			if ( $this->results_are_paged() )
				$r .= "<div class='tablenav'>\n" . $this->paging_text . "</div><div class=\"clear\"></div>\n\n";
							
				$r .= "<table class='widefat'>\n";
				$r .= "<thead>\n";
				$r .= "\t<tr>\n";
				$r .= "\t\t<th style='width:10;'>&nbsp;</th>\n";
				$r .= "\t\t<th>" . __('Username') . "</th>\n";
				$r .= "\t\t<th>" . __('Email') . "</th>\n";
				$r .= "\t\t<th>" . __('Registered') . "</th>\n";
				$r .= "\t\t<th>" . __('Elapsed') . "</th>\n";
				$r .= "\t</tr>\n";
				$r .= "</thead>\n\n";
	
				$r .= "<tbody id='role-$role'>\n";
				foreach ( (array) $this->get_results() as $user_object ) {
					//$r .= bb_user_row($user_object->ID, $role, $show_email);
					$user = bb_get_user( $user_object->ID );
					$registered = date( 'Y/m/d H:i:s', bb_offset_time( bb_gmtstrtotime( $user->user_registered ) ) );
					$date_eng = date( 'H:i:s - d/m/Y', bb_offset_time( bb_gmtstrtotime( $user->user_registered ) ) );
					$difference = (strtotime($now) - strtotime($registered));
					$hours_ago = number_format( ( $difference / 60 ) / 60 , 0 );

					if ( $hours_ago < '15') {
						$reg_compare = '"color: red"';
						$checked = '';
					}
					if ( $hours_ago >= '15' && $hours_ago < '24' ) { 
						$reg_compare = '"color: blue"';
						$checked = 'checked';
					}
					if ( $hours_ago >= '24' ) { 
						$reg_compare = '"color: purple"';
						$checked = 'checked';
					}
						
					$r .= "\t<tr id='user-$user->ID'" . get_alt_class("user-$role") . ">\n";
					$r .= "\t\t<td><input type='checkbox' value='$user->ID' name='userids[]' $checked/></td>\n";
					$r .= "\t\t<td><a href='" . get_user_profile_link( $user->ID ) . "' style=" . $reg_compare . ">" . get_user_name( $user->ID ) . "</a></td>\n";
					$email = bb_get_user_email( $user->ID );
					$r .= "\t\t<td><a href='mailto:$email'>$email</a></td>\n";
					$r .= "\t\t<td>" . $date_eng . "</td>\n";
					$r .= "\t\t<td>" . $hours_ago . " hours</td>\n";
					$r .= "\n\t</tr>";
				}
					$r .= "</tbody>\n";
					$r .= "<tfoot>\n";
					$r .= "\t<tr>\n";
					$r .= "\t\t<th>&nbsp;</th>\n";
					$r .= "\t\t<th>" . __('Username') . "</th>\n";
					$r .= "\t\t<th>" . __('Email') . "</th>\n";
					$r .= "\t\t<th>" . __('Registered') . "</th>\n";
					$r .= "\t\t<th>" . __('Elapsed') . "</th>\n";
					$r .= "\t</tr>\n";
					$r .= "</tfoot>\n\n";					
					$r .= "</table>\n";
					$r .= "<p style=\"text-align: right; color: #9f9f9f; font-size: small; font-style: normal;\">Registered: Red: < 15 hours ago. Blue: > 15 hours ago. Purple: > 24 hours ago.</p>";
			if ( $this->results_are_paged() )
				$r .= "<div class='tablenav bottom'>\n" . $this->paging_text_bottom . "</div><div class=\"clear\"></div>\n\n";
			}
			?>
			<form class="settings" method="post" name="approve_user_registration_form">
			<?php
			echo $r;
			?>
	       	<fieldset class="submit">
	          <input type="submit" class="submit left" name="approve_user_registration_button_approve" value="Approve" />
	          <input type="submit" class="submit left" name="approve_user_registration_button_reject" value="Reject" />
	        </fieldset>
	        </form>
			<?php
//			}
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
	
	$user = defined('BACKPRESS_PATH') ? new BP_User($user_id) : new BB_User($user_id);
	$user->remove_cap('waitingapproval');
	
	$options = bb_get_option('approve_user_registration_options');

	$user = bb_get_user($user_id);
	
	if ($options['send_password_with'] == 'A') {
	
	$password = wp_generate_password();
	$user_pass = wp_hash_password( $password );

	$bbdb->update( $bbdb->users,
		compact( 'user_pass'),
		array('ID' => $user->ID)
	);
	$passtext = sprintf("\nYour password is: %1\$s ",$password);
	} else {$passtext="";}
	
	$message = __("Your account has been approved.\n\nYour username is: %1\$s $passtext\n\nYou can now log on: %2\$s \n\nYou can change your password by visiting your user profile edit page.");

	return bb_mail(
		bb_get_user_email( $user->ID ),
		bb_get_option('name') . ': ' . __('User Approved'),
		sprintf( $message, $user->user_login, bb_get_option('uri') )
	);
}

function approve_user_registration_reject_user($user_id) {
	global $bbdb; 
		
	$user = defined('BACKPRESS_PATH') ? new BP_User($user_id) : new BB_User($user_id);
	$user->remove_cap('waitingapproval');
	$user->remove_cap('member');
	$user->add_cap('blocked');
	
	$user = bb_get_user($user_id);
	
	$message = __("Your user %1\$s has been rejected by the administrator. This may be due to an unsuitable username, or perhaps your email address looks suspicious. If you think you have been rejected in error please contact the site administrator.");

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
		<div class="wrap"><div class="dashboard left">
		<h3><?php _e('Users waiting for approval'); ?></h3>
			<ul>
		 		<li><a href="<?php echo bb_get_option('path') . 'bb-admin/' . bb_get_admin_tab_link('approve_user_registration_admin_page') ; ?>"><?php echo $waiting_user->total_users_for_query . (($waiting_user->total_users_for_query == 1) ? ' user' : ' users') . ' waiting for approval'; ?></a> </li>
			</ul>
		</div></div>
		<div style="clear: both;"></div>
		<?php
	endif;
}
?>