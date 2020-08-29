<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\CurrencyWebService;

class CurrencyWebServiceTest extends TestCase
{
    private $_convertTests = [
        [ //Check standard
            'amount' => "$10.00",
            'convert' => "€",
            'expected' => "€100.00"
        ],
        [ //Check if the currency type is the same
            'amount' => '$10.00',
            'convert' => '$',
            'expected' => '$10.00'
        ],
        [ //Check if amount is not present 
            'amount' => '10.00',
            'convert' => '$',
            'expected' => 'INVALID_CURRENCY_TYPE'
        ],
        [ //Check if amount is not expected char 
            'amount' => '!10.00',
            'convert' => '$',
            'expected' => 'INVALID_CURRENCY_TYPE'
        ],
        [ //Check if amount is not valid 
            'amount' => '$invalid',
            'convert' => '$',
            'expected' => 'INVALID_CURRENCY_AMOUNT'
        ],
        [ //Check if amount is not valid 
            'amount' => '$1f.00',
            'convert' => '$',
            'expected' => 'INVALID_CURRENCY_AMOUNT'
        ],
        [ //Check if amount is not valid 
            'amount' => '$f10.00',
            'convert' => '$',
            'expected' => 'INVALID_CURRENCY_AMOUNT'
        ],
        [ //Check if amount is not valid 
            'amount' => '$10.00f',
            'convert' => '$',
            'expected' => 'INVALID_CURRENCY_AMOUNT'
        ],
        [ //Check if amount is not present
            'amount' => '$',
            'convert' => '$',
            'expected' => 'INVALID_CURRENCY_AMOUNT'
        ],
        [ //Check if amount is empty
            'amount' => '',
            'convert' => '$',
            'expected' => 'INVALID_CURRENCY'
        ],
        [ //Check if try to convert to non-managed currency type
            'amount' => '€100.00',
            'convert' => '!',
            'expected' => 'INVALID_CONVERT_CURRENCY_TYPE'
        ],
        [ //Check if convert is empty
            'amount' => '€100.00',
            'convert' => '',
            'expected' => 'INVALID_CONVERT_CURRENCY_TYPE'
        ]
    ];

    /**
     * Test convert function, check if return results working properly
     */
    public function testIfGetExchangeRateReturnCorrectValues()
    {
        $webService = new CurrencyWebService(env('CURRENCY_WEBSERVICE_KEY'));

        foreach($this->_convertTests as $test) {
            $this->assertEquals(
                $webService->getExchangeRate($test['amount'], $test['convert']),
                $test['expected']
            );
        }
    }

    /**
     * Test write log and verify if the error returns correctly
     */
    public function testIfWriteLogReturnsTheError()
    {
        $webService = new CurrencyWebService(env('CURRENCY_WEBSERVICE_KEY'));
        $reflection = new \ReflectionClass(get_class($webService));
        $method = $reflection->getMethod('writeLog');
        $method->setAccessible(true);

        $this->assertEquals(
            $method->invokeArgs($webService, [
                'error message',
                'value'
            ]),
            'error message'
        );
    }

    /**
     * Test write log and verify if the error returns correctly
     */
    public function testIfIsValidCurrencyWorkingProperly()
    {
        //Edit the method
        $webService = new CurrencyWebService(env('CURRENCY_WEBSERVICE_KEY'));
        $reflection = new \ReflectionClass(get_class($webService));
        $method = $reflection->getMethod('isValidCurrency');
        $method->setAccessible(true);

        //Edit private property
        $privateProperty = $reflection->getProperty('_managedCurrencies');
        $privateProperty->setAccessible(true);
        $privateProperty->setValue($webService, [
            "$"
        ]);

        //Good case 
        $this->assertTrue(
            $method->invokeArgs($webService, [
                '$',
            ])
        );

        //Bad case
        $this->assertFalse(
            $method->invokeArgs($webService, [
                'X',
            ])
        );
    }

    private $_currencyTypeCases = [
        [
            'data' => '$10.00',
            'expected' => '$'
        ],
        [
            'data' => '€10.00',
            'expected' => '€'
        ],
        [
            'data' => '£10.00',
            'expected' => '£'
        ]
    ];

    /**
     * Test if get currency type working properly
     */
    public function testIfGetCurrencyTypeWorkingProperly()
    {
        $webService = new CurrencyWebService(env('CURRENCY_WEBSERVICE_KEY'));
        $reflection = new \ReflectionClass(get_class($webService));
        $method = $reflection->getMethod('getCurrencyType');
        $method->setAccessible(true);

        foreach($this->_currencyTypeCases as $test) {
            $this->assertEquals($method->invokeArgs($webService, [
                $test['data']
            ]), $test['expected']);
        }
    }

    private $_currencyAmountCases = [
        [
            'data' => '$10.00',
            'expected' => '10.00'
        ],
        [
            'data' => '€99.99',
            'expected' => '99.99'
        ],
        [
            'data' => '£99',
            'expected' => '99.00'
        ]
    ];

    /**
     * Test if get currency amount working properly
     */
    public function testIfGetCurrencyAmountWorkingProperly()
    {
        $webService = new CurrencyWebService(env('CURRENCY_WEBSERVICE_KEY'));
        $reflection = new \ReflectionClass(get_class($webService));
        $method = $reflection->getMethod('getCurrencyAmount');
        $method->setAccessible(true);

        foreach($this->_currencyAmountCases as $test) {
            $this->assertEquals($method->invokeArgs($webService, [
                $test['data']
            ]), $test['expected']);
        }
    }
}
