<?php

/**
 * @package Keyinvoice
 */

namespace keyinvoicesync\API;

use SoapClient;

class Keyinvoice
{
    public $url;
    public $soap;
    public $sid;
    private $behaviour_name = 'Simples';

    public function __construct($url)
    {

        $this->url = $url;

        $this->soap = new SoapClient($this->url, array('wsdl_cache_enabled' => 1, 'cache_wsdl' => WSDL_CACHE_DISK));
        // $this->soap = new SoapClient($this->url, array('cache_wsdl' => WSDL_CACHE_NONE));
    }

    public function authenticate($api_key)
    {
        $response = $this->soap->authenticate($api_key);
        $this->sid = $response[1];
        return $response;
    }


    public function company($sid)
    {
        return $this->soap->company($sid);
    }

    public function countProducts()
    {
        return $this->soap->countProducts($this->sid);
    }

    public function listProducts($offset)
    {
        return $this->soap->listProducts($this->sid, $offset);
    }


    public function productExists($ref)
    {
        $result = $this->soap->productExists($this->sid, $ref);
        if ($result[0] == 1) {
            return TRUE;
        }
        return FALSE;
    }
}
