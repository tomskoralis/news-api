<?php

namespace App;

use FastRoute\{Dispatcher, RouteCollector};
use Twig\Environment;
use Twig\Error\{LoaderError, RuntimeError, SyntaxError};
use Twig\Loader\FilesystemLoader;
use function FastRoute\simpleDispatcher;

class Router
{
    private Dispatcher $dispatcher;
    private Environment $twig;

    public function __construct()
    {
        $this->twig = new Environment(new FilesystemLoader('../views'));
        $this->dispatcher = simpleDispatcher(function (RouteCollector $route) {
            $route->addRoute('GET', '/', ['App\Controllers\ArticlesController', 'index']);
        });
    }

    public function handleUri(): void
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
                    echo $this->twig->render('templates/404.twig');
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
                    echo $this->twig->render('templates/405.twig');
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
                $vars = $routeInfo[2];
                [$controller, $method] = $handler;
                $response = (new $controller)->{$method}($vars);
                try {
                    echo $this->twig->render($response->getPath(), $response->getParameters());
                } catch (LoaderError $e) {
                    echo "Twig Loader Error: " . $e->getMessage();
                } catch (RuntimeError $e) {
                    echo "Twig Runtime Error: " . $e->getMessage();
                } catch (SyntaxError $e) {
                    echo "Twig Syntax Error: " . $e->getMessage();
                }
                break;
        }
    }
}