<?php
/*
Plugin Name: Best Answer
Plugin URI: http://bbpress.org/plugins/topic/best-answer
Description: Allows the topic starter or moderators to select which reply is a "Best Answer" to the original post.
Author: _ck_
Author URI: http://bbShowcase.org
Version: 0.0.5
*/

$best_answer['automatic']=true;	 	 //  set to false if you want to place manually in post.php template via   do_action('best-answer');
$best_answer['forums']="";	 //  comma seperated list of forum id numbers to have best answer  (set blank for all)
$best_answer['max']=5;		 	 //  how many posts per topic can be designated as a "best answer"
$best_answer['display_first']=true;	 //  should Best Answer(s) be moved to the start of the topic? set false to disable
$best_answer['text']=__('Best Answer');	//  text for link  (if any)
$best_answer['add_views']=true;		//  add the two views 
$best_answer['use_label']=true;		// true or "left" for title on left,  "right" for label on right
$best_answer['label']="<span class='best_answer'></span>";   // this allows the image sprite to work with topic labels too
$best_answer['icon']=bb_get_option('uri').trim(str_replace(array(trim(BBPATH,"/\\"),"\\"),array("","/"),dirname(__FILE__)),' /\\').'/best-answer.png';  // image auto-discovery
$best_answer['css']="
	.best_answer {text-decoration:none; border:0; white-space:nowrap; display:block; margin:1em 0;		
		border:0; text-decoration:none; width:20px; height:16px;
		background: url(".$best_answer['icon'].") no-repeat scroll 0px 1px transparent;} 
	span.best_answer {margin: 0px; float: left;}
	#thread .threadauthor .best_answer {padding-left: 20px;}
	a.best_answer, a.best_answer:link {color:#999;}
	a.best_answer:hover {color:green;}	
	a.best_answer_selected {color: green;}
	a.best_answer_selected:hover {color:red;}		
	#thread li.best_answer_background { background-color: transparent; }
	#thread li.best_answer_background .threadpost { background-color: #afa; }
	#thread li.alt.best_answer_background .threadpost { background-color: #afa; }
";

/*
 note: because of lack of foresight in the bbPress output functions you have to edit the topic.php template to change post colors
 change: <?php foreach ($posts as $bb_post) : $del_class = post_del_class(); ?>
 to:   <?php foreach ($posts as $bb_post) : $del_class = apply_filters('best_answer_class',post_del_class()); ?>
*/

/*  	stop editing here 	 */

if (!empty($best_answer['forums']) && !is_array($best_answer['forums'])) {(array) $best_answer['forums']=explode(',',$best_answer['forums']); $best_answer['forums']=array_flip($best_answer['forums']);}
if ($best_answer['display_first']) {add_filter( 'get_post_link', 'best_answer_post_link', 255, 2 );}
if (is_topic()) {	add_action( 'bb_topic.php', 'best_answer_init' ); }
elseif (!is_bb_feed()) {add_filter('topic_title', 'best_answer_title',255);}		
add_action('bb_head','best_answer_head'); 
if ($best_answer['add_views']) {	// doing it this way hides them from the default view list
	$query=array('started' => '>0','append_meta'=>false,'sticky'=>false,'topic_status'=>'all','order_by'=>1,'per_page'=>1);
	bb_register_view("best-answer","Questions with a best answer",$query);
	bb_register_view("no-best-answer","Questions without a best answer",$query);
	add_action( 'bb_custom_view', 'best_answer_views' );
}

function best_answer_init() {		
	global $best_answer, $topic, $bb_current_user, $posts, $page; 
	if (!empty($best_answer['forums']) && !isset($best_answer['forums'][$topic->forum_id])) {return;}	
	add_action('best_answer','best_answer'); 
	add_action('best-answer','best_answer'); 
	add_filter('best_answer_class','best_answer_class');	
	if ($best_answer['automatic']) {
		add_filter( 'post_author_title', 'best_answer_filter',300); 
		add_filter( 'post_author_title_link', 'best_answer_filter',300);
	}
	if ((!empty($bb_current_user->ID) && $bb_current_user->ID==$topic->topic_poster) || bb_current_user_can('moderate')) {$best_answer['can_edit']=true;}
	else {$best_answer['can_edit']=false;}	
	if (empty($topic->best_answer)) {$topic->best_answer=array();} elseif (!is_array($topic->best_answer)) {(array) $topic->best_answer=explode(',',$topic->best_answer);}
	$topic->best_answer=array_flip($topic->best_answer);		// speedup by using post id as key	
	if (!empty($topic->topic_id) && !empty($_GET['best_answer']) && $best_answer['can_edit']) {		
		$value=intval($_GET['best_answer']);
		if (isset($topic->best_answer[$value])) {unset($topic->best_answer[$value]);} 
		else {if ($best_answer['max']==1) {$topic->best_answer=array();} $topic->best_answer[$value]=$value;}
		if (empty($topic->best_answer)) {bb_delete_topicmeta($topic->topic_id,'best_answer');}
		else {bb_update_topicmeta($topic->topic_id,'best_answer',implode(',',array_flip($topic->best_answer)));}
		wp_redirect(get_post_link($value)); exit;	
	}
	$best_answer[$topic->topic_id]=$topic->best_answer;
	$best_answer['count']=count($topic->best_answer);
	if ($best_answer['display_first'] && !empty($best_answer[$topic->topic_id])) {		// move best answer(s) to top of topic
		if ($page==1) {$question=$posts[0]; unset($posts[0]);}
		foreach ($posts as $key=>$bb_post) {if ($bb_post->post_status==0 && isset($best_answer[$topic->topic_id][$bb_post->post_id])) {unset($posts[$key]);}}
		if ($page==1) {
		foreach ($best_answer[$topic->topic_id] as $post_id=>$ignore) {$best=bb_get_post($post_id); if ($best->post_status==0) {array_unshift($posts,$best);} else {unset($best_answer[$topic->topic_id][$post_id]);}}
		array_unshift($posts,$question);
		}
	}
}	

function best_answer_head() {global $best_answer; echo '<style type="text/css">'.$best_answer['css'].'</style>';} 	 // css style injection + javascript 

function best_answer_filter($titlelink) {echo $titlelink; best_answer(); return '';}	// only if automatic post inserts are selected

function best_answer_class($class) {
	global $best_answer, $topic, $bb_post; 
	if ($bb_post->post_status==0 && isset($best_answer[$topic->topic_id][$bb_post->post_id])) {$class="$class best_answer_background";} 
	return $class;
}

function best_answer() {
	global $best_answer, $topic, $bb_current_user, $bb_post; 	
	if ($bb_post->post_status!=0) {return;}
	if ($topic->topic_posts>1 && $bb_post->post_position>1 && (empty($best_answer['forums']) || isset($best_answer['forums'][$topic->forum_id]))) {	
		if (!$best_answer['can_edit']) { 
			if (isset($best_answer[$topic->topic_id][$bb_post->post_id])) {
				echo "<div class='best_answer'>".$best_answer['text']."</div>";
			}
		} else {
			$url=add_query_arg(array('best_answer'=>$bb_post->post_id)); 	//  ,'r'=>rand(0,9999)))."#post-$bb_post->post_id";
			if (isset($best_answer[$topic->topic_id][$bb_post->post_id])) {
				echo "<a title='click to undo' href='$url' class='best_answer best_answer_selected'>".$best_answer['text']."</a>";
			} elseif ($best_answer['max']==1 || $best_answer['count']<$best_answer['max']) {
				echo "<a title='click to select as best answer' href='$url' class='best_answer'>".$best_answer['text']."?</a>";
			} 
		}
	}
}

function best_answer_post_link($link, $post_id ) {	// this needs to be rewritten for better performance somehow
global $best_answer; static $posts_per_page; 
	$post=bb_get_post($post_id); 
	if (empty($posts_per_page)) {$posts_per_page=bb_get_option('page_topics');}	// speedup
	if ($post->post_position>$posts_per_page) {	 // is it beyond page 1 typically?
		$topic=get_topic($post->topic_id);
		if (!empty($topic->best_answer)) {
			if (!empty($best_answer['forums']) && !isset($best_answer['forums'][$topic->forum_id])) {return $link;}
			if (!is_array($topic->best_answer)) {(array) $topic->best_answer=explode(',',$topic->best_answer); $topic->best_answer=array_flip($topic->best_answer);}
			if (isset($topic->best_answer[$post_id])) {$link=get_topic_link( $post->topic_id, 1) . "#post-$post_id";}    // change link to page 1 for best answers
		}
	}
return $link;
}

function best_answer_title( $title ) {
	global $best_answer, $topic;
	if (isset($topic->best_answer) && !empty($best_answer['use_label'])) {
		if ($best_answer['use_label']==="right") {$title=$title." ".$best_answer['label'];}
		else {$title=$best_answer['label'].$title;}				
	}
	return $title;
} 

function best_answer_views( $view ) {
global $bbdb, $topics, $view_count, $page;
if ($view=='best-answer' || $view=='no-best-answer') {
	$limit = bb_get_option('page_topics');
	$offset = ($page-1)*$limit;
	$where = apply_filters('get_latest_topics_where',"WHERE topic_status=0 ");
	if ($view=='best-answer') {
	if (defined('BACKPRESS_PATH')) {		
		$query = " FROM $bbdb->topics AS t1 
			LEFT JOIN $bbdb->meta as t2 ON t1.topic_id=t2.object_id 
			$where AND object_type='bb_topic'  AND meta_key='best_answer' ";	
	} else {		
		$query = " FROM $bbdb->topics AS t1 
			LEFT JOIN $bbdb->topicmeta as t2 ON t1.topic_id=t2.topic_id 
			$where AND meta_key='best_answer' ";
	}
	} else {
	if (defined('BACKPRESS_PATH')) {		
		$query = " FROM $bbdb->topics $where AND topic_id NOT IN 
			(SELECT object_id as topic_id FROM $bbdb->meta WHERE object_type='bb_topic' AND meta_key='best_answer')  ";	
	} else {		
		$query = " FROM $bbdb->topics $where AND topic_id NOT IN 	
			(SELECT topic_id FROM $bbdb->topicmeta WHERE meta_key='best_answer')  ";	
	}	
	}
	
	$restrict = " ORDER BY topic_time DESC LIMIT $limit OFFSET $offset";

	$view_count  = $bbdb->get_var("SELECT count(*) ".$query);	
	$topics = $bbdb->get_results("SELECT * ".$query.$restrict);
	$topics = bb_append_meta( $topics, 'topic' );
	bb_cache_last_posts();
}
}

?>