<?php declare(strict_types = 1);

return [
    'template_extension' => 'html',
    'functions' => [
        'initAuthentication', 
        'getRoute', 
        'getErrors', 
        'getStatus', 
        'createCsrfField', 
        'createMethodField', 
        'getOldValues',
        'getTranslation'
    ]
];
