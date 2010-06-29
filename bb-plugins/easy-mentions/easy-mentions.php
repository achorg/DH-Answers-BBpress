<?php
/*
 Plugin Name: Easy Mentions
 Plugin URI: http://gaut.am/bbpress/plugins/easy-mentions/
 Description: Easy Mentions allows the users to link to other users' profiles in posts by using @username (like Twitter).
 Author: Gautam Gupta
 Author URI: http://gaut.am/
 Version: 0.2
*/

/**
 * @package Easy Mentions
 * @subpackage Main Section
 * @author Gautam Gupta (www.gaut.am)
 * @link http://gaut.am/bbpress/plugins/easy-mentions/
 * @license GNU General Public License version 3 (GPLv3): http://www.opensource.org/licenses/gpl-3.0.html
 */

bb_load_plugin_textdomain( 'easy-mentions', dirname( __FILE__ ) . '/languages' ); /* Create Text Domain For Translations */

/* Defines */
define( 'EM_VER', '0.2' ); /* Version */
define( 'EM_OPTIONS','Easy-Mentions' ); /* Option Name */
define( 'EM_PLUGPATH', bb_get_plugin_uri( bb_plugin_basename( __FILE__ ) ) ); /* Plugin URL */

/* Get the options, if not found then set them */
$em_plugopts = bb_get_option( EM_OPTIONS );
if ( !is_array( $em_plugopts ) ) {
	$em_plugopts = array(
		'link-tags'	=> 1,
		'link-users'	=> 1,
		'link-user-to'	=> 'profile',
		'reply-link'	=> 0,
		'reply-text'	=> "<em>Replying to @%%USERNAME%%\'s <a href=\"%%POSTLINK%%\">post</a>:</em>",
	);
	bb_update_option( EM_OPTIONS, $em_plugopts );
}

if ( $em_plugopts['link-to'] ) { /* Update the old options, will be removed in v0.4 */
	unset( $em_plugopts['link-to'] );
	$em_plugopts['link-users']	= 1;
	$em_plugopts['link-tags']	= 1;
	$em_plugopts['link-user-to']	= ( $em_plugopts['link-to'] == 'website' ) ? 'website' : 'profile';
	bb_update_option( EM_OPTIONS, $em_plugopts );
}

if ( bb_is_admin() ) /* Load admin.php file if it is the admin area */
	require_once( 'includes/admin.php' );
else /* Else load public.php file if it is the public area */
	require_once( 'includes/public.php' );
