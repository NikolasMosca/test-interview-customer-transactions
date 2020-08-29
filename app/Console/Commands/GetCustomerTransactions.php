<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;

class GetCustomerTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:get-transactions 
                            {--id= : The id of the customer (required)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all transactions maded by a specific customer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    //Validate the customer id 
    private function validateCustomerId($id) {
        if(!$id) {
            $this->error("--id param is not defined. The customer id sould be a number");
            return false;
        }
        if(is_numeric($id) === false) {
            $this->error("--id='$id' is not a correct customer-id. The customer id sould be a number");
            return false;
        }
        return true;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Retrieve parameters and validate it
        $id = $this->option('id');
        if(!$this->validateCustomerId($id)) {
            return 0;
        }

        $this->info("Retrieve all transactions maded my customer id with ID = $id");
        $customer = new Customer();
        $transactions = $customer->getTransactions($id);

        $this->info("Results found: ".count($transactions));
        if(count($transactions) > 0) {
            $columns = array_keys($transactions[0]);
            $this->table($columns, $transactions);
        } 
        return 0;
    }
}
