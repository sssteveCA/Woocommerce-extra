<?php

namespace WoocommerceExtra\Classes;

//Generate HTML table that contains info list of a product
class ProductInfo{
    private string $path; //Filesystem path of the json file
    private string $content; //Entire file content

    public function __construct()
    {
        
    }

    public function getPath(): string{return $this->path;}
    public function getContent(): string{return $this->content;}
}
?>