<?php
/*
Plugin Name: bbPress Attachments
Plugin URI: http://bbpress.org/plugins/topic/bb-attachments
Description: Gives members the ability to upload attachments on their posts.
Author: _ck_
Author URI: http://bbShowcase.org
Version: 0.2.7

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

Donate: http://bbshowcase.org/donate/
*/

global $bb_attachments;
$bb_attachments['role']['see']="read"; 		 // minimum role to see list of attachments = read/participate/moderate/administrate
$bb_attachments['role']['inline']="read";    // minimum role to view inline reduced images = read/participate/moderate/administrate
$bb_attachments['role']['download']="participate";  // minimum role to download original = read/participate/moderate/administrate
$bb_attachments['role']['upload']="participate";  // minimum role to upload = participate/moderate/administrate (times out with post edit time)
$bb_attachments['role']['delete']="moderate";  // minimum role to delete = read/participate/moderate/administrate

$bb_attachments['allowed']['extensions']['default']=array('gif','jpeg','jpg','pdf','png','txt');	// anyone who can upload can submit these
$bb_attachments['allowed']['extensions']['moderate']=array('gif','gz','jpeg','jpg','pdf','png','txt','zip');	// only if they can moderate
$bb_attachments['allowed']['extensions']['administrate']=array('bmp','doc','gif','gz','jpeg','jpg','pdf','png','txt','xls','zip');	// only if they can administrate

