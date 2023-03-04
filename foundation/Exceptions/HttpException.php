<?php declare(strict_types = 1);

namespace Blog\Foundation\Exceptions;

use Blog\Foundation\View;
use Blog\Foundation\Internationalization as Language;

class HttpException extends \Exception {
    public static function render(int $httpCode = 404, string $message = null): void {
        http_response_code($httpCode);


        View::render('errors.default', [
            'httpCode' => $httpCode,
            'message' => $message ?? Language::getTranslation(subject: 'errors' , key: '404')
        ]);
        die;
    }
}