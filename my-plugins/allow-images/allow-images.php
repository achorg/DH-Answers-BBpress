<?php
/*
Plugin Name: Allow Images
Plugin URI: http://bbpress.org/#
Description: Allows <img /> tags to be posted in your forums.
Author: Michael D Adams
Author URI: http://blogwaffe.com/
Version: 0.9
*/

// Add "img" to the allowed tags list. By default, only allow attributes 'src', 'title', 'alt' in 'img' tags. 
function allow_images_allowed_tags( $tags ) {
	$tags['img'] = array('src' => array(), 'title' => array(), 'alt' => array());
	return $tags;
}
add_filter( 'bb_allowed_tags', 'allow_images_allowed_tags' );

?>
