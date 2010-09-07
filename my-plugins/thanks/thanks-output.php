<?php

function thanks_output_details($post_id, $uid, $logged_in = true) {
	$out = "";
	
	$meta = bb_get_post_meta("thanks", $post_id);
	$report_length = 0;
	if (isset($meta)) {
		$vote_count = count($meta);
		$msg_type = ($vote_count == 0) ? "none" : (($vote_count == 1) ? "one" : "many");
		$msg = thanks_get_voting_phrase("thanks_output_".$msg_type);
		$report_length = strlen($msg);
	  $out .= str_replace("#", "".$vote_count, $msg);
	  
	  $should_show_voters = thanks_get_voting_phrase("thanks_voters");
	  if ($should_show_voters == "yes") {
		  $out .= ' '.thanks_get_voting_phrase("thanks_voters_prefix");
		  for ($i=0; $i < count($meta); $i++) {
				$link = get_user_profile_link($meta[$i]);
				$voter = bb_get_user($meta[$i]);
				if ($i > 0) {
					$out .= ", ";
				}
				$out .= '<a href="'.$link.'">'.$voter->display_name.'</a>';
		  }
		  $out .= thanks_get_voting_phrase("thanks_voters_suffix");
		}
	}
	
	if ($logged_in) {
		if (!in_array($uid, $meta)) {
			if (isset($meta) && $report_length > 0) {
				$out .= "&nbsp;&nbsp;|&nbsp;&nbsp;";
			}
			$msg = thanks_get_voting_phrase("thanks_voting");		
			$out .= "<a class=\"thanks-vote\" user=\"".$uid."\" id=\"".$post_id."\">".$msg."</a>";
		}
	}
	
	return $out;
}

function thanks_get_voting_phrase($phrase) {
	global $DEFAULTS;
	$msg = bb_get_option($phrase);
	if (!isset($msg)) {
		$msg = $DEFAULTS[$phrase];
	}
	return $msg;
}

?>