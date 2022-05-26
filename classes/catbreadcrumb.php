<?php

namespace WoocommerceExtra\Classes;

//This class returns the HTML category breadcrumb of single product page
class CatBreadcrumb{
    private string $categoriesStr; //Categories string from product object
    private array $categoriesList; //Categories list
    public static $regex = '/((?<=href=")([^"]+))*((?<=href=")([^"]+))/i'; //Capture URL in string

    public function __construct(string $categoriesStr)
    {
        $this->categoriesStr = $categoriesStr;
    }

    public function getCategoriesList(){return $this->categoriesList;}
    public function getCategoriesStr(){return $this->categoriesStr;}
}
?>