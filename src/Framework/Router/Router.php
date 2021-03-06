<?php
declare(strict_types = 1);

namespace LapisAngularis\Senshu\Framework\Router;

use LapisAngularis\Senshu\Framework\Http\HttpRequest;

class Router
{
    private $routeCollection;
    private $httpRequest;

    public function __construct(RouteCollection $collection, HttpRequest $httpRequest)
    {
        $this->routeCollection = $collection;
        $this->httpRequest = $httpRequest;
    }

    public function matchRequest(): void
    {
        $requestMethod = $this->httpRequest->getMethod();
        $requestPath = $this->httpRequest->getUri();

        foreach ($this->routeCollection->getRoutes() as $route) {
            if (!$route->matches($requestPath)) {
                continue;
            }

            if ($requestMethod !== $route->getMethod()) {
                continue;
            }

            $arguments = Parser::parseArgumentData($requestPath, $route);

            if (!empty($arguments)) {
                $route->setArguments($arguments);
            }

            $route->dispatch();
        }
    }

    public function generateUrl(string $action, array $arguments = []): ?string
    {
        $route = $this->routeCollection->getRoutes()[$action];
        $path = $route->getOriginalPath();
        $variables = $route->getVariables();

        $url = Parser::parsePathToUrl($path, $variables, $arguments);

        return $url;
    }
}
