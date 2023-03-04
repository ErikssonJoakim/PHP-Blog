<?php declare(strict_types = 1);

namespace Blog\Foundation;

use Blog\Foundation\Router\Router;

abstract class AbstractController {
    protected function redirect(string $to, array $data = []): void {
        header(sprintf('Location: %s', Router::get($to, $data)));
        die;
    }
}