<?php

header("Content-Type: application/javascript");

require_once("../../../../wp-load.php");
require_once("../interfaces/constants.php");


use WoocommerceExtra\Interfaces\Constants as C;

$pluginDir = plugin_dir_path(__FILE__);
$pluginUrl = dirname(plugin_dir_url(__FILE__));
$url = $pluginUrl.C::DIR_SCRIPT.C::FILE_SCRIPT_ADDTOCART;
$event = C::GA_EVENT_ADD_TO_CART;

$js = <<<JS

jQuery(document).ready(()=>{

    let products_ul = jQuery('.products').first();
    let bt_add_to_cart = products_ul.find('a.add_to_cart_button');
    bt_add_to_cart.on('click',(e)=>{
        let link = jQuery(this).attr('href');
        let start = link.indexOf('=');
        let id = link.substring(start+1);
        let url = '{$url}?product_id='+id;
        let request = fetch(url);
        request.then(res =>{
            return res.json();
        }).then(data => {
            //console.log(data);
            if(data['ok'] == true){
                gtag('event','{$event}',data['data']);
            }
        }).catch(err =>{
            console.warn("Error");
        });
    });
});
JS;

echo $js;
?>