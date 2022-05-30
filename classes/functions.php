<?php

namespace WoocommerceExtra\Classes;

use WoocommerceExtra\Interfaces\Constants as C;

use WC_Order;
use WC_Product;

class Functions implements C{

    public static function purchase_data(WC_Order $wc_order): array{
        $data = array();
        $data['currency'] = $wc_order->get_currency();
        $products = $wc_order->get_items();
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
        $data['shipping'] = $wc_order->get_total_shipping();
        //$data['tax_totals'] = $wc_order->get_tax_totals();
        $data['tax'] = floatval($wc_order->get_total_tax());
        $data['value'] = floatval($wc_order->get_total());
        $data['transaction_id'] = $wc_order->get_transaction_id();
        file_put_contents(C::FILE_LOG,"Data => ".var_export($data,true)."\r\n",FILE_APPEND);
        return $data;
    }
}
?>