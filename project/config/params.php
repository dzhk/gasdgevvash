<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    'captcha' => [
        'captchaType' => 'recaptchaV2', //'simple', // 'recaptchaV3'

        'simple' => [
            'code' => 'sudke',
        ],

        'recaptchaV3' => [
            'siteKey' => getenv('RECAPTHCA_SITE_KEY') ?: '6LeqrZwrAAAAAJoBII2HNLx0ur7vAOWkH0nYoOOc',
            'secretKey' => getenv('RECAPTHCA_SECRET_KEY') ?: '6LeqrZwrAAAAALSMmTHvJJiZ7ADLUtJpgthbUco4',
        ],
        'recaptchaV2' => [
            'siteKey' => /*'6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI', */getenv('RECAPTHCA_SITE_KEY') ?: '6LfTsZwrAAAAAKo4GFzF7KuJtdEFxSM1M5Dv4fK2',
            'secretKey' => /*'6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe', */getenv('RECAPTHCA_SECRET_KEY') ?: '6LfTsZwrAAAAALzQtlSHU2tHVbeS8H3Hw3rOYsiL',
        ]
    ]
];
