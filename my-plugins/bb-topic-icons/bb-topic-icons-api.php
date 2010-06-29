<?php

$status_interpreters = array();
$status_renderers = array();

function topic_icons_get_active_icon_set() {
	$active = bb_get_option('topic-icons-active-icon-set');
	if (isset($active)) {
		if (is_dir(dirname(__FILE__).'/icon-sets/'.$active)) {
			return $active;
		}
	}
	return 'default';
}

function topic_icons_register_status_interpreter($name, $interpreter) {
	global $status_interpreters;

	if (!isset($status_interpreters[$name])) {
		$status_interpreters[$name] = $interpreter;
	}
}

function topic_icons_register_status_renderer($name, $renderer) {
	global $status_renderers;

	if (!isset($status_renderers[$name])) {
		$status_renderers[$name] = $renderer;
	}
}

function get_active_status_interpreter_name() {
	global $status_interpreters;
	$active = bb_get_option('topic-icons-active-status-interpreter');
	if (isset($active) && isset($status_interpreters[$active])) {
		return $active;
	}
	return 'default';
}

function get_active_status_interpreter() {
	global $status_interpreters;
	return $status_interpreters[get_active_status_interpreter_name()];
}

function get_active_status_renderer_name() {
	global $status_renderers;
	$active = bb_get_option('topic-icons-active-status-renderer');
	if (isset($active) && isset($status_renderers[$active])) {
		return $active;
	}
	return 'default';
}

function get_active_status_renderer() {
	global $status_renderers;
	return $status_renderers[get_active_status_renderer_name()];
}

?>