$bb_attachments['allowed']['mime_types']['default']=array('text/plain', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf', 'application/x-pdf');  // for anyone that can upload
$bb_attachments['allowed']['mime_types']['moderate']=array('text/plain', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf', 'application/x-pdf', 'application/zip', 'application/x-zip' , 'application/x-gzip');
$bb_attachments['allowed']['mime_types']['administrate']=array('application/octet-stream', 'text/plain', 'text/x-c', 'image/bmp', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf', 'application/x-pdf', 'application/zip', 'application/x-zip' , 'application/x-gzip');

$bb_attachments['max']['size']['default']=100*1024;	   // general max for all type/roles, in bytes (ie. 100k)
$bb_attachments['max']['size']['jpg'] =150*1024;	   	   // size limit override by extension, bytes (ie. 200k)
$bb_attachments['max']['size']['png']=150*1024;		   // size limit override by extension, bytes (ie. 200k)
$bb_attachments['max']['size']['moderate']=200*1024;	   // size limit override by role, bytes (ie. 250k) - note this overrides ALL extension limits
$bb_attachments['max']['size']['administrate']=500*1024; // size limit override by role, bytes (ie. 500k) - note this overrides ALL extension limits

$bb_attachments['max']['per_post']['default']=6;		// how many files can be attached per post
$bb_attachments['max']['per_post']['moderate']=10;	// override example$bb_attachments['max']['per_post']['administrate']=20;	// you don't even need to set for every role, this is just an example
$bb_attachments['max']['uploads']['default']=6;		// how many files can be uploaded at a time, in case you want to set per_post high
$bb_attachments['max']['uploads']['moderate']=10;	// and again, this is optional per extra roles

$bb_attachments['max']['filename']['default']=40;	// maximum length of filename before auto-trim
$bb_attachments['max']['filename']['administrate']=80;	// override

$bb_attachments['inline']['width']=590;		// max inline image width in pixels (for display, real width unlimited)
$bb_attachments['inline']['height']=590;		// max inline image height in pixels (for display, real height unlimited)
$bb_attachments['inline']['solution']="resize";	// resize|frame - images can be either resized or CSS framed to meet above requirement
									// only resize is supported at this time
$bb_attachments['inline']['auto']=true;		// auto insert uploaded images into post

$bb_attachments['style']=".bb_attachments_link, .bb_attachments_link img {border:0; text-decoration:none; background:none;} #thread .post li {clear:none;}";

// the following is for Amazon S3 use, get key+secret here: https://aws-portal.amazon.com/gp/aws/developer/account/index.html#AccessKey
$bb_attachments['aws']['enable']=false;			      // Amazon AWS S3 Simple Storage Service - http://amazon.com/s3
$bb_attachments['aws']['key']="12345678901234567890";				  // typically 20 letters+numbers
$bb_attachments['aws']['secret']="1234567890123456789012345678901234567890";	  // must be EXACTLY 40 characters long

// stop editing here (advanced user settings below)

// don't edit the following aws bucket or aws url unless you know what you are doing and have aws experience
// if you rename the bucket, files are NOT moved automatically - you must do it manually via an S3 utility
$bb_attachments['aws']['bucket']=strtolower("bb-attachments.".preg_replace("/^(www?[0-9]*?\.)/i","",$_SERVER['HTTP_HOST']));   

// base url to amazon for retrieval, or may be a cname mirror off your own domain
// http://docs.amazonwebservices.com/AmazonS3/2006-03-01/VirtualHosting.html#VirtualHostingCustomURLs
// cname example: bb-attachments.yoursite.com CNAME bb-attachments.yoursite.com.s3.amazonaws.com
$bb_attachments['aws']['url']="http://".$bb_attachments['aws']['bucket'].".s3.amazonaws.com/";  

$bb_attachments['path']=dirname($_SERVER['DOCUMENT_ROOT'])."/bb-attachments/";  //  make *NOT* WEB ACCESSABLE for security

$bb_attachments['upload_on_new']=true;	// allow uploads directly on new posts (set FALSE if incompatible for some reason)

$bb_attachments['icons']=array('default'=>'default.gif','bmp'=>'img.gif','doc'=>'doc.gif','gif'=>'img.gif','gz'=>'zip.gif','jpeg'=>'img.gif','jpg'=>'img.gif','pdf'=>'pdf.gif','png'=>'img.gif','txt'=>'txt.gif','xls'=>'xls.gif','zip'=>'zip.gif');

$bb_attachments['icons']['url']=bb_get_option('uri').trim(str_replace(array(trim(BBPATH,"/\\"),"\\"),array("","/"),dirname(__FILE__)),' /\\').'/icons/'; 
$bb_attachments['icons']['path']=rtrim(dirname(__FILE__),' /\\').'/icons/'; 

$bb_attachments['title']=" <img class='bb_attachments_link' title='attachments' align='absmiddle' src='".$bb_attachments['icons']['url'].$bb_attachments['icons']['default']."' />"; // text, html or image to show on topic titles if has attachments

$bb_attachments['max']['php_upload_limit']=min(bb_attachments_l2n(ini_get('post_max_size')), bb_attachments_l2n(ini_get('upload_max_filesize'))); // internal php upload limit - only edit if you know what you are doing

$bb_attachments['status']=array("ok","deleted","failed","denied extension","denied mime","denied size","denied count","denied duplicate","denied dimensions");

$bb_attachments['errors']=array("ok","uploaded file exceeds UPLOAD_MAX_FILESIZE in php.ini","uploaded file exceeds MAX_FILE_SIZE in the HTML form",
"uploaded file was only partially uploaded","no file was uploaded","temporary folder missing","failed to write file to disk","file upload stopped by PHP extension");

$bb_attachments['db']="bb_attachments";   //   $bbdb->prefix."attachments";  // database name - force to "bb_attachments" if you need compatibility with an old install

// really stop editing!

add_action( 'bb_init', 'bb_attachments_init');
add_action( 'bb_delete_post', 'bb_attachments_recount');
add_filter('post_text', 'bb_attachments_bbcode',250);	
if (isset($_FILES['bb_attachments']))  {
if (defined('BACKPRESS_PATH')) {add_action( 'bb-post.php', 'bb_attachments_process_post');}
else {add_action( 'bb_post.php', 'bb_attachments_process_post');}
}
bb_register_activation_hook(str_replace(array(str_replace("/","\\",BB_PLUGIN_DIR),str_replace("/","\\",BB_CORE_PLUGIN_DIR)),array("user#","core#"),__FILE__), 'bb_attachments_install');

function bb_attachments_init() {
global $bbdb, $bb_attachments;

if (isset($_GET['bb_attachments_diagnostic']) || isset($_GET['bb_attachments_debug'])) {include('debug.php');}

if (isset($_GET['bbat_delete'])) {bb_attachments_delete();}

if (isset($_GET['bb_attachments'])) {
	if (isset($_GET['bbat'])) {
		if (isset($_GET['inline'])) {bb_attachments_inline();}
		else {bb_attachments_download();}
	} else { 
		if (bb_attachments_location()!='edit.php') {
			bb_repermalink();
			bb_send_headers();					
			bb_get_header();		
			bb_attachments($post_id);
			bb_get_footer();
			exit(); 
		}
	}
}

if ($bb_attachments['style']) {add_action('bb_head', 'bb_attachments_add_css');}	// add css if present (including Kakumei  0.9.0.2 LI fix!)

if ($bb_attachments['title'] && !is_topic() && !is_bb_feed()) {add_filter('topic_title', 'bb_attachments_title',200);} 

if (isset($_GET["new"]) || is_topic() || is_forum()) {
	add_action( 'bb_topic.php', 'bb_attachments_cache' );	
	add_filter('post_text', 'bb_attachments_post_footer',4);
	add_filter('post_edit_uri', 'bb_attachments_link');

	if (bb_current_user_can($bb_attachments['role']['upload'])) {
		add_action('post_edit_form','bb_attachments');		// auto-insert on post edit form

		if ($bb_attachments['upload_on_new']) {
			add_action('post_form','bb_attachments_upload_form');	 // auto-insert on new post form 
			add_action('pre_post_form','bb_attachments_enctype');	 // multipart workaround on new post form

			// insane bbPress workaround - adds multipart enctype to the new post form via uri patch
			function bb_attachments_enctype() {
			global $topic,$forum;
				if ( ( is_topic() && bb_current_user_can( 'write_post', $topic->topic_id ) ) || ( !is_topic() && bb_current_user_can( 'write_topic', $forum->forum_id ) ) ) {					
					add_filter( 'bb_get_uri', 'bb_attachments_uri_10',999,3);					
					add_filter( 'bb_get_option_uri','bb_attachments_uri',999);
					add_action('post_form','bb_attachments_remove_uri',999);
					add_action('post_post_form','bb_attachments_remove_uri',999);
				}
			}
			function bb_attachments_uri_10($uri,$resource='',$context='') {
				if (strpos($uri,"bb-post.php")!==false && $context && defined('BB_URI_CONTEXT_FORM_ACTION') && $context==BB_URI_CONTEXT_FORM_ACTION) {
					bb_attachments_remove_uri();
					return $uri.'"  enctype="multipart/form-data" hack="';
				}
				return $uri;
			}			
			function bb_attachments_uri($uri) { 
				// if (strpos($uri,'bb-post.php')===false) {return $uri;}				
				bb_attachments_remove_uri();
				return $uri. 'bb-post.php"  enctype="multipart/form-data" hack="';
			} 
			function bb_attachments_remove_uri($x="") {
				remove_filter( 'bb_get_option_uri','bb_attachments_uri',999); 
				remove_filter( 'bb_get_uri', 'bb_attachments_uri_10',999);
			}
		}
	}					
} // end else

}

function bb_attachments_add_css() { global $bb_attachments;  echo '<style type="text/css">'.$bb_attachments['style'].'</style>';} // inject css

function bb_attachments($post_id=0) {
global $bb_attachments_on_page;
if (isset($bb_attachments_on_page)) {return;} else {$bb_attachments_on_page=true;}	// only insert once per page -> pre 0.9.0.2

if ($post_id==0) {if (isset($_GET['bb_attachments'])) {$post_id=intval($_GET['bb_attachments']);} else {global $bb_post; $post_id=$bb_post->post_id;}}

if ($post_id) {
	$bb_post=bb_get_post($post_id);
	if (bb_attachments_location()!='edit.php') {
	echo "<h3 class='bbcrumb'><a href='".bb_get_option('uri')."'>".bb_get_option('name')."</a> &raquo; <a href='".get_topic_link()."'>".get_topic_title( $bb_post->topic_id )."</a> &raquo; <a href='".get_post_link($bb_post->post_id)."'>".__('Post')." $bb_post->post_position</a> &raquo;  ".__('Attachments')."</h3>";
	}
	echo "<div class='indent'>";
	if (isset($_FILES['bb_attachments'])) {
	bb_attachments_process_post(intval($_GET['bb_attachments']),1); 
	echo "<br />";
	}	
	echo bb_attachments_post_attachments($post_id);
	echo "<br />";
	bb_attachments_upload_form($post_id);
	echo "<br />";
	echo "</div>";		
}
}

function bb_attachments_post_attachments($post_id=0) {
global $bbdb, $bb_attachments, $bb_attachments_cache, $bb_current_user; 

$output="";	
if ($post_id && ($bb_attachments['role']['see']=="read" || bb_current_user_can($bb_attachments['role']['see']))) {
	
	$time=time()-60; $can_delete=false; $self=false; $admin=false; $filter=true;   // " AND status = 0 "; 	// speedup checks with flag	
	if ($bb_current_user->ID==get_post_author_id( $post_id )) {$self=true;}
	if ((!is_topic() || isset($_GET['bb_attachments'])) && bb_current_user_can('moderate')) {$filter=""; $admin=bb_current_user_can('administrate');} 	 
	if (bb_current_user_can($bb_attachments['role']['delete']) && bb_current_user_can( 'edit_post', $post_id)) {$can_delete=true;}
	
	$location = bb_attachments_location();	 $can_inline=true;
	if (!($bb_attachments['role']['inline']=="read" || bb_current_user_can($bb_attachments['role']['inline']))) {$can_inline=false;}
		
	if (!isset($bb_attachments_cache[$post_id])) {
		$bb_attachments_cache[$post_id]=$bbdb->get_results("SELECT * FROM ".$bb_attachments['db']." WHERE post_id = $post_id ORDER BY time DESC LIMIT 999");
	}			

	if (count($bb_attachments_cache[$post_id])) {
		foreach ($bb_attachments_cache[$post_id] as $attachment) { 
			$showerror=($self && $attachment->time>$time) ? true : false;
			if ($attachment->status==0 || empty($filter) || $showerror) {
				$attachment->filename=stripslashes($attachment->filename);
				$output.="<li>"; 
				$output.="<span".(($attachment->status>0) ? " class='deleted' ": "")."> "; 
				if ($attachment->status>0) {$icon="missing.gif";}
				else {if (isset($bb_attachments['icons'][$attachment->ext])) {$icon=$bb_attachments['icons'][$attachment->ext];} else {$icon=$bb_attachments['icons']['default'];}}
				$output.=" <img align='absmiddle' title='".$attachment->ext."' src='".$bb_attachments['icons']['url'].$icon."' /> ";
				
				if ($attachment->status>0 && (empty($filter) || $showerror)) {					
					$output.=" [".__($bb_attachments['status'][$attachment->status])."] $attachment->filename ";
				}
						
				if ($attachment->status==0) {								
					$output.=" "."<a href='" . 
						attribute_escape(
						add_query_arg('bbat',$attachment->id,
						add_query_arg('bb_attachments',$attachment->post_id,
						remove_query_arg(array('bb_attachments','bbat','bbat_delete'))))) 
						."'>".$attachment->filename."</a> "." ";				
				}						
				
				$output.=" <span class='num'>(".round($attachment->size/1024,1)." KB";				
				if ($attachment->status<2) {$output.=", ".bb_number_format_i18n($attachment->downloads)." ".__('downloads');}
				$output.=")</span> ";
				
				if ($attachment->time<$time) {$output.=" <small>".bb_since($attachment->time)." ".__('old')."</small> ";}
								
				if ($admin) {				
					$output.=' [<a href="'.attribute_escape(bb_get_option('uri').'bb-admin/view-ip.php?ip='.long2ip($attachment->user_ip)) . '">'.long2ip($attachment->user_ip).'</a>] ';
				}	
				
				if ($attachment->status==0 && $can_delete) {
						$output.=' [<a onClick="return confirm('."'".__('Delete')." $attachment->filename ?"."'".')" href="'.add_query_arg('bbat_delete',$attachment->id).'">x</a>] ';
				}

				if ($attachment->status==0 && $location=="edit.php" && $can_inline) {				
					$fullpath=$bb_attachments['path'].floor($attachment->id/1000)."/".$attachment->id.".".$attachment->filename;
					if (list($width, $height, $type) = getimagesize($fullpath)) {								
						$output.=" [<strong><a href='#' onclick='bbat_inline_insert($attachment->post_id,$attachment->id); return false;'>".__("INSERT")."</a></strong>] ";	
					}
				}						
	
				$output.=" </span></li>";
			}
		}
	}
if ($output) {$output="<h3>".__("Attachments")."</h3><ol>".$output."</ol>";}
if ($location=="edit.php") {
$output.='<scr'.'ipt type="text/javascript" defer="defer">
	function bbat_inline_insert(post_id,id) {
	bbat_field = document.getElementsByTagName("textarea")[0];
	bbat_value="[attachment="+post_id+","+id+"]";
	if (document.selection) {bbat_field.focus(); sel = document.selection.createRange();sel.text = bbat_value;}
	else if (bbat_field.selectionStart || bbat_field.selectionStart == "0") {var startPos = bbat_field.selectionStart; var endPos = bbat_field.selectionEnd;
		bbat_field.value = bbat_field.value.substring(0, startPos)+ bbat_value+ bbat_field.value.substring(endPos, bbat_field.value.length);
	} else {bbat_field.value += bbat_value;}
return false;} </script>';
}
}
if (is_bb_feed()) {$output=wp_specialchars($output);}
return $output;
}

function bb_attachments_bbcode($text) {
global $bb_attachments,$bb_attachments_cache;  $uri=bb_get_option('uri');
if ($bb_attachments['aws']['enable']) {	// if AWS S3 enabled, do direct inline images if possible to reduce bbpress reloading
	if (preg_match_all("/\[attachment=([0-9]+?)\,([0-9]+?)\]/sim",$text,$matches,PREG_SET_ORDER)) {
		foreach ($matches as $match) {			
			$file=$bb_attachments_cache[$match[1]][$match[2]]; 
			$file->filename.=".resize";
			$path=$bb_attachments['path'].floor($file->id/1000)."/";			
			$fullpath=$path.$file->id.".".$file->filename;
			if (file_exists($fullpath)) {	// it's been resized, so it's likely on AWS, show directly
				$aws=$bb_attachments['aws']['url'].$file->id.'.'.$file->filename;
				$replace="<a class='bb_attachments_link' href='$uri?bb_attachments=".$match[1]."&bbat=".$match[2]."'><img  src='$aws' /></a>";
				if (is_bb_feed()) {$replace=wp_specialchars($replace);}
				$text=str_replace($match[0],$replace,$text);
			}
		}
	}	
}
// clean up anything left with the regular call to the inline function
$replace="<a class='bb_attachments_link' href='$uri?bb_attachments=$1&bbat=$2'><img  src='$uri?bb_attachments=$1&bbat=$2&inline' /></a>";
if (is_bb_feed()) {$replace=wp_specialchars($replace);}
$text=preg_replace("/\[attachment=([0-9]+?)\,([0-9]+?)\]/sim",$replace,$text);
return $text;
}

function bb_attachments_process_post($post_id=0,$display=0) {
global $bbdb, $bb_attachments;

if (!$post_id) {$post_id=intval($_GET['bb_attachments']);}	// only can upload if user is allowed to edit post
$user_id=bb_get_current_user_info( 'id' );
if (!isset($_FILES['bb_attachments']) || !is_array($_FILES['bb_attachments']) || !$user_id || !$post_id || !bb_current_user_can('edit_post',$post_id) || !bb_current_user_can($bb_attachments['role']['upload'])) {return;}	

$user_ip=$_SERVER["REMOTE_ADDR"];  	// $GLOBALS["HTTP_SERVER_VARS"]["REMOTE_ADDR"];
$time=time();	$inject="";					

$bb_post=bb_get_post($post_id);  $topic_id=$bb_post->topic_id; 	// fetch related topic
$topic_attachments=intval(bb_get_topicmeta($topic_id,"bb_attachments"));	// generally how many on topic (may be off if post moved)
$count = intval($bbdb->get_var("SELECT COUNT(*) FROM ".$bb_attachments['db']." WHERE post_id = $post_id AND status = 0")); // how many currently on post
$offset=0;	// counter for this pass
$strip = array(' ','`','"','\'','\\','/','..','__');  // filter for filenames
$maxlength=bb_attachments_lookup($bb_attachments['max']['filename']);
reset($_FILES);

$output="<h3>".__("Uploads")."</h3><ol>";	// start output
while(list($key,$value) = each($_FILES['bb_attachments']['name'])) {
	if(!empty($value)){ 	
		
		// don't trust these, check after upload $_FILES['bb_attachments']['type']   $_FILES['bb_attachments']['size']			
		
		$filename=trim(str_replace($strip,'_',stripslashes($value)));	// sanitize filename further ???			
		if (empty($filename)) {$filename="unknown";}
			
		if (intval($_FILES['bb_attachments']['error'][$key])==0 && $_FILES['bb_attachments']['size'][$key]>0) {		
		
			$ext = (strrpos($filename, '.')===false) ? "" : trim(strtolower(substr($filename, strrpos($filename, '.')+1)));
						
			if (strlen($filename)>$maxlength) {$filename=substr($filename,0,$maxlength-strlen($ext)+1).".".$ext;}	// fix filename length					
			
			$tmp=$bb_attachments['path'].md5(rand(0,99999).time().$_FILES['bb_attachments']['tmp_name'][$key]);	// make random temp name that can't be guessed

			if (@is_uploaded_file($_FILES['bb_attachments']['tmp_name'][$key]) && @move_uploaded_file($_FILES['bb_attachments']['tmp_name'][$key], $tmp)) {    
				$size=filesize($tmp);	
				$mime=bb_attachments_mime_type($tmp); 				
   				$status=0; $id=0;
   			} else {
   				$status=2;	//   file move to temp name failed for some unknown reason
   				$size=$_FILES['bb_attachments']['size'][$key];	// we'll trust the upload sequence for the size since it doesn't matter, it failed
   				$mime=""; $id=0;
   			}
   			
   			if ($status==0 && !in_array($ext,bb_attachments_lookup($bb_attachments['allowed']['extensions']))) {$status=3;}	// disallowed extension
   			if ($status==0 && !in_array($mime,bb_attachments_lookup($bb_attachments['allowed']['mime_types']))) {$status=4;}	// disallowed mime
   			if ($status==0 && $size>bb_attachments_lookup($bb_attachments['max']['size'],$ext)) {$status=5;}	 // disallowed size	  					
   			if ($status==0 && ($count+1)>bb_attachments_lookup($bb_attachments['max']['per_post'])) {$status=6;}	 // disallowed attachment count
   
    			if ($size>0 && $filename) {	// we still save the status code if any but don't copy file until status = 0
      								
				$failed=$bbdb->get_var("
				INSERT INTO ".$bb_attachments['db']." ( time  , post_id , user_id, user_ip, status , size , ext , mime , filename )
				VALUES ('$time', '$post_id' ,  '$user_id' , inet_aton('$user_ip') , $status, '$size', '".addslashes($ext)."', '$mime', '".addslashes($filename)."')				
				");
				
				if ($status==0 && !$failed) {$id=intval($bbdb->get_var("SELECT LAST_INSERT_ID()"));}	// fetch the assigned unique id #
				
				if ($failed || !$id) {$status=2;}		// db failure ?
										
				if ($status==0) {  // successful db insert - bbdb returns NULL on success so that !NULL is it's wierd way					
			
					$dir=$bb_attachments['path'].(floor($id/1000));
					
					if (function_exists('get_current_user') && function_exists('posix_setuid')) {	// try to set user's id so file/dir creation is under their account
						$current=get_current_user();  
						if (!($current && !in_array($current,array("nobody","httpd","apache","root")) && strpos(__FILE__,$current))) {$current="";}
						$x=posix_getuid ();
						 if (0 == $x && $current) {$org_uid = posix_getuid(); $pw_info = posix_getpwnam ($current); $uid = $pw_info["uid"];  posix_setuid ($uid);}
					}									
					
					if (!file_exists($dir)) {		 // check for sub-directory based on file number 0,1,2,3,4 etc.  
						$oldumask = umask(0);
						@mkdir($dir, 0755);		// I've found that as long as the PARENT is 777, the children don't have to be
						umask($oldumask);						
					} 
					
					$file=$dir."/".$id.".".$filename;					
					
					// file is commited here
								
					if (!$failed && $id>0 && file_exists($tmp)) {    
						@rename($tmp,$file);	// now it's officially named
						@chmod($file,0777);   	// make accessable via ftp for ease of management
						
						if ($bb_attachments['aws']['enable']) {bb_attachments_aws("$dir/","$id.$filename",$mime);}    // copy to S3
						                        			
						$count++; $offset++;		// count how many successfully uploaded this time							
					} else {			
						$status=2;	// failed - not necessarily user's fault, could be filesystem	
					}
					
					if (isset($org_uid) && $org_uid>0 && function_exists('posix_setuid')) {posix_setuid($org_uid);}
				} 
			else    {	
				    	if ($status==0) {$status=2;}	// failed for db?							    		   
				}
			}					
		} // end $_FILES['error'][$key])
		else {$status=2;}
		if (!empty($tmp) && file_exists($tmp)) {@unlink($tmp);}	// never, ever, leave temporary file behind for security
		if ($status>0) {
			if ($id>0) {$bbdb->query("UPDATE ".$bb_attachments['db']." SET 'status' = $status WHERE 'id' = $id");}
			$error=""; if ($_FILES['bb_attachments']['error'][$key]>0) {$error=" (".$bb_attachments['errors'][$_FILES['bb_attachments']['error'][$key]].") ";}
			$output.="<li><span style='color:red'><strong>$filename "." <span class='num'>(".round($size/1024,1)." KB)</span> ".__('error:')." ".$bb_attachments['status'][$status]."</strong>$error</span></li>";
		} else {			
			$output.="<li><span style='color:green'><strong>$filename "." <span class='num'>(".round($size/1024,1)." KB)</span> ".__('successful')."</strong></span></li>";			 
			if ($bb_attachments['inline']['auto'] && list($width, $height, $type) = getimagesize($file)) {
		 	if ($display) {
		 		$location = bb_attachments_location();	 $can_inline=true;
	     			if (!($bb_attachments['role']['inline']=="read" || bb_current_user_can($bb_attachments['role']['inline']))) {$can_inline=false;}
				if ($location=="edit.php" && $can_inline) {			
					$output.='<scr'.'ipt type="text/javascript" defer="defer">			
					bbat_field = document.getElementsByTagName("textarea")[0];
					bbat_value=" [attachment="+'.$post_id.'+","+'.$id.'+"] ";
					bbat_field.value += bbat_value;</script>';
				} // above auto-injects newly uploaded attachment if edit form present
			} else {	$inject.=" [attachment=$post_id,$id]";}
			}
		}
	} // end !$empty
} // end while
$output.="</ol>";
if ($display) {echo $output;} 
elseif (!empty($inject) && $bb_attachments['inline']['auto']) {$bb_post->post_text=apply_filters('edit_text', $bb_post->post_text.$inject); bb_insert_post($bb_post);} // auto-inject 
bb_update_topicmeta( $topic_id, 'bb_attachments', $topic_attachments+$offset);
}

function bb_attachments_upload_form($post_id=0) {
global $bb_attachments;

if (!$post_id) {$post_id=intval($_GET['bb_attachments']);} 	// only can upload if user is allowed to edit post
$user_id=bb_get_current_user_info( 'id' );
if (!$user_id || ($post_id && !bb_current_user_can('edit_post',$post_id)) || !bb_current_user_can($bb_attachments['role']['upload'])) {return;}	

$count=0; $allowed=__('allowed uploads:')." "; $exts=bb_attachments_lookup($bb_attachments['allowed']['extensions']);
$tcount=count($exts); foreach ($exts as $ext) {
$allowed.=$ext.' <span class="num">('.round(bb_attachments_lookup($bb_attachments['max']['size'],$ext)/1024,1).' KB)</span>, ';
$count++; if ($count==5 && $tcount>7) {$allowed.="<br />";}
}
$allowed=rtrim($allowed," ,");

if ($post_id) {echo '<form class="bb_attachments_upload_form" enctype="multipart/form-data" method="post" action="'.attribute_escape(add_query_arg('bb_attachments',$post_id,remove_query_arg(array('bb_attachments','bbat','bbat_delete')))).'">';}
else {echo '<input  type="hidden" name="bb_attachments" value="0" />';}
echo	'<h3>'.__("Upload Files from your Computer").'</h3>		
	<input  type="hidden" name="MAX_FILE_SIZE" value="'.$bb_attachments['max']['php_upload_limit'].'" />			
	<span id="bb_attachments_file_sample">
	<input type="file" name="bb_attachments[]" size="50" /><br />
	<input type="file" name="bb_attachments[]" size="50" /><br />
	</span>		
	<div id="bb_attachments_file_input_4"></div>	
	<script type="text/javascript" defer="defer">
	bb_attachment_input_count=2;
	function bb_attachment_inputs() {		
		bb_attachment_input_count=bb_attachment_input_count+2; if (bb_attachment_input_count<='.bb_attachments_lookup($bb_attachments['max']['uploads']).') {			
		document.getElementById('."'bb_attachments_file_input_'".'+bb_attachment_input_count).innerHTML+=document.getElementById('."'bb_attachments_file_sample'".').innerHTML+"<div id=bb_attachments_file_input_"+(bb_attachment_input_count+2)+"></div>";
		}					
	}
	</script>
	'.$allowed.'<br />
	<div style="margin:1em 0 0 0;">';		 
if ($post_id) {echo '<a style="margin-right:12em;" href="'. get_post_link( $post_id ).'">'.__("&laquo; return to post").'</a>';}
else {echo '<span style="margin-right:20em;">&nbsp;</span>';}
echo	'<a href="javascript:void(0)" onClick="bb_attachment_inputs();">[+] '.__('more').'</a> &nbsp; 
	<input style="font-weight:bold;" type="submit" class="submit" name="upload" value="'.__('Upload').'" />
	</div>';
if ($post_id) {echo '</form>';}
}

function bb_attachments_download($filenum=0) {
global $bbdb, $bb_attachments;
$filenum=intval($filenum);
if ($filenum==0 && isset($_GET['bbat'])) {$filenum=intval($_GET['bbat']);}
if ($filenum>0 && ($bb_attachments['role']['download']=="read" || bb_current_user_can($bb_attachments['role']['download']))) {
	$file=$bbdb->get_results("SELECT * FROM ".$bb_attachments['db']." WHERE id = $filenum AND status = 0 LIMIT 1");		
	if (isset($file[0]) && $file[0]->id) {
		$file=$file[0]; $file->filename=stripslashes($file->filename);				
		$fullpath=$bb_attachments['path'].floor($file->id/1000)."/".$file->id.".".$file->filename;
		@$bbdb->query("UPDATE ".$bb_attachments['db']." SET downloads=downloads+1 WHERE id = $file->id LIMIT 1");

		if ($bb_attachments['aws']['enable']) {
			$aws=$bb_attachments['aws']['url'].$file->id.'.'.$file->filename;
			header('Location: '.$aws); 
		}
		elseif (file_exists($fullpath)) {			
			if (ini_get('zlib.output_compression')) {ini_set('zlib.output_compression', 'Off');}	// fix for IE
			header ("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
			header("Pragma: hack");
			header("Content-Type: application/octet-stream");
			header("Content-Length: $file->size");
			header('Content-Disposition: attachment; filename="'.$file->filename.'"');
			header("Content-Transfer-Encoding: binary");              
			ob_clean();
  			flush();  			
            			$fp = @fopen($fullpath,"rb");
            			set_time_limit(0); 
			fpassthru($fp);	// avoids file touch bug with readfile
			fclose($fp);            			
            		}		
	}	
} else {nocache_headers(); 	echo "<html><body>".__('Sorry, download is restricted.')."<scr"."ipt type='text/javascript'>alert('".__('Sorry, download is restricted.')."');</script></body></html>";}
exit;	// no matter what, it's over here
}

function bb_attachments_inline($filenum=0) {
global $bbdb, $bb_attachments;
$filenum=intval($filenum);
if ($filenum==0 && isset($_GET['bbat'])) {$filenum=intval($_GET['bbat']);}
if ($filenum>0 && ($bb_attachments['role']['inline']=="read" || bb_current_user_can($bb_attachments['role']['inline']))) {
	$file=$bbdb->get_results("SELECT * FROM ".$bb_attachments['db']." WHERE id = $filenum AND status = 0 LIMIT 1");		
	if (isset($file[0]) && $file[0]->id) {
		$file=$file[0]; $file->filename=stripslashes($file->filename);				
		$path=$bb_attachments['path'].floor($file->id/1000)."/";
		$fullpath=$path.$file->id.".".$file->filename;
		if (file_exists($fullpath)) {				
			if (!list($width, $height, $type) = getimagesize($fullpath)) {exit();} // not an image?!
			$mime=image_type_to_mime_type($type); 	// lookup number to full string
			$mime=trim(substr($mime,0,strpos($mime.";",";")));	// trim full string if necessary					
			
			if ($height>$bb_attachments['inline']['height'] || $width>$bb_attachments['inline']['width']) {
				if (!file_exists($fullpath.".resize")) { 
					if (bb_attachments_resize($fullpath,$type,$width,$height)) {
						if ($bb_attachments['aws']['enable']) {
							bb_attachments_aws($path,$file->id.'.'.$file->filename.".resize",$mime);    // copy to S3
						}
					} else {exit;}		
				}
				$fullpath.=".resize";
				$file->filename.=".resize";			
				$file->size=filesize($fullpath);
				if (!$file->size) {exit();}
			}
			
			if ($bb_attachments['aws']['enable']) {
				$aws=$bb_attachments['aws']['url'].$file->id.'.'.$file->filename;
				header('Location: '.$aws); exit;
			}
			
			// $headers = apache_request_headers();  $ifModifiedSince=$headers['If-Modified-Since'];
			$ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;
			$filemtime=filemtime($fullpath);
			$httpcode="200";
			if ($ifModifiedSince && (strtotime($ifModifiedSince) >= $filemtime)) {$httpcode="304";}      				
			if (ini_get('zlib.output_compression')) {ini_set('zlib.output_compression', 'Off');}	// fix for IE
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $filemtime).' GMT', true, $httpcode);
			header("Cache-Control: Public");
			header("Pragma: Public");
			header("Expires: " . gmdate("D, d M Y H:i:s", time() + (86400 * 30)) . " GMT");
			header("Content-Type: ".$mime);
			if ($httpcode=="200") {			
				header("Content-Length: $file->size");
				header('Content-Disposition: inline; filename="'.$file->filename.'"');
				header("Content-Transfer-Encoding: binary");              
				ob_clean();
  				flush(); 
  				$fp = @fopen($fullpath,"rb");
            				set_time_limit(0); 
				fpassthru($fp);	// avoids file touch bug with readfile
				fclose($fp);
				exit();			
			} 							           			
            		} else {	bb_attachments_inline_missing();}						
	} else {	bb_attachments_inline_missing();}	
} else {	bb_attachments_inline_missing();}
}

function bb_attachments_inline_missing() {
global $bb_attachments;
	$missing=$bb_attachments['icons']['path']."missing.gif";
	header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: image/gif");
	header("Content-Length: ".filesize($missing));
	header('Content-Disposition: inline; filename="missing.gif"');
	header("Content-Transfer-Encoding: binary");              
	ob_clean();
	flush(); 
	$fp = @fopen($missing,"rb");
	set_time_limit(0); 
	fpassthru($fp);	// avoids file touch bug with readfile
	fclose($fp);				
	exit();
}

function bb_attachments_resize($filename, $type, $width, $height, $suffix=".resize") {		
global $bb_attachments;
	switch($type) {
	case IMAGETYPE_JPEG:
		$img = imagecreatefromjpeg($filename); 	$save= (string) 'imagejpeg'; break;
	case IMAGETYPE_GIF:
		$img = imagecreatefromgif($filename); $save= (string) 'imagegif';  break;
	case IMAGETYPE_PNG:			
		$img = imagecreatefrompng($filename); 	$save = (string) 'imagepng';  break;
	default:		
		return false;
	}	
	$scale_x=1; if ($width>$bb_attachments['inline']['width']) {$scale_x = $width/$bb_attachments['inline']['width'];}
	$scale_y=1; if ($height>$bb_attachments['inline']['height']) {$scale_y = $height/$bb_attachments['inline']['height'];}
	$scale=$scale_x; if ($scale_y>$scale_x) {$scale=$scale_y;};
	if ($scale<=1) {return false;}	
	$new_width = round(1/$scale * $width);
	$new_height = round(1/$scale * $height);
		
	$output=$filename.$suffix;		
	if (!file_exists($output)) {
		$new= imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		$save($new, $output);
		imagedestroy($new);
	}
	imagedestroy($img);		
return true;		
}

function bb_attachments_delete($filenum=0) {
global $bbdb, $bb_attachments;
$filenum=intval($filenum);
if ($filenum==0 && isset($_GET['bbat_delete'])) {$filenum=intval($_GET['bbat_delete']);}
if ($filenum>0 && bb_current_user_can($bb_attachments['role']['delete'])) {
	$file=$bbdb->get_results("SELECT * FROM ".$bb_attachments['db']." WHERE id = $filenum AND status = 0 LIMIT 1");		
	if (isset($file[0]) && $file[0]->id && bb_current_user_can( 'edit_post', $file[0]->post_id)) {
		$file=$file[0]; $file->filename=stripslashes($file->filename);				
		$fullpath=$bb_attachments['path'].floor($file->id/1000)."/".$file->id.".".$file->filename;
		if (file_exists($fullpath)) {
			@unlink($fullpath);
			@$bbdb->query("UPDATE ".$bb_attachments['db']." SET status = 1 WHERE id = $file->id LIMIT 1");			
            		}
            		bb_attachments_recount($file->post_id);
            		if (!isset($_GET['bb_attachments'])) {wp_redirect(get_post_link($file->post_id));}			
	}	
}
}

function bb_attachments_recount($post_id=0) {    	// update topic icon flag and sync attachment count for topic  given a post_id
global $bb_attachments,$bbdb; $count=0; 
if (empty($topic_id)) {$topic_id=intval($bbdb->get_var("SELECT topic_id FROM $bbdb->posts WHERE post_id=$post_id LIMIT 1"));}
if ($topic_id) {
	$query="SELECT count(status) as count FROM ".$bb_attachments['db']." AS t1 LEFT JOIN $bbdb->posts AS t2 ON t1.post_id=t2.post_id 
 			WHERE t1.status=0 AND t1.user_id>0 AND t1.size>0 AND t2.post_status=0 AND t2.topic_id=$topic_id";
	$count=intval($bbdb->get_var($query));
	bb_update_topicmeta( $topic_id, 'bb_attachments', $count);
}
return  $count;
}

function bb_attachments_cache() {
	global $bbdb, $posts, $bb_attachments, $bb_attachments_cache;
	if ($posts) {
		$list=""; foreach ($posts as $post)  {$bb_attachments_cache[$post->post_id]=array(); $list.=$post->post_id.",";} $list=trim($list," ,");
		$results=$bbdb->get_results("SELECT * FROM ".$bb_attachments['db']." WHERE post_id IN ($list) ORDER BY id DESC");	// needs to be optimized
		if ($results) {foreach ($results as $result) {$bb_attachments_cache[$result->post_id][$result->id]=$result;} unset($results);}				
	}
}

function bb_attachments_post_footer($text) {
if (!is_bb_feed()) {
	global $bb_post, $bb_attachments;
	$text.="<div class='bb_attachments'>".bb_attachments_post_attachments($bb_post->post_id)."</div>";
}
return $text;
}

function bb_attachments_link($link) { 
global $bb_attachments, $bb_attachments_cache, $bb_post, $bb_current_user;
$post_id=$bb_post->post_id;
	if (($bb_current_user->ID ==$bb_post->poster_id && $bb_attachments_cache[$post_id]) && bb_current_user_can($bb_attachments['role']['upload']) ) { 
		echo " <a href='" . attribute_escape(add_query_arg('bb_attachments',$post_id,remove_query_arg(array('bb_attachments','bbat','bbat_delete')))) . "' >" . __('Attachments') ."</a> ";
	}
	return $link;
}

function bb_attachments_title( $title ) {
	global $bb_attachments, $topic;
	if ($bb_attachments['title'] && isset($topic->bb_attachments) && intval($topic->bb_attachments)>0)  {
		$title=$title.$bb_attachments['title'];			
	}
	return $title;
} 

function bb_attachments_location() {
	$file = '';
	foreach ( array($_SERVER['PHP_SELF'], $_SERVER['SCRIPT_FILENAME'], $_SERVER['SCRIPT_NAME']) as $name )
		if ( false !== strpos($name, '.php') )
			$file = $name;

	return bb_find_filename( $file );
}	

function bb_attachments_lookup($array,$specific='') {
$key='default';	// there is probably a faster/more dynamic way to do role checks???
if (isset($array['administrate']) && bb_current_user_can('administrate')) {$key='administrate';}
else {if (isset($array['moderate']) && bb_current_user_can('moderate')) {$key='moderate';}
	else {if ($specific && isset($array[$specific])) {$key=$specific;}}}
if (isset($array[$key])) {return $array[$key];} else {return '';}
}

function bb_attachments_l2n($v){
$l = substr($v, -1); $ret = substr($v, 0, -1); 
switch(strtoupper($l)){   case 'P':   $ret *= 1024;  case 'T':  $ret *= 1024;  case 'G':  $ret *= 1024;  case 'M':  $ret *= 1024; case 'K':  $ret *= 1024;  break;}
return $ret;
}

function bb_attachments_mime_type($f) {
$disabled=strtolower(ini_get('disable_functions')); $mime="";

if (function_exists('mime_content_type') && strpos($disabled,'mime_content_type')===false) {	// many newer PHP doesn't have this
	$mime=mime_content_type($f);
}
elseif (function_exists('finfo_open') && function_exists('finfo_file') && strpos($disabled,'finfo_open')===false) {	// try finfo
	$finfo=finfo_open(FILEINFO_MIME);  $mime=trim(finfo_file($finfo, $f));
} 
elseif (function_exists('exec') && strpos($disabled,'exec')===false) {	//  so try shell  ?  - will fail on windows 100% of the time?
	$mime=trim(@exec('file -bi '.escapeshellarg($f)));
}
if ((!$mime || strpos($mime,'file -bi')!==false) && function_exists('getimagesize') && function_exists('image_type_to_mime_type')  && strpos($disabled,'getimagesize')===false) { 
	// use image function in worst case senario - won't do text types - must fix !
   	$mime=""; $imgt =@getimagesize($f);  $mime=image_type_to_mime_type($imgt[2]); 	// 0=width  1=height  if ($imgt) {} 
}
return substr($mime,0,strpos($mime.";",";"));	
}

function bb_attachments_install() { 
global $bbdb,$bb_attachments; 
$bbdb->query("CREATE TABLE IF NOT EXISTS ".$bb_attachments['db']." (
		id		int(10)        UNSIGNED NOT NULL auto_increment,
		time       	int(10)        UNSIGNED NOT NULL default '0',
		post_id 	int(10)        UNSIGNED NOT NULL default '0',
		user_id 	int(10)        UNSIGNED NOT NULL default '0',
		user_ip 	int(10) 	       UNSIGNED NOT NULL default '0',
		status       	tinyint(10) UNSIGNED NOT NULL default '0',
		downloads 	int(10)         UNSIGNED NOT NULL default '0',
		size        	int(10)         UNSIGNED NOT NULL default '0',
		ext 	     	varchar(255)           NOT NULL default '',
		mime     	varchar(255) 	         NOT NULL default '',
		filename     	varchar(255) 	         NOT NULL default '',
		PRIMARY KEY ( id ),
		INDEX ( post_id )
		) CHARSET utf8  COLLATE utf8_general_ci");	
		
$bbdb->query("ALTER TABLE ".$bb_attachments['db']." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci,
CHANGE 'ext' 'ext' VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE 'mime' 'mime' VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE 'filename' 'filename' VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");

if (!file_exists($bb_attachments['path'])) {		// this usually fails for open_basedir or safe-mode but we'll give it a shot
	$oldumask = umask(0);
	@mkdir(rtrim($bb_attachments['path']," /"), 0777);
	@chmod(rtrim($bb_attachments['path']," /"), 0777);                      
	umask($oldumask);	
}		
} 

function bb_attachments_aws($path,$name,$mime) { 
global $bb_attachments;     
if (!$bb_attachments['aws']['enable']) {return;}
                  
$bucket=$bb_attachments['aws']['bucket'];
$key=$bb_attachments['aws']['key'];
$secret=$bb_attachments['aws']['secret'];
$date = gmdate('r'); 
$count=0;
$file_data = file_get_contents($path.$name); 	// doing this for files over 2MB might be impractical
$file_length = strlen($file_data); 

$fp=fsockopen("s3.amazonaws.com", 80, $errno, $errstr, 15);  if (!$fp) {return;}  // die("$errstr ($errno)\n");}
@socket_set_blocking($socket, TRUE);
@socket_set_timeout($socket,15);

do { $count++;    // upload file
$hmac = bb_attachments_hmac($secret,"PUT\n\n{$mime}\n{$date}\nx-amz-acl:public-read\n/{$bucket}/{$name}");
$query = "PUT /{$bucket}/{$name} HTTP/1.1\nHost: s3.amazonaws.com\nx-amz-acl: public-read
Connection: keep-alive\nContent-Type: {$mime}\nContent-Length: {$file_length}\nDate: $date
Authorization: AWS {$key}:$hmac\n\n".$file_data;
$response = bb_attachments_aws_upload($fp,$query); 

if (strpos($response, '<Error>') !== false) {$count++;      // echo $response; exit;
if (strpos($response, 'NoSuchBucket')!==false && $count==2 ) {       // make bucket on bucket error
$hmac = bb_attachments_hmac($secret,"PUT\n\n\n{$date}\n/{$bucket}");
$query = "PUT /{$bucket} HTTP/1.1\nHost: s3.amazonaws.com
Connection: keep-alive\nDate: $date
Authorization: AWS {$key}:$hmac\n\n";
$response = bb_attachments_aws_upload($fp,$query);  if (strpos($response, '<Error>') !== false) {break;} // echo $response; exit;}
} // bucket error
} // upload error
} while ($count==2);	// retry once and only once on first error
fclose($fp);
}

function bb_attachments_aws_upload($fp,&$q) {    
    $r="";     
    fwrite($fp, $q);
    while (!feof($fp)) {
        $tr = fgets($fp, 256); $r .= $tr;
        if (strpos($r, "\r\n\r\n")!==false && strpos($r,'Content-Length: 0') !== false) {break;}
        if (substr($r, -7) == "\r\n0\r\n\r\n") {break;}
    }
return $r;
}

function bb_attachments_hmac($secret,$hmac) {
    if (!function_exists('binsha1')) { 
        if (version_compare(phpversion(), "5.0.0", ">=")) {function binsha1($d) { return sha1($d, true);}} 
        else {function binsha1($d){return pack('H*', sha1($d));}}
    }   
    if (strlen($secret)==40) {$secret=$secret.str_repeat(chr(0),24);}
    $ipad = str_repeat(chr(0x36), 64);
    $opad = str_repeat(chr(0x5c), 64);     
return base64_encode(binsha1(($secret^$opad).binsha1(($secret^$ipad).$hmac)));
}
?>