# openroutes

>轻量级php路由

###安装
>编辑composer.json
```json
{
    "require": {
        "openroutes/openroutes": "dev-master",
    }
}
```

>执行 composer update

###使用
```php
require './vendor/autoload.php';
use \openroutes\openroutes\openroutes;
openroutes::get('index', function () {
    echo 'index';
});
openroutes::run();
```
###示例

#####get/post/delete/put
```php
openroutes::get('article/info', function () {
     echo 'article/info';
});
```
#####any
```php
openroutes::any('article/info', function () {
     echo 'article/info';
});
```
#####match
```php
openroutes::match(['get', 'post'], 'article/info', function () {
     echo 'article/info';
});
```
#####带参数
```php
openroutes::get('article/id/{$id}', function ($id) {
     echo 'article/id'.$id;
});
```
#####参数验证
```php
openroutes::get('article/id/{$id}', function ($id) {
     echo 'article/id'.$id;
})->verify(['id' => '/^[0-9]*$/']);
```

12321321
