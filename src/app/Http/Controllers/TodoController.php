<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\TodoRequest;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::with('category')
        ->where('is_completed', 0)
        ->orderBy('due_date', 'asc')
        ->orderBy('priority', 'desc')
        ->paginate(5);
        $categories = Category::all();
        $todos_count = Todo::where('is_completed', 0)->count();
        $message = session('message');
        return view('index', compact('todos', 'categories', 'todos_count', 'message'));
    }

    public function store(TodoRequest $request)
    {
        $todo = $request->only(['content', 'category_id', 'priority', 'due_date']);
        // priorityが空文字列の場合は配列から削除してデフォルト値（medium）を使用
        if (empty($todo['priority'])) {
            unset($todo['priority']);
        }
        $todo = Todo::create($todo);
        return redirect('/')->with('message', 'Todoを作成しました');
    }

    public function update(TodoRequest $request)
    {
        $id = $request->id;
        $todo = Todo::findOrFail($id);
        $todo->content = $request->content;
        $todo->category_id = $request->category_id;
        $todo->priority = $request->priority;
        $todo->due_date = $request->due_date;
        // チェックボックスがチェックされている場合は1、されていない場合は0
        $todo->is_completed = $request->has('is_completed') && $request->is_completed == '1' ? 1 : 0;
        $todo->save();
        return redirect('/')->with('message', 'Todoを更新しました');
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        Todo::findOrFail($id)->delete();
        return redirect('/')->with('message', 'Todoを削除しました');
    }

    public function search(Request $request)
    {
        // $content = $request->content;
        // $category = $request->category;
        // $todos = Todo::where('content', 'like', "%$content%")->where('category_id', 'like', "%$category%")->get();
        $todos = Todo::with('category')
        ->categorySearch($request->category_id)
        ->keywordSearch($request->keyword)
        ->prioritySearch($request->priority)
        ->dueDateSearch($request->due_date)
        ->isCompletedSearch($request->is_completed)
        ->orderBy('is_completed', 'asc')
        ->orderBy('due_date', 'asc')
        ->orderBy('priority', 'desc')
        ->paginate(5);
        $categories = Category::all();
        $todos_count = Todo::where('is_completed', 0)->count();
        return view('index', compact('todos', 'categories', 'todos_count'));
    }
}
