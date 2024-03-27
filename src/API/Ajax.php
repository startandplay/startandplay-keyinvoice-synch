<?php

/**
 * @package Keyinvoice
 */

namespace keyinvoicesync\API;

use keyinvoicesync\API\KeyinvoiceSynch;
use keyinvoicesync\API\Keyinvoice;
use keyinvoicesync\API\WPress;
use keyinvoicesync\API\Middleware\KeyinvoiceApiManager;
use keyinvoicesync\API\Middleware\KeyInvoiceApiConnection;

class Ajax
{
	public $actions = [];
	public $wp;

	public function register()
	{
		$this->wp = new WPress();
		$this->actions = [
			'test_api' => 'testAjax',
			'synch_products' => 'synchProducts',
		];

		foreach ($this->actions as $action => $callback) {

			if (method_exists($this, $callback))
				add_action('wp_ajax_' . $action, [$this, $callback]);
		}
	}

	public function testAjax()
	{
		$post = $_POST;
		$api_url = sanitize_text_field($post['api_url']);
		$api_key = sanitize_text_field($post['api_key']);
		// error_log(print_r($GLOBALS['wp_filter'], true));

		if ($api_url != "" && $api_key != "") {
			try {
				$soap = new Keyinvoice($api_url);
				$result = $soap->authenticate($api_key);
				if (intval($result[0]) != 1) {
					echo json_encode(array('msg' => __('Autenticação falhada. Verifique a configuração da sua ligação API!')));
				} else {
					$company = $soap->company($result[1]);
					$msg = __('Autenticação feita com sucesso!') . '</br>';
					$msg .= __("Empresa") . ":  " .  mb_convert_encoding($company->DAT[0]->Name, 'UTF-8');
					echo json_encode(array('msg' => $msg));
				}
			} catch (\Exception $ex) {
				echo json_encode(array('msg' => __('Autenticação falhada. Verifique a configuração da sua ligação API!'), 'erroe' => $ex));
			}
		}

		wp_die();
	}


	public function synchProducts()
	{
		$apiManager = new KeyinvoiceApiManager();

		// $data = $this->loadDataFromKeyinvoiceApiFile();
		// error_log(print_r($data, true));

		if ($apiManager->isTransientDateExpire()) {

			$apiManager->saveTimestampTransientDate();

			$apiConnection = new KeyInvoiceApiConnection(get_option('url_api'));
			$apiConnection->authenticate(get_option('api_key'));

			$productsData = $apiConnection->getAllKeyinvoiceProducts();
			$coutProducts = $apiConnection->countProducts()[1];

			if (isset($productsData)) {

				//save data into a file 
				// $apiManager->createKeyinvoiceApiDataFile($productsData);

				foreach ($productsData as $product) {

					$price = floatval($product->Price);

					if (intval($product->TaxIncluded) == 0) {
						$price_plus_tax = floatval($product->Price) * (1 + floatval($product->TAX) / 100);
						$price = number_format($price_plus_tax, 2, '.', ''); // round price with two decimal places
					}

					$this->wp->updateProduct([
						'sku'                => $product->Ref,
						'name'               => $product->Name,
						'regular_price'      => $price,
						'stock_qty'			 => $product->Stock,
						'manage_stock'		 => (($product->HasStocks == 1) ? true : false),
					]);
				}
			} else {
				//trow an exception no data found 
				echo json_encode(array('msg' => __('Erro na obtenção de dados da API Keyinvoice')));
				wp_die();
			}

			echo json_encode(array('msg' => __('Stock sincronizado com sucesso! ' . $coutProducts . ' Artigo(s) sincronizado(s)')));
		} else {

			$transientTimestamp = $apiManager->getTransientTimestamp();
			echo json_encode(array('msg' => __('The last update was less than 5 hours ago!  ') . $transientTimestamp));
		}

		wp_die();
	}
}
