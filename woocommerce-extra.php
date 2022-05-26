<?php
/**
 * Plugin Name: Woocommerce extra
 * Description: addons functions for Woocommerce plugin
 * Version: 1.10.3
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Author: Stefano Puggioni
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

require_once('interfaces/constants.php');
require_once('classes/catbreadcrumb.php');

use WoocommerceExtra\Interfaces\Constants as C;
use WoocommerceExtra\Classes\CatBreadcrumb;

$logFile = plugin_dir_path(__FILE__).C::FILE_LOG;
$current_order_id = 0;
$wc_order = null; //Woocommerce order instance
$data = array(); //Data needed from current order

register_activation_hook(__FILE__,'we_activation');
function we_activation(){
    global $logFile;
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        // Yes, WooCommerce is enabled
        //file_put_contents($logFile,"Woocommerce attivo\r\n",FILE_APPEND);     
    } else {
        // WooCommerce is NOT enabled!
        wp_die(C::ERR_WOOCOMMERCE_NOT_FOUND);
    }
}

//Add categories breadcrumb in product page
add_action('woocommerce_before_single_product','we_product_categories_breadcrumb');
function we_product_categories_breadcrumb(){
    global $logFile,$product;
    //file_put_contents($logFile,"Product => ".var_export($product,true)."\r\n",FILE_APPEND);
    $categories = $product->get_categories();
    try{
       $breadcrumb = new CatBreadcrumb($categories); 
    }
    catch(Exception $e){
        file_put_contents($logFile,$e->getMessage()."\r\n",FILE_APPEND);
    }
    
}

//Get order id from URL
add_action('wp_head','we_get_order_id');
function we_get_order_id(){
    if(is_wc_endpoint_url(C::ENDPOINT_ORDER_RECEIVED)){
        //Order received page
        global $wp,$logFile,$current_order_id;
        //file_put_contents($logFile,"Wp object => ".var_export($wp,true)."\r\n",FILE_APPEND);
        $current_order_id = intval(str_replace(C::REQ_ORDER_RECEIVED,'',$wp->request));
        //file_put_contents($logFile,"Wp request => ".var_export($wp->request,true)."\r\n",FILE_APPEND);
        //file_put_contents($logFile,"Current order id => ".var_export($current_order_id,true)."\r\n",FILE_APPEND);
        we_get_order_info($current_order_id);
    }
}

//Get order info from current order id
function we_get_order_info($order_id){
    global $wc_order,$logFile;
    $wc_order = new WC_Order($order_id);
    //file_put_contents($logFile,"WC_Order => ".var_export($wc_order,true)."\r\n",FILE_APPEND);
    we_set_array_data();
}

//Assign the needed order data to $data array
function we_set_array_data(){
    global $wc_order;
    if($wc_order != null){
        //WC_Order object instantiated
        global $data,$logFile;
        $data['currency'] = $wc_order->get_currency();
        $products = $wc_order->get_items();
        $data['items'] = array();
        $i = 0;
        foreach($products as $product){
            $data['items'][$i]['id'] = $product['product_id'];
            $wc_product = new WC_Product($data['items'][$i]['id']);
            //$data['items'][$i]['categories'] = strip_tags($wc_product->get_categories());
            $data['items'][$i]['name'] = $wc_product->get_name();
            $data['items'][$i]['price'] = floatval($wc_product->get_price());
            $data['items'][$i]['quantity'] = $product['quantity'];
            $data['items'][$i]['total'] = floatval($product['total']);
            $i++;
        }
        $data['shipping'] = $wc_order->get_total_shipping();
        //$data['tax'] = $wc_order->get_tax_totals();
        $data['value'] = floatval($wc_order->get_total());
        $data['transaction_id'] = $wc_order->get_transaction_id();
        //file_put_contents($logFile,"Data => ".var_export($data,true)."\r\n",FILE_APPEND);
    }//if($wc_order != null){
}

//Send order data to Google Analytics
add_action('wp_footer','we_send_order_data');
function we_send_order_data(){
    global $data;
    $count = count($data);
    if($count > 0){
        //Array is not empty
?>
<script>
    var data = <?php echo json_encode($data); ?>;
    var jsonData = JSON.stringify(data);
    console.log(jsonData);
    var gTagEl = document.querySelectorAll('<?php echo C::ELEMENT_ID_GTAG; ?>');
    //console.log(gTagEl);
    if(gTagEl){
        gTagEl[0].addEventListener('load',()=>{
                //console.log("gTagEl loaded");
        });
        gTagEl[0].addEventListener('error',()=>{
            //console.warn("gTagEl error");
        });
        //Send object to Google Analytics
        gtag('event','<?php echo C::GA_EVENT_WC_PURCHASE; ?>',data);
    }// if(gTagEl){
</script>
<?php
    }//if($count > 0){
}

//Send data to Google Analytics if a Paypal button is clicked
add_action('wp_footer','we_send_paypal_button_click');
function we_send_paypal_button_click(){
    global $logFile,$wp;
    //file_put_contents($logFile,"Wp => ".var_export($wp,true)."\r\n",FILE_APPEND);
    if($wp->request == C::PAGES_CART){
        //User is in the cart page
?>
<script>
    var gTagEl = document.querySelectorAll('<?php echo C::ELEMENT_ID_GTAG; ?>');
    //console.log(gTagEl);
    if(gTagEl){
        gTagEl[0].addEventListener('load',()=>{
                //console.log("gTagEl loaded");
        });
        gTagEl[0].addEventListener('error',()=>{
            //console.warn("gTagEl error");
        });
        window.addEventListener('load', ()=>{
            var button_container = document.getElementById('buttons-container');
            console.log(button_container);
        });//window.addEventListener('load', ()=>{   
    }// if(gTagEl){
</script>
<?php
        
    }//if($wp->request == C::PAGES_CART){
}
?>