<?php
namespace app\services\verification;
use app\services\verification\HumanVerificationInterface;

class VerificationFactory
{
    public static function create(string $type, array $config = []): HumanVerificationInterface
    {
        switch ($type) {
            case 'simple':
                return new \app\services\verification\SimpleCaptchaService($config['code'] ?? 'sudke');
            case 'recaptchaV3':
                if (empty($config['siteKey'])) {
                    throw new \InvalidArgumentException('Recaptcha secret key is required');
                }
                return new \app\services\verification\RecaptchaV3Service($config['siteKey']);
            case 'recaptchaV2':
                if (empty($config['siteKey'])) {
                    throw new \InvalidArgumentException('Recaptcha secret key is required');
                }
                return new \app\services\verification\RecaptchaV2Service($config['siteKey']);
            default:
                throw new \InvalidArgumentException("Unknown verification type: {$type}");
        }
    }

    public static function getAvailableTypes(): array
    {
        return [
            'simple' => 'Простая капча',
            'recaptcha' => 'Google reCAPTCHA',
        ];
    }
}