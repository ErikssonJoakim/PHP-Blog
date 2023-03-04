<?php declare(strict_types = 1);

return [
    'csrf_token_length' => (int) env(key: 'CSRF_TOKEN_LENGTH', default: 25)
];
