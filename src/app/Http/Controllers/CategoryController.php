<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        // $categories = Category::all();
        $categories = Category::orderBy('created_at', 'desc')->paginate(5);
        $message = session('message');
        return view('category', compact('categories', 'message'));
    }

    public function store(CategoryRequest $request)
    {
        $category = $request->only(['name']);
        Category::create($category);
        return redirect('/categories')->with('message', 'Categoryを作成しました');
    }

    public function update(CategoryRequest $request)
    {
        $id = $request->id;
        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->save();
        return redirect('/categories')->with('message', 'Categoryを更新しました');
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        Category::findOrFail($id)->delete();
        return redirect('/categories')->with('message', 'Categoryを削除しました');
    }
}
