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
        console.log(e);
        let product_id = e.target.dataset['product_id'];
        console.log(product_id);
    });//removes.on('click', ()=>{
});//jQuery(document).ready(()=>{
JS;

echo $js;
?>