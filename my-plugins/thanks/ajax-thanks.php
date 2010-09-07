<?php
require_once( '../../bb-load.php' );
require_once( "thanks-output.php" );

$post_id = $_POST['post_id'];
$user_id = $_POST['user_id'];

$meta = bb_get_post_meta("thanks", $post_id);
if (!isset($meta)) {
	$meta = array();
}
$tmp = array();
for ($i=0; $i<count($meta); $i++) {
	$tmp[$meta[$i]] = "X";
}
$tmp[$user_id] = "X";
$meta = array_keys($tmp);
bb_update_postmeta($post_id, "thanks", $meta);

$opt = bb_get_option("thanks_posts");
if (!isset($opt)) {
	$opt = array();
}
$tmp = array();
for ($i=0; $i<count($opt); $i++) {
	$tmp[$opt[$i]] = "X";
}
$tmp[$post_id] = "X";
$opt = array_keys($tmp);
bb_update_option( 'thanks_posts', $opt );

echo thanks_output_details($post_id, $user_id, true);
?>