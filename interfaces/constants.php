<?php

namespace WoocommerceExtra\Interfaces;

//Generic constants
interface Constants{

    //Directories
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
    const GA_EVENT_WC_PURCHASE = 'wc_purchase';

    //Requests
    const REQ_ORDER_RECEIVED = 'pagamento/order-received/';

}
?>