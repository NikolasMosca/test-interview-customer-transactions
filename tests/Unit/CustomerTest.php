<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Customer;

class CustomerTest extends TestCase
{
    private $_formatDataTests = [
        [ //Standard case
            'data' => [
                [
                    'customer',
                    'date',
                    'value'
                ],
                [
                    'test1',
                    'test2',
                    'test3'
                ]
            ],
            'expected' => [
                [
                    'customer' => 'test1',
                    'date' => 'test2',
                    'value' => 'test3',
                ]
            ]
        ],
        [ //If there is any difference between columns and data
            'data' => [
                [
                    'customer',
                    'date',
                    'value'
                ],
                [
                    'test1',
                    'test2'
                ]
            ],
            'expected' => [
                [
                    'customer' => 'test1',
                    'date' => 'test2',
                    'value' => null
                ]
            ]
        ],
        [ //If there is any difference between columns and data
            'data' => [
                [
                    'customer',
                    'date'
                ],
                [
                    'test1',
                    'test2',
                    'test3'
                ]
            ],
            'expected' => [
                [
                    'customer' => 'test1',
                    'date' => 'test2'
                ]
            ]
        ]
    ];

    /**
     * Test if the formatData method in Customer model transform the results properly
     */
    public function testIfFormatDataReturnCorrectValues() {
        $customer = new Customer();
        $reflection = new \ReflectionClass(get_class($customer));
        $method = $reflection->getMethod('formatData');
        $method->setAccessible(true);

        foreach($this->_formatDataTests as $test) {
            $this->assertEquals($method->invokeArgs($customer, [
                $test['data']
            ]), $test['expected']);
        }
    }

    /**
     * Test if the file provided doesn't exists, it should be display an error and returns an empty array
     */
    public function testIfReadFileFailsWithFileNotFound() {
        $customer = new Customer();
        $reflection = new \ReflectionClass(get_class($customer));
        $method = $reflection->getMethod('readFile');
        $method->setAccessible(true);

        $this->assertEquals($method->invokeArgs($customer, [
            '/file-not-found.csv'
        ]), []);
    }
}
