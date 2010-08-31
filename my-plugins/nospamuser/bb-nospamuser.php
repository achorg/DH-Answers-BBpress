<?php
/*
Plugin Name: bb-NoSpamUser
Version: 0.8
Plugin URI: http://nightgunner5.wordpress.com/tag/bb-nospamuser/
Description: Prevents known spam users from registering on your forum.
Author: Nightgunner5
Author URI: http://llamaslayers.net/
Requires at least: 1.0
Tested up to: trunk
Text Domain: nospamuser
Domain Path: translations/
*/

define( 'NOSPAMUSER_AGENT', ' | NoSpamUser/0.8' );

if ( !function_exists( 'add_action' ) ) {
	@include_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/bb-load.php' ) or exit;
	if ( strtoupper( $_SERVER['REQUEST_METHOD'] ) == 'POST' && isset( $_POST['nonce'] ) && bb_verify_nonce( $_POST['nonce'], 'nospamuser-nonce-' . $_SERVER['REMOTE_ADDR'] ) ) {
		$settings = bb_get_option( 'nospamuser-settings' );

		if ( $settings['recaptcha_mode'] == 'aggressive' )
			exit;

		if ( !function_exists( 'recaptcha_check_answer' ) ) // Compatibility with anything else that uses reCAPTCHA
			require_once dirname( __FILE__ ) . '/recaptchalib.php';

		$resp = recaptcha_check_answer( $settings['recaptcha_priv'], $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'] );

		if ( $resp->is_valid ) {
			setcookie( 'nospamuser-override', bb_create_nonce( 'nospamuser-override-' . $_SERVER['REMOTE_ADDR'] ), bb_nonce_tick() * apply_filters( 'bb_nonce_life', 86400 ) / 2 );
		}

		bb_safe_redirect( bb_get_uri( 'register.php', null, BB_URI_CONTEXT_BB_USER_FORMS + BB_URI_CONTEXT_HEADER ) );
	}
	exit;
}

function nospamuser_install() {
	bb_update_option( 'nospamuser-settings', wp_parse_args( bb_get_option( 'nospamuser-settings' ), array(
		'days' => 30,
		'min_occur' => 5,
		'max_occur' => 10,
		'api_key' => '',
		'recaptcha_mode' => 'aggressive',
		'recapthca_pub' => '',
		'recaptcha_priv' => '',
		'stats_public' => 0
	) ) );
}
bb_register_plugin_activation_hook( __FILE__, 'nospamuser_install' );

function nospamuser_admin_parse() {
	bb_check_admin_referer( 'nospamuser-admin' );

	$settings = bb_get_option( 'nospamuser-settings' );

	$success = array();
	$error = array();

	if ( $_POST['days'] != $settings['days'] ) {
		if ( (int)$_POST['days'] > 0 ) {
			$settings['days'] = $_POST['days'];
			$success[] = __( 'Maximum days', 'nospamuser' );
		} else {
			$error[] = __( 'Maximum days', 'nospamuser' );
		}
	}
	if ( $_POST['min_occur'] != $settings['min_occur'] ) {
		if ( (int)$_POST['min_occur'] > 0 ) {
			$settings['min_occur'] = $_POST['min_occur'];
			$success[] = __( 'Minimum frequency', 'nospamuser' );
		} else {
			$error[] = __( 'Minimum frequency', 'nospamuser' );
		}
	}
	if ( $_POST['max_occur'] != $settings['max_occur'] ) {
		if ( (int)$_POST['max_occur'] > 0 ) {
			$settings['max_occur'] = $_POST['max_occur'];
			$success[] = __( 'Maximum frequency', 'nospamuser' );
		} else {
			$error[] = __( 'Maximum frequency', 'nospamuser' );
		}
	}
	if ( $_POST['api_key'] != $settings['api_key'] ) { // There's not a way I know of to check this.
		$settings['api_key'] = $_POST['api_key'];
		$success[] = __( 'Stop Forum Spam API key', 'nospamuser' );
	}
	if ( $_POST['recaptcha_mode'] != $settings['recaptcha_mode'] ) {
		if ( in_array( $_POST['recaptcha_mode'], array( 'aggressive', 'adaptive', 'friendly' ) ) ) {
			$settings['recaptcha_mode'] = $_POST['recaptcha_mode'];
			$success[] = __( 'reCAPTCHA mode', 'nospamuser' );
		} else {
			$error[] = __( 'reCAPTCHA mode', 'nospamuser' );
		}
	}
	if ( $_POST['recaptcha_pub'] != $settings['recaptcha_pub'] ) { // There's not a way I know of to check this.
		$settings['recaptcha_pub'] = $_POST['recaptcha_pub'];
		$success[] = __( 'reCAPTCHA public key', 'nospamuser' );
	}
	if ( $_POST['recaptcha_priv'] != $settings['recaptcha_priv'] ) { // There's not a way I know of to check this.
		$settings['recaptcha_priv'] = $_POST['recaptcha_priv'];
		$success[] = __( 'reCAPTCHA private key', 'nospamuser' );
	}

	if ( (int)$_POST['stats-public'] != $settings['stats-public'] ) {
		$settings['stats-public'] = (int)$_POST['stats-public'];
		$success[] = __( 'Public statistics', 'nospamuser' );
	}

	if ( $success ) {
		bb_update_option( 'nospamuser-settings', $settings );
		bb_admin_notice( __( 'The following settings were updated successfully:', 'nospamuser' ) . '</p><ul><li>' . implode( '</li><li>', $success ) . '</li></ul>', 'updated' );
	}
	if ( $error ) {
		bb_admin_notice( __( 'The following settings had errors and were not updated:', 'nospamuser' ) . '</p><ul><li>' . implode( '</li><li>', $error ) . '</li></ul>', 'error' );
	}
}
if ( strtoupper( $_SERVER['REQUEST_METHOD'] ) == 'POST' )
	add_action( 'nospamuser_admin_pre_head', 'nospamuser_admin_parse' );

function nospamuser_admin() {
	$settings = bb_get_option( 'nospamuser-settings' );
	$options = array(
		'days' => array(
			'title' => __( 'Maximum days', 'nospamuser' ),
			'note' => __( 'Any possible spammer that was last active over this many days ago will be allowed through.', 'nospamuser' ),
			'class' => 'short',
			'value' => $settings['days']
		),
		'min_occur' => array(
			'title' => __( 'Minimum frequency', 'nospamuser' ),
			'note' => __( 'Any possible spammer that do not have at least this many reports will be allowed through.', 'nospamuser' ),
			'class' => 'short',
			'value' => $settings['min_occur']
		),
		'max_occur' => array(
			'title' => __( 'Maximum frequency', 'nospamuser' ),
			'note' => __( 'Possible spammers that have at least this many reports will be disallowed in adaptive mode. This also affects agressive mode, where spammers with at least this many reports will be blocked even if the maximum days prerequisite is not met.', 'nospamuser' ),
			'class' => 'short',
			'value' => $settings['max_occur']
		),
		'api_key' => array(
			'title' => __( 'Stop Forum Spam API key', 'nospamuser' ),
			'note' => __( 'Required to submit spammers to Stop Forum Spam. <a href="http://www.stopforumspam.com/signup">Get a Stop Forum Spam API key here</a>.', 'nospamuser' ),
			'class' => array( 'code', 'long' ),
			'value' => $settings['api_key']
		),
		'recaptcha_mode' => array(
			'title' => __( 'reCAPTCHA mode', 'nospamuser' ),
			'note' => __( 'All modes except aggressive require reCAPTCHA public and private keys.', 'nospamuser' ),
			'type' => 'radio',
			'options' => array(
				'aggressive' => __( '<strong>Aggressive:</strong> Never allow possible spammers to override blocks.', 'nospamuser' ),
				'adaptive' => __( '<strong>Adaptive:</strong> Allow possible spammers between the minimum and maximum frequency to override blocks.', 'nospamuser' ),
				'friendly' => __( '<strong>Friendly:</strong> Allow all possible spammers to override blocks.', 'nospamuser' )
			),
			'value' => $settings['recaptcha_mode']
		),
		'recaptcha_pub' => array(
			'title' => __( 'reCAPTCHA public key', 'nospamuser' ),
			'note' => sprintf( __( '<a href="%s">Get it here</a>.', 'nospamuser' ), 'http://recaptcha.net/api/getkey?domain=' . urlencode( $_SERVER['SERVER_NAME'] ) . '&app=bb-NoSpamUser' ),
			'class' => array( 'code', 'long' ),
			'value' => $settings['recaptcha_pub']
		),
		'recaptcha_priv' => array(
			'title' => __( 'reCAPTCHA private key', 'nospamuser' ),
			'note' => sprintf( __( '<a href="%s">Get it here</a>.', 'nospamuser' ), 'http://recaptcha.net/api/getkey?domain=' . urlencode( $_SERVER['SERVER_NAME'] ) . '&app=bb-NoSpamUser' ),
			'class' => array( 'code', 'long' ),
			'value' => $settings['recaptcha_priv']
		),
		'stats-public' => array(
			'title' => __( 'Public statistics', 'nospamuser' ),
			'type' => 'radio',
			'options' => array(
				0 => __( 'Keep all statistics private', 'nospamuser' ),
				1 => sprintf( __( 'Display the number of caught spammers on the <a href="%s">statistics page</a>.', 'nospamuser' ), bb_get_uri( 'statistics.php' ) )
			),
			'value' => $settings['stats-public'] ? $settings['stats-public'] : 0
		)
	);
?><h2><?php _e( 'bb-NoSpamUser', 'nospamuser' ); ?></h2>
<?php do_action( 'bb_admin_notices' ); ?>
<form class="settings" method="post" action="<?php bb_uri( 'bb-admin/admin-base.php', array( 'plugin' => 'nospamuser_admin' ), BB_URI_CONTEXT_FORM_ACTION + BB_URI_CONTEXT_BB_ADMIN ); ?>">
	<fieldset>
<?php
foreach ( $options as $option => $args ) {
	bb_option_form_element( $option, $args );
}
?>
	</fieldset>
	<fieldset class="submit">
		<?php bb_nonce_field( 'nospamuser-admin' ); ?>
		<input class="submit" type="submit" name="submit" value="<?php _e('Save Changes'); ?>" />
	</fieldset>
</form>
<?php if ( $blocks = (int)bb_get_option( 'nospamuser-blocks' ) ) { ?>
<div style="font-size: .75em; position: absolute; bottom: 50px; right: 5px"><?php printf( _n( '%s spammer blocked by bb-NoSpamUser', '%s spammers blocked by bb-NoSpamUser', $blocks, 'nospamuser' ), bb_number_format_i18n( $blocks ) ); ?></div>
<?php }
}
function nospamuser_admin_add() {
	bb_admin_add_submenu( __( 'bb-NoSpamUser', 'nospamuser' ), 'use_keys', 'nospamuser_admin', 'options-general.php' );
}
add_action( 'bb_admin_menu_generator', 'nospamuser_admin_add' );

function nospamuser_check( $type, $data ) {
	$settings = bb_get_option( 'nospamuser-settings' );
	if ( !$settings )
		bb_update_option( 'nospamuser-settings', $settings = array(
			'days' => 30,
			'min_occur' => 5,
			'max_occur' => 10,
			'api_key' => '',
			'recaptcha_mode' => 'aggressive',
			'recapthca_pub' => '',
			'recaptcha_priv' => '',
			'stats_public' => 0
		) );

	if ( !is_array( $result = bb_get_transient( 'nospamuser-' . $type . '-' . md5( $data ) ) ) ) {
		$wp_http = new WP_Http;
		$response = $wp_http->get( 'http://www.stopforumspam.com/api?' . urlencode( $type ) . '=' . urlencode( $data ), array(
			'user-agent' => apply_filters( 'http_headers_useragent', backpress_get_option( 'wp_http_version' ) ) . NOSPAMUSER_AGENT
		) );
		$response = $response['body'];


		if ( strpos( $response, '<response success="true">' ) === false )
			return;

		if ( strpos( $response, '<appears>no</appears>' ) !== false )
			$result = array( 0, 0 );
		else {
			preg_match( '/<lastseen>([^<>]+)<\/lastseen>/', $response, $matches );
			$result = array( (int)substr( $response, strpos( $response, '<frequency>' ) + 11 ), strtotime( $matches[1] ) );
		}

		bb_set_transient( 'nospamuser-' . $type . '-' . md5( $data ), $result, 604800 );
	}

	if ( $result == array( 0, 0 ) ) // Even if the settings are set incorrectly, non-spammers shouldn't be blocked.
		return;

	if ( ( $result[0] >= $settings['min_occur'] && $result[1] >= time() - $settings['days'] * 86400 ) ||
		 ( $result[0] >= $settings['max_occur'] && $settings['recaptcha_mode'] == 'aggressive' ) ) {
		if ( $result[0] >= $settings['max_occur'] && $settings['recaptcha_mode'] == 'adaptive' )
			nospamuser_block( $type, $data, true );
		elseif ( $settings['recaptcha_mode'] == 'aggressive' || !$settings['recaptcha_pub'] || !$settings['recaptcha_priv'] )
			nospamuser_block( $type, $data, true );
		else
			nospamuser_block( $type, $data, false );
	}
}

function nospamuser_block( $type, $data, $noway ) {
	$settings = bb_get_option( 'nospamuser-settings' );

	bb_update_option( 'nospamuser-blocks', bb_get_option( 'nospamuser-blocks' ) + 1 );

	$types = array(
		'email' => __( 'email address', 'nospamuser' ),
		'ip' => __( 'IP address', 'nospamuser' ),
		'username' => __( 'username', 'nospamuser' )
	);

	if ( $noway )
		bb_die( sprintf( __( 'Your %1$s (%2$s) is listed in <a href="%3$s">Stop Forum Spam</a>\'s database. You have been automatically blocked. If you are not a spammer, you may <a href="http://www.stopforumspam.com/removal">appeal this listing</a>.', 'nospamuser' ), $types[$type], $data, 'http://www.stopforumspam.com/' . ( $type == 'ip' ? 'ipcheck/' : 'search?q=' ) . $data ), 'Registration forbidden', 403 );

	if ( !isset( $_COOKIE['nospamuser_override'] ) || !bb_verify_nonce( $_COOKIE['nospamuser_override'], 'nospamuser-override-' . $_SERVER['REMOTE_ADDR'] ) ) {
		if ( !function_exists( 'recaptcha_check_answer' ) ) // Compatibility with anything else that uses reCAPTCHA
			require_once dirname( __FILE__ ) . '/recaptchalib.php';

		bb_die( sprintf( __( 'Your %1$s (%2$s) is listed in <a href="%3$s">Stop Forum Spam</a>\'s database. You have been automatically blocked. If you are not a spammer, you may <a href="http://www.stopforumspam.com/removal">appeal this listing</a> or solve the CAPTCHA below.', 'nospamuser' ), $types[$type], $data, 'http://www.stopforumspam.com/' . ( $type == 'ip' ? 'ipcheck/' : 'search?q=' ) . $data ) . '<form method="post" action="' . bb_get_plugin_uri( bb_plugin_basename( __FILE__ ) ) . '/bb-nospamuser.php"><script type="text/javascript">var RecaptchaOptions={theme:\'clean\'}</script>' . recaptcha_get_html( $settings['recaptcha_pub'] ) . '<br/><input type="submit" value="' . esc_attr__( 'Submit', 'nospamuser' ) . '"/></form>', 'Registration forbidden', 401 );
	}
}

function nospamuser_check_email( $r, $email, $type ) {
	if ( $type ) // Only check the last time.
		return $r;

	if ( $r )
		nospamuser_check( 'email', $email );

	return $r;
}

function nospamuser_check_username( $username ) {
	if ( $username )
		nospamuser_check( 'username', $username );

	return $username;
}

function nospamuser_check_ip() {
	nospamuser_check( 'ip', $_SERVER['REMOTE_ADDR'] );
}

if ( bb_get_location() == 'register-page' ) {
	nospamuser_check_ip();
	add_filter( 'sanitize_user', 'nospamuser_check_username' );
	add_filter( 'is_email', 'nospamuser_check_email', 10, 3 );
}

function nospamuser_check_bozo( $user_id ) { // Most of this function is taken from Akismet
	$settings = bb_get_option( 'nospamuser-settings' );

	if ( empty( $settings['api_key'] ) )
		return;

	global $bb_current_user, $user_obj;
	$bb_current_id = bb_get_current_user_info( 'id' );
	bb_set_current_user( $user_id );
	if ( $bb_current_id && $bb_current_id != $user_id )
		if ( $user_obj->data->is_bozo || !$bb_current_user->data->is_bozo )
			return;
	bb_set_current_user( (int) $bb_current_id );

	$wp_http = new WP_Http;
	$wp_http->post( 'http://www.stopforumspam.com/post.php', array(
		'body' => array(
			'username' => $user_obj->user_login,
			'ip_addr' => $user_obj->data->nospamuser_ip,
			'email' => $user_obj->user_email,
			'api_key' => $settings['api_key']
		),
		'user-agent' => apply_filters( 'http_headers_useragent', backpress_get_option( 'wp_http_version' ) ) . NOSPAMUSER_AGENT
	) );
}
add_action( 'profile_edited', 'nospamuser_check_bozo' );

function nospamuser_set_user_ip_field( $user_id ) {
	bb_update_usermeta( $user_id, 'nospamuser_ip', $_SERVER['REMOTE_ADDR'] );
}
add_action( 'register_user', 'nospamuser_set_user_ip_field' );

function nospamuser_maybe_set_user_ip_field() {
	if ( bb_is_user_logged_in() && !bb_get_usermeta( bb_get_current_user_info( 'ID' ), 'nospamuser_ip' ) )
		nospamuser_set_user_ip_field( bb_get_current_user_info( 'ID' ) );
}
add_action( 'bb_init', 'nospamuser_maybe_set_user_ip_field' );

function nospamuser_stats_display() {
	if ( bb_is_statistics() && ( $settings = bb_get_option( 'nospamuser-settings' ) ) &&
		( $settings['stats-public'] & 1 ) && ( $blocks = (int)bb_get_option( 'nospamuser-blocks' ) ) )
		echo '<dt>' . sprintf( __( 'Spammers blocked by <a href="%s" rel="nofollow">bb-NoSpamUser</a>', 'nospamuser' ),
			'http://bbpress.org/plugins/topic/nospamuser/' ) . '</dt><dd><strong>' . bb_number_format_i18n( $blocks ) .
			'</strong></dd>';
}
add_action( 'bb_stats_left', 'nospamuser_stats_display' );
