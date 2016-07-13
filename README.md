# openroutes
>轻量级php路由

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
