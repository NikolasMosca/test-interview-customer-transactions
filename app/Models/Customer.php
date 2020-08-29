<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\CurrencyConverter;
use App\Models\CurrencyWebService;

/**
 * Model to retrieve informations about Customer
 *
 */
class Customer
{
    //File path with transaction data
    private $_transactions = '/data.csv';

    //Format the result obtained from file, first row is column, return sql like result
    private function formatData($rows) {
        if(count($rows) <= 1) {
            return [];
        }

        //Get columns from the first row 
        $columns = $rows[0];
        $results = [];

        //Remove the first row
        unset($rows[0]);

        //Parse each row and create sql like result
        foreach($rows as $row) {
            $data = [];
            if($row && count($row) > 0) {
                foreach($columns as $key => $column) {
                    //If it's not isset, default value shoud be null
                    if(!isset($row[$key])) {
                        $row[$key] = null;
                    }
                    $data[$column] = $row[$key];
                }
            }
            if(count($data) > 0) {
                $results[] = $data;
            }
        }
        return $results; 
    }

    //Read the file and return all of data
    private function readFile($path) {
        $pathFile = base_path().$path;
        try {
            if ( !file_exists($pathFile) ) {
                throw new Exception('File not found');
            }

            $fileHandler = fopen($pathFile, 'r');
            $rows = [];
            while(!feof($fileHandler)) {
                $rows[] = fgetcsv($fileHandler, 0, ';');
            }
            fclose($fileHandler);
        } catch(Exception $e) { 
            Log::error('Customer->getTransactions :::: File not found! ['.$pathFile.']');
            return [];
        }
        return $this->formatData($rows);
    }

    //Get all of transactions for a specific customer id 
    public function getTransactions($id) {
        $results = $this->readFile($this->_transactions);
        if(count($results) === 0) {
            return $results;
        }

        //Login to currency webservice 
        $webService = new CurrencyWebService(env('CURRENCY_WEBSERVICE_KEY'));
        $currencyConverter = new CurrencyConverter($webService);

        //Filter results by the customer id 
        $filteredResults = [];
        foreach($results as $result) {
            if($result['customer'] === $id) {
                $result['value'] = $currencyConverter->convert($result['value'], '€');
                $result['date'] = date('Y-m-d H:i:s', strtotime($result['date']));
                $filteredResults[] = $result;
            }
        }

        return $filteredResults;
    }
}
