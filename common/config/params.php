<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'noreplyEmail' => 'noreply@example.com',
    // Secret codes expiration time
    'secureKey.expirationTime' => 48 * 60 * 60,
    // User password validation pattern
    'user.passwordPattern' => '/^[a-z0-9]+$/is',
    // User name validation pattern
    'user.namePattern' => '/^[\w\s]+$/is',
    // Send user account activation email (if false - new users will have an active status after registration)
    'user.sendActivationEmail' => false,
    // reCAPTCHA API keys [https://www.google.com/recaptcha/admin]
    'reCAPTCHA.siteKey' => '6Le7DjgUAAAAAEWMT94psWvKT4RQQMipErcIdKjN',
    'reCAPTCHA.secretKey' => '6Le7DjgUAAAAAJ_QVUJOgtK_v-hw2Ai9z-9yooZl',
    // Project invite minimal resend email interval
    'inviteMinResendInterval' => 5 * 50,
];
