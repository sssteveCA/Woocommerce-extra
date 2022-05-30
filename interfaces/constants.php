<?php

namespace WoocommerceExtra\Interfaces;

//Generic constants
interface Constants{

    //Directories
    const DIR_JS = "/js";
    const DIR_JSON = "/json";
    const DIR_PLUGIN = "/woocommerce-extra";
    const DIR_SCRIPT = "/script";

    //Endpoint
    const ENDPOINT_ORDER_RECEIVED = 'order-received';

    //Elements id
    const ELEMENT_ID_GTAG = 'script#google-tag-manager-js';

    //Errors
    const ERR_WOOCOMMERCE_NOT_FOUND = 'Installa e attiva Woocommerce per utilizzare questo plugin';

    //Files
    const FILE_JS_REMOVEFROMCART = '/remove_from_cart.php';
    const FILE_LOG = "log.txt";
    CONST FILE_SCRIPT_REMOVEFROMCART = '/remove_from_cart_data.php';

    //Google Analytics events
    const GA_EVENT_ADD_TO_CART = 'add_to_cart';
    const GA_EVENT_PURCHASE = 'purchase';
    const GA_EVENT_REMOVE_FROM_CART = 'remove_from_cart';

    //Handles
    const H_JS_ADDTOCART = 'addtocart_js';
    const H_JS_REMOVEFROMCART = 'removefromcart_js';

    //Pages

    const PAGES_HOME = 'https://postoinformatico.altervista.org/';
    const PAGES_CART = 'carrello';
    const PAGES_SHOP = Constants::PAGES_HOME.'/prodotti/';

    //Requests
    const REQ_ORDER_RECEIVED = 'pagamento/order-received/';

    //Selectors
    const SEL_PAYPAL_3RATES_BUTTON = '#buttons-container > div';
    const SEL_PAYPAL_BUTTON = '#buttons-container > div';

}



?>