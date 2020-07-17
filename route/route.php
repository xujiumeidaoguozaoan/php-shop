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
// 首页轮播图
Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

// 获取主题全部主题 和 单个主题
Route::get('api/:version/theme','api/:version.Theme/getSimpleList');
Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');

// 获取分类产品列表、单个产品、最近新品
Route::get('api/:version/product/by_category/:id', 'api/:version.Product/getAllInCategory');
Route::get('api/:version/product/:id', 'api/:version.Product/getOne',[],['id'=>'\d+']);
Route::get('api/:version/product/recent','api/:version.Product/getRecent');
// 获取所有分类
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');

// 请求用户token
Route::post('api/:version/token/user', 'api/:version.Token/getToken');

// 添加或更新用户收货地址
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');

// 订单
Route::post('api/:version/order', 'api/:version.Order/placeOrder');
//详细订单
Route::get('api/:version/order/:id', 'api/:version.Order/getDetail',
    [],['id'=>'\d+']);
// 历史订单
Route::get('api/:version/order/by_user', 'api/:version.Order/getSummaryByUser');
// 预订单
Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');
// 支付回调地址
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');
