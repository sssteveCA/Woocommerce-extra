<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\Constants as C;
use WoocommerceExtra\Interfaces\BreadcrumbErrors as Be;

//parent class for custom breadcrumbs
abstract class Breadcrumb implements Be,C{
   
    protected ?string $breadcrumb = null;
    protected array $catInfo = array();
    protected int $errno = 0;
    protected ?string $error = null;
    protected string $logFile; //Filesystem path of log file

    public function __construct(array $data)
    {
        $this->logFile = isset($data['logFile']) ? $data['logFile'] : C::FILE_LOG;
    }

    public function getBreadcrumb():?string{return $this->breadcrumb;}
    public function getCatInfo():array{return $this->catInfo;}
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
    protected function setBreadcrumb(): bool{
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