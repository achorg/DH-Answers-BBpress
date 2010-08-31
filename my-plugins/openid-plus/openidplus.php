<?php
/*
Plugin Name: OpenID Plus for bbPress
Plugin URI: http://www.binaryturf.com/free-software/openid-bbpress/
Description:  Seamless OpenID authentication and registeration with bbPress. Please activate and see  <strong><a href="admin-base.php?plugin=oip_configuration">settings</a></strong> to get started.
Author: Shivanand Sharma with inputs from _ck_ and Steve Love
Author URI: http://www.binaryturf.com
Version: 1.0 beta

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/
Donate: http://www.binaryturf.com/donate-and-contribute/
*/

$openid_options['profile_text']="OpenID";
$openid_options['add_text']="Add OpenID providers to your account:";
$openid_options['remove_text']="Remove OpenID provider";
$openid_options['register_text']="Optionally register via OpenID instead of a password:";
$openid_options['approved_text']="OpenID account approved for instant registration:";

$openid_options['debug']=true;	// true = some debug info on process
$openid_options['root']=bb_get_option('uri');
$openid_options['whitelist']="";  // todo
$openid_options['blacklist']="";  // todo
$openid_options['icon']=bb_get_option('uri').trim(str_replace(array(trim(BBPATH,"/\\"),"\\"),array("","/"),dirname(__FILE__)),' /\\').'/openid.png'; 

/*  stop editing here  */

//add_action('bb_init', 'openid');
add_action('bb_init', 'oip');
add_action('bb_init', 'oip_return');
//add_action('bb_new_user','oip_reg_notice');
add_action('oip_login','oip_login_form');
//add_action('extra_profile_info', 'openid_profile_edit',8);
add_action('extra_profile_info', 'oid_register',8);			// <- Called on the register page and profile edit too?
//add_action('openid_login','openid_login');
//add_action('register_user', 'openid_register_success',25); 
add_action('bb_user_logout', 'oip_logout');

if(bb_is_admin())
	{
	add_action('bb_admin_head','oip_admin_css','1');
	}

function oip_admin_css()
	{
	?>
	<link rel="stylesheet" href="<?php echo bb_get_option('uri').trim(str_replace(array(trim(BBPATH,"/\\"),"\\"),array("","/"),dirname(__FILE__)),' /\\').'/oip.css' ?>" type="text/css" />
	<?php
	}

function bb_oip_configuration_page_add()
	{
	bb_admin_add_submenu( "OpenID Plus", "administrate", "oip_configuration", 'options-general.php' );
	}
add_action( 'bb_admin_menu_generator', 'bb_oip_configuration_page_add' );


