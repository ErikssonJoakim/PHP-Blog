<?php declare(strict_types = 1);

namespace Blog\Foundation;

use Illuminate\Database\Capsule\Manager as Capsule;
use Blog\Foundation\Exceptions\HttpException;
use Blog\Foundation\Router\Router;
use Blog\Foundation\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;

class App {

    protected Router $router;

    public function __construct() {
        $this->initDotenv();
        if(Config::get(config: 'app.environment') === 'production') {
            $this->initProductionExceptionHandler();
            $this->initProductionErrorHandler();
        }
        $this->initSession();
        $this->initDatabase();
        $this->router = new Router(require ROOT.'/app/routes.php');
    }

    protected function initDotenv(): void {
        $dotenv = \Dotenv\Dotenv::createImmutable(paths: ROOT);

        $dotenv->safeLoad();
    }

    protected function initProductionExceptionHandler(): void {
        set_exception_handler(
            fn () => HttpException::render(httpCode: 500, message: "Oopsi, there is a problem with the site. Please let us know about this error! ✉️")
        );
    }

    protected function initProductionErrorHandler(): void {
        error_reporting(error_level: 0);
    }

    protected function initSession(): void {
        Session::init();
        Session::addValue(key: '_token', value: Session::getValue(key: '_token') ?? $this->generateCsrToken());
    }

    protected function generateCsrToken(): string {
        $length = Config::get(config: 'hashing.csrf_token_length');

        return bin2hex(random_bytes($length));
    }

    protected function initDatabase(): void {
        date_default_timezone_set(Config::get(config: 'app.timezone'));
        
        $capsule = new  Capsule();
        $capsule->addConnection(config: [
            'driver' => Config::get(config: 'database.driver'),
            'host' => Config::get(config: 'database.host'),
            'database' => Config::get(config: 'database.name'),
            'username' => Config::get(config: 'database.username'),
            'password' => Config::get(config: 'database.password'),
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
 
    public function render() {
        $this->router->getInstance();
        Session::resetFlash();
    }

    public function getGenerator(): UrlGenerator {
        return $this->router->getGenerator();
    }
}