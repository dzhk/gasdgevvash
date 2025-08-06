<?php

namespace app\services\verification;

use Yii;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

class RecaptchaV3Service implements HumanVerificationInterface
{
    private string $secretKey;
    private string $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    private float $threshold = 0.5; // Пороговое значение для прохождения проверки

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function verify(array $data): bool
    {
        $recaptchaResponse = ArrayHelper::getValue($data, 'g-recaptcha-response');

        if (empty($recaptchaResponse)) {
            return false;
        }

        $client = new Client();
        $response = $client->post($this->verifyUrl, [
            'secret' => $this->secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => Yii::$app->request->userIP
        ])->send();

        if (!$response->isOk) {
            return false;
        }

        $responseData = $response->data;

        Yii::debug($responseData, 'recaptcha');

        $success = (bool)ArrayHelper::getValue($responseData, 'success', false);
        $score = (float)ArrayHelper::getValue($responseData, 'score', 0);

        return $success && $score >= $this->threshold;
    }
}