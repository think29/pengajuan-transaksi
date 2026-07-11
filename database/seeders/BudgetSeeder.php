<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = 2026;

        $budgets = [

            'ATK' => 100000000,
            'Operasional' => 500000000,
            'Perjalanan Dinas' => 250000000,
            'Marketing' => 300000000,
            'Maintenance' => 200000000,
            'Training' => 150000000,
            'PO Product' => 1000000000,

        ];

        foreach ($budgets as $categoryName => $totalBudget) {

            $category = Category::where('name', $categoryName)->first();

            if (!$category) {
                continue;
            }

            Budget::updateOrCreate(

                [
                    'category_id' => $category->id,
                    'year' => $year,
                ],

                [
                    'total_budget' => $totalBudget,
                    'used_budget' => 0,
                ]

            );
        }
    }
}