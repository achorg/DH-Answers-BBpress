<?php

add_action( 'bb_admin-header.php','blocklist_process_post');

function blocklist_process_post() {
	if (!bb_current_user_can('administrate')) {return;}
	global $blocklist;
	if (isset($_POST['submit']) && isset($_POST['blocklist'])) {
	$options=array('data','email');
	foreach ($options as $option) {
		if (!empty($_POST[$option])) {
			(array) $data=explode("\n",trim($_POST[$option]));
			array_walk($data,create_function('&$arr','$arr=trim($arr);'));		
			$blocklist[$option]=implode("\r\n",$data)."\r\n";
		} else {$blocklist[$option]="";}
	}
	bb_update_option('blocklist',$blocklist);		
	}
}

function blocklist_admin() {
	global $blocklist; blocklist_initialize();
	?>	
		<h2>Blocklist</h2>
		<form class="settings" method="post" name="blocklist_form" id="blocklist_form">
		<input type=hidden name="blocklist" value="1">
			<table class="widefat">
				<tbody>
				<tr>
					<td valign="top">
					<div style="width:380px">
					<label for="data"><b>Blocklist</b></label>
					<br /><br />
					enter only one item per line
					<br /><br /> 
					username, email, IP address, subject, and post text are&nbsp;tested and&nbsp;if matched, post is sent to spam
					<br /><br /> 
					you can use partial words and partial ip addresses, which&nbsp;match&nbsp;to the left (ie. "starting with")
					<br /><br /> 
					lines that begin with <b>#</b><br />or <i>less than 4 characters</i> are <b>ignored</b>
					</div>
					</td>
					<td align="left">
					<div style="max-width:500px;">
					<textarea rows="15" cols="40" style="padding:3px;font-size:1.5em;" name="data"><?php echo $blocklist['data']; ?></textarea>											
					</div>					
					</td>
					<td></td>
				</tr>
				<tr>
					<td valign="top">
					<label for="email"><b>Email notification</b></label>
					<br /><br />
					these emails will be notified when a post is blocked<br />					
					only one email per line, leave blank for none					
					</td>
					<td align="left">
					<div style="max-width:500px;">
					<textarea rows="3" cols="40" style="padding:3px;font-size:1.5em;" name="email"><?php echo $blocklist['email']; ?></textarea>
					</div>					
					</td>
					<td></td>
				</tr>
				<tr style="border:0">
					<td></td>
					<td align="left">
					<div style="max-width:500px;">					
						<p class="submit" style="border:0"><input class="submit" type="submit" name="submit" value="Save Blocklist Settings"></p>					
					</div>					
					</td>
					<td></td>
				
				</tr>
				</tbody>
			</table>		
		
		</form>
		<?php
}

?>