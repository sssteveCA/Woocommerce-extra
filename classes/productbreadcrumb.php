<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\Constants as C;
use WoocommerceExtra\Interfaces\ProductBreadcrumbErrors as Pbe;

//This class returns the HTML category breadcrumb of single product page
class ProductBreadcrumb implements C,Pbe{
    private string $categoriesStr; //Categories string from product object
    private array $categoriesList = array(); //Categories list
    private array $urlList = array(); //Categories URL page
    private string $breadcrumb = ""; //HTML Bootstrap breadcrumb generated
    private string $homepage; //URL homepage
    private string $logFile; //Filesystem path of log file
    private static $regex = '/((?<=href=")([^"]+))*((?<=href=")([^"]+))/i'; //Capture URL in string

    public function __construct(array $data)
    {
        $this->categoriesStr = $data['categories'];
        $this->logFile = isset($data['logFile']) ? $data['logFile'] : C::FILE_LOG;
        $this->homepage = isset($data['homepage']) ? $data['homepage'] : C::PAGES_HOME;
        $this->setCategoriesList();
        if(!$this->setUrlList())throw new \Exception(Pbe::INCORRECTPATTERN_EXC);
        $this->setBreadcrumb();
    }

    public function getBreadcrumb(): string{return $this->breadcrumb;}
    public function getCategoriesList(): array{return $this->categoriesList;}
    public function getCategoriesStr(): string{return $this->categoriesStr;}


    //Generate breadcrumb for Woocommerce product
    private function setBreadcrumb(){
        $i = 0;
        $nCat = count($this->categoriesList);
        $items = "";
        for($i = 0; $i < $nCat; $i++){
            $items .= '<li class="breadcrumb-item"><a href="'.$this->urlList[$i].'">'.$this->categoriesList[$i].'</a></li>';
        }//for($i = 0; $i < $nCat; $i++){
        $this->breadcrumb = <<<HTML
<nav aria-label="breadcrumb">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{$this->homepage}">Home</a></li>
    {$items}
    </ul>
</nav>
HTML;
            //file_put_contents($this->logFile,"breadcrumb => ".var_export($this->breadcrumb,true)."\r\n",FILE_APPEND);
    }

    //Delete HTML tags and get the Categories
    private function setCategoriesList(){
        $stripped = strip_tags($this->categoriesStr);
        $this->categoriesList = explode(',',$stripped);
    }

    //Set URL list from given categories string
    private function setUrlList(): bool{
        $ok = false;
        $match = preg_match_all(ProductBreadcrumb::$regex,$this->categoriesStr,$matches);
        if($match){
            //String passed is valid
            $this->urlList = $matches[0];
            //file_put_contents($this->logFile,"urlList => ".var_export($this->urlList,true)."\r\n",FILE_APPEND);
            $ok = true;
        }
        return $ok;
    }
}
?>