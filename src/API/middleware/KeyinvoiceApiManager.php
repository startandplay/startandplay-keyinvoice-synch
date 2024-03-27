<?php

/**
 * @package Keyinvoice
 */

namespace keyinvoicesync\API\Middleware;

class KeyinvoiceApiManager
{

    const TIMESTAMP_TRANSIENT_NAME = 'keyinvoice_api_timestamp';


    public function saveTimestampTransientDate()
    {
        $timestamp = '';

        $transient_date = get_transient(self::TIMESTAMP_TRANSIENT_NAME);

        // Check if transient exists
        if ($transient_date === false) {

            $currentDateTime = date("Y-m-d H:i:s");

            // Serialize date along with current timestamp
            $serialized_date = serialize(array(
                'timestamp' => $currentDateTime
            ));

            // Set transient with a current timestamp
            set_transient(self::TIMESTAMP_TRANSIENT_NAME, $serialized_date, 18000); // 18000 = 5h

            // Unserialize the data
            $unserialized_date = unserialize($serialized_date);

            // Extract timestamp
            $timestamp = $unserialized_date['timestamp'];
        }

        return $timestamp;
    }


    public function getTransientTimestamp()
    {
        // Get the transient data
        $serialized_date = get_transient(self::TIMESTAMP_TRANSIENT_NAME);

        // Check if transient data exists
        if ($serialized_date !== false) {
            // Unserialize the timestamp
            $unserialized_date = unserialize($serialized_date);

            // Extract timestamp 
            return $unserialized_date['timestamp'];
        } else {
            // Transient data doesn't exist or has expired
            return false;
        }
    }

    public function isTransientDateExpire()
    {
        // Delete transient data
        // $this->deleteTransient() ? "TRUE" : "FALSE";

        // Get the transient data
        $serialized_date = get_transient(self::TIMESTAMP_TRANSIENT_NAME);
        $test = $serialized_date ? "TRUE" : "FALSE";
        error_log(print_r("serialized_date: " . $test, true));

        // Check if transient data exists
        if ($serialized_date === false) {
            return true;
        }

        // Transient data doesn't exist or has expired
        return false;
    }


    function deleteTransient()
    {
        $deleted = delete_transient(self::TIMESTAMP_TRANSIENT_NAME);

        error_log(print_r($deleted ? "TRUE" : "FALSE", true));

        return $deleted;
    }


    public function createKeyinvoiceApiDataFile($allKeyInvoiceProducts): void
    {
        if (isset($allKeyInvoiceProducts)) {

            $jsonString = json_encode($allKeyInvoiceProducts);
            $filename = dirname(__FILE__) . '\\' . date("YmdHis") . ".json";
            file_put_contents($filename, $jsonString);
            error_log(print_r($filename, true));
        } else {
            error_log(print_r("File couln't be created!", true));
        }
    }

    public function loadDataFromKeyinvoiceApiFile()
    {
        $file = dirname(__FILE__) . '\20240318171234.json';
        $fileContents = file_get_contents($file);

        return json_decode($fileContents);
    }
}
