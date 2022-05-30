<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\Constants as C;

use WC_Order;
use WC_Product;
use WC_Cart;

class Functions implements C{

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
                'quantity' => intval($product['quantity']),
                'total' => floatval($product['total'])
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
        $data['currency'] = $currency;
        $removed = $cart->get_removed_cart_contents();
        file_put_contents($logFile,"Cart removed => ".var_export($removed,true)."\r\n",FILE_APPEND);
        foreach($removed as $k => $v){
            //Check product key
            if($k == $product_key){
                $data['value'] = floatval($v['line_total']);
                $data['tax'] = floatval($v['line_tax']);
                $data['items'] = [];
                $product = new WC_Product($v['product_id']);
                $data['items'][] = array(
                    'item_id' => $product->get_id(),
                    'item_name' => $product->get_name(),
                    'category' => $product->get_categories(),
                    'price' => floatval($product->get_price()),
                    'quantity' => intval($v['quantity'])
                );
            }//if($k == $product_key){
        }//foreach($removed as $k => $v){
        file_put_contents($logFile,"removed products data => ".var_export($data,true)."\r\n",FILE_APPEND);
        return $data;     
    }
}
?>