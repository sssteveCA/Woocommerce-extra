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
require_once('interfaces/productbreadcrumberrors.php');
require_once('interfaces/productinfoerrors.php');
require_once('classes/functions.php');
require_once('classes/productbreadcrumb.php');
require_once('classes/productinfo.php');

use WoocommerceExtra\Interfaces\Constants as C;
use WoocommerceExtra\Classes\Functions;
use WoocommerceExtra\Classes\ProductBreadcrumb;
use WoocommerceExtra\Classes\ProductInfo;

$pluginDir = plugin_dir_path(__FILE__);
$pluginUrl = plugin_dir_url(__FILE__);
$logFile = $pluginDir.C::FILE_LOG;
$current_order_id = 0;
$purchase_data = array(); //Purchase Data to send at Google Analytics
$product_removed_data = array(); //Products removed data to send at Google Analytics

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

//Prevent single product page reloading when user add a product to cart
add_action('woocommerce_add_to_cart_redirect','we_prevent_addtocart_redirect');
function we_prevent_addtocart_redirect($url = false){
    // if another plugin gets here first, let it keep the URL
    if(!empty($url)){
        return $url;
    }
    // redirect back to the original page, without the 'add-to-cart' parameter.
  // we add the 'get_bloginfo' part so it saves a redirect on https:// sites.
    return get_bloginfo('wpurl').add_query_arg(array(),remove_query_arg('add-to-cart'));
} // end function

//Add categories breadcrumb in product page
add_action('woocommerce_before_single_product','we_product_categories_breadcrumb');
function we_product_categories_breadcrumb(){
    global $logFile,$product;
    $content = "";
    //file_put_contents($logFile,"Product => ".var_export($product,true)."\r\n",FILE_APPEND);
    $data = [
        'categories' => $product->get_categories(),
        'logFile' => $logFile
    ];
    try{
       $breadcrumb = new ProductBreadcrumb($data); 
       $content = $breadcrumb->getBreadcrumb();
    }
    catch(Exception $e){
         file_put_contents($logFile,$e->getMessage()."\r\n",FILE_APPEND);
    }
    echo $content;
}

//Check when a product is removed from cart
add_action('woocommerce_cart_item_removed','we_cart_product_removed',10,2);
function we_cart_product_removed($product_key,$cart){
    global $logFile;
    $currency = get_woocommerce_currency();
    file_put_contents($logFile,"Product key => ".var_export($product_key,true)."\r\n",FILE_APPEND);
    file_put_contents($logFile,"Currency => ".var_export($currency,true)."\r\n",FILE_APPEND);
    $data = Functions::removed_products_data($cart,$currency,$product_key,['logFile' => $logFile]);
    file_put_contents($logFile,"Removed Data => ".var_export($data,true)."\r\n",FILE_APPEND);
}

//Edit product description tab content
add_filter('woocommerce_product_tabs','we_edit_tabs',98);
function we_edit_tabs($tabs){
    global $logFile,$product;
    //file_put_contents($logFile,"Content => ".var_export($tabs['description'],true)."\r\n",FILE_APPEND);
    $tabs['description']['callback'] = 'we_edit_description_tab';
    return $tabs;
}

//Edit product description tab
function we_edit_description_tab(){
    global $logFile,$pluginDir,$product;
    //file_put_contents($logFile,"Product => ".var_export($product,true)."\r\n",FILE_APPEND);
    $html = "".get_the_content(); //If product JSON is not found, use the default description content
    //file_put_contents($logFile,"Content => ".var_export($html,true)."\r\n",FILE_APPEND);
    //echo 'Buongiorno!';
    $id = $product->get_id(); //Get Product id to choose which JSON open
    file_put_contents($logFile,"Product id => ".var_export($id,true)."\r\n",FILE_APPEND);
    $data = [
        'logFile' => $logFile,
        'path' => $pluginDir.C::DIR_JSON."/product_{$id}.json"
    ];
    //file_put_contents($logFile,"Arraydata => ".var_export($data,true)."\r\n",FILE_APPEND);
    try{
       $pi = new ProductInfo($data);
       $html = $pi->getHtml();
      //file_put_contents($logFile,"Product HTML => ".var_export($html,true)."\r\n",FILE_APPEND);
    }
    catch(Exception $e){
        file_put_contents($logFile,$e->getMessage()."\r\n",FILE_APPEND);
    }
    echo $html;
}

add_action('wp_enqueue_scripts','we_scripts');
function we_scripts(){
    global $pluginUrl;
    if(is_cart()){
        //If user is in cart page
        wp_enqueue_script(C::H_JS_REMOVEFROMCART,$pluginUrl.C::DIR_JS.C::FILE_JS_REMOVEFROMCART,array(),null,true);
    }//if(is_cart()){
    if(is_product()){
        //Single product page
        wp_enqueue_script(C::H_JS_ADDTOCART_SINGLEPRODUCT,$pluginUrl.C::DIR_JS.C::FILE_JS_ADDTOCART_SP,array(),null,true);  
    }//if(is_product()){
    if(is_shop()){
        //Shop page
        wp_enqueue_script(C::H_JS_ADDTOCART,$pluginUrl.C::DIR_JS.C::FILE_JS_ADDTOCART,array(),null,true);
    }//if(is_shop()){
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
        $wc_order = new WC_Order($current_order_id);
        if($wc_order != null){
            //WC_Order object instantiated
            global $purchase_data;
            $purchase_data = Functions::purchase_data($wc_order,['logFile' => $logFile]);
        }//if($wc_order != null){
        //file_put_contents($logFile,"WC_Order => ".var_export($wc_order,true)."\r\n",FILE_APPEND);
    }
}

//Send order data to Google Analytics
add_action('wp_footer','we_send_data_to_ga');
function we_send_data_to_ga(){
    global $logFile,$purchase_data;
    $data = array();
    $send_to_ga = false; //If it's true send data array to Google Analytics
    if(count($purchase_data) > 0){
        //Purchase data array is not void
        file_put_contents($logFile,"Purchase data count\r\n",FILE_APPEND);
        $data = $purchase_data;
        $event = C::GA_EVENT_PURCHASE;
        $send_to_ga = true;
    }
    if($send_to_ga){
?>
<script>
    var data = <?php echo json_encode($data); ?>;
    console.log(data);
    /* var jsonData = JSON.stringify(data);
    console.log(jsonData); */
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
        gtag('event','<?php echo $event; ?>',data);
    }// if(gTagEl){
</script>
<?php
    }//if($send_to_ga){
}

?>