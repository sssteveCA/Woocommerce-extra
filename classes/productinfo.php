<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\ProductInfoErrors as Pie;
use WoocommerceExtra\Interfaces\Constants as C;

//Generate HTML table that contains info list of a product
class ProductInfo implements Pie,C{
    private string $path; //Filesystem path of the json file
    private string $content; //Entire file content
    private array $arrayContent; //JSON string decoded into array
    private string $html; //HTML generated
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
    public function getHtml(): string{return $this->html;}

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
        $gen = true;
        $this->arrayContent = json_decode($this->content,true);
        //file_put_contents($this->logFile,"Array content => ".var_export($this->arrayContent,true)."\r\n",FILE_APPEND);
        $this->html = "<h2>Descrizione</h2>";
        foreach($this->arrayContent['description'] as $table){
            $thead = isset($table['thead']); //Check if $table has 'thead' key with a set value
            $tbody = (is_array($table['tbody']) && !empty($table['tbody'])); //Check if tbody property is an array and if it's not empty
            /* file_put_contents($this->logFile,"thead => ".var_export($thead,true)."\r\n",FILE_APPEND);
            file_put_contents($this->logFile,"tbody => ".var_export($tbody,true)."\r\n",FILE_APPEND); */
            if($thead && $tbody){
                //Correct format
                $this->html .= '<div><table class="table">';
                $this->html .= '<thead><tr>';
                $this->html .= '<th colspan="2">'.$table['thead'].'</th>';
                $this->html .= '</tr></thead>';
                $this->html .= '<tbody>';
                foreach($table['tbody'] as $key => $value){
                    $this->html .= '<tr>';
                    $this->html .= '<th scope="row">'.$key.'</th>';
                    $this->html .= '<td>'.$value.'</td>';
                    $this->html .= '</tr>';
                }//foreach($table['tbody'] as $key => $value){
                $this->html .= '</tbody>';
                $this->html .= '</table></div>';
            }//if($thead && $tbody){
            else{
                $gen = false;
                break;
            }        
        }//foreach($this->arrayContent as $table){
        return $gen;
    }

    //Check if file is JSON type
    private function jsonFile(): bool{
        $json = false;
        $type = mime_content_type($this->path);
        //file_put_contents($this->logFile,"Mime => {$type}\r\n",FILE_APPEND);
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