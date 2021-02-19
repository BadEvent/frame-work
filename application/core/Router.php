<?php

namespace application\core;

/**
 * Class Router
 * @package application\core
 */
class Router
{
    /**
     * @var array
     */
    protected $routes = [];
    /**
     * @var array
     */
    protected $params = [];

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $arr = require 'application/config/routes.php';
        foreach ($arr as $key => $val) {
            $this->add($key, $val);
        }
    }

    /**
     * @param $route
     * @param $params
     */
    public function add($route, $params)
    {
        $route = preg_replace('/{([a-z]+):([^\}]+)}/', '(?P<\1>\2)', $route);
        $route = '#^' . $route . '$#';
        $this->routes[$route] = $params;
    }

    /**
     * @return bool
     */
    public function match()
    {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        if (is_numeric($match)) {
                            $match = (int) $match;
                        }
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * Запуск Контроллеров
     *
     * @return bool
     */
    public function run()
    {
        if ($this->match()) {
            // Объявляем имя Controller
            $path = 'application\controllers\\' . ucfirst($this->params['controller']) . 'Controller';

            // Существует ли данный класс?
            if (class_exists($path)) {

                // Объявляем имя Action
                $action = $this->params['action'] . 'Action';

                // Существует ли данный метод?
                if (method_exists($path, $action)) {

                    // Создаем экземпляр класса с именем => $this->params['controller']
                    $controller = new $path($this->params);

                    // Вызываем метод с именем $action класса $controller
                    $controller->$action();
                } else {
                    echo 'not found this Action-> ' . $action;
                }

            } else {
                echo 'not found this Class-> ' . $path;
            }
        } else {
            View::errorCode('404');
        }
    }
}