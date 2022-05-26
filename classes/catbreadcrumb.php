<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\Constants as C;
use WoocommerceExtra\Interfaces\CatBreadcrumbErrors as Cbe;

//This class returns the HTML category breadcrumb of single product page
class CatBreadcrumb implements C,Cbe{
    private string $categoriesStr; //Categories string from product object
    private array $categoriesList = array(); //Categories list
    private array $urlList = array(); //Categories URL page
    private static $regex = '/((?<=href=")([^"]+))*((?<=href=")([^"]+))/i'; //Capture URL in string
    private static $logFile = C::FILE_LOG;

    public function __construct(string $categoriesStr)
    {
        $this->categoriesStr = $categoriesStr;
        $this->setCategoriesList();
        if(!$this->setUrlList())throw new \Exception(Cbe::INCORRECTPATTERN_EXC);
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
            file_put_contents(CatBreadcrumb::$logFile,"urlList => ".var_export($this->urlList,true)."\r\n",FILE_APPEND);
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