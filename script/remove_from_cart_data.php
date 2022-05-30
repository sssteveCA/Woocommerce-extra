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
/*$cart = WC()->cart->get_cart();
echo '<pre>';
var_dump($cart);
echo '</pre>';*/
//file_put_contents($logFile,"Cart => ".var_export($cart,true)."\r\n",FILE_APPEND);

if(isset($_GET['product_key']) && $_GET['product_key'] != ''){
    $key = $_GET['product_key'];
    $cart = WC()->cart;
    $currency = get_woocommerce_currency();
    $response['data'] = Functions::removed_products_data($cart,$currency,$key,['logFile' => $logFile]);
    if(!empty($response['data'])){
        $response['ok'] = true;
    }
    else
        $response['msg'] = 'No product found with given key';
}//if(isset($_GET['product_id']) && is_numeric($_GET['product_id'])){
else
    $response['msg'] = 'No product specified';

echo json_encode($response);
?>