<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categoriesJson = file_get_contents(storage_path('app/data/categories.json'));
        $categoriesData = json_decode($categoriesJson, true);

        foreach ($categoriesData as $categoryName => $subcategories) {
            // Criar categoria principal
            $category = Category::create([
                'name' => $categoryName,
                'type' => 'category',
            ]);

            // Criar subcategorias
            foreach ($subcategories as $subcategoryName) {
                Category::create([
                    'name' => $subcategoryName,
                    'type' => 'subcategory',
                    'parent_id' => $category->id,
                ]);
            }
        }
    }
}
