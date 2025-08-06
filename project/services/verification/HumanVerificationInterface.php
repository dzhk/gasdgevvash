<?php

namespace app\services\verification;

interface HumanVerificationInterface
{
    public function verify(array $data): bool;
}