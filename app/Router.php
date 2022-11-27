<?php

namespace App;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use function FastRoute\simpleDispatcher;

class Router
{
    private Dispatcher $dispatcher;

    public function __construct()
    {
        $this->dispatcher = simpleDispatcher(function (RouteCollector $route) {
            $route->addRoute('GET', '/', ['App\Controllers\SearchResultsController', 'index']);
        });
    }

    public function handleUri(Environment $twig, ApiAccess $newsApi): void
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                try {
                    echo $twig->render('404.twig');
                } catch (LoaderError $e) {
                    echo "Twig Loader Error: " . $e->getMessage();
                } catch (RuntimeError $e) {
                    echo "Twig Runtime Error: " . $e->getMessage();
                } catch (SyntaxError $e) {
                    echo "Twig Syntax Error: " . $e->getMessage();
                }
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
//                $allowedMethods = $routeInfo[1];
                try {
                    echo $twig->render('405.twig');
                } catch (LoaderError $e) {
                    echo "Twig Loader Error: " . $e->getMessage();
                } catch (RuntimeError $e) {
                    echo "Twig Runtime Error: " . $e->getMessage();
                } catch (SyntaxError $e) {
                    echo "Twig Syntax Error: " . $e->getMessage();
                }
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
//                $vars = $routeInfo[2];
                [$controller, $method] = $handler;
                (new $controller)->$method($twig, $newsApi);
                break;
        }
    }
}