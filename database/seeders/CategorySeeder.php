<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [

            [
                'name' => 'ATK',
                'is_po_product' => false,
                'description' => 'Pengadaan alat tulis kantor',
            ],

            [
                'name' => 'Operasional',
                'is_po_product' => false,
                'description' => 'Biaya operasional perusahaan',
            ],

            [
                'name' => 'Perjalanan Dinas',
                'is_po_product' => false,
                'description' => 'Biaya perjalanan dinas',
            ],

            [
                'name' => 'Marketing',
                'is_po_product' => false,
                'description' => 'Biaya promosi dan pemasaran',
            ],

            [
                'name' => 'Maintenance',
                'is_po_product' => false,
                'description' => 'Biaya perawatan aset',
            ],

            [
                'name' => 'Training',
                'is_po_product' => false,
                'description' => 'Pelatihan dan sertifikasi',
            ],

            [
                'name' => 'PO Product',
                'is_po_product' => true,
                'description' => 'Pengadaan barang melalui Purchase Order',
            ],

        ];

        foreach ($categories as $category) {

            Category::updateOrCreate(

                [
                    'name' => $category['name'],
                ],

                [
                    'is_po_product' => $category['is_po_product'],
                    'description' => $category['description'],
                ]

            );

        }
    }
}