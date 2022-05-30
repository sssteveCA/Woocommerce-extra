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
    $response['data'] = Functions::add_to_cart_data($id,$currency,['logFile' => $logFile]);
    if(!empty($response['data']))
        $response['ok'] = true;
    else
        $response['msg'] = 'Product with id '.$id.' doesn\' t exists';
}//if(isset($_GET['product_id']) && is_numeric($_GET['product_id'])){
else
    $response['msg'] = 'No product specified';

echo json_encode($response);


?>