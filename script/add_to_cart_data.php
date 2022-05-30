<?php

require_once("../../../../wp-load.php");

require_once("../interfaces/constants.php");

require_once("../classes/functions.php");



use WoocommerceExtra\Interfaces\Constants as C;

use WoocommerceExtra\Classes\Functions;



use WC_Product;



$pluginDir = plugin_dir_path(__FILE__);

$pluginUrl = plugin_dir_url(__FILE__);

$logFile = $pluginDir.C::FILE_LOG;

$response['ok'] = false;

$response['msg'] = '';

$response['data'] = [];


if(isset($_GET['product_id']) && is_numeric($_GET['product_id'])){
    $id = $_GET['product_id'];
    $currency = get_woocommerce_currency();
    $product = new WC_Product($id);
    if($product->exists()){
        $response['data'] = [
            'currency' => $currency,
            'value' => $product->get_price(),
            'items' => [
                'item_id' => (string)$id,
                'item_name' => $product->get_name(),
                'affiliation' => "Google",
                'coupon' => "Summer",
                'currency' => $currency,
                'discount' => 0,
                'index' => 0,
                'item_brand' => 0
            ]
        ];
        $response['ok'] = true;
    }//if($product->exists()){
    else
        $response['msg'] = 'Product with id '.$id.' doesn\' t exists';
}//if(isset($_GET['product_id']) && is_numeric($_GET['product_id'])){
else
    $response['msg'] = 'No product specified';

echo json_encode($response);


?>