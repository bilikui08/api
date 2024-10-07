<?php

namespace Src\Infrastructure;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Src\Domain\Exception\AuthorizationException;
use Src\Infrastructure\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Swagger Docs para Api de Verifarma",
    version: "1.0.0",
    description: "Esta es una API de documentación con Swagger para el Challenge de Verifarma"
)]
#[OA\PathItem(
    path: "http://localhost/"
)]
#[OA\SecurityScheme(
    name:"Authorization",
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class Router
{
    private string $response = '';
    private Logger $logger;
    public function __construct(
        private Request $request,
        private array $routes
    ) {
    }

    public function dispatch(): string
    {
        $isNotFound = true;
        $appName = getenv('APP_NAME');
        $logPath = getenv('LOG_PATH');
        $logFile = getenv('LOG_FILE');
        $this->logger = new Logger($appName, [new StreamHandler($logPath . $logFile)]);

        foreach ($this->routes as $route) {
            $uriParams = $this->getParamsMatchRouteAndRequest($route['path'], $this->request->getUri());
            $queryParams = $this->extractQueryParams($this->request->getQueryString());

            $this->logger->debug("Route: " . $route['path']);
            $this->logger->debug("Route: " . $route['name']);

            if (!empty($uriParams) || $route['path'] === $this->request->getUri()) {
                $isNotFound = false;
                $this->logger->debug("Route match: " . $route['name']);
                if ($this->request->isMethod($route['method'])) {
                    try {
                        $arrController = explode('::', $route['controller']);
                        $className = $arrController[0];
                        $methodName = isset($arrController[1]) ? $arrController[1] : null;
                        $authorization = $route['authorization'] === 'true';
                        $controller = new $className($authorization, $this->logger);
                        $params = [];
                        if (isset($route['params']) && !empty($route['params'])) {
                            foreach ($route['params'] as $param) {
                                $params[] = new $param($this->logger);
                            }
                        }

                        if (!empty($uriParams)) {
                            $this->request->setUriParam($uriParams);
                        }

                        if (!empty($queryParams)) {
                            $this->request->setUriParam($queryParams);
                        }

                        if (null !== $methodName) {
                            $this->response = call_user_func_array([$controller, $methodName], [$this->request, ...$params]);
                        } else {
                            // By __invoke()
                            $this->response = call_user_func_array($controller, [$this->request, ...$params]);
                        }

                        break;
                    } catch (AuthorizationException $authorizationException) {
                        $this->logger->error("Route error: " . $authorizationException->getMessage());
                        http_response_code(401);
                        header('Content-type: application/json');
                        return json_encode(['message' => $authorizationException->getMessage()]);
                    }
                } else {
                    if (next($route) === false) {
                        $this->logger->error("Route error: Invalid method.");
                        http_response_code(405);
                        header('Content-type: application/json');
                        return json_encode(['message' => 'Invalid method.']);
                    }

                    continue;
                }
            }
        }

        if ($isNotFound) {
            http_response_code(404);
            header('Content-type: application/json');
            return json_encode(['message' => 'API not found.']);
        }

        return $this->response;
    }

    private function getParamsMatchRouteAndRequest(string $routeUri, string $requestUri): array
    {
        $params = [];
        if ($this->hasUriParameters($routeUri)) {
            $params = $this->getParamFromRequestUri($routeUri, $requestUri);
        }

        return $params;
    }

    private function hasUriParameters(string $uri): bool
    {
        if (preg_match_all('/\{(\w+)\}/', $uri, $matches)) {
            return !empty($matches[0]);
        }

        return false;
    }

    private function getParamFromRequestUri(string $routeUri, string $requestUri): array
    {
        $params = [];

        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $routeUri);
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';

        if (preg_match($pattern, $requestUri, $matches)) {
            preg_match_all('/\{(\w+)\}/', $routeUri, $paramMatches);
            $paramNames = $paramMatches[1];
            array_shift($matches);
            return array_combine($paramNames, $matches);
        }

        return $params;
    }

    private function extractQueryParams($uri): array
    {
        $pattern = '/\?(.*)$/';

        if (preg_match($pattern, $uri, $matches)) {
            $queryString = $matches[1];

            $queryParams = [];
            parse_str($queryString, $queryParams);

            return $queryParams;
        }

        return [];
    }
}
