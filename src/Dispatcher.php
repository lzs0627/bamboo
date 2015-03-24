<?php
/**
 * Created by PhpStorm.
 * User: lizhaoshi
 * Date: 2015/01/14
 * Time: 12:19
 */
namespace IQnote\Bamboo;

use \IQnote\Bamboo\Controller\Controller as Controller;

class Dispatcher
{
    protected $routes;
    protected $actionRoot;
    protected static $instance = null;
    public $params;
    public static $nomacthStr;

    private function __construct()
    {
        $this->routes = Config::get('routing');
        if (empty($this->routes)) {
            trigger_error('routing undefined');
        }

        if (defined('APP_ACTION_ROOT')) {
            $this->actionRoot = APP_ACTION_ROOT;
        } else {
            trigger_error('APP_ACTION_ROOT undefined');
        }
    }

    /**
     * Dispatcherを初期化する
     * @return Dispatcher
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new Dispatcher();
        }

        return static::$instance;
    }

    /**
     * @param string $requestUri
     */
    public function dispatch($requestUri = null)
    {
        if (! $requestUri) {
            $requestUri = server_variable("REQUEST_URI");
        }

        if (strpos($requestUri, '?') !== false) {
            $requestUri = substr($requestUri, 0, strpos($requestUri, '?'));
        }

        if (strpos($requestUri, '#') !== false) {
            $requestUri = substr($requestUri, 0, strpos($requestUri, '#'));
        }

        if (static::$nomacthStr) {
            $requestUri = str_replace(static::$nomacthStr, '', $requestUri);
        }

        $path = $this->parsePath($requestUri);
        if ($path) {
            $actionPath = $this->actionRoot . DIRECTORY_SEPARATOR . ltrim($path, '/');
        } else {
            throw new \Exception();
        }

        $controller = new Controller($actionPath, $this->params['_route']['id'], $this->params);
        $controller->reponse();
    }

    /**
     * ルーティング情報を探す
     * @param string $requestUri
     * @return bool
     */
    public function parsePath($requestUri)
    {
        foreach ($this->routes as $route) {
            if (isset($route['str']) && (rtrim($route['str'], '/') == rtrim($requestUri, '/')) ) {
                $this->params['_route'] = $route;

                return $route['path'];

            } elseif (isset($route['rexp']) && (preg_match($route['rexp'],$requestUri,$matches))) {
                $this->params['_route'] = $route;
                if (isset($route['match']) && is_array($route['match'])) {
                    foreach($route['match'] as $i=>$key) {
                        $this->params[$key]=$matches[$i+1];
                    }
                }

                return $route['path'];
            }
        }

        return false;
    }

    /**
     * ルーティングＩＤでＵＲＩを作成
     * @param string $id
     * @param array $params
     * @return bool|mixed
     */
    public function getUri($id, $params = null)
    {
        $route = null;

        foreach($this->routes as $r){
            if ($r['id'] != $id) {
                continue;
            }
            $route = $r;
            break;
        }

        if (is_null($route)) {
            return false;
        }

        $uri = false;
        if (isset($route['rexp'])) {
            $uri = str_replace('\\', '', $route['rexp']);
            if (isset($route['match']) && is_array($route['match'])) {
                foreach($route['match'] as $key) {
                    if (! array_key_exists($key, $params) ) {
                        return false;
                    }
                    $uri = preg_replace("/\([^)]+\)/", urlencode($params[$key]), $uri, 1);
                }
            }
        } elseif (isset($route['str'])) {
            $uri = $route['str'];
        }

        return $uri;
    }
}