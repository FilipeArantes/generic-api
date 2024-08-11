<?php

namespace App\Core;

use App\Core\Responses\Exceptions\AppError;
use App\Core\Responses\Responses;

abstract class AbstractRouter
{
    protected array $routes = [];

    public function addRoute(string $method, string $uri, string $controller, string $action, ?string $middleware = null): void
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public function execute(string $verb, string $uri)
    {
        foreach ($this->routes as $route) {
            $placeholders = [];
            $pattern = preg_replace_callback('/\{([^\}]+)\}/', function ($matches) use (&$placeholders) {
                $placeholders[] = $matches[1];

                return '([^\/]+)';
            }, $route['uri']);

            if ($verb == $route['method'] && preg_match("#^{$pattern}$#", $uri, $matches)) {
                $params = array_combine($placeholders, array_slice($matches, 1));
                $data = json_decode(file_get_contents('php://input'), true);

                $controller = new $route['controller']();

                try {
                    if ('POST' == $verb) {
                        $response = call_user_func_array([$controller, $route['action']], [$data]);
                    }
                    if ('PUT' == $verb) {
                        $response = call_user_func_array([$controller, $route['action']], [$params, $data]);
                    }
                    if ('GET' == $verb && count($params)) {
                        $response = call_user_func_array([$controller, $route['action']], [current($params), $data]);
                    }
                    if ('DELETE' == $verb && count($params)) {
                        $response = call_user_func_array([$controller, $route['action']], [$params]);
                    }
                    if ('GET' == $verb && !count($params)) {
                        $response = call_user_func_array([$controller, $route['action']], [$data]);
                    }
                } catch (AppError $th) {
                    return Responses::notAcceptable($th);
                } catch (\Throwable $th) {
                    return Responses::failed($th);
                }

                print json_encode($response);

                return;
            }
        }
    }
}
