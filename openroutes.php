<?php
namespace openroutes\openroutes;

/**
 * @author      jiangkaiqiang
 * @email       1227427774@qq.com
 * @date        2016/07/12
 * @version     :1.0
 */

class openroutes
{
    //支持数组
    private static $methods = ['get', 'post', 'delete', 'put', 'any', 'match'];
    //路由数组
    private static $routes = [];
    //请求method
    private static $requestMethod = '';
    //请求路由
    private static $requestRoute = [];
    //路由参数
    private static $routeParams = [];

    public static function __callstatic($method, $params)
    {
        if (!in_array($method, self::$methods)) {
            self::errorLog("不支持该方法", true);
        }
        if ($method == 'any') {
            self::$routes[] = [
                'methods'  => ['get', 'post', 'delete', 'put'],
                'routes'   => explode('/', $params[0]),
                'callback' => $params[1],
            ];
        } elseif ($method == 'match') {
            self::$routes[] = [
                'methods'  => $params[0],
                'routes'   => explode('/', $params[1]),
                'callback' => $params[2],
            ];
        } else {
            self::$routes[] = [
                'methods'  => [$method],
                'routes'   => explode('/', $params[0]),
                'callback' => $params[1],
            ];
        }
        return new openroutes();
    }

    /**
     * 添加路由参数验证规则
     * @param  array $preg
     */
    public function verify($preg)
    {
        if (!is_array($preg)) {
            self::errorLog("验证参数必须为array", true);
        }
        self::$routes[count(self::$routes) - 1]['rule'] = $preg;
    }

    /**
     * 运行路由
     */
    public static function run()
    {
        self::$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
        self::$requestRoute  = explode('/', str_replace($_SERVER['SCRIPT_NAME'] . '/', '', $_SERVER['REQUEST_URI']));
        foreach (self::$routes as $key => $value) {
            if (self::verifyRoute($key)) {
                //规则验证
                self::verifyRule($key);
                //运行callback
                self::runCallback($key);
            }
        }
        self::errorLog("没有可匹配的路由", true);
    }

    /**
     * 匹配路由
     * @param  int $routeIndex 路由索引
     * @return boolen  true:匹配成功/false:匹配失败
     */
    private static function verifyRoute($routeIndex)
    {
        if (!in_array(self::$requestMethod, self::$routes[$routeIndex]['methods'])) {
            return false;
        }
        if (count(self::$requestRoute) != count(self::$routes[$routeIndex]['routes'])) {
            return false;
        }
        foreach (self::$requestRoute as $requestRouteKey => $requestRouteValue) {
            if ($requestRouteValue != self::$routes[$routeIndex]['routes'][$requestRouteKey]) {
                if (!preg_match('/^{(.*)}$/', self::$routes[$routeIndex]['routes'][$requestRouteKey], $matchParamName)) {
                    return false;
                }
                //添加参数
                self::$routeParams[$matchParamName[1]] = $requestRouteValue;
            }
        }
        return true;
    }

    /**
     * 验证路由参数规则
     * @param  int $routeIndex 路由索引
     */
    private static function verifyRule($routeIndex)
    {
        if (isset(self::$routes[$routeIndex]['rule'])) {
            foreach (self::$routes[$routeIndex]['rule'] as $key => $value) {
                if (!preg_match($value, self::$routeParams[$key])) {
                    self::errorLog("{$key}格式错误", true);
                }
            }
        }
    }

    /**
     * 运行闭包函数
     * @param  int $routeIndex 路由索引
     */
    private static function runCallback($routeIndex)
    {
        if (self::$routeParams) {
            //有参数
            call_user_func_array(self::$routes[$routeIndex]['callback'], array_values(self::$routeParams));
            exit();
        } else {
            //无参数
            call_user_func(self::$routes[$routeIndex]['callback']);
            exit();
        }
    }

    /**
     * 错误日志
     * @param  string  $tips
     * @param  boolean $exit
     */
    private static function errorLog($tips, $exit = false)
    {
        echo sprintf("<p style='font-size:48px'>openroutes error:%s<p>", $tips);
        if ($exit == true) {
            exit();
        }
    }
}