function oip_configuration()
	{
	echo "<h2>OpenID Plus</h2>";
	?>
	<div id="oip_options">
	<p style="padding:.5em;background:#ffc;border:1px solid #ccc;">This plugin is a beta so caution should be exercised while using on a public site. Configuration options are coming very soon.</p>
	
	<h3>Introduction &mdash </h3>
	<p>The philosophy is simple &mdash a seamless unified authentication using OpenID. We'll place a donations button too.</p>
	<p>As of now, if you place the OpenID URL input box on the login form, it will:</p>
	<ol>
	<li>Validate the OpenID provider's URL.</li>
	<li>Redirect you to the OpenID for authentication.</li>
	<li>Will request at least your email address from your OpenID provider.</li>
	<li>Will log you in if you are previously registered with that email address.</li>
	<li>Will register you silently with a nick or first name if available from your OpenID provider/profile.</li>
	<li>Else will register using your email address as username.</li>
	<li>Will log you in on successful registration.</li>
	<li>Throw error if something goes wrong.</li>
	</ol>

	<p>Note: Your OpenID provider must return at least your email ID for the registration or login to be successful.</p>
	
	<h3>Testing &mdash </h3>
	
	<p>Now that you've come so far the first thing to do is to test it out.</p>
	<ol>
	<li>Logout</li>
	<li>Go to the <a href="<?php echo bb_get_option('uri').'register.php'; ?>">Register page</a>.</li>
	<li>Enter the URL of an OpenID provider where you have an account.</li>
	<li>Submit the form.</li>
	<li>A successful authentication shall redirect you to the forum, register you (if email address returned is different from the Administrator's email)and log you in.
	<li>If your OpenID providers returns the same email as your existing email in on this forum's profile you'll be logged in.</li>
	</ol>

	<h3>Customizing &mdash</h3>

	<p>In order to further customize the login you may want to place the OpenID URL input box into the login template or the login form. This is what the code looks like. It is plain HTML.</p>
	<pre><code>
	&lt;fieldset&gt;
	&lt;legend&gt;OpenID&lt;/legend&gt;
	&lt;p&gt;Optionally register/login via OpenID instead of a password:&lt;/p&gt;
	&lt;label for=&quot;openid_url&quot;&gt;OpenID URL&lt;/label&gt;
	&lt;input id=&quot;openid_identity&quot; name=&quot;openid_identity&quot; /&gt;
	[&lt;a href=&quot;#openid&quot; onclick=&quot;openid_help()&quot;&gt;help&lt;/a&gt;]
	&lt;/fieldset&gt;
	</code></pre>

	<ul>
	<li>Place the above code into the login.php template in your bbPress theme. This will integrate the OpenID box with the Login page.</li>
	<li>Optionally place the above code into the login-form.php template in your bbPress theme. This will integrate the OpenID box with the Login widget which appears in the header of the default theme.</li>
	</ul>

	<p>Help: <a href="http://www.binaryturf.com/forum">Binary Turf Forums</a> | <a href="http://www.binaryturf.com/free-software/openid-bbpress/">Download latest version</a> | <a href="mailto:varun21@gmail.com">Email a bug <small><em><strong style="letter-spacing:1px;">(use this as a last option and include instructions on how the bug can be duplicated &mdash; in addition to other relevant info)</strong></em></small></a></p>
	
	</div>
	<?php
	}

function oip_login_form() 
	{
	global $openid_options;
	?>
	<h2 id="register"><?php _e('OpenID Login'); ?></h2>
	<form method="post" action="<?php bb_option('uri'); ?>bb-login.php">
	<fieldset>
	<?php 
	if (isset($_GET['openid_error'])) 
		{
		echo "<div onclick='this.style.display=\"none\"' style='margin:0 0 1em 0; color:#000;width:75%;overflow:hidden;padding:3px 10px;background:#FFF6BF;border:1px solid #FFD324;'>".substr(addslashes(strip_tags($_GET['openid_error'],"<br>")),0,200)."</div>";
		}
		?>
	<table>
		<tr valign="top">
			<th scope="row"><?php _e('OpenID URL:'); ?></th>
			<td><input style="width:50%;padding-left:20px;background: #fff url(<?php echo $openid_options['icon']; ?>) no-repeat 2px 50%;" name="openid_identity" id="openid_identity"> [<a onclick=openid_help() href="#openid">help</a>]
			<?php openid_help(); ?></td>
		</tr>
	</table>
	</fieldset>
	</form>
	<?php
	}


function oip_session()
	{
	if (!isset($_SESSION)) 
		{
		ini_set('session.use_trans_sid', false);
		ini_set("url_rewriter.tags","");
		session_start();	// sent with headers
		}
	}
//openid_session();

//session_start();
require_once('class.dopeopenid.php');

function oip()
	{
	//	echo 	"<!-- this is where the openid action happens -->";
	if(!isset($_POST['openid_identity'])) 
			return;
	//error_reporting(E_ALL);
	global $bb_current_user, $bbdb; 

	//could use "login-page"
	
	if ((bb_get_location()=="register-page" || bb_get_location()=="login-page" )&& isset($_POST['openid_identity'])) 
		{
		$openid_identity = trim($_POST['openid_identity']);		
		if (!preg_match("/^https?:\/\//i",$openid_identity))  	//fix the missing "http://" if absent
			{
			$openid_identity = 'http://'.$openid_identity;
			}
		/*if(function_exists('filter_input')) 
			{
			if( ! filter_input(INPUT_POST, 'openid_identity', FILTER_VALIDATE_URL)) 
				{
				$error = "Error: OpenID Identifier is not in proper format.";
				}
			}
		else 
			{
			if( ! eregi("^((https?)://)?(((www\.)?[^ ]+\.[com|org|net|edu|gov|us]))([^ ]+)?$",$openid_identity)) 
				{
				$error = "Error: OpenID Identifier is not in proper format.";
				}
			}
		*/
			if( ! eregi("^((https?)://)?(((www\.)?[^ ]+\.[com|org|net|edu|gov|us]))([^ ]+)?$",$openid_identity)) 
				{
				$error = "Error: OpenID Identifier is not in proper format.";
				}
		//echo $openid_identity;
		
		if(!isset($error))
			{
			
			oip_session();			
			$openid = new Dope_OpenID($openid_identity);
			$openid->setReturnURL(bb_get_option('uri')."register.php?action=verify");
			$openid->SetTrustRoot(bb_get_option('uri'));
			$openid->setRequiredInfo(array('email','nickname','fullname'));
			//print_r($openid);
			//echo "sending";
			$endpoint_url = $openid->getOpenIDEndpoint();
			if($endpoint_url)
				{
				// If we find the endpoint, you might want to store it for later use.
				//$_SESSION['oip_url'] = $endpoint_url;
				// Redirect the user to their OpenID Provider
				$openid->redirect();
				// Call exit so the script stops executing while we wait to redirect.
				exit;
				}
			else
				{
				$the_error = $openid->getError();
				$error = "Error Code: {$the_error['code']}<br />";
				$error .= "Error Description: {$the_error['description']}<br />";				
				}		 
			}
		else
			{
			//echo "OpenidPlus: ".$error;
			}
			// /!isset($error)
			//}	// /isset($_POST['openid_url'])
		}	// bb_get_location()=="register-page"
	}

function oip_login_success($uID = false,$oip_redir=false)
	{
	if( ! $uID)
		{
		return false;
		}
	else
		{
		if(is_object($uID))
			{
			$uID =  (int) $uID->ID;
			}
		//echo "loading userID" . $uID;		
		wp_set_auth_cookie($uID, 0 );	
		bb_update_usermeta($uID,'openid_debug',$_GET);
		if($_GET['openid_op_endpoint'])
			{
			bb_update_usermeta($uID,'oip_openid_url',$_GET['openid_op_endpoint']);
			}
		}
		
	//update_user_meta	//if user tries with a new openid provider and we get the same email address, we should add it to the user's profile
	//do_action('oip_register_success');

	if($oip_redir)
		{
		bb_safe_redirect($oip_redir);
		exit;
		}
	else
		{
		bb_safe_redirect(bb_get_option('uri'));
		exit;
		}
	exit;
	}

function oip_register_success($uID = false,$oip_redir=false)
	{
	oip_login_success($uID);
	}

function oip_logout()
	{
	//$_SESSION['OPENID']="";
	}

function oip_reg_notice()
	{
	echo "Hey you just registered.";
	}


function oip_return()	
	{
	//add_action('oip_just_registered_pre_head','oip_reg_notice');

	//echo 	"<!-- this is where the return action happens -->";

	if (isset($_GET['openid_mode']) && $_GET['openid_mode'] == "cancel") 
		{
		$error = "OpenID authorization canceled by user.";
		return;
		}

	if(isset($_GET['action']) && $_GET['action']=="verify" && $_GET['openid_mode'] != "cancel")
		{
		//echo "returned from the server";
		//exit();
		$openid_url = $_GET['openid_identity'];
		$openid = new Dope_OpenID($openid_url);
		$validate_result = $openid->validateWithServer();
		if ($validate_result === TRUE) 
			{		
			$oip_user = oip_get_user($openid_url);	//check if user exists	
			if ($oip_user > 0 ) 
				{
				//do the login
				//wp_set_auth_cookie( (int) $oip_user, 0 );	// 0 = don't remember, short login, todo: use form value				
				echo "User with that OpenID found successfully and is now logged in";				
				oip_login_success($oip_user);
				}
			else
				{	
				//detect the user by other means
				$userinfo = $openid->filterUserInfo($_GET);
				$oip_email = $userinfo['email'];
				$oip_nick = $userinfo['nickname'];
				$oip_name = $userinfo['fullname'];

				$oip_username = '';
				$oip_useremail = (!empty($oip_email))?$oip_email:"";

				//try to load/login the user if email is available else register

				if(!empty($oip_useremail))
					{
					global $wp_users_object;
					if($oip_user = $wp_users_object->get_user( $oip_useremail, array( 'by' => 'email' ) ))
						{	//log the user in						
						oip_login_success($oip_user);
						}
					else
						{
						//register the user 
						//try to force using one of the fields as username for the purpose of registration
						$oip_username = (!empty($oip_nick))?$oip_nick:((!empty($oip_name))?$oip_name:$oip_useremail);
						//(		((!empty($oip_name))?$oip_name:		(((!empty($oip_email))?$oip_email:"")		)						
						if(!empty($oip_username)  )
							{							
							//check if you can get user by nicename or login because those throw "... already exists" errors
							//and you want to work around that possibility

							$oip_login_exists = $wp_users_object->get_user( $oip_username, array( 'by' => 'login' ) );
							$oip_nicename_exists = $wp_users_object->get_user( $oip_username, array( 'by' => 'nicename' ) );

							//we're already finished doing the existing email check, so email is definitely unique

							if($oip_login_exists || $oip_nicename_exists)
								{	//we got an existing UserID
								$oip_username = $oip_useremail;	//just use the OpenID email as the username.
								}

							$oip_user = bb_new_user( $oip_username, $oip_useremail,"",0 );	//success returns a user id, user receives password in email.
							if(!is_wp_error($oip_user))
								{
								 								
								//$oid_user_openid_url = bb_update_usermeta( $oip_user, "openid_url", $openid_url );						
								do_action('register_user', $oip_user->ID);	//for other plugins which hook here? Will they break us?		
								
								//login directly without troubling the new user
								// 0 = don't remember, short login, todo: use form value		
								$oip_reg_success = "Welcome to ".bb_get_option('name').". You are now successfully logged in. Additionally, your registration details and password has been emailed to your email address provided in your OpenID profile.";
								oip_register_success($oip_user);
								}
							}
						else
							{
							$oip_error = "No profile info returned.";	//unlikely... only if openid server returned nothing at all.
							}
						}
					}
				else
					{
					$oip_error = "Email missing.";
					}

				
				fme($oip_error);
				fme($oip_user);
				echo "<p>Your OpenID Identity is (".$_GET['openid_identity']."). You are a new user to this site.</p>";
				echo "<p>The following information came back from your OpenID provider:</p>";
				print_r($userinfo);
				echo "<ul>";				
				foreach($userinfo as $ufield => $uvalue)
					{
					echo "<li><b>".$ufield."</b>: ".$uvalue."</li>";
					//print_r($uvalue);
					//echo  "</li>";	
					}
				//echo "\t<li><b>Nickname</b>: " . $userinfo['nickname'] . "</li>";
				//echo "\t<li><b>Language</b>: " . $userinfo['language'] . "</li>";
				//echo "\t<li><b>Email</b>: " . $userinfo['email'] . "</li>";
				echo "</ul><p>DEBUG: </p>";
				echo "<p>GET</p>";
				print_r($_GET);
				echo "<p>POST</p>";
				print_r($_POST);
				echo '<script type="text/javascript">alert("hi")</script>';
				exit;
				} // /registration
			}	// /valid result
		else 
			{
			if ($openid->isError() === TRUE)
				{		
				$the_error = $openid->getError();
				$error = "Error Code: {$the_error['code']}<br />";
				$error .= "Error Description: {$the_error['description']}<br />";
				}
			else
				{		
				$error = "Error: Could not validate the OpenID at {$_SESSION['openid_url']}";
				}
			//echo $error;
			}
		}

	}
	 

function oip_get_user($openid_url)
	{
	global $bbdb;
	$openid_user_id=$bbdb->get_var("SELECT user_id FROM $bbdb->usermeta WHERE meta_key='openid' AND meta_value LIKE '%:\"$openid_url\";%' LIMIT 1");
	if($openid_user_id)
		{
		return $openid_user_id;
		}
	else
		{
		return 0;
		}
	}

function oid_register()
	{
	global $bbdb,$user_id,$openid_options; 
	if (!bb_get_location()=="register-page") 
		{
		return;
		}
	if(bb_is_user_logged_in()) return;

	/*
	echo "<p>Session:<br />";
	print_r($_SESSION);
	echo "</p>";
	*/
	
	echo '<fieldset><legend>'.$openid_options['profile_text'].'</legend>';
	
	if (!empty($_SESSION['OPENID']) && 0)
		{
		
		openid_session();
		$url=$_SESSION['OPENID']; 
		$instructions=$openid_options['approved_text'];	
		echo '<p>'.$instructions.'</p><table><tr class="form-field"><th scope="row" style="padding-left:20px;background: url('.$openid_options['icon'].') no-repeat  50% 50%;">
		<label>[<a title="'.$openid_options['remove_text'].'" href="'.add_query_arg('remove_openid',urlencode($url)).'"><strong>x</strong></a>]</label></th><td> '.$url.' </td></tr>';
		}
	else 
		{
		
		$value="";	
		 
		$instructions=$openid_options['register_text'];

		if (isset($_GET['openid_error'])) //in case the form has been submitted? this could be a GET request if redirecting from openid provider?
			{
			echo "<div  style='color:#000;width:75%;overflow:hidden;padding:3px 10px;background:#FFF6BF;border:1px solid #FFD324;'>".substr(addslashes(strip_tags($_GET['openid_error'],"<br>")),0,200)."</div>";
			}

		echo '<p>'.$instructions.'</p><table><tr class="form-field"><th scope="row"><label for="openid_url">OpenID URL</label></th>';

		echo '<td><input value="'.$value.'"  name="openid_identity" id="openid_identity"  style="padding-left:20px;  background: #fff url('.$openid_options['icon'].') no-repeat center left;"/> [<a onclick="openid_help()" href="#openid">help</a>]'; 

		if ($session_id=session_id()) //what is this?
			{
			$session_name=session_name(); 
			echo '<input tabindex="0" type="hidden" name = "'.$session_name.'" value = "'.$session_id.'" />';
			}

		echo '</td></tr>';
		}
		// session else
		echo '</table></fieldset>';
	}


// error_reporting(E_ALL);

function fme($s)
	{
	if(is_string($s))
		{
		echo '<p style="padding:1em;border:1px solid #c00;background:#ffc;">'.$s."</p>";
		}
	else
		{
		echo '<p style="padding:1em;border:1px solid #c00;background:#ffc;">';
		print_r($s);
		echo "</p>";
		}
	}

function openid_help() 
	{
	echo "
	<script type='text/javascript'>
		function openid_help() {   
		var e=document.getElementById('openid_help'); 
		if (e.style.visibility == 'hidden') {e.style.visibility = 'visible';} else {e.style.visibility = 'hidden';}
		setTimeout(\"document.getElementById('openid_url').focus()\",1000);
		return false;
		} </script>
	<table id='openid_help' onclick='this.style.visibility=\"hidden\"' border='0' cellpadding='3' cellspacing='0' nowrap style='font-size:1.2em;width:50%;visibility:hidden;margin:3px 0 0 -70px;white-space:nowrap;position:absolute;color:#000;overflow:hidden;padding10px;background:#FFF6BF;border:1px solid #FFD324;'>
	<tr><td colspan=2>
	There are <a target='_blank' href='http://wiki.openid.net/OpenIDServers'>dozens of OpenID providers</a>. Here's what to enter for some.<br>
	<em>Parts in <b>bold</b> need to be replaced with your username on that service.</em></br>
	</td></tr>
	<tr><td style='text-align:right;color:navy;'>AOL</td><td>openid.aol.com/<b>screenname</b> <em>(AIM usernames work too)</em></td></tr>
	<tr><td style='text-align:right;color:navy;'>Blogger</td><td><b>blogname</b>.blogspot.com</td></tr>
	<tr><td style='text-align:right;color:navy;'>Flickr</td><td>flickr.com/photos/<b>username</b></td></tr>
	<tr><td style='text-align:right;color:navy;'>Googe</td><td>google.com</td></tr>
	<tr><td style='text-align:right;color:navy;'>LiveJournal</td><td><b>username</b>.livejournal.com</td></tr>
	<tr><td style='text-align:right;color:navy;'>LiveDoor</td><td>profile.livedoor.com/<b>username</b> <em>(Japan)</em></td></tr>
	<tr><td style='text-align:right;color:navy;'>Orange</td><td>openid.orange.fr <em>(France)</em></td></tr>
	<tr><td style='text-align:right;color:navy;'>SmugMug </td><td><b>username</b>.smugmug.com</td></tr>
	<tr><td style='text-align:right;color:navy;'>Technorati</td><td>technorati.com/people/technorati/<b>username</b></td></tr>
	<tr><td style='text-align:right;color:navy;'>Vox</td><td><b>member</b>.vox.com</td></tr>
	<tr><td style='text-align:right;color:navy;'>Yahoo</td><td>openid.yahoo.com</td></tr>
	<tr><td style='text-align:right;color:navy;'>WordPress.com</td><td><b>username</b>.wordpress.com <em>(first login to wordpress.com)</em></td></tr>
	</table>";
	}

?>