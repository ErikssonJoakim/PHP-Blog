<?php declare(strict_types = 1);

namespace Blog\Foundation\Router;

use Blog\Foundation\Exceptions\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Router {
    protected RouteCollection $routes;
    protected RequestContext $context;
    protected Request $request;
    protected array $params;
    protected string $controller;
    protected string $method;

    public function __construct(array $routes) {
        $this->initCSRF();
        $this->provisionRoutes(routes: $routes);
        $this->createRequestContext();

        try {
            [$this->controller, $this->method] = $this-> urlMatching();
        } catch(\Exception) {
            HttpException::render();
        }
    }

    protected function initCSRF(): void {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                !isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['_token'] &&
                throw new HttpException();
            } catch(HttpException) {
                HttpException::render(httpCode: 403, message: 'This action is not allowed!');
            }
        }
    }

    protected function provisionRoutes(array $routes): void {
        $this->routes = new RouteCollection();

        foreach ($routes as $key => $route) {
            $this->routes->add($key, $route);
        }
    }

    protected function createRequestContext(): void {
        $this->request = Request::createFromGlobals();
        $this->context = new RequestContext();
        $this->context->fromRequest($this->request);

        if(isset($_POST['_method']) && in_array(strtoupper($_POST['_method']), Route::HTTP_METHODS)) {
            $this->context->setMethod($_POST['_method']); 
        }
    }

    protected function urlMatching(): array {
        $matcher = new UrlMatcher($this->routes, $this->context);
        $this->params = $matcher->match($this->request->getPathInfo());

        return [$this->params['_controller'], $this->params['_method']];
    }

    public function getInstance(): void {
        $this->cleanParams();
        call_user_func_array(callback: [new $this->controller(), $this->method], args: $this->params);
        
    }

    protected function cleanParams(): void {
        foreach ($this->params as $key => $_) {
            if (str_starts_with($key, '_')) {
                unset($this->params[$key]);
            }
        }
    }

    public function getGenerator(): UrlGenerator {
        return new UrlGenerator(routes: $this->routes, context: $this->context);
    }

    public static function get(string $name, array $data = []): string {
        $generator = $GLOBALS['app']->getGenerator();
        $uri = $generator->generate($name, $data);

        return $uri;
    }
}