<?php

//This script is listening the 'add to cart' button click event and send the data to Google Analtics

header("Content-Type: application/javascript");

require_once("../../../../wp-load.php");
require_once("../interfaces/constants.php");


use WoocommerceExtra\Interfaces\Constants as C;

$pluginDir = plugin_dir_path(__FILE__);
$pluginUrl = dirname(plugin_dir_url(__FILE__));
$url = $pluginUrl.C::DIR_SCRIPT.'/add_to_cart_data.php';
$event = C::GA_EVENT_ADD_TO_CART;

$js = <<<JS

jQuery(document).ready(()=>{
    let bt_addtocart = jQuery("form.cart button[name=add-to-cart]");
    console.log(bt_addtocart)
});//jQuery(document).ready(()=>{
JS;

echo $js;
?>