<?php

namespace WoocommerceExtra\Classes;

//This class returns the HTML category breadcrumb of single product page
class CatBreadcrumb{
    private string $categoriesStr; //Categories string from product object
    private array $categoriesList = array(); //Categories list
    private array $urlList = array(); //Categories URL page
    private static $regex = '/((?<=href=")([^"]+))*((?<=href=")([^"]+))/i'; //Capture URL in string

    public function __construct(string $categoriesStr)
    {
        $this->categoriesStr = $categoriesStr;
        $this->setCategoriesList();
        if(!$this->setUrlList())throw new \Exception("");
    }

    public function getCategoriesList(){return $this->categoriesList;}
    public function getCategoriesStr(){return $this->categoriesStr;}

    //Set URL list from given categories string
    private function setUrlList(): bool{
        $ok = false;
        $match = preg_match_all(CatBreadcrumb::$regex,$this->categoriesStr,$matches);
        if($match){
            //String passed is valid
            $this->urlList = $matches[0];
            $ok = true;
        }
        return $ok;
    }

    //Delete HTML tags and get the Categories
    private function setCategoriesList(){
        $stripped = strip_tags($this->categoriesStr);
        $this->categoriesList = explode(',',$stripped);
    }
}
?>