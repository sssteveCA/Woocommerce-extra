<?php

//This script is listening the 'add to cart' button click event and send the data to Google Analtics

header("Content-Type: application/javascript");

require_once("../../../../wp-load.php");
require_once("../interfaces/constants.php");


use WoocommerceExtra\Interfaces\Constants as C;

$pluginDir = plugin_dir_path(__FILE__);
$pluginUrl = dirname(plugin_dir_url(__FILE__));
$url = $pluginUrl.C::DIR_SCRIPT.C::FILE_JS_ADDTOCART_SP;
$event = C::GA_EVENT_ADD_TO_CART;

$js = <<<JS

jQuery(document).ready(()=>{
    let bt_addtocart = jQuery("form.cart button[name=add-to-cart]");
    console.log(bt_addtocart);
    bt_addtocart.on('click', (e)=>{
        let id = jQuery(this).val();
        console.log(id);
    });//bt_addtocart.on('click', ()=>{
});//jQuery(document).ready(()=>{
JS;

echo $js;
?>