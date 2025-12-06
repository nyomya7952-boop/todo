<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'content' => $this->faker->text(20),
            'category_id' => Category::factory(),
            // priority を設定しない場合、データベースのデフォルト値（'medium'）が使用される
            'due_date' => $this->faker->date,
        ];
    }
}
