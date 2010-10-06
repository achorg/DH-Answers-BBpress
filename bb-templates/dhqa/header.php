<?php
$_head_profile_attr = '';
if ( bb_is_profile() ) {
	global $self;
	if ( !$self ) {
		$_head_profile_attr = ' profile="http://www.w3.org/2006/03/hcard"';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php bb_language_attributes( '1.1' ); ?>>
<head<?php echo $_head_profile_attr; ?>>
	<meta http-equiv="X-UA-Compatible" content="IE=8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title><?php bb_title() ?></title>
	<link rel="stylesheet" href="<?php bb_stylesheet_uri(); ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo(bb_active_theme_uri().'blueprint/screen.css'); ?>" type="text/css" media="screen" title="blueprint-print" charset="utf-8"/>
	<link rel="stylesheet" href="<?php echo(bb_active_theme_uri().'blueprint/print.css'); ?>" type="text/css" media="print" title="blueprint-print" charset="utf-8"/>
	<!--[if lt IE 8]>
	  <link rel="stylesheet" href="<?php echo(bb_active_theme_uri().'blueprint/ie.css'); ?>" type="text/css" media="screen, projection">
	<![endif]-->
<?php if ( 'rtl' == bb_get_option( 'text_direction' ) ) : ?>
	<link rel="stylesheet" href="<?php bb_stylesheet_uri( 'rtl' ); ?>" type="text/css" />
<?php endif; ?>
	<!--[if IE]>
		<style type="text/css" media="screen">
			.header-button, form.login input, input[type="submit"], #header div.search, form.login input {
				border-radius:4px;
				behavior: url(border-radius.htc);
			}
		</style>
	<![endif]-->
	<!--[if IE 7]>
		<style type="text/css" media="screen">
			#header .search .search-form input.submit {
				padding: 0;
				margin: 0;
				height: 29px;
				width: 70px;
			}
		</style>
	<![endif]-->

<?php bb_feed_head(); ?>

<?php bb_head(); ?>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-18811436-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>
<body id="<?php bb_location(); ?>">
	<div class="container prepend-top append-bottom">
		<div id="util-login">
		<?php if ( !bb_is_user_logged_in() ) 
			{ 
				printf(
					__( '<a href="%2$s">Log in</a> | <a href="%1$s">Register</a>' ),
					bb_get_uri( 'register.php', null, BB_URI_CONTEXT_A_HREF + BB_URI_CONTEXT_BB_USER_FORMS ),
					bb_get_uri( 'bb-login.php', null, BB_URI_CONTEXT_FORM_ACTION + BB_URI_CONTEXT_BB_USER_FORMS )
				); 
			} else { 
				printf(__('Logged in as %1$s'), bb_get_profile_link(bb_get_current_user_info( 'name' )));
				echo ' | ';
				if( $bb_current_user->has_cap( 'administrate' ) || $bb_current_user->has_cap( 'moderate' ) ) 
				{
					bb_admin_link();
					echo ' | '; 
				}
					bb_logout_link();
				}?>
		</div>
		<div id="header" class="prepend-6 span-18">
			<a id="ach-logo" href="http://www.ach.org">ACH</a>
			<h1><a href="<?php bb_uri(); ?>"><?php bb_option('name'); ?></a></h1>
			<?php if ( bb_get_option('description') ) : ?><h3 class="description span-24"><?php bb_option('description'); ?></h3><?php endif; ?>
			<div class="login-container span-12">
				<?php if ( !in_array( bb_get_location(), array( 'login-page', 'register-page' ) ) ) login_form(); ?>
			</div>

			<div class="search span-6 last">
<?php search_form(); ?>
			</div>
		</div>
		<div id="main" class="span-24">
