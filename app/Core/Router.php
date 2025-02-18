<?php

namespace App\Core;

/**
 * Handles application routing.
 *
 * This class allows defining routes for HTTP methods GET, POST, PUT, and DELETE.
 * It executes the corresponding callback function when a registered route is accessed.
 *
 * @package App\Core
 */
class Router
{
    /**
     * Stores registered routes in the application.
     * 
     * The array structure follows: ['METHOD' => ['URI' => callback]].
     *
     * @var array<string, array<string, callable>>
     */
    private array $routes = [];

    /**
     * Registers a GET route.
     *
     * @param string $uri The URI of the route.
     * @param callable $callback The function to execute when the route is accessed.
     * @return void
     */
    public function get(string $uri, callable $callback): void
    {
        $this->routes['GET'][$uri] = $callback;
    }

    /**
     * Registers a POST route.
     *
     * @param string $uri The URI of the route.
     * @param callable $callback The function to execute when the route is accessed.
     * @return void
     */
    public function post(string $uri, callable $callback): void
    {
        $this->routes['POST'][$uri] = $callback;
    }

    /**
     * Registers a PUT route.
     *
     * @param string $uri The URI of the route.
     * @param callable $callback The function to execute when the route is accessed.
     * @return void
     */
    public function put(string $uri, callable $callback): void
    {
        $this->routes['PUT'][$uri] = $callback;
    }

    /**
     * Registers a DELETE route.
     *
     * @param string $uri The URI of the route.
     * @param callable $callback The function to execute when the route is accessed.
     * @return void
     */
    public function delete(string $uri, callable $callback): void
    {
        $this->routes['DELETE'][$uri] = $callback;
    }

    /**
     * Registers an OPTIONS route.
     *
     * @param string $uri The URI of the route.
     * @param callable $callback The function to execute when the route is accessed.
     * @return void
     */
    public function options(string $uri, callable $callback): void
    {
        $this->routes['OPTIONS'][$uri] = $callback;
    }

    /**
     * Build a regex pattern for the given route.
     *
     * - Si la ruta contiene parámetros del estilo {id} se reemplazan por un grupo de captura.
     * - Si la ruta contiene paréntesis, se asume que es un patrón regex definido por el usuario.
     * - Si es una ruta estática, se escapan los caracteres especiales.
     *
     * @param string $route La definición de la ruta.
     * @return string El patrón regex resultante.
     */
    private function buildPattern(string $route): string
    {
        // Si la ruta tiene parámetros {param}
        if (strpos($route, '{') !== false) {
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
            return "@^" . $pattern . "$@";
        }
        // Si la ruta contiene paréntesis, asumimos que es un patrón regex proporcionado
        if (strpos($route, '(') !== false) {
            return "@^" . $route . "$@";
        }
        // Para rutas estáticas, escapamos los caracteres especiales
        return "@^" . preg_quote($route, '@') . "$@";
    }

    /**
     * Executes the corresponding route based on the client request.
     * 
     * If the route exists, it executes the associated callback.
     * If the route does not exist, it returns a 404 JSON response.
     *
     * @return void
     */
    public function run(): void
    {
        $requestedMethod = $_SERVER['REQUEST_METHOD'];
        $requestedUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!isset($this->routes[$requestedMethod])) {
            http_response_code(404);
            echo json_encode(["error" => "Route not found"]);
            return;
        }

        foreach ($this->routes[$requestedMethod] as $route => $callback) {
            $pattern = $this->buildPattern($route);

            if (preg_match($pattern, $requestedUri, $matches)) {
                array_shift($matches);
                call_user_func_array($callback, $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(["error" => "Route not found"]);
    }
}
