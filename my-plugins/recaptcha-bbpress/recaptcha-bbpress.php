<?php
/*
Plugin Name: reCAPTCHA bbPress
Plugin URI: http://www.gospelrhys.co.uk/plugins/bbpress-plugins/recaptcha-bbpress-plugin
Description:  Recapture bbPress from bots by adding reCAPTCHA to your forum.
Version: 0.2
Author: Rhys Wynne
Author URI: http://www.gospelrhys.co.uk

*/

require_once('recaptchalib.php');

add_action('bb_admin_menu_generator', 'recaptcha_bbpress_add_admin_page');
add_action('bb_admin-header.php', 'recaptcha_bbpress_admin_page_process');
add_action('extra_profile_info', 'recaptcha_bbpress_registration_add_field', 11);
add_action('bb_send_headers', 'recaptcha_bbpress_verify');


function recaptcha_bbpress_add_admin_page() {
	bb_admin_add_submenu(__('reCAPTCHA bbPress'), 'use_keys', 'recaptcha_bbpress_admin_page');

}

function recaptcha_bbpress_admin_page() {
?>
<h2>reCAPTCHA bbPress </h2>
	<?php if (isset ($_POST['submit'])) {
?>
		<div style="background-color:#EDF2EC;border: 1px solid #BAC0C8;padding: 10px;font-weight: bold;">Options Saved</div>

<?php	
	} ?>
<p>To use this plugin, you need two keys (a private and a public key) from the reCAPTCHA website - you can sign up for these for free <a href="http://recaptcha.net/whyrecaptcha.html">here</a>.</p>
<form method="post">
	<table width="50%"  border="0">
      <tr>
	  <td>
	  Public Key
	  </td>
	  	  <td><input type="text" name="rcpbbp_public" value="<?php echo bb_get_option('recaptcha_bbpress_public_key'); ?>">
	  	  </td>
	  </tr>
	        <tr>
	  <td>
	  Private Key
	  </td>
	  	  <td><input type="text" name="rcpbbp_private" value="<?php echo bb_get_option('recaptcha_bbpress_private_key'); ?>">
	  	  </td>
	  </tr>
	  <tr><td colspan="2">
	<p class="submit alignleft">
		<input name="submit" type="submit" value="<?php _e('Update'); ?>" tabindex="90" />
		<input type="hidden" name="action" value="rcpbbp_update" />
	</p></td>
	</tr>
	  </table></form>
	  
	  	<div style="clear:both;">&nbsp;</div>
	<h4>Like this? Please Donate!</h4>
	<p>Donations help keep me chugging away at plugins. Donations as much and as little as you want.</p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="6462319">
<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>

<?php } 

function recaptcha_bbpress_admin_page_process() {
	if (isset ($_POST['submit'])) {
		if ('rcpbbp_update' == $_POST['action']) {
				bb_update_option('recaptcha_bbpress_public_key', $_POST['rcpbbp_public']);
				bb_update_option('recaptcha_bbpress_private_key', $_POST['rcpbbp_private']);
				
		}
	}
}

function recaptcha_bbpress_register_page() 
{	
    foreach (array($_SERVER['PHP_SELF'], $_SERVER['SCRIPT_FILENAME'], $_SERVER['SCRIPT_NAME']) as $page) {
        if (strpos($page, '.php') !== false) 
            $file = $page;
    }
    return (bb_find_filename($file) == "register.php");
}

function recaptcha_bbpress_registration_add_field()
{
	if (recaptcha_bbpress_register_page()) {
    	$pubkey = bb_get_option('recaptcha_bbpress_public_key');
		echo "<script type='text/javascript'>var RecaptchaOptions = { theme : 'white', lang : 'en' , tabindex : 5 };</script>";
		echo "Please prove you are human";
		echo recaptcha_get_html($pubkey);
	} 
	
} 

function recaptcha_bbpress_verify()
{
	if (recaptcha_bbpress_register_page())
    {
		if(bb_get_option('recaptcha_bbpress_public_key') && bb_get_option('recaptcha_bbpress_private_key'))
		{
			if ($_POST["recaptcha_response_field"] || $_POST) {
				$privatekey = bb_get_option('recaptcha_bbpress_private_key');
				$resp = null;
				$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

 				if (!$resp->is_valid) {
   					bb_get_header();
   					echo "<h2>Error</h2><p>You failed to complete the form correctly, please return to the previous page and try again.</p>";
  		 			bb_get_footer();
					exit;
				}
			}
		}
		else
		{
			echo "The public & private keys aren't set at the moment! ";
		} 
	}

}
?>