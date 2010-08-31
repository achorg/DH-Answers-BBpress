<?php
/*
Plugin Name: Mass Edit - Moderate Posts
Plugin URI: http://bbpress.org/plugins/topic/89
Description:  Adds a "mass edit" feature to bbPress admin panel, similar to WordPress, for easily moderating posts in bulk.
Author: _ck_
Author URI: http://bbShowcase.org
Version: 1.1.4

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

Donate: http://bbshowcase.org/donate/

Instructions:   install, activate, look under Content in admin menu for Mass Edit
*/

if ((defined('BB_IS_ADMIN') && BB_IS_ADMIN) || !(strpos($_SERVER['REQUEST_URI'],"/bb-admin/")===false)) { // "stub" only load functions if in admin 	
	function mass_edit_admin_page() {bb_admin_add_submenu( __('Mass Edit'), 'administrate', 'mass_edit','content.php');}
	add_action( 'bb_admin_menu_generator', 'mass_edit_admin_page',200);	// try to be last menu feature
	if (isset($_GET['plugin']) && $_GET['plugin']=="mass_edit") {require_once("mass-edit-admin.php");} // load entire core only when needed
}
?>