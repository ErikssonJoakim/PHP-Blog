<?php declare(strict_types = 1);

namespace Blog\Foundation\Router;

use Blog\Foundation\AbstractController;
use Symfony\Component\Routing\Route as SymfonyRoute;

class Route {
    public const HTTP_METHODS = ['GET', 'POST', 'HEAD', 'PUT', 'PATCH', 'DELETE'];

    public static function __callStatic(string $httpMethod, array $arguments): SymfonyRoute {
        if(!in_array(strtoupper($httpMethod), static::HTTP_METHODS)) {
            throw new \BadMethodCallException();
            sprintf('HTTP method %s not available', $httpMethod);
        }

        [$uri, $actions] = $arguments;

        return static::createRoute(uri: $uri, actions: $actions, httpMethod: $httpMethod);
    }

    protected static function createRoute(string $uri, array $actions, string $httpMethod): SymfonyRoute {
        [$controller, $method] = $actions;

        if(!static::actionExists(controller: $controller, method: $method)) {
            throw new \InvalidArgumentException();
            sprintf('The action doesn\'t exist (%s)', implode(', ', $actions));
        }

        return new SymfonyRoute(path: $uri, defaults: [
            '_controller' => $controller,
            '_method' => $method
        ], 
        methods: [$httpMethod],
    options: [
        'utf8' => true
    ]);
    }

    protected static function actionExists(string $controller, string $method): bool {
        return class_exists($controller) && is_subclass_of($controller, AbstractController::class) && method_exists($controller, $method);
    }
}