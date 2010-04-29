<?php

/**
 * @package Easy Mentions
 * @subpackage Admin Section
 * @author Gautam Gupta (www.gaut.am)
 * @link http://gaut.am/bbpress/plugins/easy-mentions/
 */

/**
 * Check for Updates
 * 
 * @return string|bool Returns version if update is available, else false
 * @uses WP_Http
 */
function em_update_check(){
	$latest_ver = trim( wp_remote_retrieve_body( wp_remote_request( 'http://gaut.am/uploads/plugins/updater.php?pid=7&chk=ver&soft=bb&current=' . EM_VER ) ) );
	if ( $latest_ver && version_compare( $latest_ver, EM_VER, '>' ) )
		return strval( $latest_ver );
	
	return false;
}

/**
 * Makes a settings page for the plugin
 * 
 * @uses bb_option_form_element() to generate the page
 */
function em_options(){
	global $em_plugopts;
	
	if ( $_POST['em_opts_submit'] == 1 ) { /* Settings have been received, now save them! */
		bb_check_admin_referer( 'em-save-chk' ); /* Security Check */
		
		/* Checks on options, and then save them */
		$em_plugopts['link-tags']	= ( intval( $_POST['link-tags'] ) == 1 ) ? 1 : 0;
		$em_plugopts['link-users']	= ( intval( $_POST['link-users'] ) == 1 ) ? 1 : 0;
		$em_plugopts['link-user-to']	= ( $_POST['link-user-to'] == 'website' ) ? 'website' : 'profile';
		$em_plugopts['reply-link']	= ( intval( $_POST['reply-link'] ) == 1 ) ? 1 : 0;
		$em_plugopts['reply-text']	= esc_attr( $_POST['reply-text'] );
		
		bb_update_option( EM_OPTIONS, $em_plugopts );
		bb_admin_notice( __( 'The options were successfully saved!', 'easy-mentions' ) );
	}
	
	if ( $ver = em_update_check() ) /* Check for Updates and if available, then notify */
		bb_admin_notice( sprintf( __( 'New version (%1$s) of Easy Mentions is available! Please download the latest version from <a href="%2$s">here</a>.', 'easy-mentions' ), $ver, 'http://bbpress.org/plugins/topic/easy-mentions/' ) );
	
	/* Options in an array to be printed */
	$options = array(
		'link-tags' => array(
			'title'		=> __( 'Link the Tags?', 'easy-mentions' ),
			'type'		=> 'checkbox',
			'value'		=> ( $em_plugopts['link-tags'] == 1 ) ? '1' : '0',
			'note'		=> sprintf( __( 'Check this option if you want the tags to be linked (by using %s) in the posts.', 'easy-mentions' ), '<code>#tag</code>' ),
			'options'	=> array(
				'1'	=> __( 'Yes', 'easy-mentions' ),
			)
		),
		'link-users' => array(
			'title'		=> __( 'Link the Users?', 'easy-mentions' ),
			'type'		=> 'checkbox',
			'value'		=> ( $em_plugopts['link-users'] == 1 ) ? '1' : '0',
			'note'		=> sprintf( __( 'Check this option if you want the users to be linked (by using %s) in the posts.', 'easy-mentions' ), '<code>@user</code>' ),
			'options'	=> array(
				'1'	=> __( 'Yes', 'easy-mentions' ),
			)
		),
		'link-user-to' => array(
			'title'		=> __( 'Link the user to profile or website?', 'easy-mentions' ),
			'type'		=> 'radio',
			'value'		=> ( $em_plugopts['link-user-to'] == 'website' ) ? 'website' : 'profile',
			'note'		=> __( 'If you selected the website option and the user\'s website does not exist, then the user will be linked to his or her profile page.', 'easy-mentions' ),
			'options'	=> array(
				'profile' => __( 'Profile', 'easy-mentions' ),
				'website' => __( 'Website', 'easy-mentions' ),
			)
		),
		'reply-link' => array(
			'title'		=> __( 'Add a reply link below each post?', 'easy-mentions' ),
			'type'		=> 'checkbox',
			'value'		=> ( $em_plugopts['reply-link'] == 1 ) ? '1' : '0',
			'note'		=> sprintf( __( 'Before checking this option, please verify that there is a post form below the topic on each page. (<a href="%s">Help</a>)', 'easy-mentions' ), 'http://bbpress.org/plugins/topic/easy-mentions/faq/' ),
			'options'	=> array(
				'1' => __( 'Yes', 'easy-mentions' ),
			)
		),
		'reply-text' => array(
			'title'		=> __( 'Reply Text', 'easy-mentions' ),
			'class'		=> array( 'long' ),
			'value'		=> $em_plugopts['reply-text'] ? stripslashes( $em_plugopts['reply-text'] ) : '<em>Replying to @%%USERNAME%%\'s <a href="%%POSTLINK%%">post</a>:</em>',
			'after'		=> '<div style="clear:both;"></div>' . sprintf( __( 'Some HTML is allowed. The following keys can also be used:%1$s - Post\'s author\'s name%2$s - Post\'s link', 'after-the-deadline' ), '<br /><strong>%%USERNAME%%</strong>', '<br /><strong>%%POSTLINK%%</strong>' ) . '<br />'
		)
	);
	if ( $em_plugopts['link-users'] != 1 )
		$options['link-user-to']['attributes'] = array( 'disabled' => 'disabled' );
		
	if ( $em_plugopts['reply-link'] != 1 )
		$options['reply-text']['attributes'] = array( 'disabled' => 'disabled' );
	
	?>
	
	<h2><?php _e( 'Easy Mentions', 'easy-mentions' ); ?></h2>
	<?php do_action( 'bb_admin_notices' ); ?>
	<form method="post" class="settings options">
		<fieldset>
			<?php
			foreach ( $options as $option => $args ) {
				bb_option_form_element( $option, $args );
			}
			?>
		</fieldset>
		<fieldset class="submit">
			<?php bb_nonce_field( 'em-save-chk' ); ?>
			<input type="hidden" name="em_opts_submit" value="1"></input>
			<input class="submit" type="submit" name="submit" value="Save Changes" />
		</fieldset>
		<p><?php printf( __( 'Happy with the plugin? Why not <a href="%1$s">buy the author a cup of coffee or two</a> or get him something from his <a href="%2$s">wishlist</a>?', 'easy-mentions' ), 'http://gaut.am/donate/EM/', 'http://gaut.am/wishlist/' ); ?></p>
	</form>
<?php
}

/**
 * Enqueue the javascript in the admin head section
 *
 * @uses wp_enqueue_script()
 */
function em_admin_head() {
	global $bb_admin_page;
	if( $bb_admin_page == 'em_options' )
		wp_enqueue_script( 'easy-mentions', EM_PLUGPATH.'js/admin.js', array('jquery'), EM_VER );
}

/**
 * Adds a menu link to the setting's page in the Settings section
 *
 * @uses bb_admin_add_submenu()
 */
function em_menu_link() {
	bb_admin_add_submenu( __( 'Easy Mentions', 'easy-mentions' ), 'administrate', 'em_options', 'options-general.php' );
}

add_action( 'bb_admin_menu_generator', 'em_menu_link', 8, 0 ); /* Adds a menu link to setting's page */
add_action( 'bb_admin_print_scripts', 'em_admin_head', 2 ); /* Enqueue the Javascript */