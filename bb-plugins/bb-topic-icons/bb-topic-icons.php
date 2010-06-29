<?
/*
Plugin Name: bb Topic Icons
Plugin URI: http://devt.caffeinatedbliss.com/bbpress/topic-icons
Description: Adds configurable icons next to topics based on their status
Author: Paul Hawke
Author URI: http://paul.caffeinatedbliss.com/
Version: 0.6
*/

/****************************************************************************
 *
 * Configure the following constants to fine-tune the CSS classes that are
 * generated, the icon filenames that are used, and the text used in the
 * legend (if you have one displayed).  Note: filenames are likely to be
 * taken away in a future version and replaced with the concept of "icon sets"
 * whose filenames are fixed, so dont get used to editing the filenames,
 * as this will break in future versions.
 *
 ****************************************************************************/

// css class for the unsorted list used in the legend display
define( LEGEND_CLASS, 'topic_icon_legend' );

// busy threshold - a topic with more posts than this is counted as "busy"
// for purposes of picking an icon.
define( BUSY_THRESHOLD, 15 );

// width of the images, in pixels
define( ICON_WIDTH, '20' );

// height of the images, in pixels
define( ICON_HEIGHT, '20' );

// the URL base for where to find the default icon set.
define( ICON_SET_URL_BASE, BB_PLUGIN_URL.'bb-topic-icons/icon-sets/' );

/****************************************************************************
 *
 * Shouldnt be much need to edit anything beyond this point - configuration
 * is all done via the constants (above) and through and admin area page in
 * bbPress at runtime.
 *
 ****************************************************************************/

require( 'bb-topic-icons-api.php' );
require( 'bb-topic-icons-admin.php' );
require( 'interface.status-interpreter.php' );
require( 'interface.status-renderer.php' );
require( 'class.default-status-interpreter.php' );
require( 'class.default-status-renderer.php' );

function topic_icons_legend() {
	$icon_set_name = topic_icons_get_active_icon_set();
	$icon_set_url = ICON_SET_URL_BASE . $icon_set_name;
	$statuses = get_active_status_interpreter()->getAllStatuses();
	$renderer = get_active_status_renderer();
	
	echo '<ul id="'.LEGEND_CLASS.'">';
	for ($i=0; $i < count($statuses); $i++) {
		$image = $renderer->renderStatus($statuses[$i]);
		$tooltip = $renderer->renderStatusTooltip($statuses[$i]);
		$exists = file_exists(dirname(__FILE__).'/icon-sets/'.$icon_set_name.'/'.$image);

		if (isset($image) && strlen($image) > 0 &&
			isset($tooltip) && strlen($tooltip) > 0 && $exists) {
			echo '<li><img src="'.$icon_set_url.'/'.$image.
				'" width="'.ICON_WIDTH.'" height="'.ICON_HEIGHT.
				'" align="absmiddle">&nbsp;'.$tooltip.'</li>';
		}
	}
	echo '</ul>';
}

function topic_icons_css() {
	echo "\n<style type=\"text/css\"><!--\n";
	require( 'bb-topic-icons.css' );
	echo "\n--></style>";
}

function topic_icons_label( $label ) {
	global $topic;
	
	if (bb_is_front() || bb_is_forum() || bb_is_view() || bb_is_tag()) {		
		$icon_set_name = topic_icons_get_active_icon_set();
		$icon_set_url = ICON_SET_URL_BASE . $icon_set_name;

		$status = get_active_status_interpreter()->getStatus(bb_get_location(), $topic);
		$renderer = get_active_status_renderer();
		$image = $renderer->renderStatus($status);
		$tooltip = $renderer->renderStatusTooltip($status);
		$exists = file_exists(dirname(__FILE__).'/icon-sets/'.$icon_set_name.'/'.$image);

		if (!$exists) {
			return sprintf(__('<div class="topic-icon-image"><a href="%s"><img src="%s" width="%s" height="%s" alt="%s" border="0"></a></div> %s'), 
				get_topic_link($topic->topic_id), ICON_SET_URL_BASE.'/empty.png', ICON_WIDTH, ICON_HEIGHT, $tooltip, $label);
		} else if (strlen($tooltip) > 0) {		
			return sprintf(__('<div class="topic-icon-image"><a href="%s"><img src="%s" width="%s" height="%s" alt="%s" border="0"><span>%s</span></a></div> %s'), 
				get_topic_link($topic->topic_id), $icon_set_url.'/'.$image, ICON_WIDTH, ICON_HEIGHT, $tooltip, $tooltip, $label);
		} else {
			return sprintf(__('<div class="topic-icon-image"><a href="%s"><img src="%s" width="%s" height="%s" alt="%s" border="0"></a></div> %s'), 
				get_topic_link($topic->topic_id), $icon_set_url.'/'.$image, ICON_WIDTH, ICON_HEIGHT, $tooltip, $label);
		}
	}
	
	return $label;
}

function topic_icons_init( ) {
	remove_filter('bb_topic_labels', 'bb_closed_label', 10);
	remove_filter('bb_topic_labels', 'bb_sticky_label', 20);

	add_filter('bb_topic_labels', 'topic_icons_label', 11);

	add_action('bb_head', 'topic_icons_css');

	add_action('bb_admin_menu_generator', 'topic_icons_admin_page_add');
	add_action('bb_admin-header.php', 'topic_icons_admin_page_process');
	
	topic_icons_register_status_interpreter('default', new DefaultStatusInterpreter(BUSY_THRESHOLD));
	topic_icons_register_status_renderer('default', new DefaultStatusRenderer());
}

topic_icons_init();

?>
