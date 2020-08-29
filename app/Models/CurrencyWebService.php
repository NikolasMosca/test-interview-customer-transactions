<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;

/**
 * Dummy web service returning random exchange rates
 *
 */
class CurrencyWebService
{
    //Autentication key
    private $_key; 

    //The currencies managed by webservices
    private $_managedCurrencies = [
        "£",
        "$",
        "€"
    ];
    
    //Error messages
    private $_INVALID_CURRENCY = 'INVALID_CURRENCY'; 
    private $_INVALID_CURRENCY_TYPE = 'INVALID_CURRENCY_TYPE'; 
    private $_INVALID_CURRENCY_AMOUNT = 'INVALID_CURRENCY_AMOUNT'; 
    private $_INVALID_CONVERT_CURRENCY_TYPE = 'INVALID_CONVERT_CURRENCY_TYPE'; 

    public function __construct($key) {
        $this->_key = $key;
    }

    //Write log and return the text error
    private function writeLog($text, $value) {
        Log::error("CurrencyWebService :::: [$value] :::: $text");
        return $text;
    }

    //Check if this currency is valid 
    private function isValidCurrency($currency) {
        foreach($this->_managedCurrencies as $currencyType) {
            if($currency === $currencyType) {
                return true;
            }
        }
        return false;
    }

    //Check currency value passed
    private function checkCurrency($currency) {
        if(!$currency || gettype($currency) !== 'string' || strlen($currency) === 0) {
            return $this->writeLog($this->_INVALID_CURRENCY, $currency);
        }

        //Check first char if it's a valid currency type 
        $currencyType = $this->getCurrencyType($currency);
        if($this->isValidCurrency($currencyType) === false) {
            return $this->writeLog($this->_INVALID_CURRENCY_TYPE, $currencyType);
        }

        //Check the remain chars if it's a correct number 
        $amount = str_replace('.', '', str_replace(',', '', mb_substr($currency, 1)));
        if(!ctype_digit($amount)) {
            return $this->writeLog($this->_INVALID_CURRENCY_AMOUNT, $currency);
        }
        return true;
    }

    //Returns a currency type
    private function getCurrencyType($currency) {
        return mb_substr($currency, 0, 1);
    }

    //Returns a currency amount
    private function getCurrencyAmount($currency) {
        return floatval(mb_substr($currency, 1));
    }

    //return random value here for basic currencies like GBP USD EUR (simulates real API)
    public function getExchangeRate($currency, $convertType) {
        //Check currency
        $isValid = $this->checkCurrency($currency);
        if($isValid !== true) {
            return $isValid;
        }

        //Check convert type
        if($this->isValidCurrency($convertType) === false) {
            return $this->writeLog($this->_INVALID_CONVERT_CURRENCY_TYPE, $convertType);
        }

        //The first char is the currency type
        $currencyType = $this->getCurrencyType($currency);
        $amount = $this->getCurrencyAmount($currency); 

        //If the currency is not equal to the convert type, multiply amount
        if($currencyType !== $convertType) {
            $amount *= 10;
        }

        return $convertType.number_format($amount, 2);
    }
}