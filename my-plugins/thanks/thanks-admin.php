<?

function thanks_admin_page() {
	require( 'thanks-admin-page.php' );
}

function thanks_admin_page_add() {
	if (function_exists('bb_admin_add_submenu')) {
	    bb_admin_add_submenu('Thanks', 'moderate', 'thanks_admin_page' );
	}
}

function thanks_admin_head() { ?>
<script type="text/javascript"><!--
   	var ajaxConfigUrl = "<?php echo BB_PLUGIN_URL; ?>thanks/ajax-config.php";
   	var ajaxUninstallUrl = "<?php echo BB_PLUGIN_URL; ?>thanks/ajax-reset.php";
// -->
</script>
<link rel="stylesheet" href="<?php echo BB_PLUGIN_URL; ?>thanks/thanks-admin-style.css" type="text/css" />
<?php 
}

function thanks_admin_page_process() {
	global $_POST;
	
	if (isset($_POST['thanks_option_submit'])) {
		bb_update_option('thanks_voting', $_POST['thanks_voting']);
		bb_update_option('thanks_output_none', $_POST['thanks_output_none']);
		bb_update_option('thanks_output_one', $_POST['thanks_output_one']);
		bb_update_option('thanks_output_many', $_POST['thanks_output_many']);
		bb_update_option('thanks_position', $_POST['thanks_position']);
		bb_update_option('thanks_voters', $_POST['thanks_voters']);
		bb_update_option('thanks_voters_prefix', $_POST['thanks_voters_prefix']);
		bb_update_option('thanks_voters_suffix', $_POST['thanks_voters_suffix']);
	}
	
	if (isset($_POST['thanks_option_reset'])) {
		bb_delete_option('thanks_voting');
		bb_delete_option('thanks_output_none');
		bb_delete_option('thanks_output_one');
		bb_delete_option('thanks_output_many');
		bb_delete_option('thanks_success');
		bb_delete_option('thanks_position');
		bb_delete_option('thanks_voters');
		bb_delete_option('thanks_voters_prefix');
		bb_delete_option('thanks_voters_suffix');
	}
	
	if (isset($_POST['thanks_remove_all'])) {
		$opt = bb_get_option("thanks_posts");
		for ($i=0; $i < count($opt); $i++) {
			$post_id = $opt[$i];
			bb_delete_postmeta($post_id, "thanks");
		}
		bb_delete_option("thanks_posts");
		bb_delete_option('thanks_voting');
		bb_delete_option('thanks_output_none');
		bb_delete_option('thanks_output_one');
		bb_delete_option('thanks_output_many');
		bb_delete_option('thanks_success');
		bb_delete_option('thanks_position');
		bb_delete_option('thanks_voters');
		bb_delete_option('thanks_voters_prefix');
		bb_delete_option('thanks_voters_suffix');
	}
}

?>
