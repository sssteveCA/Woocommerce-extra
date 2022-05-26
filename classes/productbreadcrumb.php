<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\Constants as C;
use WoocommerceExtra\Interfaces\ProductBreadcrumbErrors as Pbe;

//This class returns the HTML category breadcrumb of single product page
class ProductBreadcrumb implements C,Pbe{
    private string $categoriesStr; //Categories string from product object
    private array $categoriesList = array(); //Categories list
    private array $urlList = array(); //Categories URL page
    private string $logFile; //Filesystem path of log file
    private static $regex = '/((?<=href=")([^"]+))*((?<=href=")([^"]+))/i'; //Capture URL in string

    public function __construct(array $data)
    {
        $this->categoriesStr = $data['categories'];
        $this->logFile = isset($data['logFile']) ? $data['logFile'] : C::FILE_LOG;
        $this->setCategoriesList();
        if(!$this->setUrlList())throw new \Exception(Pbe::INCORRECTPATTERN_EXC);
    }

    public function getCategoriesList(){return $this->categoriesList;}
    public function getCategoriesStr(){return $this->categoriesStr;}

    //Set URL list from given categories string
    private function setUrlList(): bool{
        $ok = false;
        $match = preg_match_all(ProductBreadcrumb::$regex,$this->categoriesStr,$matches);
        file_put_contents($this->logFile,"match => ".var_export($match,true)."\r\n",FILE_APPEND);
        file_put_contents($this->logFile,"categoriesStr => ".var_export($this->categoriesStr,true)."\r\n",FILE_APPEND);
        file_put_contents($this->logFile,"categoriesList => ".var_export($this->categoriesList,true)."\r\n",FILE_APPEND);
        file_put_contents($this->logFile,"matches => ".var_export($matches,true)."\r\n",FILE_APPEND);
        if($match){
            //String passed is valid
            $this->urlList = $matches[0];
            file_put_contents($this->logFile,"urlList => ".var_export($this->urlList,true)."\r\n",FILE_APPEND);
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