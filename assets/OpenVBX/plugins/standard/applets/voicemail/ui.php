<?php
// get the current owner (user or group) to decide if we should show the Prompt area on load
use application\modules\settings\integrations\twilio\libraries\AppletInstance;
use application\modules\settings\integrations\twilio\libraries\AppletUI;
use application\modules\user\models\User;

$currentlyIsUser = AppletInstance::getUserGroupPickerValue('permissions') instanceof User;
?>

<div class="vbx-applet voicemail-applet">
    <div class="prompt-for-group" style="display: <?php echo $currentlyIsUser ? "none" : "" ?>">
        <h2>Prompt</h2>
        <p>What will the caller hear before leaving their message?</p>
        <?= AppletUI::AudioSpeechPicker('prompt') ?>
    </div>

    <div class="prompt-for-individual" style="display: <?= !$currentlyIsUser ? "none" : "" ?>">
        <h2>Prompt</h2>

        <div class="vbx-full-pane">
            <fieldset class="vbx-input-container">
                The individual's personal voicemail greeting will be played.
            </fieldset>
        </div>
    </div>
    <br/>
    <h2>Take voicemail</h2>
    <p>Which individual or group should receive the voicemail?</p>
    <?= AppletUI::UserGroupPicker('permissions'); ?>
</div><!-- .vbx-applet -->
<div style="clear:both;"></div> 
