<?php

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'sms-list') {
		$query = $this->db->query("select (select created from messages where caller = data.from order by created desc limit 1) as created, data.*, (select status from messages where caller = data.from and called = data.to order by created desc limit 1) as 'status' from (select caller as 'from',called as 'to', count(id) as 'message_count' from messages where created > DATE_SUB(NOW(), INTERVAL 2.5 DAY) group by caller, called order by created desc) as data order by created desc ");
		echo json_encode($query->result());
		exit;
	}
	else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'sms-messages') {

		$callId = explode("-", $_POST['id']);
		$caller_id = $callId[0];
		$called_id = $callId[1];

		//first mark all messages are read
		$this->db->query("update `messages` set `read`=current_timestamp, `status`='read' where status='new' and caller='".$caller_id."' and called='".$called_id."'");

		$sql = "select (select id from `messages` where caller = '". $caller_id ."' and called = '". $called_id ."' order by created desc limit 1) as 'id',data.* from (select created, call_sid, caller as 'to',called as 'from',content_text as 'message' from messages where caller = '". $caller_id ."' and called = '". $called_id ."' union select created, '' as 'call_sid', Replace(Replace(Replace(Replace(substring(description, 1, instr(description,'to')-2),'(',''),')',''),' ',''),'-','') as 'to', Replace(Replace(Replace(Replace(substring(description, instr(description,'to')+3, instr(description,':')-instr(description,'to')-3),'(',''),')',''),' ',''),'-','') as 'from', substring(description, instr(description,':')+2) as 'message'  from annotations where annotation_type = 6 and message_id in (select id from messages where caller = '". $caller_id ."' and called = '". $called_id ."')) as data order by created";

		$query = $this->db->query($sql);
		echo json_encode($query->result());
		exit;
	}
	else if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'mms-media') {
		$ci = & get_instance();

		require_once(APPPATH . 'libraries/twilio.php');

		$ci->twilio = new TwilioRestClient($ci->twilio_sid,$ci->twilio_token,$ci->twilio_endpoint);
		$media_url = "Accounts/{$this->twilio_sid}/Messages/{$_POST['id']}/Media.json";
		$mediaObj = $ci->twilio->request($media_url, "GET");
		echo $mediaObj->ResponseText;

		exit;
	}
	else if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'plugin-json') {
		echo file_get_contents('plugin.json', true);
		exit;
	}

?>

<link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'>

<?php

// Chat UI Design by Julia Smith
// http://www.bypeople.com/web-chat-widget/
// http://codepen.io/drehimself/pen/KdXwxR?utm_source=bypeople


OpenVBX::addCSS('css/style.css');
OpenVBX::addCSS('css/jquery.light.css');

?>


<div class="vbx-content-main">
	<div class="vbx-content-menu vbx-content-menu-top">
		<h2 class="vbx-content-heading">SMS Conversation List</h2>
		<div id="sound-notification" style="float: right; color: white; font-size: 25px;"><a href="javascript:void(0);" style="color: white;"><i class="fa"></i></a>&nbsp;</div>
	</div><!-- .vbx-content-menu -->
	<div class="vbx-content-container">
		<div class="vbx-content-section">
			<div class="smsList" style="float:left; width:45%;">
				<table class="vbx-items-grid" border="0" id="smsList">
					<thead>
						<tr class="items-head recording-head"><th>Date</th><th>From</th><th>To</th><th>Messages</th></tr>
					</thead>
					<tbody id="smss">

					</tbody>
				</table>
			</div>
			<div style="float:left; width:55%;">
				<div class="chat">
					<div class="chat-header clearfix">
						<div class="chat-about">
							<div class="chat-with" style="float:left; width:95%;"><-- Click to View Conversation</div>
							<div style="float:left;"><a href="#" class="quick-call-button"></a></div>
						</div>
					</div> <!-- end chat-header -->

				  <div class="chat-history">
					<ul class="chat-history-messages">

					</ul>

				  </div> <!-- end chat-history -->

				  <div class="chat-message clearfix">
					<textarea name="message-to-send" id="message-to-send" placeholder ="Type your message" rows="3"></textarea>
					<button id="sendSMS">Send</button>
					<input type="hidden" id="sms-conversationid" value="">
					<input type="hidden" id="sms-messageid" value="">
					<input type="hidden" id="sms-to-phone" value="">
					<input type="hidden" id="sms-from-phone" value="">
				  </div> <!-- end chat-message -->

				</div> <!-- end chat -->
			</div>
		</div><!-- .vbx-content-section -->
	</div><!-- .vbx-content-container -->
</div><!-- .vbx-content-main -->

<audio id="notify">
<?php
	// Find plugin location to correctly link sound file
	$file = "assets/notify.mp3";
	$found_plugin_dir = false;
	$plugin_dir = "plugins";

	$plugin = OpenVBX::$currentPlugin;
	$info = $plugin->getInfo();
	$path = $info['plugin_path'] .'/'. $file;
	$plugin_dir = implode('/', array('plugins', $info['dir_name'], $file));

	$is_ssl_proto = false;
	switch(true) {
		case !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off':
			$is_ssl_proto = true;
			break;
		case !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https':
			$is_ssl_proto = true;
			break;
	}

	$base_url = "http".($is_ssl_proto ? 's' : '') . "://".$_SERVER['HTTP_HOST']. rtrim(WEB_ROOT, '/') . '/';
?>
<source src="<?= $base_url.$plugin_dir ?>"></source>
</audio>

<?php

OpenVBX::addJS('js/index.js');
OpenVBX::addJS('js/jquery.light.js');
?>
