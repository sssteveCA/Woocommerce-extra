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
$logFile = $pluginDir.C::FILE_LOG;
$current_order_id = 0;
$wc_order = null; //Woocommerce order instance
$purchase_data = array(); //Purchase Data to send at Google Analytics

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
function we_cart_product_removed($product_id,$cart){
    global $logFile;
    $currency = get_woocommerce_currency();
    file_put_contents($logFile,"Product id => ".var_export($product_id,true)."\r\n",FILE_APPEND);
    file_put_contents($logFile,"Currency => ".var_export($currency,true)."\r\n",FILE_APPEND);

    //file_put_contents($logFile,"Cart => ".var_export($cart,true)."\r\n",FILE_APPEND);

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
            $purchase_data = Functions::purchase_data($wc_order);
            we_send_order_data($purchase_data);
        }//if($wc_order != null){
        //file_put_contents($logFile,"WC_Order => ".var_export($wc_order,true)."\r\n",FILE_APPEND);
    }
}

//Send order data to Google Analytics
add_action('wp_footer','we_send_order_data');
function we_send_order_data($data){
    global $purchase_data,$logFile;
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
        gtag('event','<?php echo C::GA_EVENT_WC_PURCHASE; ?>',data);
    }// if(gTagEl){
</script>
<?php
}

?>