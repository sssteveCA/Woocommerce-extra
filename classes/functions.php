<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\Constants as C;
use WC_Order;
use WC_Product;
use WC_Cart;

class Functions implements C{

    //Data to send in add to cart event
    public static function add_to_cart_data($product_id,$currency,array $params = array()): array{
        $data = [];
        $logFile = isset($params['logFile']) ? $params['logFile'] : C::FILE_LOG;
        $product = new WC_Product($product_id);
        if($product->exists()){
            $data['data'] = [
                'currency' => $currency,
                'value' => $product->get_price(),
                'items' => [
                    'item_id' => (string)$product_id,
                    'item_name' => $product->get_name(),
                    'currency' => $currency,
                    'permalink' => $product->get_permalink(),
                    'price' => $product->get_price(),
                    'rating' => $product->get_rating_count(),
                    'sku' => $product->get_sku(),
                    'slug' => $product->get_slug()
                ]
            ];
        }//if($product->exists()){
        return $data;
    }

    //Data to send in purchase event
    public static function purchase_data(WC_Order $order,array $params = array()): array{
        $data = array();
        $logFile = isset($params['logFile']) ? $params['logFile'] : C::FILE_LOG;
        $data['currency'] = $order->get_currency();
        $products = $order->get_items();
        $data['items'] = array();
        foreach($products as $product){
            $wc_product = new WC_Product($product['product_id']);
            //$data['items'][$i]['categories'] = strip_tags($wc_product->get_categories());
            $data['items'][] = array(
            	'item_id' => $product['product_id'],
                'item_name' => $wc_product->get_name(),
                'price' => floatval($wc_product->get_price()),
                'permalink' => $wc_product->get_permalink(),
                'rating' => $wc_product->get_rating_count(),
                'sku' => $wc_product->get_sku(),
                'slug' => $wc_product->get_slug(),
                'quantity' => intval($wc_product['quantity']),
                'total' => floatval($wc_product['total'])
            );
        }//foreach($products as $product){
        $data['shipping'] = $order->get_total_shipping();
        //$data['tax_totals'] = $order->get_tax_totals();
        $data['tax'] = floatval($order->get_total_tax());
        $data['value'] = floatval($order->get_total());
        $data['transaction_id'] = $order->get_transaction_id();
        //file_put_contents($logFile,"Data => ".var_export($data,true)."\r\n",FILE_APPEND);
        return $data;
    }

    //Data to send in removed cart event($product_key = removed product key)
    public static function removed_products_data(WC_Cart $cart,$currency,$product_key,array $params = array()): array{
        $data = [];
        $logFile = isset($params['logFile']) ? $params['logFile'] : C::FILE_LOG;
        $removed = $cart->get_removed_cart_contents();
        file_put_contents($logFile,"Cart removed => ".var_export($removed,true)."\r\n",FILE_APPEND);
        foreach($removed as $k => $v){
            //Check product key
            if($k == $product_key){
                $product = new WC_Product($v['product_id']);
                $data['currency'] = $currency;
                $data['item_id'] = $product->get_id();
                $data['item_name'] = $product->get_name();
                $data['value'] = floatval($v['line_total']);
                $data['tax'] = floatval($v['line_tax']);
                $data['items'] = [];
                $data['items'][] = array(
                    'currency' => $currency,
                    'item_id' => "".$product->get_id(),
                    'item_name' => $product->get_name(),
                    'affiliation' => 'Google',
                    'coupon' => 'coupon',
                    'discount' => 0,
                    'item_category' => 'category',
                    'item_category2' => 'category2',
                    'item_category3' => 'category3',
                    'item_category4' => 'category4',
                    'item_category5' => 'category5',
                    'index' => 0,
                    'item_list_id' => "Related",
                    "item_list_name" => "Related products",
                    'item_brand' => 'Brand',
                    'item_variant' => 'No',
                    'location_id' => "L_12345",
                    'price' => floatval($product->get_price()),
                    'promotion_id' => 'P_67890',
                    'promotion_name' => 'Summer sale',
                    'quantity' => intval($v['quantity']),
                    'permalink' => $product->get_permalink(),
                    'rating' => $product->get_rating_count(),
                    'sku' => $product->get_sku(),
                    'slug' => $product->get_slug(),
                );
            }//if($k == $product_key){
        }//foreach($removed as $k => $v){
        file_put_contents($logFile,"removed products data => ".var_export($data,true)."\r\n",FILE_APPEND);
        return $data;     

    }

}

?>