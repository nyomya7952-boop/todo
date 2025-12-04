<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Todo;
use App\Models\Category;

class HelloTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        // $response = $this->get('/');
        // $response->assertStatus(200);

        // $response = $this->get('/no_route');
        // $response->assertStatus(404);

        // カテゴリを先に作成
        $category = Category::create([
            'name' => 'test_cat'
        ]);

        Todo::factory()->create([
            'content'=>'test12345',
            'category_id'=>$category->id,
            'priority'=>'medium',
            'due_date'=>'2025-12-04',
            'is_completed'=>0
        ]);
        $this->assertDatabaseHas('todos', [
            'content' => 'test12345',
            'category_id' => $category->id,
            'priority' => 'medium',
            'due_date' => '2025-12-04',
            'is_completed' => 0
        ]);
    }

    public function test_which_database()
{
    dump(\DB::connection()->getDatabaseName());
    $this->assertTrue(true);
}
}
