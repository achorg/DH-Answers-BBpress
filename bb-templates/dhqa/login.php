<?php bb_get_header(); ?>

<div class="bbcrumb"><a href="<?php bb_uri(); ?>"><?php bb_option('name'); ?></a> &raquo; <?php _e('Log in'); ?></div>

<form class="login-form" method="post" action="<?php bb_uri('bb-login.php', null, BB_URI_CONTEXT_FORM_ACTION + BB_URI_CONTEXT_BB_USER_FORMS); ?>">
<h2 id="userlogin" role="main"><?php isset($_POST['user_login']) ? _e('Log in Failed') : _e('Log in') ; ?></h2>
<fieldset>
	<legend>With OpenID <span id="openid-note">(GMail, Yahoo!, or other <a href='http://openid.net/get-an-openid/'>OpenID account</a>)</span></legend>
	<table>
		<tbody>
		<tr class="form-field">
			<th scope="row">
				<label for="openid_url">OpenID</label>
			</th>
			<td>
				<input id="openid_identity" name="openid_identity" type="text"/>
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>
<fieldset>
<legend>With DHAnswers ID</legend>
<table>
<?php
	$user_login_error = $bb_login_error->get_error_message( 'user_login' );
	$user_email_error = $bb_login_error->get_error_message( 'user_email' );
	$user_password_error = $bb_login_error->get_error_message( 'password' );
?>
	<tr valign="top" class="form-field <?php if ( $user_login_error || $user_email_error ) echo ' form-invalid error'; ?>">
		<th scope="row">
			<label for="user_login"><?php _e('User name'); ?></label>
			<?php if ( $user_login_error ) echo "<em>$user_login_error</em>"; ?>
			<?php if ( $user_email_error ) echo "<em>$user_email_error</em>"; ?>
		</th>
		<td>
			<input name="user_login" id="user_login" type="text" value="<?php echo $user_login; ?>" />
		</td>
	</tr>
	<tr valign="top" class="form-field <?php if ( $user_password_error ) echo 'form-invalid error'; ?>">
		<th scope="row">
			<label for="password"><?php _e('Password'); ?></label>
			<?php if ( $user_password_error ) echo "<em>$user_password_error</em>"; ?>
		</th>
		<td>
			<input name="password" id="password" type="password" />
		</td>
	</tr>

	<tr valign="top" class="form-field">
		<th scope="row"><label for="remember"><?php _e('Remember me'); ?></label></th>
		<td><input name="remember" type="checkbox" id="remember" value="1"<?php echo $remember_checked; ?> /></td>
	</tr>
	<tr>
		<th scope="row">&nbsp;</th>
		<td>
			<input name="re" type="hidden" value="<?php echo $redirect_to; ?>" />
			<input type="submit" value="<?php echo esc_attr( isset($_POST['user_login']) ? __('Try Again &raquo;'): __('Log in &raquo;') ); ?>" />
			<?php wp_referer_field(); ?>
		</td>
	</tr>
</table>

</fieldset>
</form>
<div class="register-container">
<h2 id="userreg" role="main"><?php _e('Register'); ?></h2>
	<?php
	printf(
		__( '<a href="%1$s" id="register-link">Create a New Account &raquo;</a>' ),
		bb_get_uri( 'register.php', null, BB_URI_CONTEXT_A_HREF + BB_URI_CONTEXT_BB_USER_FORMS )
	);
	?>
</div>
<h2 id="passwordrecovery"><?php _e( 'Password Recovery' ); ?></h2>
<form method="post" action="<?php bb_uri('bb-reset-password.php', null, BB_URI_CONTEXT_FORM_ACTION + BB_URI_CONTEXT_BB_USER_FORMS); ?>">
<fieldset>
	<p><?php _e('To recover your password, enter your information below.'); ?></p>
	<table>
		<tr valign="top" class="form-field">
			<th scope="row">
				<label for="user_login_reset_password"><?php _e( 'Username' ); ?></label>
			</th>
			<td>
				<input name="user_login" id="user_login_reset_password" type="text" value="<?php echo $user_login; ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"></th>
			<td>
				<input type="submit" value="<?php echo esc_attr__( 'Recover Password &raquo;' ); ?>" />
			</td>
		</tr>
	</table>
</fieldset>
</form>

<?php bb_get_footer(); ?>
