<?php

function topic_icons_admin_page_process() {
	if (isset($_POST['topic-icons-submit'])) {
		bb_update_option('topic-icons-active-icon-set', $_POST['icon-set']);
		bb_update_option('topic-icons-active-status-interpreter', $_POST['status-interpreter']);
		bb_update_option('topic-icons-active-status-renderer', $_POST['status-renderer']);
	}
}

function topic_icons_admin_page() {
	global $status_interpreters, $status_renderers;

	echo '<h2>Topic Icons</h2>';

	$icon_set_name = topic_icons_get_active_icon_set();
	$statuses = get_active_status_interpreter()->getAllStatuses();
	$renderer = get_active_status_renderer();

	echo '<form method="post">';
	echo '<table width="80%"><tr><td width="50%" valign="top">';
	echo '<p>Select a status interpreter to activate:</p>';
	reset($status_interpreters);
	while (list($key, $val) = each($status_interpreters)) {
		echo '&nbsp;&nbsp;&nbsp;<input type="radio" name="status-interpreter" value="'.$key.'"'.($key == get_active_status_interpreter_name() ? ' checked' : '').'>&nbsp;'.$key.'<br/>';
	}
	echo '</td><td width="50%" style="background-color: #ddd; padding: 10px; border: 1px solid #ccc;" valign="top">';
	echo '<b>Note: </b><i>Status interpreters examine a given topic and return a status value (say, "closed") based on the data they are given.  New status interpreters can extend the range of status values that icons can be associated with.</i>';
	echo '</td></tr></table>';

	echo '<br/><table width="80%"><tr><td width="50%" valign="top">';
	echo '<p>Select a status renderer to activate:</p>';
	reset($status_renderers);
	while (list($key, $val) = each($status_renderers)) {
		echo '&nbsp;&nbsp;&nbsp;<input type="radio" name="status-renderer" value="'.$key.'"'.($key == get_active_status_renderer_name() ? ' checked' : '').'>&nbsp;'.$key.'<br/>';
	}	
	echo '</td><td width="50%" style="background-color: #ddd; padding: 10px; border: 1px solid #ccc;" valign="top">';
	echo '<b>Note: </b><i>Status renderers take a status value (say, "closed") and map it to a corresponding image filename (say, "closed-topic.png").  This filename will then be loaded from the active <strong>icon set</strong></i>';
	echo '</td></tr></table>';

	echo '<br/><p>Select an icon set to activate:</p>';
	echo '<table class="widefat"><thead>';
	echo '<tr><th>&nbsp;</th>';
	for ($i=0; $i < count($statuses); $i++) {
		echo '<th>'.$statuses[$i].'</th>';
	}
	echo '</tr></thead>';
	
	$raw = scandir(dirname(__FILE__).'/icon-sets');
	$iconsets = array();
	for ($i=0; $i < count($raw); $i++) {
		if (strlen($raw[$i]) > 2 && is_dir(dirname(__FILE__).'/icon-sets/'.$raw[$i])) {
			$iconsets[] = $raw[$i];
		}
	}

	echo '<tbody>';
	for ($j=0; $j < count($iconsets); $j++) {	
		echo '<tr><td><input type="radio" name="icon-set" value="'.$iconsets[$j].'"'.($icon_set_name==$iconsets[$j] ? ' checked' : '').'>&nbsp;'.$iconsets[$j].'</td>';
		$icon_set_url = ICON_SET_URL_BASE . $iconsets[$j];
		for ($i=0; $i < count($statuses); $i++) {
			$image = $renderer->renderStatus($statuses[$i]);
			$tooltip = $renderer->renderStatusTooltip($statuses[$i]);
			$exists = file_exists(dirname(__FILE__).'/icon-sets/'.$iconsets[$j].'/'.$image);

			if (isset($image) && strlen($image) > 0 && $exists) {
				echo '<td><img src="'.$icon_set_url.'/'.$image.
					'" width="'.ICON_WIDTH.'" height="'.ICON_HEIGHT.
					'" align="absmiddle">&nbsp;'.$tooltip.'</td>';
			} else {
				echo '<td><img src="'.ICON_SET_URL_BASE.'/empty.png'.
					'" width="'.ICON_WIDTH.'" height="'.ICON_HEIGHT.
					'" align="absmiddle">&nbsp;</td>';
			}
		}
		echo '</tr>';
	}
	echo '</tbody></table>';
	echo '<input type="submit" name="topic-icons-submit" value="save">';
	echo '</form>';
}

function topic_icons_admin_page_add() {
	if (function_exists('bb_admin_add_submenu')) {
		bb_admin_add_submenu('Topic Icons', 'moderate', 'topic_icons_admin_page' );
	}
}

?>