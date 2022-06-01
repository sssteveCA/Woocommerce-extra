<?php



header("Content-Type: application/javascript");



require_once("../../../../wp-load.php");

require_once("../interfaces/constants.php");



use WoocommerceExtra\Interfaces\Constants as C;


$pluginDir = plugin_dir_path(__FILE__);

$pluginUrl = dirname(plugin_dir_url(__FILE__));
$url = $pluginUrl.C::DIR_SCRIPT.C::FILE_SCRIPT_REMOVEFROMCART;
$event = C::GA_EVENT_REMOVE_FROM_CART;

$js = <<<JS

jQuery(document).ready(()=>{
    //Get table list of products in the cart
    let table = jQuery('#content > div > div > div > div > form > table').first();
    let removes = table.find('a.remove');
    removes.on('click', (e)=>{
        //User remove a product from cart
        console.log("Remove product click");
        //console.log(e);
        const link = e.target.href;
        const query = "?remove_item=";
        const keyStart = link.indexOf(query)+query.length
        const keyEnd = link.indexOf("&",keyStart);
        const l = keyEnd - keyStart;
        const item_key = link.substr(keyStart,l);
        let url = '{$url}?product_key='+item_key;
        let request = fetch(url);
        request.then(res =>{
            return res.json();
        }).then(data =>{
            //console.log(data);
            if(data['ok'] == true){
                gtag('event','{$event}',data['data']);
            }  
        }).catch(err => {
            console.warn('Error');
        });
    });//removes.on('click', ()=>{
});//jQuery(document).ready(()=>{
JS;

echo $js;

?>