<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\Constants as C;
use WoocommerceExtra\Interfaces\BreadcrumbErrors as Be;
use WoocommerceExtra\Interfaces\CatBreadcrumbErrors as Cbe;
use WoocommerceExtra\Classes\Breadcrumb;

//This class returns the HTML category breadcrumb of single product page

class CatBreadcrumb extends Breadcrumb implements C,Cbe,Be{
    private array $categoriesList = array(); //Categories list
    private string $homepage; //URL homepage
    private string $shoppage; //URL of the shop page
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->checkValues($data);
        $this->formatArray();
        $this->setBreadcrumb();
    }

    public function getHomepageUrl():string {return $this->homepage;}
    public function getShoppageUrl():string {return $this->shoppage;}
    public function getCategoriesList():array {return $this->categoriesList;}

    //Check if required value are in correct format
    private function checkValues(array $data){
        if(!isset($data['homepage'],$data['shoppage'],$data['categories']))throw new \Exception(Cbe::VALUENOTSET_EXC);
        if(!is_array($data['categories']))throw new \Exception(Cbe::CATEGORIES_PARAM_NOTARRAY);
        $this->homepage = $data['homepage'];
        $this->shoppage = $data['shoppage'];
        $this->categoriesList = $data['categories'];
    }

    //Format the array in the correct way for generate HTML
    private function formatArray(){
        $this->catInfo[0] = ['name' => 'Home','link' => $this->homepage];
        $this->catInfo[1] = ['name' => 'Prodotti','link' => $this->shoppage];
        //The length of urlList and categoriesLIst must be the same
        $catListL = count($this->categoriesList);
        for($i = 0; $i < $catListL; $i++){
            $this->catInfo[] = [
                'name' => $this->categoriesList[$i]['name'],
                'link' => $this->categoriesList[$i]['link']
            ];
        }//for($i = 0; $i < $catListL; $i++){ 
    }

    //Generate breadcrumb HTML for product category pages
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
            file_put_contents($this->logFile,"CatBreadcrumb catInfo => ".var_export($this->catInfo,true)."\r\n",FILE_APPEND);
            foreach($this->catInfo as $k => $v){
                if($k != array_key_last($this->catInfo)){
                     //If item is not the last in array
                    $items .= '<li class="breadcrumb-item"><a href="'.$v['link'].'">'.$v['name'].'</a></li>';
                }
                else{
                    //If term is last in the loop
                    $items .= '<li class="breadcrumb-item active" aria-current="page">'.$v['name'].'</li>';
                }   
            }//foreach($catInfo_rev as $k => $v){
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