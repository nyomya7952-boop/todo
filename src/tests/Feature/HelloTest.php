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

    /**
     * Todoモデルの作成テスト
     */
    public function test_todo_can_be_created()
    {
        $category = Category::factory()->create();
        
        $todo = Todo::factory()->create([
            'content' => 'テストTodo',
            'category_id' => $category->id,
            'priority' => 'high',
            'due_date' => '2025-12-31',
            'is_completed' => false
        ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'content' => 'テストTodo',
            'category_id' => $category->id,
            'priority' => 'high',
            'due_date' => '2025-12-31',
            'is_completed' => 0
        ]);
    }

    /**
     * Todoモデルの更新テスト
     */
    public function test_todo_can_be_updated()
    {
        $category = Category::factory()->create();
        $todo = Todo::factory()->create([
            'category_id' => $category->id,
            'content' => '元の内容',
            'priority' => 'low',
            'is_completed' => false
        ]);

        $todo->update([
            'content' => '更新された内容',
            'priority' => 'high',
            'is_completed' => true
        ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'content' => '更新された内容',
            'priority' => 'high',
            'is_completed' => 1
        ]);
    }

    /**
     * Todoモデルの削除テスト
     */
    public function test_todo_can_be_deleted()
    {
        $category = Category::factory()->create();
        $todo = Todo::factory()->create([
            'category_id' => $category->id
        ]);

        $todoId = $todo->id;
        $todo->delete();

        $this->assertDatabaseMissing('todos', [
            'id' => $todoId
        ]);
    }

    /**
     * TodoとCategoryのリレーションテスト
     */
    public function test_todo_belongs_to_category()
    {
        $category = Category::factory()->create(['name' => 'テストカテゴリ']);
        $todo = Todo::factory()->create([
            'category_id' => $category->id
        ]);

        $this->assertInstanceOf(Category::class, $todo->category);
        $this->assertEquals($category->id, $todo->category->id);
        $this->assertEquals('テストカテゴリ', $todo->category->name);
    }

    /**
     * CategorySearchスコープのテスト
     */
    public function test_todo_category_search_scope()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        
        Todo::factory()->create(['category_id' => $category1->id]);
        Todo::factory()->create(['category_id' => $category2->id]);
        Todo::factory()->create(['category_id' => $category1->id]);

        $results = Todo::categorySearch($category1->id)->get();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results->every(function ($todo) use ($category1) {
            return $todo->category_id === $category1->id;
        }));
    }

    /**
     * KeywordSearchスコープのテスト
     */
    public function test_todo_keyword_search_scope()
    {
        $category = Category::factory()->create();
        
        Todo::factory()->create([
            'category_id' => $category->id,
            'content' => 'テストキーワード'
        ]);
        Todo::factory()->create([
            'category_id' => $category->id,
            'content' => '別の内容'
        ]);
        Todo::factory()->create([
            'category_id' => $category->id,
            'content' => 'キーワード検索'
        ]);

        $results = Todo::keywordSearch('キーワード')->get();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results->every(function ($todo) {
            return strpos($todo->content, 'キーワード') !== false;
        }));
    }

    /**
     * PrioritySearchスコープのテスト
     */
    public function test_todo_priority_search_scope()
    {
        $category = Category::factory()->create();
        
        Todo::factory()->create([
            'category_id' => $category->id,
            'priority' => 'high'
        ]);
        Todo::factory()->create([
            'category_id' => $category->id,
            'priority' => 'medium'
        ]);
        Todo::factory()->create([
            'category_id' => $category->id,
            'priority' => 'high'
        ]);

        $results = Todo::prioritySearch('high')->get();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results->every(function ($todo) {
            return $todo->priority === 'high';
        }));
    }

    /**
     * DueDateSearchスコープのテスト
     */
    public function test_todo_due_date_search_scope()
    {
        $category = Category::factory()->create();
        $targetDate = '2025-12-25';
        
        Todo::factory()->create([
            'category_id' => $category->id,
            'due_date' => $targetDate
        ]);
        Todo::factory()->create([
            'category_id' => $category->id,
            'due_date' => '2025-12-26'
        ]);
        Todo::factory()->create([
            'category_id' => $category->id,
            'due_date' => $targetDate
        ]);

        $results = Todo::dueDateSearch($targetDate)->get();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results->every(function ($todo) use ($targetDate) {
            return $todo->due_date === $targetDate;
        }));
    }

    /**
     * IsCompletedSearchスコープのテスト
     */
    public function test_todo_is_completed_search_scope()
    {
        $category = Category::factory()->create();
        
        Todo::factory()->create([
            'category_id' => $category->id,
            'is_completed' => true
        ]);
        Todo::factory()->create([
            'category_id' => $category->id,
            'is_completed' => false
        ]);
        Todo::factory()->create([
            'category_id' => $category->id,
            'is_completed' => true
        ]);

        $completedResults = Todo::isCompletedSearch('1')->get();
        $this->assertCount(2, $completedResults);
        $this->assertTrue($completedResults->every(function ($todo) {
            return $todo->is_completed === 1;
        }));

        $incompleteResults = Todo::isCompletedSearch('0')->get();
        $this->assertCount(1, $incompleteResults);
        $this->assertTrue($incompleteResults->every(function ($todo) {
            return $todo->is_completed === 0;
        }));
    }

    /**
     * 複数スコープの組み合わせテスト
     */
    public function test_todo_multiple_scopes_combination()
    {
        $category = Category::factory()->create();
        
        Todo::factory()->create([
            'category_id' => $category->id,
            'content' => 'テスト内容',
            'priority' => 'high',
            'is_completed' => false
        ]);
        Todo::factory()->create([
            'category_id' => $category->id,
            'content' => '別の内容',
            'priority' => 'high',
            'is_completed' => false
        ]);
        Todo::factory()->create([
            'category_id' => $category->id,
            'content' => 'テスト内容',
            'priority' => 'low',
            'is_completed' => false
        ]);

        $results = Todo::categorySearch($category->id)
            ->keywordSearch('テスト')
            ->prioritySearch('high')
            ->isCompletedSearch('0')
            ->get();

        $this->assertCount(1, $results);
        $this->assertEquals('テスト内容', $results->first()->content);
        $this->assertEquals('high', $results->first()->priority);
    }

    /**
     * Categoryモデルの作成テスト
     */
    public function test_category_can_be_created()
    {
        $category = Category::factory()->create([
            'name' => '新規カテゴリ'
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => '新規カテゴリ'
        ]);
    }

    /**
     * Categoryモデルの更新テスト
     */
    public function test_category_can_be_updated()
    {
        $category = Category::factory()->create([
            'name' => '元の名前'
        ]);

        $category->update([
            'name' => '更新された名前'
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => '更新された名前'
        ]);
    }

    /**
     * Categoryモデルの削除テスト
     */
    public function test_category_can_be_deleted()
    {
        $category = Category::factory()->create();
        $categoryId = $category->id;

        $category->delete();

        $this->assertDatabaseMissing('categories', [
            'id' => $categoryId
        ]);
    }

    /**
     * Category削除時のカスケード削除テスト
     */
    public function test_todos_are_cascaded_when_category_is_deleted()
    {
        $category = Category::factory()->create();
        $todo1 = Todo::factory()->create(['category_id' => $category->id]);
        $todo2 = Todo::factory()->create(['category_id' => $category->id]);

        $category->delete();

        $this->assertDatabaseMissing('todos', ['id' => $todo1->id]);
        $this->assertDatabaseMissing('todos', ['id' => $todo2->id]);
    }

    /**
     * Todoのpriorityデフォルト値テスト
     */
    public function test_todo_priority_default_value()
    {
        $category = Category::factory()->create();
        
        // priority を指定しないことで、データベースのデフォルト値（'medium'）が使用される
        $todo = Todo::factory()->create([
            'category_id' => $category->id,
            'content' => 'テスト'
        ]);

        // マイグレーションでデフォルト値がmediumに設定されているため
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'priority' => 'medium'
        ]);
    }

    /**
     * Todoのis_completedデフォルト値テスト
     */
    public function test_todo_is_completed_default_value()
    {
        $category = Category::factory()->create();
        
        // is_completed を指定しないことで、データベースのデフォルト値（false）が使用される
        $todo = Todo::factory()->create([
            'category_id' => $category->id,
            'content' => 'テスト'
        ]);

        // マイグレーションでデフォルト値がfalseに設定されているため
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'is_completed' => 0
        ]);
    }

    /**
     * Todoのdue_dateがnull許容のテスト
     */
    public function test_todo_due_date_can_be_null()
    {
        $category = Category::factory()->create();
        
        $todo = Todo::factory()->create([
            'category_id' => $category->id,
            'content' => 'テスト',
            'due_date' => null
        ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'due_date' => null
        ]);
        $this->assertNull($todo->due_date);
    }

    /**
     * 空のスコープパラメータのテスト
     */
    public function test_scopes_with_empty_parameters()
    {
        $category = Category::factory()->create();
        Todo::factory()->count(3)->create(['category_id' => $category->id]);

        $results1 = Todo::categorySearch(null)->get();
        $results2 = Todo::keywordSearch('')->get();
        $results3 = Todo::prioritySearch(null)->get();
        $results4 = Todo::dueDateSearch('')->get();

        $this->assertCount(3, $results1);
        $this->assertCount(3, $results2);
        $this->assertCount(3, $results3);
        $this->assertCount(3, $results4);
    }
}
