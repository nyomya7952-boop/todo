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
        $todos = Todo::with('category')->get();
        $categories = Category::all();
        $message = session('message');
        return view('index', compact('todos', 'categories', 'message'));
    }

    public function store(TodoRequest $request)
    {
        $todo = $request->only(['content', 'category_id']);
        $todo = Todo::create($todo);
        return redirect('/')->with('message', 'Todoを作成しました');
    }

    public function update(TodoRequest $request)
    {
        $id = $request->id;
        $todo = Todo::findOrFail($id);
        $todo->content = $request->content;
        $todo->category_id = $request->category_id;
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
        $todos = Todo::with('category')->categorySearch($request->category_id)->keywordSearch($request->keyword)->get();
        $categories = Category::all();
        return view('index', compact('todos', 'categories'));
    }
}
