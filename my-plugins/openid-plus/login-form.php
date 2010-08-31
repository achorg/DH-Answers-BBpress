<form class="login" method="post" action="<?php echo bb_get_option('uri').'bb-login.php'; ?>">
	<p><?php
		printf(
			__('<a href="%1$s">Register</a> or log in (<a href="%2$s">lost password?</a>):'),
			bb_get_option('uri').'register.php',
			bb_get_option('uri').'bb-login.php'); 
	?></p>
	<div>	
		<span id='regular_login'>
		<label><?php _e('Username:'); ?><br />
			<input name="user_login" type="text" id="user_login" size="13" maxlength="40" value="<?php if (!is_bool($user_login)) echo $user_login; ?>" tabindex="1" />
		</label>
		<label><?php _e('Password:'); ?><br />
			<input name="password" type="password" id="password" size="13" maxlength="40" tabindex="2" />
		</label>
		</span>
		<span id='openid_login' style="display:none;">
		<label><?php _e('OpenID URL:'); ?> &nbsp; (<a  href="#" onclick="window.open('?openid_help', 'openid_help', 'scrollbars=yes,menubar=no,width=525,height=400,resizable=yes,toolbar=no,location=no,status=no'); return false;">help</a>)<br />
			<input style="padding-left:20px;background: #fff url(<?php global $openid_options; echo $openid_options['icon']; ?>) no-repeat 2px 50%;" name="openid_url" type="text" id="openid_url" size="30" maxlength="80" value="" tabindex="1" />
		</label>				
		</span>		
		<input name="re" type="hidden" value="<?php echo $re; ?>" />
		<?php wp_referer_field(); ?>
		<input type="submit" name="Submit" class="submit" value="<?php echo attribute_escape( __('Log in &raquo;') ); ?>" tabindex="4" />
	</div>
	<div class="remember">
		<label>		
			<input name="remember" type="checkbox" id="remember" value="1" tabindex="3"<?php echo $remember_checked; ?> />
			<?php _e('Remember me'); ?>
		</label>
	</div>
	<div>
		<label style="margin:7px 0 0 20px;">
			<input onclick=openid_toggle() name="openid_checkbox" type="checkbox" id="openid_checkbox" value="1" tabindex="5" />
			<span style="padding-left:16px;background: url(<?php global $openid_options; echo $openid_options['icon']; ?>) no-repeat;">
			<?php _e('OpenID'); ?>
			</span>			
		</label>				
	</div>
</form>
<script type='text/javascript' defer="defer">
    document.getElementById('openid_checkbox').checked=0; 
    function openid_toggle() {   
    var e=document.getElementById('openid_checkbox'); 
    var r=document.getElementById('regular_login'); 
    var o=document.getElementById('openid_login'); 
    if (e.checked) {o.style.display = '';r.style.display = 'none'; setTimeout("document.getElementById('openid_url').focus()",500);} 
    else {r.style.display = '';o.style.display = 'none';setTimeout("document.getElementById('user_login').focus()",500);}    
    return false;}
</script>