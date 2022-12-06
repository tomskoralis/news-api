<?php

namespace App;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Twig\{Environment, Loader\FilesystemLoader};
use Twig\Error\{LoaderError, RuntimeError, SyntaxError};

class Twig
{
    private static Environment $twig;

    public static function renderTemplate(Template $template): void
    {
        if (!isset(self::$twig)) {
            self::$twig = new Environment(new FilesystemLoader("../views"));
            foreach (ClassMapGenerator::createMap("../app/ViewVariables") as $symbol => $path) {
                if (stripos($symbol, "interface") === false) {
                    $variable = new $symbol;
                    self::$twig->addGlobal($variable->getName(), $variable->getValue());
                }
            }
        }
        try {
            echo self::$twig->render($template->getPath(), $template->getParameters());
        } catch (LoaderError $e) {
            echo "Twig Loader Error: " . $e->getMessage();
        } catch (RuntimeError $e) {
            echo "Twig Runtime Error: " . $e->getMessage();
        } catch (SyntaxError $e) {
            echo "Twig Syntax Error: " . $e->getMessage();
        }
    }
}