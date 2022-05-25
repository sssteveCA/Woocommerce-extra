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

$logDir = plugin_dir_url(__FILE__).C::FILE_LOG;

register_activation_hook(__FILE__,'we_activation');
function we_activation(){
    global $logDir;
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        // Yes, WooCommerce is enabled
        file_put_contents($logDir,"Woocommerce attivo\r\n",FILE_APPEND);     
    } else {
        // WooCommerce is NOT enabled!
    }
}

add_action('wp_head','we_get_order_info');
function we_get_order_info(){
    if(is_wc_endpoint_url(C::ENDPOINT_ORDER_RECEIVED)){
        //Order received page
        global $wp,$logDir;
        file_put_contents($logDir,"Wp object => ".var_export($wp,true)."\r\n",FILE_APPEND);
        $current_order_id = intval(str_replace('checkout/order-received','',$wp->request));
        file_put_contents($logDir,"Wp request => ".var_export($wp->request,true)."\r\n",FILE_APPEND);
        file_put_contents($logDir,"Current order id => ".var_export($current_order_id,true)."\r\n",FILE_APPEND);
    }
}
?>