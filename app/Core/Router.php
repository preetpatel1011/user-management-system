<?php
namespace App\Core;

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Core\Mailer;
use App\Services\AdminService;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\MailService;

class Router
{
    private array $routes = [];

    public function get(string $uri, string $action, ?string $middleware = null): void
    {
        $this->routes['GET'][$uri] = ['action' => $action, 'middleware' => $middleware];
    }

    public function post(string $uri, string $action, ?string $middleware = null): void
    {
        $this->routes['POST'][$uri] = [
            'action'     => $action,
            'middleware' => $middleware,
        ];
    }

    private function resolveController(string $controllerClass): object
    {
        return match ($controllerClass) {
            'App\\Controllers\\AdminController' => new AdminController(
                new AdminService(new Database(), new MailService(new Mailer())),
                new UserService(new Database())
            ),
            'App\\Controllers\\UserController' => new UserController(
                new UserService(new Database()),
                new AdminService(new Database(), new MailService(new Mailer()))
            ),
            'App\\Controllers\\AuthController' => new AuthController(
                new AuthService(new Database(), new MailService(new Mailer()))
            ),
            default => new $controllerClass
        };
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        if (empty($uri)) {
            $uri = '/';
        }

        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            require __DIR__ . '/../Views/layouts/404.php';
            return;
        }

        $route = $this->routes[$method][$uri];

        if ($route['middleware']) {
            $middlewareClass = "App\\Middleware\\{$route['middleware']}";
            (new $middlewareClass)->handle();
        }

        [$controllerName, $methodName] = explode('@', $route['action']);
        $controllerClass = "App\\Controllers\\$controllerName";

        // (new $controllerClass)->$methodName();
        $controller = $this->resolveController($controllerClass);
        $controller->$methodName();
    }
}