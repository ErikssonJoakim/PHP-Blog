<?php declare(strict_types = 1);

use Blog\Foundation\Authentication;
use Blog\Foundation\Router\Router;
use Blog\Foundation\Session;
use Blog\Foundation\View;
use Blog\Foundation\Internationalization as Language;

if(!function_exists(function: 'initAuthentication')) {
    function initAuthentication(): Authentication {
        return new Authentication();
    }
}

if(!function_exists(function: 'getRoute')) {
    function getRoute(string $name, array $data = []): string  {
        return Router::get(name: $name, data: $data);
    }
}

if(!function_exists(function: 'getErrors')) {
    function getErrors(?string $field = null): ?array  {
        $errors = Session::getFlash(key: Session::ERRORS);
        return $field ? $errors[$field] ?? null : $errors;
    }
}

if(!function_exists(function: 'getStatus')) {
    function getStatus(): ?string  {
        return Session::getFlash(key: Session::STATUS) ?? null;
    }
}
if(!function_exists(function: 'createCsrfField')) {
    function createCsrfField(): string  {
        return View::csrfField();
    }
}

if(!function_exists(function: 'createMethodField')) {
    function createMethodField(string $httpMethod): string  {
        return View::methodField( httpMethod: $httpMethod);
    }
}

if(!function_exists(function: 'getOldValues')) {
    function getOldValues(string $key, mixed $default = null): mixed  {
        return View::oldValues(key: $key, default: $default);
    }
}

if(!function_exists(function: 'getTranslation')) {
    function getTranslation(string $subject, string $key): string  {
        return Language::getTranslation(subject: $subject ,key: $key);
    }
}