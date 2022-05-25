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

use WoocommerceExtra\Interfaces\Constants as C;

$logDir = plugin_dir_path(__FILE__).C::FILE_LOG;
$current_order_id = 0;
$wc_order = null; //Woocommerce order instance
$data = array(); //Data needed from current order

register_activation_hook(__FILE__,'we_activation');
function we_activation(){
    global $logDir;
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        // Yes, WooCommerce is enabled
        //file_put_contents($logDir,"Woocommerce attivo\r\n",FILE_APPEND);     
    } else {
        // WooCommerce is NOT enabled!
        wp_die(C::ERR_WOOCOMMERCE_NOT_FOUND);
    }
}

//Get order id from URL
add_action('wp_head','we_get_order_id');
function we_get_order_id(){
    if(is_wc_endpoint_url(C::ENDPOINT_ORDER_RECEIVED)){
        //Order received page
        global $wp,$logDir,$current_order_id;
        //file_put_contents($logDir,"Wp object => ".var_export($wp,true)."\r\n",FILE_APPEND);
        $current_order_id = intval(str_replace(C::REQ_ORDER_RECEIVED,'',$wp->request));
        //file_put_contents($logDir,"Wp request => ".var_export($wp->request,true)."\r\n",FILE_APPEND);
        file_put_contents($logDir,"Current order id => ".var_export($current_order_id,true)."\r\n",FILE_APPEND);
        we_get_order_info($current_order_id);
    }
}

//Get order info from current order id
function we_get_order_info($order_id){
    global $wc_order,$logDir;
    $wc_order = new WC_Order($order_id);
    //file_put_contents($logDir,"WC_Order => ".var_export($wc_order,true)."\r\n",FILE_APPEND);
    we_set_array_data();
}

//Assign the needed order data to $data array
function we_set_array_data(){
    global $wc_order;
    if($wc_order != null){
        //WC_Order object instantiated
        global $data,$logDir;
        $data['currency'] = $wc_order->get_currency();
        $products = $wc_order->get_items();
        $data['products'] = array();
        $i = 0;
        foreach($products as $product){
            $data['id'] = $product['product_id'];
            $wc_product = new WC_Product($data['id']);
            $data['products'][$i]['categories'] = strip_tags($wc_product->get_categories());
            $data['products'][$i]['name'] = $wc_product->get_name();
            $data['products'][$i]['price'] = $wc_product->get_price();
            $data['products'][$i]['quantity'] = $product['quantity'];
            $data['products'][$i]['total'] = $product['total'];
            $i++;
        }
        $data['shipping'] = $wc_order->get_total_shipping();
        $data['tax'] = $wc_order->get_tax_totals();
        $data['total'] = $wc_order->get_total();
        $data['transaction_id'] = $wc_order->get_transaction_id();
        file_put_contents($logDir,"Data => ".var_export($data,true)."\r\n",FILE_APPEND);
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
    console.log(data);
</script>
<?php
    }//if($count > 0){
}
?>