<?php

header("Content-Type: application/javascript");

require_once("../../../../wp-load.php");
require_once("../interfaces/constants.php");

use WoocommerceExtra\Interfaces\Constants as C;

$js = <<<JS
jQuery(document).ready(()=>{
    //Get table list of products in the cart
    let table = jQuery('#content > div > div > div > div > form > table').first();
    //console.log(table);
    let removes = table.find('a.remove');
    console.log(removes); 
    removes.on('click', (e)=>{
        //User remove a product from cart
        console.log("Remove product click");
        //console.log(e);
        const link = e.target.href;
        //console.log(link);
        const query = "?remove_item=";
        const keyStart = link.indexOf(query)+query.length
        const keyEnd = link.indexOf("&",keyStart);
        const l = keyEnd - keyStart;
        const item_key = link.substr(keyStart,l);
        console.log(item_key);
    });//removes.on('click', ()=>{
});//jQuery(document).ready(()=>{
JS;

echo $js;
?>