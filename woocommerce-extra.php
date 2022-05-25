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

register_activation_hook(__FILE__,'we_activation');
function we_activation(){
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        // Yes, WooCommerce is enabled
        
    } else {
        // WooCommerce is NOT enabled!
    }
}
?>