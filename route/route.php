<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::rule('index.php', 'index');
Route::rule('label','index/label');
Route::rule('about', 'index/about');
Route::rule('message','index/message');
Route::get('read/:id','article/read');
Route::get('lst/:label','label/lst');
Route::rule('link','index/link');
return [

];
