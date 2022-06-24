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
require_once('interfaces/breadcrumberrors.php');
require_once('interfaces/productbreadcrumberrors.php');
require_once('interfaces/productinfoerrors.php');
require_once('classes/functions.php');
require_once('classes/breadcrumb.php');
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
$addtocart_data = array(); //Add to cart data to send at Google Analytics
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

//Add categories breadcrumb in product page
add_action('woocommerce_before_single_product','we_product_categories_breadcrumb');
function we_product_categories_breadcrumb(){
    global $logFile,$product;
    $content = "";
    //file_put_contents($logFile,"Product => ".var_export($product,true)."\r\n",FILE_APPEND);
    $data = [
        'categories' => $product->get_categories(),
        'logFile' => $logFile,
        'product_name' => $product->get_name()
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

//Check when a product is added to the cart
add_action('woocommerce_add_to_cart','we_cart_product_added',10,6);
function we_cart_product_added($cart_item_key,$product_id,$quantity,$variation_id,$variation,$cart_item_data){
    global $logFile;
    file_put_contents($logFile,"we cart product added => \r\n",FILE_APPEND);
    if(!is_shop()){
        //Only if is not in shop page
        global $addtocart_data;
        file_put_contents($logFile,"we cart product added not shop page => \r\n",FILE_APPEND);
       /*  file_put_contents($logFile,"we_cart_product_added\r\n",FILE_APPEND);
        file_put_contents($logFile,"cart item key => ".var_export($cart_item_key,true)."\r\n",FILE_APPEND);
        file_put_contents($logFile,"product id => ".var_export($product_id,true)."\r\n",FILE_APPEND);
        file_put_contents($logFile,"quantity => ".var_export($quantity,true)."\r\n",FILE_APPEND);
        file_put_contents($logFile,"variation id => ".var_export($variation_id,true)."\r\n",FILE_APPEND);
        file_put_contents($logFile,"variation => ".var_export($variation,true)."\r\n",FILE_APPEND);
        file_put_contents($logFile,"cart item data => ".var_export($cart_item_data,true)."\r\n",FILE_APPEND); */
        $currency = get_woocommerce_currency();
        $addtocart_data = Functions::add_to_cart_data($product_id,$currency,['logFile' => $logFile]);
        file_put_contents($logFile,"add to cart data => ".var_export($addtocart_data,true)."\r\n",FILE_APPEND);
    }//if(is_product()){   
}

add_action('wp_head','we_category_breadcrumb');
function we_category_breadcrumb($content){
    global $logFile,$woocommerce,$wp_query;
    if(is_product_category()){
        //If user is viewing product category page
        $catObj = $wp_query->get_queried_object();
        $id = $catObj->term_id;
        $i = 1;
        $cat_term = get_term_by('id',$id,'product_cat');
        file_put_contents($logFile,"cat_term {$i} => ".var_export($cat_term,true)."\r\n",FILE_APPEND);
        while($cat_term->parent != 0){
            $i++;
            $parent_id = $cat_term->parent;
            $cat_term = get_term_by('id',$parent_id,'product_cat');
            file_put_contents($logFile,"cat_term {$i} => ".var_export($cat_term,true)."\r\n",FILE_APPEND);
        }
        
?>
        <script>
            window.addEventListener('DOMContentLoaded',()=>{
                var mainContainer = document.getElementById('content');
                if(mainContainer){
                    var div = document.createElement('div');
                    var divContent = `<?php ?>              
                    `;
                    div.setAttribute('id','we-cat-breadcrumb');
                    div.classList.add('cat-breadcrumb');
                    div.innerHTML = divContent;
                    mainContainer.insertBefore(div,mainContainer.firstChild);
                }//if(mainContainer){   
            });
            
        </script>
<?php
    $content =<<<HTML
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Library</a></li>
    <li class="breadcrumb-item active" aria-current="page">Data</li>
  </ol>
</nav>
HTML;
    //echo $content;
    }
}

//Check when a product is removed from cart
//add_action('woocommerce_cart_item_removed','we_cart_product_removed',10,2);
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
    //file_put_contents($logFile,"Product id => ".var_export($id,true)."\r\n",FILE_APPEND);
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
        //wp_enqueue_script(C::H_JS_ADDTOCART_SINGLEPRODUCT,$pluginUrl.C::DIR_JS.C::FILE_JS_ADDTOCART_SP,array(),null,true);  
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
    global $addtocart_data,$logFile,$purchase_data;
    $data = array();
    $send_to_ga = false; //If it's true send data array to Google Analytics
    if(count($purchase_data) > 0){
        //Purchase data array is not void
        //file_put_contents($logFile,"Purchase data count\r\n",FILE_APPEND);
        $data = $purchase_data;
        $event = C::GA_EVENT_PURCHASE;
        $send_to_ga = true;
    }
    if(count($addtocart_data) > 0){
        //Add to cart data array is not void
        //file_put_contents($logFile,"Add to cart data count\r\n",FILE_APPEND);
        $data = $addtocart_data['data'];
        $event = C::GA_EVENT_ADD_TO_CART;
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