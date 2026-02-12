<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoryInscription;

class CategoryInscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Delegate (Member)',
                'price' => 675.00,
                'price_low' => 280.00,
                'description' => '',
                'type' => 'radio',
                'order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Delegate (Non-Member)',
                'price' => 775.00,
                'price_low' => 350.00,
                'description' => '',
                'type' => 'radio',
                'order' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Student (Member)',
                'price' => 225.00,
                'price_low' => 150.00,
                'description' => '',
                'type' => 'radio',
                'order' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'Student (Non-Member)',
                'price' => 275.00,
                'price_low' => 200.00,
                'description' => '',
                'type' => 'radio',
                'order' => 4,
                'status' => 'active',
            ],
            [
                'name' => 'Scholars',
                'price' => 0.00,
                'price_low' => 0.00,
                'description' => '',
                'type' => 'radio',
                'order' => 5,
                'status' => 'active',
            ],
            [
                'name' => 'Special Code',
                'price' => 0.00,
                'price_low' => 0.00,
                'description' => '',
                'type' => 'radio',
                'order' => 6,
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            CategoryInscription::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
