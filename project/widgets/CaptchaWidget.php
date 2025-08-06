<?php

namespace app\widgets;

use yii\base\Widget;
use app\services\verification\HumanVerificationInterface;
use Yii;

class CaptchaWidget extends Widget
{
    public function run()
    {
        $type = Yii::$app->params['captcha']['captchaType'] ?? 'simple';

        return $this->render('/widgets/captcha/' . $type, [
            'widget' => $this,
        ]);
    }

    public function registerAssets()
    {
        if (Yii::$app->params['captcha']['captchaType'] === 'recaptchaV3') {
            $this->view->registerJsFile(
                'https://www.google.com/recaptcha/api.js?render='.Yii::$app->params['captcha']['recaptchaV3']['siteKey'],
                ['async' => true, 'defer' => true]
            );
        }
        if (Yii::$app->params['captcha']['captchaType'] === 'recaptchaV2') {
            $this->view->registerJsFile(
                'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit',
                ['async' => true, 'defer' => true]
            );
        }
    }
}