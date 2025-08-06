<?php
/** @var $widget CaptchaWidget */

use app\widgets\CaptchaWidget;

$widget->registerAssets();
?>
<div class="form-group">
    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
</div>

<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('<?= Yii::$app->params['captcha']['recaptchaV3']['siteKey'] ?>', {action: 'submit'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
        });
    });
</script>