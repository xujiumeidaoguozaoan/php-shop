<?php


namespace app\api\controller\v1;


use app\api\Validate\Count;
use app\api\Validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;
use think\Controller;
use app\api\model\Product as ProductModel;

class Product extends Controller
{
    public function getRecent($count = 15)
    {
        $validate = new Count();
        $validate->goCheck();

        $products = ProductModel::getMostRecent($count);
        if(!$products) throw new ProductException();
        return $products;
    }

    public function getOne($id)
    {
        $validate = new IDMustBePositiveInt();
        $validate -> goCheck();

        $product = ProductModel::getProductDetail($id);
        if(!$product) throw new ProductException();
        return $product;
    }

    public function getAllInCategory($id)
    {
        $validate = new IDMustBePositiveInt();
        $validate -> goCheck();

        $products = ProductModel::getProductsByCategoryID($id);
        if(!$products) throw new ProductException();
        return $products;
    }
}