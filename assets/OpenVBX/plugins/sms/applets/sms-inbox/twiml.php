<?php

use application\modules\settings\integrations\twilio\libraries\AppletInstance;
use application\modules\settings\integrations\twilio\libraries\OpenVBX;
use application\modules\settings\integrations\twilio\libraries\TwimlResponse;
use application\modules\user\models\User;

$response = new TwimlResponse;

$forward = AppletInstance::getUserGroupPickerValue('forward');

$devices = array();
switch (get_class($forward)) {
    case 'VBX_User':
        foreach ($forward->twilioVoiceDevices as $device) {
            $devices[] = $device;
        }
        $voicemail = $forward->voicemail;
        break;
    case 'VBX_Group':
        foreach ($forward->users as $user) {
            $user = User::findOrFail($user->user_id);
            foreach ($user->twilioVoiceDevices as $device) {
                $devices[] = $device;
            }
        }
        $voicemail = $groupVoicemail;
        break;
    default:
        break;
}

$required_params = array('SmsSid', 'From', 'To', 'Body');
$sms_found = true;
foreach ($required_params as $param) {
    if (!in_array($param, array_keys($_REQUEST))) {
        $sms_found = false;
    }
}

if ($sms_found) {
    $ci = &get_instance();
    OpenVBX::addSmsMessage($forward,
        $ci->input->get_post('SmsSid'),
        $ci->input->get_post('From'),
        $ci->input->get_post('To'),
        $ci->input->get_post('Body')
    );
} else {
    $response->message('Unable to send sms message');
}

$response->respond();