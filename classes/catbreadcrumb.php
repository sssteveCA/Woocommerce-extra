<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\Constants as C;
use WoocommerceExtra\Interfaces\CatBreadcrumbErrors as Cbe;
use WoocommerceExtra\Classes\Breadcrumb;

//This class returns the HTML category breadcrumb of single product page

class CatBreadcrumb extends Breadcrumb implements C,Cbe{
    private string $categoriesStr; //Categories string from product object
    private array $categoriesList = array(); //Categories list
    private array $urlList = array(); //Categories URL page
    private string $homepage; //URL homepage
    private string $shoppage; //URL of the shop page
    private static $regex = '/((?<=href=")([^"]+))*((?<=href=")([^"]+))/i'; //Capture URL in string


    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->homepage = isset($data['homepage']) ? $data['homepage'] : C::PAGES_HOME;
        $this->shoppage = isset($data['shoppage']) ? $data['shoppage'] : C::PAGES_SHOP;
        $this->categoriesStr = $data['categories'];
        $this->setCategoriesList();
        if(!$this->setUrlList())throw new \Exception(Cbe::INCORRECTPATTERN_EXC);
        if(!$this->formatArray())throw new \Exception(Cbe::URL_CAT_ARRAY_LENGTHMISMATCH);
        $this->setBreadcrumb();
    }

    public function getCategoriesList(){return $this->categoriesList;}
    public function getCategoriesStr(){return $this->categoriesStr;}

    //Delete HTML tags and get the Categories
    private function setCategoriesList(){
        $stripped = strip_tags($this->categoriesStr);
        $this->categoriesList = explode(',',$stripped);
    }

    //Set URL list from given categories string
    private function setUrlList(): bool{
        $ok = false;
        $match = preg_match_all(CatBreadcrumb::$regex,$this->categoriesStr,$matches);
        if($match){
            //String passed is valid
            $this->urlList = $matches[0];
            //file_put_contents($this->logFile,"urlList => ".var_export($this->urlList,true)."\r\n",FILE_APPEND);
            $ok = true;
        }
        return $ok;
    }

    //Format the array in the correct way for generate HTML
    private function formatArray(): bool{
        $format = false;
        $this->catInfo[0] = ['Home',$this->homepage];
        $this->catInfo[1] = ['Prodotti',$this->shoppage];
        //The length of urlList and categoriesLIst must be the same
        $catListL = count($this->categoriesList);
        $urlListL = count($this->urlList); 
        if($catListL == $urlListL){
            for($i = 0; $i < $catListL; $i++){
                $this->catInfo[] = [
                    $this->categoriesList[$i],
                    $this->urlList[$i]
                ];
            }//for($i = 0; $i < $catListL; $i++){
            $format = true;
        }
        return $format;
    }

}


?>