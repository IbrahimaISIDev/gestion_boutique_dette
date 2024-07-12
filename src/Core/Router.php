<?php

namespace Src\Core;

use ReflectionClass;
use Src\App\Controller\ErrorController;

class Router
{
    private static $routes = [];

    public static function get($uri, $controllerAction)
    {
        $uri = preg_replace('#/{2,}#', '/', $uri);
        $controllerName = $controllerAction['Controller'];
        $action = $controllerAction['action'];
        self::$routes['GET'][$uri] = "{$controllerName} => {$action}";
    }

    public static function post($uri, $controllerAction)
    {
        $uri = preg_replace('#/{2,}#', '/', $uri);
        $controllerName = $controllerAction['Controller'];
        $action = $controllerAction['action'];
        self::$routes['POST'][$uri] = "{$controllerName} => {$action}";
    }

    public static function routePage($method, $uri)
    {
        $basePath = '/Diallo_Lebalma/public';
        $uri = str_replace($basePath, '', $uri);
        $uri = strtok($uri, '?');
        $uri = preg_replace('#/{2,}#', '/', $uri);

        if (isset(self::$routes[$method][$uri])) {
            $controllerAction = self::$routes[$method][$uri];

            if (preg_match('/^(?<controller>[^:]+) => (?<action>[^:]+)$/', $controllerAction, $matches)) {
                $controllerName = $matches['controller'];
                $action = $matches['action'];

                $controllerClass = "Src\\App\\Controller\\{$controllerName}";

                // Utilisation de ReflectionClass pour vérifier l'existence de la classe
                $reflectionClass = new ReflectionClass($controllerClass);

                if ($reflectionClass->isInstantiable()) {
                    $constructor = $reflectionClass->getConstructor();
                    
                    if ($constructor !== null) {
                        $parameters = $constructor->getParameters();
                        $dependencies = [];

                        foreach ($parameters as $parameter) {
                            $dependencyClass = $parameter->getClass();
                            if ($dependencyClass !== null) {
                                $dependencyClassName = $dependencyClass->getName();
                                $dependencyInstance = new $dependencyClassName(); // Instanciation automatique de la dépendance
                                $dependencies[] = $dependencyInstance;
                            } else {
                                throw new \Exception("Impossible de résoudre la dépendance pour {$parameter->getName()}");
                            }
                        }

                        $controllerInstance = $reflectionClass->newInstanceArgs($dependencies);
                    } else {
                        $controllerInstance = $reflectionClass->newInstance();
                    }

                    if ($reflectionClass->hasMethod($action)) {
                        $reflectionMethod = $reflectionClass->getMethod($action);
                        $reflectionMethod->invoke($controllerInstance);
                        return;
                    } else {
                        echo "La méthode {$action} n'existe pas dans le contrôleur {$controllerName}";
                    }
                } else {
                    echo "Le contrôleur {$controllerName} n'est pas instantiable";
                }
            } else {
                echo "Format invalide pour le contrôleur et l'action: {$controllerAction}";
            }
        }

        http_response_code(404);
        $errorController = new ErrorController();
        $errorController->error404();
    }

    public static function getRoutes()
    {
        return self::$routes;
    }
}
