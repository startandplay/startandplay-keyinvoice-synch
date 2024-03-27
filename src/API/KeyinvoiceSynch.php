<?php

/**
 * @package Keyinvoice
 */

namespace keyinvoicesync\API;

class KeyinvoiceSynch
{

    public function getKeyinvoiceProducts($soap, $result)
    {

        $keyInvoiceProducts = [];

        $numProducts = intval($result[1]);
        // 25 is the limit given by the API
        for ($i = 0; $i < $numProducts; $i += 25) {
            $result = $soap->listProducts($i);
            if (intval($result->RC) != 1) {
                if ($result->RC == '-600') {
                    echo "<br/> " . $this->mydecode($soap->responseMessage($result->RC, 0));
                    return;
                }

                echo "<br/>" . __('Erro ao ler lista de produtos.');
                return;
            }

            for ($j = 0; $j < count($result->DAT); ++$j) {
                $product = $result->DAT[$j];
                array_push($keyInvoiceProducts, $product);
            }
        }

        return $keyInvoiceProducts;
    }


    public function getKeyinvoiceProductsSkus($keyInvoiceProducts)
    {
        $keyInvoiceProductsIds = [];

        foreach ($keyInvoiceProducts as $product) {
            array_push($keyInvoiceProductsIds, $product->Ref);
        }

        return $keyInvoiceProductsIds;
    }


    // Get a list of products by identifier could be the: sku, id or other
    public function getAllWooPoductsByIdentifier($identifier)
    {
        $args = array(
            'limit' => 10000,
        );
        $products = wc_get_products($args);

        $test =  count($products);

        $woocommerceIds = array();

        foreach ($products as $wooProduct) {
            array_push($woocommerceIds, $wooProduct->$identifier);
        }

        return $woocommerceIds;
    }

    // Return an Array with all keyinvoiceProducts Objects filled
    function createKeyinvoiceCommonObjects($keyInvoiceProducts, $commonProductsSkus)
    {

        $keyInvoiveObject = array();

        foreach ($keyInvoiceProducts as $product) {

            if (in_array($product->Ref, $commonProductsSkus)) {

                $price = floatval($product->Price);
                if (intval($product->TaxIncluded) == 0) {
                    $price = floatval($product->Price) * (1 + floatval($product->TAX) / 100);
                }

                array_push($keyInvoiveObject, [
                    'sku'                => $product->Ref,
                    'name'               => $product->Name,
                    'regular_price'      => $price,
                    'manage_stock'         => (($product->HasStocks == 1) ? true : false),
                    'stock_qty'             => $product->Stock,
                    'stock_status'       => $product->Status,
                    'menu_order'         => $product->MenuOrder
                ]);
            }
        }
        return $keyInvoiveObject;
    }
}
