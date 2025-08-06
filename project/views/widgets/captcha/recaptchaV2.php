<?php
/** @var $widget CaptchaWidget */

use app\widgets\CaptchaWidget;

$widget->registerAssets();
?>
<div class="form-group">
    <div id="recaptcha-container"></div>
</div>

<script>
    var onloadCallback = function() {
        var widgetId = grecaptcha.render('recaptcha-container', {
            'sitekey': '<?= Yii::$app->params['captcha']['recaptchaV2']['siteKey'] ?>',
            'callback': function(response) {
                console.log('Captcha verified:', response);
            },
            'expired-callback': function() {
                console.log('Captcha expired');
                grecaptcha.reset(widgetId);
            }
        });

        // Принудительно показываем капчу (имитируем сомнение)
        setTimeout(function() {
            var iframe = document.querySelector('iframe[src*="recaptcha/api2/bframe"]');
            if (iframe) {
                iframe.contentWindow.postMessage(JSON.stringify({
                    event: 'clienterror',
                    error: '2fa',
                    message: 'Not a human'
                }), 'https://www.google.com');
            }
        }, 1000);
    };
</script>