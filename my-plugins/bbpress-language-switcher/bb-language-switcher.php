<?php
/*
Plugin Name: bbPress Language Switcher
Plugin URI: http://bbpress.org/plugins/topic/bbpress-language-switcher
Description: Allows any user (guest or member) to select a different bbPress language for templates.
Version: 0.0.6
Author: _ck_
Author URI:  http://bbshowcase.org
Donate: http://bbshowcase.org/donate/
*/ 

define('BB_LANG_USE_FILE_META',false);	// change to true to scan inside of .mo files for language name, set to 'only' or 'force' to ONLY use file meta

/*  stop editing here  */

bb_language_switcher_set_cookie();
add_filter('locale', 'bb_language_switcher_filter');
add_action('bb_language_switcher','bb_language_switcher');
if (defined('BACKPRESS_PATH') && bb_language_switcher_filter()) {bb_load_default_textdomain();}

// admin hooks
if (defined('BB_IS_ADMIN') && BB_IS_ADMIN && ((isset($_GET['name']) && strpos(strtolower($_GET['name']),"language+switcher")) || (isset($_GET['plugin']) && strpos($_GET['plugin'],basename(__FILE__)) ))) {
	require_once("bb-language-switcher-admin.php");	
	bb_register_activation_hook(str_replace(array(str_replace("/","\\",BB_PLUGIN_DIR),str_replace("/","\\",BB_CORE_PLUGIN_DIR)),array("user#","core#"),__FILE__), 'bb_language_switcher_update');
}
if (isset($_GET['bb_language_switcher_update'])) {require_once("bb-language-switcher-admin.php"); add_action('bb_init','bb_language_switcher_update');}
if (isset($_GET['bb_language_switcher_debug'])) {require_once("bb-language-switcher-admin.php"); add_action('bb_init','bb_language_switcher_debug');}

function bb_language_switcher_filter($locale='') {if (isset($_COOKIE['bblang_'.BB_HASH])) {$locale=bb_language_switcher_get_cookie();} return $locale;}

function bb_language_switcher_get_cookie() {return trim(substr(strip_tags(stripslashes($_COOKIE['bblang_'.BB_HASH])),0,20));}

function bb_language_switcher_set_cookie() { 		// makes the url request into a cookie
	if (!isset($_GET['bblang'])) {return;}
	$bblang=trim(substr(strip_tags(stripslashes($_GET['bblang'])),0,20)); if (empty($bblang)) {if (defined('BB_LANG')) {$bblang=BB_LANG;} else {$bblang=' ';}}
	$cookiepath=bb_get_option('cookiepath'); if (empty($cookiepath)) {$cookiepath="/";}
	if ($cookiedomain=bb_get_option('cookiedomain')) {setcookie("bblang_".BB_HASH, $bblang, 2147483647, $cookiepath, $cookiedomain);}
	else {setcookie("bblang_".BB_HASH, $bblang, 2147483647, $cookiepath);}				
	header('Location: ' . remove_query_arg('bblang')); exit;
}

function bb_language_switcher($ignore='') {				// builds and displays the language dropdown UI
	global $bb_language_switcher; $output=""; $current=bb_language_switcher_filter();
	if (empty($bb_language_switcher)) {$bb_language_switcher=bb_get_option('bb_language_switcher');}
	if (empty($current) && defined('BB_LANG')) {$bblang=BB_LANG; if (!empty($bblang)) {$current=$bblang;}}	
	$output.= '<form id="bb_language_switcher" style="display:inline-block;"><select  style="width:150px;" name="bb_language_switcher" onchange="location.href=\''.add_query_arg('bblang','',remove_query_arg('bblang')).'=\' + this.options[this.selectedIndex].value;">'."\n"	;		
	foreach ($bb_language_switcher as $value=>$description) {
		if ($value==$current) {$selected='" selected="selected"  ';} else {$selected='';}
		if (empty($value) || $value==" ") {$bk="style='background:#ECE9D8;color:#000;font-weight:bold;' ";} else {$bk="";}	 // highlight english
		$output.= '	<option '.$bk.'value="'.$value.'"'.$selected.'>&nbsp;'.$description.'</option>'."\n";	// padding mess for cross-browser
	}
	$output.="</select></form>\n";
	echo $output;
}

?>