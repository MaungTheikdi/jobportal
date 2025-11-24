<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\JobCategory; // Ensure your Model is imported

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // List of real-world job categories
        $categories = [
            'Information Technology',
            'Marketing & Sales',
            'Finance & Accounting',
            'Human Resources',
            'Engineering',
            'Healthcare',
            'Customer Service',
            'Design & Creative',
            'Operations',
            'Legal',
        ];

        foreach ($categories as $category) {
            // Using updateOrCreate prevents duplicates if you run the seeder twice
            JobCategory::updateOrCreate(
                ['name' => $category], // Check if this name exists
                [
                    'slug' => Str::slug($category), // Auto-generate slug (e.g., 'finance-accounting')
                ]
            );
        }
    }
}