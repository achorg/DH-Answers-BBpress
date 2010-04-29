<?php
/*
Plugin Name: Social It
Plugin URI: http://gaut.am/bbpress/plugins/social-it/
Description: Social It adds a (X)HTML compliant list of social bookmarking icons to topics, front page, tags, etc. See <a href="admin-base.php?plugin=socialit_settings_page">configuration panel</a> for more settings. This plugin is inspired from the <a href="http://sexybookmarks.net/">SexyBookmarks plugin for Wordpress</a>. This plugin is also compatible with <a href="http://bbpress.org/plugins/topic/support-forum/">Support Forum plugin</a>.
Version: 1.5
Author: Gautam
Author URI: http://gaut.am/

	Original Social It bbPress Plugin Copyright 2009 Gautam (email: admin@gaut.am) (website: http://gaut.am)
	Original SexyBookmarks Plugin Copyright 2009 Josh (email: josh@sexybookmarks.net), Jamie, Gautam (email: admin@gaut.am), Norman (www.robotwithaheart.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

/*
 Main PHP File for
 Social It plugin (for bbPress) by www.gaut.am
*/

// Create Text Domain For Translations
load_plugin_textdomain('socialit', '/my-plugins/social-it/languages/');

//defines
define('SOCIALIT_OPTIONS','SocialIt');
define('SOCIALIT_VER','1.5');
define('SOCIALIT_PLUGPATH', bb_get_option('uri').trim(str_replace(array(trim(BBPATH,"/\\"),"\\"),array("","/"),dirname(__FILE__)),' /\\').'/');
define('SOCIALIT_PLUGDIR', dirname(__FILE__).'/');

//load json functins if they are not present
if ( !class_exists('SERVICES_JSON') ) { 
	if ( !function_exists('json_decode') ){
		function json_decode($content, $assoc=false){
			require_once 'includes/JSON.php';
			if ( $assoc ){
				$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			} else {
				$json = new Services_JSON;
			}
		return $json->decode($content);
		}
	}
	
	if ( !function_exists('json_encode') ){
	    function json_encode($content){
			require_once 'includes/JSON.php';
			$json = new Services_JSON;
		return $json->encode($content);
	    }
	}
}

//requires
require_once('includes/functions.php'); //social it functions file
require_once('includes/bookmarks-data.php'); //bookmarks data file
require_once('includes/mobile.php'); //mobile/bot check file
//requires have to be above the below code as the below code calls $socialit_bookmarks_data which is in bookmarks-data.php file

//load options
$socialit_plugopts = bb_get_option(SOCIALIT_OPTIONS);
if(!$socialit_plugopts){
	//add defaults to an array
	$socialit_plugopts = array(
		'reloption' => 'nofollow', // 'nofollow', or ''
		'targetopt' => '_blank', // '_blank' or '_self'
		'bgimg-yes' => 'yes', // 'yes' or blank
		'bgimg' => 'caring', // 'sexy', 'caring', 'wealth', 'care-old', 'enjoy', 'german'
		'shorty' => 'b2l',
		'shortyapi' => array(
			'snip' => array(
				'user' => '',
				'key' => '',
			),
			'bitly' => array(
				'user' => '',
				'key' => '',
			),
			'supr' => array(
				'chk' => 0,
				'user' => '',
				'key' => '',
			),
			'trim' => array(
				'chk' => 0,
				'user' => '',
				'pass' => '',
			),
			'tinyarrow' => array(
				'chk' => 0,
				'user' => '',
			),
			'cligs' => array(
				'chk' => 0,
				'key' => '',
			),
		),
		'shorturls' => array(),
		'topic' => '1',
		'bookmark' => array_keys($socialit_bookmarks_data),
		'xtrastyle' => '',
		'feed' => '0', // 1 or 0
		'expand' => '1',
		'autocenter' => '0',
		'ybuzzcat' => 'science',
		'ybuzzmed' => 'text',
		'twittcat' => 'Internet',
		'defaulttags' => 'Forum,Internet',
		'warn-choice' => '',
		'sfpnonres' => 'yes',
		'sfpres' => 'yes',
		'sfpnonsup' => 'yes',
		'mobile-hide' => 'yes',
		'custom-mods' => '',
	);
	bb_update_option(SOCIALIT_OPTIONS, $socialit_plugopts);
}

/* this part of code will be removed after the release of 1.5 */
if($socialit_plugopts['shortyapi'] == "1"){ //means that 1.2 ver was installed, and we have to fix some issues
	$socialit_plugopts['shortyapi'] = array(
		'snip' => array(
			'user' => '',
			'key' => '',
		),
		'bitly' => array(
			'user' => '',
			'key' => '',
		),
		'supr' => array(
			'chk' => 0,
			'user' => '',
			'key' => '',
		),
		'trim' => array(
			'chk' => 0,
			'user' => '',
			'pass' => '',
		),
		'tinyarrow' => array(
			'chk' => 0,
			'user' => '',
		),
		'cligs' => array(
			'chk' => 0,
			'key' => '',
		),
	);
	bb_update_option(SOCIALIT_OPTIONS, $socialit_plugopts); //update with edited options
}

if($socialit_plugopts['shorty'] == 'e7t'){
	$socialit_plugopts['shorty'] = 'b2l'; //now, no more e7t, instead b2l.me
	bb_update_option(SOCIALIT_OPTIONS, $socialit_plugopts); //update with edited options
}

//fix for Social It 1.3, will be removed after the release of 1.5
if(!is_array($socialit_plugopts['bookmark'])){
	$socialit_plugopts['bookmark'] = array_keys($socialit_bookmarks_data);
	bb_update_option(SOCIALIT_OPTIONS, $socialit_plugopts); //update with edited options
}

//add actions/filters
add_action('bb_admin_menu_generator', 'socialit_menu_link', -998); //link in settings
add_action('bb_admin_head', 'socialit_admin', 997); //admin css
add_action('bb_head', 'socialit_public', 997); //public css
if(bb_is_topic() && $_GET['socialit_hide_show'] == "1"){
	add_action('bb_init', 'socialit_hide_show_do', 997); //do the function of hide/show socialit
}
add_filter('post_text', 'socialit_insert_in_post', 997); //to insert social it automatically below the first post of every topic
add_filter('bb_topic_admin', 'socialit_hide_show', 6); //to show the option to the admin whether to show the bookmark menu on a particular topic
?>