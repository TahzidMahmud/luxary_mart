<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShopsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('shops')->delete();

        \DB::table('shops')->insert(array(
            array(
                'id'                            => 1,
                'user_id'                       => 1,
                'is_approved'                   => 1,
                'is_verified_by_admin'          => 1,
                'is_published'                  => 1,
                'logo'                          => NULL,
                'banner'                        => NULL,
                'tagline'                       => NULL,
                'name'                          => 'Admin Shop',
                'slug'                          => 'admin-shop',
                'rating'                        => 5,
                'address'                       => NULL,
                'min_order_amount'              => 0.00,
                'admin_commission_percentage'   => 0.00,
                'current_balance'               => 0.00,
                'manage_stock_by'               => 'inventory',
                'is_cash_payout'                => 0,
                'is_bank_payout'                => 0,
                'bank_name'                     => NULL,
                'bank_acc_name'                 => NULL,
                'bank_acc_no'                   => NULL,
                'bank_routing_no'               => NULL,
                'created_at'                    => '2020-12-31 14:01:30'
            ),
        ));
    }
}
