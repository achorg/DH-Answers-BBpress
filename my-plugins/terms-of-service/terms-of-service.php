<?php
/*
Plugin Name: Terms of Service
Plugin URI:  http://bbpress.org/plugins/topic/117
Description:  Adds a "Terms of Service" (aka TOS) agreement to your registration page (and can optionally be linked to directly).
Version: 0.0.3
Author: _ck_
Author URI: http://bbshowcase.org

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

Donate: http://bbshowcase.org/donate/
*/ 

if (bb_get_location()=="register-page") {	// determines if we're actually on register.php and only hooks in that case
	add_action( 'extra_profile_info', 'terms_of_service',20);		// attach to register.php via extended profile info
	add_action('bb_send_headers', 'terms_of_service_check');	// check before headers finish sending
} 

if (isset($_GET['terms_of_service']) || isset($_GET['terms-of-service'])) {	// check for direct link
	add_action( 'bb_init', 'terms_of_service_get');	
}

function terms_of_service() {	// show the form
	echo '<fieldset><legend>'.__("Terms of Service").'</legend>'
	.'<div id="terms_of_service" style="padding:0.5em 1em 1em 1em; margin:0em 3em; background: #eee; color: #000; overflow:auto; height:7em;">';	
	@readfile(rtrim(dirname(__FILE__),' /\\').'/terms-of-service.html');	 
	echo '</div><table width="100%"><tr class="required"><th scope="row" nowrap><sup class="required" style="color:red;">*</sup> '.__("I understand and agree:").'</th><td width="72%">'
	.'<input name="terms_of_service" type="checkbox" id="terms_of_service" value="agree" />'
	.'</td></tr></table></fieldset>';	
} 

function terms_of_service_check() {	// examine the answer
	if ($_POST && (!isset($_POST['terms_of_service']) || $_POST['terms_of_service']!="agree")) {		
		bb_get_header();	?>
		<h2 id="register"><?php _e('Error'); ?></h2>
		<p align='center'><?php _e("You must agree to the Terms of Service"); ?>
		<br /><a href='register.php'><?php _e("Please go back and try again"); ?>.</a></p>
		<?php bb_get_footer();
		exit;
	}
}

function terms_of_service_get() {	
	bb_send_headers();
	bb_get_header();	?>
	<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; <?php _e('Terms of Service'); ?></h3>
	<div class="indent">
	<h2 id="register"><?php _e('Terms of Service'); ?></h2>		
	<div id="terms_of_service" style="padding:0.5em 1em 1em 1em; margin:0em 3em; background: #eee; color: #000;">	
	<?php @readfile(rtrim(dirname(__FILE__),' /\\').'/terms-of-service.html');
	echo '</div></div>';
	bb_get_footer(); 
	exit;	
}
?>