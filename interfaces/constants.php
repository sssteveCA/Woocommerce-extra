<?php



namespace WoocommerceExtra\Interfaces;



//Generic constants

interface Constants{



    //Directories

    const DIR_JSON = "/json";

    const DIR_PLUGIN = "/woocommerce-extra";



    //Endpoint

    const ENDPOINT_ORDER_RECEIVED = 'order-received';



    //Elements id

    const ELEMENT_ID_GTAG = 'script#google-tag-manager-js';



    //Errors

    const ERR_WOOCOMMERCE_NOT_FOUND = 'Installa e attiva Woocommerce per utilizzare questo plugin';



    //Files

    const FILE_LOG = "log.txt";



    //Google Analytics events
    const GA_EVENT_PURCHASE = 'purchase';
    const GA_EVENT_REMOVE_FROM_CART = 'remove_from_cart';



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