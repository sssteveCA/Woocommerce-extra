<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\ProductInfoErrors as Pie;
use WoocommerceExtra\Interfaces\Constants as C;

//Generate HTML table that contains info list of a product
class ProductInfo implements Pie,C{
    private string $path; //Filesystem path of the json file
    private string $content; //Entire file content
    private string $logFile; //Filesystem path of log file

    public function __construct(array $data)
    {
        $this->logFile = isset($data['logFile']) ? $data['logFile'] : C::FILE_LOG;
        $this->path = isset($data['path'])? $data['path']: null;
        if(!$this->path)throw new \Exception(Pie::PATHNOTSPECIFIED_EXC);
        if(!$this->jsonFile())throw new \Exception(Pie::INVALIDTYPE_EXC);
    }

    public function getPath(): string{return $this->path;}
    public function getContent(): string{return $this->content;}

    //Check if file is JSON type
    private function jsonFile(): bool{
        $json = false;
        $type = mime_content_type($this->path);
        file_put_contents($this->logFile,"Mime => {$type}\r\n",FILE_APPEND);
        if($type == 'application/json'){
            //File type is JSON
            $json = true;
        }//if($type == 'application/json'){
        return $json;
    }
}
?>