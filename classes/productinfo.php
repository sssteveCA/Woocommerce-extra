<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\ProductInfoErrors as Pie;
use WoocommerceExtra\Interfaces\Constants as C;

//Generate HTML table that contains info list of a product
class ProductInfo implements Pie,C{
    private string $path; //Filesystem path of the json file
    private string $content; //Entire file content
    private array $arrayContent; //JSON string decoded into array
    private string $logFile; //Filesystem path of log file

    public function __construct(array $data)
    {
        $this->logFile = isset($data['logFile']) ? $data['logFile'] : C::FILE_LOG;
        $this->path = isset($data['path'])? $data['path']: null;
        if(!$this->path)throw new \Exception(Pie::PATHNOTSPECIFIED_EXC);
        if(!$this->exists())throw new \Exception(Pie::FILENOTEXISTS_EXC);
        if(!$this->jsonFile())throw new \Exception(Pie::INVALIDTYPE_EXC);
        if(!$this->genHtml())throw new \Exception(Pie::UNEXPECTEDCONTENT_EXC);
    }

    public function getPath(): string{return $this->path;}
    public function getArrayContent(): array{return $this->arrayContent;}
    public function getContent(): string{return $this->content;}

    //Check if specified file exists
    private function exists(): bool{
        $exists = false;
        $ex1 = file_exists($this->path);
        $ex2 = is_file($this->path);
        if($ex1 && $ex2){
            $exists = true;
        }
        return $exists;
    }

    //Generate HTML table from array
    private function genHtml(): bool{
        $gen = false;
        $this->arrayContent = json_decode($this->content,true);
        file_put_contents($this->logFile,"Array content => ".var_export($this->arrayContent,true)."\r\n",FILE_APPEND);
        $gen = true;
        return $gen;
    }

    //Check if file is JSON type
    private function jsonFile(): bool{
        $json = false;
        $type = mime_content_type($this->path);
        file_put_contents($this->logFile,"Mime => {$type}\r\n",FILE_APPEND);
        if($type == 'application/json'){
            //File type is JSON
            $this->content = file_get_contents($this->path);
            //file_put_contents($this->logFile,"File content =>".var_export($this->content,true)."\r\n",FILE_APPEND);
            $json = true;
        }//if($type == 'application/json'){
        return $json;
    }

}
?>