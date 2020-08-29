<?php

namespace App\Models;

/**
 * Dummy web service returning random exchange rates
 *
 */
class CurrencyConverter
{
    private $_webService;
    public function __construct(CurrencyWebService $webService) {
        $this->_webService = $webService;
    }

    //request to webservice to convert received currency into new currency type
    public function convert($amount, $type) {
        $result = $this->_webService->getExchangeRate($amount, $type);

        //If something went wrong display a generic error
        if(strpos($result, 'INVALID') !== false) {
            return 'invalid';
        }
        return $result;
    }
}