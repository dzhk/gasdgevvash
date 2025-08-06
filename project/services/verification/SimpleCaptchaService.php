<?php

namespace app\services\verification;

class SimpleCaptchaService implements HumanVerificationInterface
{
    private string $expectedCode;

    public function __construct(string $expectedCode = 'sudke')
    {
        $this->expectedCode = $expectedCode;
    }

    public function verify(array $data): bool
    {
        $captcha = $data['captcha'] ?? '';
        return !empty($captcha) && strtolower($captcha) === strtolower($this->expectedCode);
    }
}