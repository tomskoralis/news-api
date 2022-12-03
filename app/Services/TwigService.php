<?php

namespace App\Services;

use App\Template;
use Twig\{Environment, Loader\FilesystemLoader, TwigFunction};
use Twig\Error\{LoaderError, RuntimeError, SyntaxError};
use function App\loadUserService;

class TwigService
{
    private Environment $twig;

    public function __construct()
    {
        $this->twig = new Environment(new FilesystemLoader("../views"));
        $this->twig->addGlobal('session', $_SESSION);
        $this->twig->addFunction(
            new TwigFunction('getUsername', function ($id) {
                return loadUserService()->getNameFromId($id) ?: "unknown";
            })
        );
    }

    public function renderTemplate(Template $template): void
    {
        try {
            echo $this->twig->render($template->getPath(), $template->getParameters());
        } catch (LoaderError $e) {
            echo "Twig Loader Error: " . $e->getMessage();
        } catch (RuntimeError $e) {
            echo "Twig Runtime Error: " . $e->getMessage();
        } catch (SyntaxError $e) {
            echo "Twig Syntax Error: " . $e->getMessage();
        }
    }
}