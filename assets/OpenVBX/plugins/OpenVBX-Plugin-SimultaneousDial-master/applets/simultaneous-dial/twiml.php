<?php
/* State Types */

// We use the number index to move sequentially through the list of the numbers we'll be dialing
use application\modules\settings\integrations\twilio\libraries\AppletInstance;
use application\modules\settings\integrations\twilio\libraries\OpenVBX;

define('DIAL_ACTION', 'dialAction');
define('DIAL_COOKIE', 'state-'.AppletInstance::getInstanceId());

/* States */
define('DIAL_STATE_DIAL', 'dialStateDial');
define('DIAL_STATE_NO_ANSWER', 'dialStateNoAnswer');
define('DIAL_STATE_RECORDING', 'dialStateRecording');
define('DIAL_STATE_HANGUP', 'dialStateHangup');

$response = new Response();

// Default State
$state = array();
$state[DIAL_ACTION] = DIAL_STATE_DIAL;
$version = AppletInstance::getValue('version', null);

/* Get current instance	 */
$dial_whom_selector = AppletInstance::getValue('dial-whom-selector');
$dial_whom_user_or_group = AppletInstance::getUserGroupPickerValue('dial-whom-user-or-group');
$dial_whom_number = AppletInstance::getValue('dial-whom-number');


$no_answer_action = AppletInstance::getValue('no-answer-action', 'hangup');
$no_answer_group_voicemail = AppletInstance::getAudioSpeechPickerValue('no-answer-group-voicemail');
$no_answer_redirect = AppletInstance::getDropZoneUrl('no-answer-redirect');
$no_answer_redirect_number = AppletInstance::getDropZoneUrl('no-answer-redirect-number');

$numbers = array();
$voicemail = null;

if ($dial_whom_selector === 'user-or-group')
{
	$dial_whom_instance = null;
	if(is_object($dial_whom_user_or_group))
	{
		$dial_whom_instance = get_class($dial_whom_user_or_group);
	}

	switch($dial_whom_instance)
	{
		case 'VBX_User':
			foreach($dial_whom_user_or_group->twilioVoiceDevices as $device)
			{
				if($device->is_active == 1)
					$numbers[] = $device->value;
			}
			$voicemail = $dial_whom_user_or_group->voicemail;
			break;
		case 'VBX_Group':
			foreach($dial_whom_user_or_group->users as $user)
			{
				$user = \application\modules\user\models\User::findOrFail($user->user_id);
				foreach($user->twilioVoiceDevices as $device)
				{
					if($device->is_active == 1)
						$numbers[] = $device->value;
				}
			}
			$voicemail = $no_answer_group_voicemail;
			break;
		default:
			$response->addSay('Missing user or group to dial');
			break;
	}
}
else if ($dial_whom_selector === 'number')
{
	$numbers[] = $dial_whom_number;
	$voicemail = null;
}
else
{
	error_log("Unexpected dial-whom-selector value of '$dial_whom_selector'");
}

/* Grab current state of applet */
if(isset($_COOKIE[DIAL_COOKIE]))
{
	$stateString = str_replace(', $Version=0', '', $_COOKIE[DIAL_COOKIE]);
	$state = json_decode($stateString, true);
	if(is_object($state)) 
	{
		$state = get_object_vars($state);
	}
}


$dial_status = isset($_REQUEST['DialStatus'])? $_REQUEST['DialStatus'] : null;
if($dial_status)
{
	switch($dial_status)
	{
		case 'answered':
			$state[DIAL_ACTION] = DIAL_STATE_HANGUP;
			break;
		default:
			break;
	}
}

// This loop exists only so that we can quickly make state transitions by
// setting a new DIAL_ACTION and jumping to the top of the loop.

$keepLooping = true;
while($keepLooping)
{
	// By default we'll only go once through the loop.
	$keepLooping = false;
	switch($state[DIAL_ACTION])
	{
		case DIAL_STATE_DIAL:
			error_log('numbers left to try');
			$dial = $response->addDial(array('action' => current_url()));

			if ($dial_whom_selector === 'user-or-group')
			{
				$name = null;

				if ($dial_whom_user_or_group instanceof VBX_User)
				{
					$name = $dial_whom_user_or_group->first_name . " " . $dial_whom_user_or_group->last_name;
				}
				else if ($dial_whom_user_or_group instanceof VBX_Group)
				{
					$name = $dial_whom_user_or_group->name;
				}
				else
				{
					// In practice, this should never ever happen.
					$name = "Unknown";
				}
				
        foreach($numbers as $number) {
          $dial->addNumber($number,
                  array(
                      'url' => site_url('twiml/whisper?name='.urlencode($name)),
                      )
                  );
        }
			}
			else
			{
				// If we're just dialing an arbitrary number, we don't announce anything about
				// who's calling because we don't really know anything.
				$dial->addNumber($numbers[0]);
			}
 
      $state[DIAL_ACTION] = DIAL_STATE_NO_ANSWER;
			break;
		case DIAL_STATE_HANGUP:
			$response->addHangup();
			break;
		case DIAL_STATE_NO_ANSWER:
			if ($dial_whom_selector == 'number')
			{
				if(empty($no_answer_redirect_number))
				{
					$response->addHangup();
				}
				
				$response->addRedirect($no_answer_redirect_number);
			}
			else
			{
				if ($no_answer_action === 'voicemail')
				{
					$response->append(AudioSpeechPickerWidget::getVerbForValue($voicemail, new Say("Please leave a message.")));
					$response->addRecord(array(
											   'transcribeCallback' => site_url('client_twilio_calls/transcribe'),
											   ));
					$state[DIAL_ACTION] = DIAL_STATE_RECORDING;
				}
				else if ($no_answer_action === 'redirect')
				{
					if(empty($no_answer_redirect))
					{
						$response->addHangup();
					}
				
					$response->addRedirect($no_answer_redirect);
				}
				else if ($no_answer_action === 'hangup')
				{
					$response->addHangup();
				}
				else
				{
					trigger_error("Unexpected no_answer_action");
				}
			}
			break;
		case DIAL_STATE_RECORDING:
			if(isset($_REQUEST['testing']))
			   break;
			OpenVBX::addVoiceMessage($dial_whom_user_or_group);
			break;
	}
}

setcookie(DIAL_COOKIE, json_encode($state), time() + (5 * 60));
$response->Respond();
?>
