<?php

namespace WoocommerceExtra\Classes;

use WC_Product;
use WoocommerceExtra\Interfaces\Constants as C;
use WoocommerceExtra\Interfaces\BreadcrumbErrors as Be;
use WoocommerceExtra\Interfaces\ProductBreadcrumbErrors as Pbe;
use WoocommerceExtra\Classes\Breadcrumb;

//This class returns the HTML category breadcrumb of single product page

class ProductBreadcrumb extends Breadcrumb implements C,Pbe{
    private string $categoriesStr; //Categories string from product object
    private array $categoriesList = array(); //Categories list
    private array $urlList = array(); //Categories URL page
    private string $homepage; //URL homepage
    private string $shoppage; //URL of the shop page
    private ?string $product_name; //Woocommerce product
    private static $regex = '/((?<=href=")([^"]+))*((?<=href=")([^"]+))/i'; //Capture URL in string

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->checkValues($data);
        $this->homepage = isset($data['homepage']) ? $data['homepage'] : C::PAGES_HOME;
        $this->shoppage = isset($data['shoppage']) ? $data['shoppage'] : C::PAGES_SHOP;
        $this->categoriesStr = $data['categories'];
        $this->setCategoriesList();
        if(!$this->setUrlList())throw new \Exception(Pbe::INCORRECTPATTERN_EXC);
        if(!$this->formatArray())throw new \Exception(Pbe::URL_CAT_ARRAY_LENGTHMISMATCH_EXC);
        $this->setBreadcrumb();
    }

    public function getProductName(): ?string{return $this->product_name;}
    public function getCategoriesList(): array{return $this->categoriesList;}
    public function getCategoriesStr(): string{return $this->categoriesStr;}

    //Check if required value are in correct format
    private function checkValues(array $data){
        if(!isset($data['product_name']))throw new \Exception(Pbe::NO_PRODUCTNAME_EXC);
        $this->product_name = $data['product_name'];
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

    //Set the breadcrumb in product single page
    protected function setBreadcrumb(): bool{
        $ok = false;
        $i = 0;
        $this->errno = 0;
        $items = '';
        $nCat = count($this->catInfo);
        if($nCat > 0){
            //Extract first part of breadcrumb
            $catInfoTemp = $this->catInfo;
            $catInfo_1p = []; //This include home and shop part of breadcrumb
            for($i = 0; $i < 2; $i++){
                $el = array_shift($catInfoTemp);
                array_push($catInfo_1p,$el);
            }
            //Reverse array product categories list order
            $catInfoTemp = array_reverse($catInfoTemp);
            //Join home,shop with product categories
            $this->catInfo = array_merge($catInfo_1p,$catInfoTemp);
            foreach($this->catInfo as $k => $v){
                //If item is not the last in array
                $items .= '<li class="breadcrumb-item"><a href="'.$v[1].'">'.$v[0].'</a></li>';
            }//foreach($catInfo_rev as $k => $v){
            $items .= '<li class="breadcrumb-item active">'.$this->product_name.'</li>';
            $this->breadcrumb = <<<HTML
<nav aria-label="breadcrumb">
    <ul class="breadcrumb">
    {$items}
    </ul>
</nav>
HTML;
        }//if($nCat > 0){
        else 
            $this->errno = Be::NOCATEGORIES;
        return $ok;
    }
    

}
?>