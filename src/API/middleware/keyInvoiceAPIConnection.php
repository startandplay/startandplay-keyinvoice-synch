<?php

namespace keyinvoicesync\API\Middleware;

use function Composer\Autoload\includeFile;
use Soapclient;

class keyInvoiceAPIConnection
{
    const CALL_MAX_LIMIT = '-600';

    public $api_url;
    public $soap;
    public $sid;


    public function __construct($api_url)
    {
        $this->api_url = $api_url;
        $this->soap = new Soapclient($this->api_url, array('wsdl_cache_enabled' => 1, 'cache_wsdl' => WSDL_CACHE_DISK));
        error_log(print_r($this->soap, true));
    }


    public function authenticate($api_key)
    {
        $response = $this->soap->authenticate($api_key);
        $this->sid = $response[1];
        return $response;
    }


    public function countProducts()
    {
        return $this->soap->countProducts($this->sid);
    }


    public function listProducts($offset)
    {
        return $this->soap->listProducts($this->sid, $offset);
    }


    function maxLimitCallValidation($result)
    {
        if ($result == self::CALL_MAX_LIMIT) {
            echo "<br/> " . $this->mydecode($this->soap->responseMessage($result, 0));
            return;
        }
    }


    public function getAllKeyinvoiceProducts()
    {
        $result = $this->countProducts();
        $totalProducts = $result[1];
        $keyinvoiceProduct = array();

        $this->maxLimitCallValidation($result[0]);

        // 25 is the limit given by the API per query
        for ($i = 0; $i <= $totalProducts; $i += 25) {

            $productsList = $this->listproducts($i);

            if (intval($productsList->RC) != 1) {
                $this->maxLimitCallValidation($productsList->RC);
                echo json_encode(array('msg' => __('Erro ao ler lista de produtos.')));
                return;
            }

            foreach ($productsList->DAT as $product) {
                array_push($keyinvoiceProduct, $product);
            }
        }

        return $keyinvoiceProduct;
    }
}
