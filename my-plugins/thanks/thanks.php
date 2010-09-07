<?
/*
Plugin Name: Thanks
Plugin URI: http://devt.caffeinatedbliss.com/bbpress/thanks
Description: Empowers users to leave a vote of thanks for posts
Author: Paul Hawke
Author URI: http://paul.caffeinatedbliss.com/
Version: 0.7
*/

$DEFAULTS = array(
	"thanks_output_none" => "", 
	"thanks_output_one" => "# vote of thanks", 
	"thanks_output_many" => "# votes of thanks",
	"thanks_voting" => "Add your vote of thanks",
	"thanks_position" => "after",
	"thanks_voters" => "no",
	"thanks_voters_prefix" => "(",
	"thanks_voters_suffix" => ")",
);

require_once( "thanks-admin.php" );
require_once( "thanks-output.php" );

function thanks_js() {
	$src = BB_PLUGIN_URL.'thanks/thanks-plugin.js';
	wp_register_script('thanks-plugin-js', $src, array('jquery'));
	
	wp_enqueue_script('thanks-plugin-js');
}

function thanks_head() { ?>
<script type="text/javascript"><!--
   	var ajaxThanksUrl = "<?php echo BB_PLUGIN_URL; ?>thanks/ajax-thanks.php";
// -->
</script>
<?php }

function thanks_output_before() {
		$msg = thanks_get_voting_phrase("thanks_position");
		if ($msg == "before") {
			 thanks_output();
		}
}

function thanks_output_after() {
		$msg = thanks_get_voting_phrase("thanks_position");
		if ($msg == "after") {
			 thanks_output();
		}
}

function thanks_output() {
	global $bb_post, $DEFAULTS;

	$logged_in = bb_is_user_logged_in();
	$post_id = $bb_post->post_id;
	$user = bb_get_current_user();
	$uid = ($logged_in) ? (int) $user->ID : false;
	
	echo "<div class=\"thanks-output\" id=\"thanks-".$post_id."\">";
	echo thanks_output_details($post_id, $uid, $logged_in);
	echo "</div>";
}

function thanks_bootstrap( ) {
	add_action('bb_init', 'thanks_js');

	add_action('admin_init', 'thanks_js');
	
	add_action('bb_head', 'thanks_head');

	add_action('bb_admin_head', 'thanks_admin_head');
	
	add_action('bb_admin-header.php', 'thanks_admin_page_process');

	add_action('bb_post.php', 'thanks_output_before');

	add_action('bb_after_post.php', 'thanks_output_after');
	
	add_action('bb_admin_menu_generator', 'thanks_admin_page_add');
}

thanks_bootstrap();

?>
