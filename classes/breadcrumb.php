<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\Constants as C;
use WoocommerceExtra\Interfaces\BreadcrumbErrors as Be;

//parent class for custom breadcrumbs
abstract class Breadcrumb implements Be,C{
   
    private ?string $breadcrumb = null;
    private array $catInfo = array();
    private string $homepage; //URL homepage
    private string $shoppage; //URL of the shop page
    private int $errno = 0;
    private ?string $error = null;
    private string $logFile; //Filesystem path of log file

    public function __construct(array $data)
    {
        $this->logFile = isset($data['logFile']) ? $data['logFile'] : C::FILE_LOG;
        $this->homepage = isset($data['homepage']) ? $data['homepage'] : C::PAGES_HOME;
        $this->shoppage = isset($data['shoppage']) ? $data['shoppage'] : C::PAGES_SHOP;
    }

    public function getBreadcrumb():?string{return $this->breadcrumb;}
    public function getCatInfo():array{return $this->catInfo;}
    public function getHomepage():string{return $this->homepage;}
    public function getShoppage():string{return $this->shoppage;}
    public function getErrno():int{return $this->errno;}
    public function getError():?string{
        switch($this->errno){
            case Be::NOCATEGORIES:
                $this->error = Be::NOCATEGORIES_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Generate breadcrumb HTML snippet
    private function setBreadcrumb(): bool{
        $ok = false;
        $i = 0;
        $this->errno = 0;
        $items = '';
        $nCat = count($this->catInfo);
        if($nCat > 0){
            foreach($this->catInfo as $k => $v){
                $items .= '<li class="breadcrumb-item"><a href="'.$v[1].'">'.$v[0].'</a></li>';
            }//foreach($this->catInfo as $k => $v){
                $this->breadcrumb = <<<HTML
<nav aria-label="breadcrumb">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{$this->homepage}">Home</a></li>
        <li class="breadcrumb-item"><a href="{$this->shoppage}">Prodotti</a></li>
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