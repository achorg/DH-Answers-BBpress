<?php

function mass_edit() {
	if ( !bb_current_user_can('browse_deleted') ) {die(__("Now how'd you get here?  And what did you think you'd be doing?"));}
	add_action( 'bb_get_option_page_topics', 'mass_edit_topic_limit',250);
	global $bbdb, $bb_post_cache, $bb_user_cache, $bb_posts, $bb_post, $page, $mass_edit_options;
	
	if (isset($_GET['mass_edit_reset'])) {
		bb_delete_option('mass_edit_options');			
		wp_redirect(remove_query_arg(array('mass_edit_options','mass_edit_reset')));
	}
	if ( !empty( $_POST['mass_edit_save_options'] ) ) {				
		$mass_edit_options['mass_edit_columns']=implode(",",array_unique(array_map('trim',explode(",",strtolower(stripslashes($_POST['mass_edit_columns'].", checkbox , excerpt , name , meta , actions" ))))));
		$mass_edit_options['mass_edit_css']=stripslashes($_POST['mass_edit_css']);
		bb_update_option('mass_edit_options',$mass_edit_options);
		wp_redirect(remove_query_arg(array('mass_edit_options','mass_edit_reset')));	// may not work since headers are already sent
	}
	
	echo '<div style="text-align:right;margin-bottom:-1.5em;">';
	if (isset($_GET['mass_edit_options'])) { 	
	echo '[ <a href="'.bb_get_admin_tab_link("mass_edit").'&mass_edit_reset=1">Reset To Defaults</a> ]';
	} else { echo '[ <a href="'.bb_get_admin_tab_link("mass_edit").'&mass_edit_options=1">Settings</a> ]';}
	echo '</div>';
	
	echo "<h2><a style='color:black;border:0;text-decoration:none;' href='".bb_get_admin_tab_link("mass_edit")."'>".__('Mass Edit')."</a></h2>";
		
	if(!isset($mass_edit_options)) {$mass_edit_options = bb_get_option('mass_edit_options');	}
	if (!isset($mass_edit_options['mass_edit_columns']) || is_array($mass_edit_options['mass_edit_columns'])) {
		$mass_edit_options['mass_edit_columns']="checkbox , excerpt , name , meta , actions";
		bb_update_option('mass_edit_options',$mass_edit_options);
	}

	$mass_edit_columns=explode(",",strtolower($mass_edit_options['mass_edit_columns']));
	array_walk($mass_edit_columns ,create_function('&$arr','$arr=trim($arr);')); 	

	if (isset($_GET['mass_edit_options'])) { ?>
	<form action="<?php echo bb_get_admin_tab_link("mass_edit"); ?>" method="post" id="mass-edit-options">
	
	<fieldset><legend><strong>Mass Edit Column Order</strong> - default: checkbox , excerpt , name , meta , actions</legend>
	<input name="mass_edit_columns" id="mass_edit_columns" type="text" size="50" value="<?php echo $mass_edit_options['mass_edit_columns']; ?>" />
	<span style="padding-left:2em;" class=submit><input class=submit type="submit" name="mass_edit_save_options" value="<?php _e('Save Options') ?> &raquo;"  /></span>
	</fieldset>
		
	<fieldset><legend><b>Mass Edit CSS</b></legend>
	<textarea name="mass_edit_css" id="mass_edit_css" cols="100" rows="10"><?php echo $mass_edit_options['mass_edit_css']; ?></textarea>
	</fieldset>
	</form>
	<br clear=both />
	<hr />
	<?php 	}

/*	
	add_filter( 'get_topic_where', 'no_where' ); add_filter( 'get_topic_link', 'bb_make_link_view_all' );	
	$bb_post_query = new BB_Query_Form( 'post',array( 'post_status' => 0, 'count' => true ));
	$bb_posts =& $bb_post_query->results; 	$total = $bb_post_query->found_rows;
*/	

if ( !empty( $_POST['mass_edit_delete_posts'] ) ) :
	bb_check_admin_referer('mass-edit-bulk-posts');

	$i = 0;
	
	$bbdb->hide_errors();	// bbPress still has some db function issues with topic delete/undelete	
	
	foreach ($_POST['mass_edit_delete_posts'] as $bb_post_id) : // Check the permissions on each
		$bb_post_id = (int) $bb_post_id;
		// $bb_post_id = $bbdb->get_var("SELECT post_id FROM $bbdb->posts WHERE post_id = $bb_post");
		// $authordata = bb_get_usermeta( $bbdb->get_var("SELECT poster_id FROM $bbdb->posts WHERE ID = $bb_post_id") );
		if ( bb_current_user_can('delete_posts', $bb_post_id) ) {
			if ( !empty( $_POST['mass_edit_spam_button'] ) ) {bb_delete_post( $bb_post_id, 2 );}
			if ( !empty( $_POST['mass_edit_delete_button'] ) ) {bb_delete_post( $bb_post_id, 1 );}
			if ( !empty( $_POST['mass_edit_undelete_button'] ) ) {bb_delete_post( $bb_post_id, 0 );}		
			++$i;
		}
	endforeach;
	
	$bbdb->show_errors();	// bbPress still has some db function issues with topic delete/undelete
	// $bbdb->flush();	
	// global $bb_cache,$bb_post_cache, $bb_topic_cache;  unset($bb_cache); unset($bb_post_cache); unset($bb_topic_cache);

	
	echo '<div  id="message" class="updated fade" style="clear:both;"><p>';
	if ( !empty( $_POST['mass_edit_spam_button'] ) ) {printf(__('%s posts marked as spam.'), $i);}
 	if ( !empty( $_POST['mass_edit_delete_button'] ) ) {printf(__('%s posts deleted.'), $i);}
	if ( !empty( $_POST['mass_edit_undelete_button'] ) ) {printf(__('%s posts undeleted.'), $i);}		
	echo '</p></div>';
endif;

if (isset($_GET['post_text']))       {$post_text = substr($bbdb->escape($_GET['post_text']),0,100);} else {$post_text="";}
if (isset($_GET['post_author'])) {$post_author = substr($bbdb->escape($_GET['post_author']),0,30);} else {$post_author="";}
if (isset($_GET['post_status']))  {$post_status = substr($bbdb->escape($_GET['post_status']),0,3);} else {$post_status="0";}
if (isset($_GET['post_order']))   {$post_order = ($_GET['post_order']=="ASC") ? "ASC" : "DESC";} else {$post_order="DESC";}
if (isset($_GET['exact_match'])) {$exact_match = intval($_GET['exact_match']);} else {$exact_match = 0;}
if (isset($_GET['per_page']))      {$per_page = intval(substr($bbdb->escape($_GET['per_page']),0,3));} else {$per_page="20";} 
$offset = (intval($page) -1) *  $per_page;  		// if (isset($_GET['page']))  {} else {$offset = 0;}

$query=" FROM $bbdb->posts ";
if ($post_text || $post_author || $post_status!="all") {$query.=" WHERE ";}

if ($post_text) {
	if ($exact_match) {
		$query.=" (post_text REGEXP '[[:<:]]".$post_text."[[:>:]]'	OR poster_ip = '".$post_text."') ";
	} else {
		$query.=" (post_text LIKE '%$post_text%' OR poster_ip LIKE '%$post_text%' ) ";
	}
}

if ($post_author) {	
	$authors="SELECT ID FROM $bbdb->users WHERE ";
	$authors.=($exact_match) ? " (user_login REGEXP '[[:<:]]".$post_author."[[:>:]]') " : " (user_login LIKE '%$post_author%') ";
	$authors.=" LIMIT 99";
	if ($authors= $bbdb->get_results($authors)) {	
		if (is_array($authors)) {
			foreach ($authors as $key=>$value) {$trans[]=$value->ID;}
			$authors=join(',', $trans);
		}
		
	} else {$authors="-1";}	
	$query.=(($post_text) ? " AND " : "")." poster_id IN ($authors) ";
}

if ($post_status!="all") {$query.=(($post_text || $authors) ? " AND " : "")." post_status = '$post_status' ";}
$restrict=" ORDER BY post_time $post_order LIMIT $offset,$per_page";

// echo $query;	// diagnostic
$total = $bbdb->get_var("SELECT COUNT(*) ".$query); 	// intval(bb_count_last_query($query));
if ($total) {$bb_posts = $bbdb->get_results("SELECT * ".$query.$restrict);} else {unset($bb_posts);}

?>

<form action="<?php echo bb_get_admin_tab_link("mass_edit"); ?>" method="get" id="post-search-form" class="search-form">
	<fieldset><legend><?php _e('Show Posts or IPs That Contain &hellip;') ?></legend> 
	<input name="post_text" id="post-text" class="text-input" type="text" value="<?php echo wp_specialchars($post_text); ?>" size="30" /> 	
	</fieldset>

<?php /*  selection by forum and tag not included in initial versions
<fieldset><legend>Forum &hellip;</legend>
<select name="forum_id" id="forum-id" tabindex="5">
<option value="0">Any</option>
<option value="1"> bbPress chat</option>
</select>
</fieldset>
<fieldset><legend>Tag&hellip;</legend>
<input name="tag" id="topic-tag" class="text-input" value="" type="text" />	</fieldset>
*/ ?>

	<fieldset><legend>Post Author&hellip;</legend>
	<input name="post_author" id="post-author" class="text-input" type="text" value="<?php if (isset($_GET['post_author'])) echo wp_specialchars($_GET['post_author'], 1); ?>" />	
	</fieldset>

	<fieldset><legend>Post Status &hellip;</legend>
		<select name="post_status" id="post-status">			
			<option value="0" <?php echo ($post_status==0) ? 'selected="selected"' : ''; ?>>Visible</option>
			<option value="1" <?php echo ($post_status==1) ? 'selected="selected"' : ''; ?>>Deleted</option>
			<option value="2" <?php echo ($post_status==2) ? 'selected="selected"' : ''; ?>>Spam</option>
			<option value="all" <?php echo ($post_status=="all") ? 'selected="selected"' : ''; ?>>All</option>
		</select>
	</fieldset>
	
	<fieldset><legend>Sort Order &hellip;</legend>
		<select name="post_order" id="post-order">
			<option value="DESC" <?php echo ($post_order=="DESC") ? 'selected="selected"' : ''; ?>>Newest</option>
			<option value="ASC" <?php echo ($post_order=="ASC") ? 'selected="selected"' : ''; ?>>Oldest</option>			
		</select>
	</fieldset>
	
	<fieldset><legend>Per Page</legend>
		<select name="per_page" id="per-page">
			<option value="20" <?php echo ($per_page==20) ? 'selected="selected"' : ''; ?>>20</option>
			<option value="50" <?php echo ($per_page==50) ? 'selected="selected"' : ''; ?>>50</option>			
			<option value="100" <?php echo ($per_page==100) ? 'selected="selected"' : ''; ?>>100</option>
		</select>
	</fieldset>

	<fieldset><legend>Exact Match</legend>
	<input type="hidden" name="plugin" value="mass_edit"  />
	<span style="padding-left:1em;"><input style="height:1.4em;width:1.4em;" name="exact_match" id="exact-match" class="checkbox" type="checkbox" value="1" <?php echo ($exact_match) ? 'checked="checked"' : ''; ?> /></span>
    	<span style="padding-left:1em;" class=submit><input class=submit type="submit" name="submit" value="<?php _e('Search') ?> &raquo;"  /></span>
    	</fieldset>
    
 </form>

<?php

if ($total) {echo $pagelinks="<p style='clear:left'>[ ".(($total>$per_page) ? "showing ".(($page-1)*$per_page+1)." - ".(($total<$page*$per_page) ? $total : $page*$per_page)." of " : "")."$total posts found ] ".'<span style="padding-left:1em">'.get_page_number_links( $page, $total )."</span></p>";}

if ($bb_posts) {

// lazy cache loading to radically reduce query count
foreach ($bb_posts as $bb_post) {$users[$bb_post->poster_id]=$bb_post->poster_id; $topics[$bb_post->topic_id]=$bb_post->topic_id;}  
bb_cache_users($users);
unset($users); 
$topics=join(',', $topics);
$topics=$bbdb->get_results("SELECT topic_id,topic_title,topic_slug FROM $bbdb->topics WHERE topic_id IN ($topics)");
$topics = bb_append_meta( $topics, 'topic' );
unset($topics);

echo '<form name="deleteposts" id="deleteposts" action="" method="post"> ';
bb_nonce_field('mass-edit-bulk-posts');

echo '<table class="widefat">
<thead>
<tr>';
foreach ($mass_edit_columns as $position) :	
switch ($position) :
case "checkbox" :
    echo '<th scope="col"><input type="checkbox" onclick="checkAll(this,document.getElementById(\'deleteposts\'));" /></th>';
break;
case "excerpt" :   
    echo '<th scope="col" width="90%">' . __('Post Excerpt') . '</th>';
break;   
case "name" :   
   echo '<th scope="col">' .  __('Name') . '</th>';
break;
case "meta" :
   echo '<th scope="col">' . __('Meta') . '</th>';
break;
case 'actions' :   
    echo '<th scope="col" colspan="2">' .  __('Actions') . '</th>';
break;
endswitch;
endforeach;
echo '</tr></thead>';

	foreach ($bb_posts as $bb_post) {
	
	$bb_post_cache[$bb_post->post_id]=$bb_post;		// yes this is naughty but lazy workaround for using internal functions without extra mysql queries

	switch ( $bb_post->post_status ) :
	case 0 : $del_class=''; break;
	case 1 : $del_class= 'deleted'; break;
	case 2 : $del_class= 'spam'; break;
	default: $del_class=apply_filters( 'post_del_class', $bb_post->post_status, $bb_post->post_id );
	endswitch;
?>
  <tr id="post-<?php echo $bb_post->post_id; ?>" <?php alt_class('post', $del_class); ?>>   
<?php  
foreach ($mass_edit_columns as $position) :	
switch ($position) :
  case "checkbox" : ?>
    <td><?php if ( bb_current_user_can('edit_post', $bb_post->post_id) ) { ?><input type="checkbox" name="mass_edit_delete_posts[]" value="<?php echo $bb_post->post_id; ?>" /><?php } ?></td>
  <?php break;    
  case "excerpt" : ?>
    <td><?php echo "<a class=metext href='".mass_edit_get_post_link()."'>[<strong>".get_topic_title($bb_post->topic_id) ."</strong>] ".mass_edit_scrub_text($bb_post->post_text,$post_text,45,$exact_match).'</a>'; ?></td>
<?php break;    
  case "name" : ?>
    <td><a href="<?php echo attribute_escape(get_user_profile_link( $bb_post->poster_id) ); ?>"><?php echo get_user_name( $bb_post->poster_id ); ?></a></td>    
<?php break;    
  case "meta" : ?>
    <td><span class=timetitle title="<?php echo date("r",strtotime(bb_get_post_time())); ?>"><?php printf( __('%s ago'), bb_get_post_time() ); ?></span> 
    	<?php post_ip_link(); ?></td>    
<?php break;    
  case "actions" : ?>	
    <td><a href="<?php post_link(); ?>"><?php _e('View'); ?></a>
    	<?php if ( bb_current_user_can('edit_post', $bb_post->post_id) ) {post_edit_link();} ?></td>
    <td><?php if ( bb_current_user_can('edit_post', $bb_post->post_id) ) {post_delete_link();} ?></td>    
<?php  
  endswitch;
  endforeach;
  echo '</tr>';	
	} // end foreach
	unset($bb_posts);
	?></table>

<?php if ($total) {echo $pagelinks;} ?>

<p class="submit">
<input type="submit" class="deleted" name="mass_edit_delete_button" value="<?php _e('Delete Checked posts &raquo;') ?>" onclick="var numchecked = getNumChecked(document.getElementById('deleteposts')); if(numchecked < 1) { alert('<?php _e("Please select some posts to delete"); ?>'); return false } return confirm('<?php printf(__("You are about to delete %s posts  \\n  \'Cancel\' to stop, \'OK\' to delete."), "' + numchecked + '"); ?>')" />
<input type="submit" class="spam" name="mass_edit_spam_button" value="<?php _e('Mark Checked posts as Spam &raquo;') ?>" onclick="var numchecked = getNumChecked(document.getElementById('deleteposts')); if(numchecked < 1) { alert('<?php _e("Please select some posts to mark as spam"); ?>'); return false } return confirm('<?php printf(__("You are about to mark %s posts as spam \\n  \'Cancel\' to stop, \'OK\' to spam."), "' + numchecked + '"); ?>')" />
<input type="submit" class="normal" name="mass_edit_undelete_button" value="<?php _e('Undelete Checked posts &raquo;') ?>" onclick="var numchecked = getNumChecked(document.getElementById('deleteposts')); if(numchecked < 1) { alert('<?php _e("Please select some posts to delete"); ?>'); return false } return confirm('<?php printf(__("You are about to undelete %s posts  \\n  \'Cancel\' to stop, \'OK\' to undelete."), "' + numchecked + '"); ?>')" />
</form>

