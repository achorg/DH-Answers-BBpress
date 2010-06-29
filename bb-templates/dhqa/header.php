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
	<!--[if IE7]>
		<style type="text/css" media="screen">
			#header .search .search-form input.submit {
				padding: 0;
				margin: 0;
				height: 23px;
				width: 70px;
			}
		</style>
	<![endif]-->
	<script src="<?php echo(bb_active_theme_uri().'scripts/typeface-0.14.js'); ?>"></script>
  <script src="<?php echo(bb_active_theme_uri().'scripts/helvetiker_font/helvetiker_bold.typeface.js'); ?>"></script>

<?php bb_feed_head(); ?>

<?php bb_head(); ?>

</head>
<body id="<?php bb_location(); ?>">
	<div class="container prepend-top append-bottom">
		<div id="header" role="banner" class="prepend-6 span-18">
			<a id="ach-logo" href="http://www.ach.org">ACH</a>
			<h1 class="typeface-js" style="font-family: Helvetiker; font-weight: bold;"><a href="<?php bb_uri(); ?>"><?php bb_option('name'); ?></a></h1>
			<?php if ( bb_get_option('description') ) : ?><h3 class="description span-24"><?php bb_option('description'); ?></h3><?php endif; ?>
			<div class="login-container span-12">
				<?php if ( !in_array( bb_get_location(), array( 'login-page', 'register-page' ) ) ) login_form(); ?>
			</div>

			<div class="search span-6 last">
<?php search_form(); ?>
			</div>
		</div>
		<div id="main" class="span-24">
