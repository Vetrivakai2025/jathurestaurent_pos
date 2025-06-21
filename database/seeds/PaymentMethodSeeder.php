<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       	// Insert some stuffs
        DB::table('payment_methods')->insert(
            array(
            [
                'id'           => 6,
                'title'        => 'Cash',
                'is_default'   => 0,

            ],
            )
            
        );
    }
}
