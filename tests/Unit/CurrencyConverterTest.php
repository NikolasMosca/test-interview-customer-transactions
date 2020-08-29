<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\CurrencyWebService;
use App\Models\CurrencyConverter;

class CurrencyConverterTest extends TestCase
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
            'expected' => 'invalid'
        ],
        [ //Check if amount is not expected char 
            'amount' => '!10.00',
            'convert' => '$',
            'expected' => 'invalid'
        ],
        [ //Check if amount is not valid 
            'amount' => '$invalid',
            'convert' => '$',
            'expected' => 'invalid'
        ],
        [ //Check if amount is not valid 
            'amount' => '$1f.00',
            'convert' => '$',
            'expected' => 'invalid'
        ],
        [ //Check if amount is not valid 
            'amount' => '$f10.00',
            'convert' => '$',
            'expected' => 'invalid'
        ],
        [ //Check if amount is not valid 
            'amount' => '$10.00f',
            'convert' => '$',
            'expected' => 'invalid'
        ],
        [ //Check if amount is not present
            'amount' => '$',
            'convert' => '$',
            'expected' => 'invalid'
        ],
        [ //Check if amount is empty
            'amount' => '',
            'convert' => '$',
            'expected' => 'invalid'
        ],
        [ //Check if try to convert to non-managed currency type
            'amount' => '',
            'convert' => '!',
            'expected' => 'invalid'
        ]
    ];

    /**
     * Test convert function, check if return results working properly
     */
    public function testIfConvertReturnCorrectValues()
    {
        $webService = new CurrencyWebService(env('CURRENCY_WEBSERVICE_KEY'));
        $currencyConverter = new CurrencyConverter($webService);

        foreach($this->_convertTests as $test) {
            $this->assertEquals(
                $currencyConverter->convert($test['amount'], $test['convert']),
                $test['expected']
            );
        }
    }
}