<div id="ajax-response"></div>
<?php
	} else {
?>
<p style="clear:both;">
<?php if ($exact_match)  {
echo " <strong>".__('No results found for exact match.')." ";
echo ' <a href="'.attribute_escape( remove_query_arg( 'exact_match' ) ).'">'.__("Try non-exact?").'</a></strong> ';
}
else {echo "<strong>".__('No results found.')."</strong>";} ?>
</p>
<?php
	} // end if ($bb_posts)
?>

</div>

<?php 
}

function mass_edit_topic_limit($per_page) {
if (isset($_GET['per_page'])) {$per_page = intval(substr($_GET['per_page'],0,3));} else {$per_page="20";} 
return $per_page;
}

function mass_edit_get_post_link( $post_id = 0 ) {	// to do, get proper page link for delete posts based on complete post count, not position
	$bb_post = bb_get_post( get_post_id( $post_id ) );
	$page = get_page_number( $bb_post->post_position );
	$link=get_topic_link( $bb_post->topic_id, $page ) . "#post-$bb_post->post_id"; 
	if ($bb_post->post_status) {$link=add_query_arg('view','all',$link);}
	return $link; 	// apply_filters( 'get_post_link', $link, $bb_post->post_id );
}

function mass_edit_list_posts() {
	global $bb_posts, $bb_post;	
	if ( $bb_posts ) : foreach ( $bb_posts as $bb_post ) : ?>
	<li<?php alt_class('post'); ?>>
		<div class="threadauthor">
			<p><strong><?php poster_id_link(); ?></strong><br />
				<small><?php poster_id_type(); ?></small></p>
		</div>
		<div class="threadpost">
			<div class="post"><?php post_text(); ?></div>
			<div class="poststuff">
				<?php printf(__('Posted: %1$s in <a href="%2$s">%3$s</a>'), bb_get_post_time(), get_topic_link( $bb_post->topic_id ), get_topic_title( $bb_post->topic_id ));?> IP: <?php post_ip_link(); ?> <?php post_edit_link(); ?> <?php post_delete_link();?></div>
			</div>
	</li><?php endforeach; endif;
}

