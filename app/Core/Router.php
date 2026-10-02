<?php

namespace App\Core;

class Router
{
    private static array $routes = [];

    public static function get(string $path, array $handler, array $middlewares = []): void
    {
        self::add('GET', $path, $handler, $middlewares);
    }

    public static function post(string $path, array $handler, array $middlewares = []): void
    {
        self::add('POST', $path, $handler, $middlewares);
    }

    private static function add(string $method, string $path, array $handler, array $middlewares = []): void
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public static function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Normalize trailing slashes
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        foreach (self::$routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match

                // CSRF Check for POST requests
                if ($requestMethod === 'POST') {
                    $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
                    if (!Csrf::verify($token)) {
                        http_response_code(419);
                        require_once BASE_PATH . '/app/Views/errors/419.php';
                        exit;
                    }
                }

                // Run middlewares
                if (!Middleware::handle($route['middlewares'])) {
                    return;
                }

                [$controllerClass, $method] = $route['handler'];
                $controller = new $controllerClass();
                call_user_func_array([$controller, $method], $matches);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        require_once BASE_PATH . '/app/Views/errors/404.php';
    }
}