function mass_edit_scrub_text($text,$term="",$limit=0,$exact){
// 'script',  'style', 'embed',  'iframe',  'object'  // 'ilayer',  'iframe', 'layer', 
$search = array('@<>@',
		'@<script[^>]*?>.*?</script>@siU',  // Strip out javascript  
               '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
               '@<embed[^>]*?>.*?</embed>@siU',    // embed
               '@<object[^>]*?>.*?</object>@siU',    // object
	       '@<iframe[^>]*?>.*?</iframe>@siU',    // iframe	       
               '@<![\s\S]*?--[ \t\n\r]*>@',        // Strip multi-line comments including CDATA               
               '@</?[^>]*>*@' 		  // html tags
);

while($text != strip_tags($text)) { $text = preg_replace($search, '', $text); }

if ($term) {
	$search = preg_quote($term); if ($exact) {$search="\b$search\b";}
	$text = preg_replace("|.*?(.{0,200})$search(.{0,200}).*|is", "... $1<strong>$term</strong>$2 ...", $text, 1);
}

if ($limit) {
 	        $blah = explode(' ', $text);
 	        if (count($blah) > $limit) { $k = $limit;  $use_dotdotdot = 1; } 
 	        else { $k = count($blah); $use_dotdotdot = 0; }
 	        for ($i=0; $i<$k; $i++) {$excerpt .= $blah[$i].' ';}
 	        $excerpt .= ($use_dotdotdot) ? '...' : '';
 	        $text = $excerpt;
}

$htmlEntities = array_values (get_html_translation_table (HTML_ENTITIES, ENT_QUOTES));
$entitiesDecoded = array_keys  (get_html_translation_table (HTML_ENTITIES, ENT_QUOTES));
$num = count ($entitiesDecoded); 
for ($u = 0; $u < $num; $u++) {  $utf8Entities[$u] = '&#'.ord($entitiesDecoded[$u]).';'; }

return str_replace ($htmlEntities, $utf8Entities, $text);
}

function mass_edit_add_css() { 
if (!(isset($_GET['plugin']) && $_GET['plugin']=="mass_edit")) {return;}

$mass_edit_options = bb_get_option('mass_edit_options');	
if (!isset($mass_edit_options['mass_edit_css'])) {
	$mass_edit_options['mass_edit_css']= 'fieldset {display:inline;}
	.submit input {cursor:pointer;cursor:hand;} 
	table td a {line-height:160%;border:0;text-decoration:none;}
	table.widefat tbody tr:hover { background: #ccddcc; }
	.timetitle {border-bottom: 1px dashed #aaa; cursor: help;}
	.metext,.metext:hover,.metext:visited {line-height:120%;text-decoration:none;border:0;color:black;overflow:hidden;display:block;}
	.bozo { background: #eeee88; }
	.alt.bozo { background: #ffff99; }
	.deleted,INPUT.deleted { background: #ee8888; }
	.alt.deleted { background: #ff9999; }
	.spam,INPUT.spam {background:#FFF380;}
	.alt.spam {background:#FAF8CC;}
	.normal,INPUT.normal {background:#D4E8D4;}';	
bb_update_option('mass_edit_options',$mass_edit_options);
}
echo '<style type="text/css">'.$mass_edit_options['mass_edit_css'].'</style>';

?><script type="text/javascript">
<!--
function checkAll(source,form)
{
	for (i = 3, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(source.checked ==false)
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
	}
}

function getNumChecked(form)
{
	var num = 0;
	for (i = 3, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				num++;
		}
	}
	return num;
}
//-->
</script>
<?php
} 
add_action( 'bb_admin_head', 'mass_edit_add_css'); 
?>