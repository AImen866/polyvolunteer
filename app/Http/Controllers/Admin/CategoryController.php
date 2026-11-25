<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories', ['categories' => Category::withCount('events')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        Category::create($request->validate([
            'name' => 'required|string|max:80|unique:categories,name',
            'hours_coefficient' => 'required|numeric|min:0.5|max:5',
            'guests_per_volunteer' => 'required|integer|min:1|max:500',
        ]));

        return back()->with('status', 'Категория добавлена.');
    }

    public function update(Request $request, Category $category)
    {
        $category->update($request->validate([
            'name' => 'required|string|max:80|unique:categories,name,'.$category->id,
            'hours_coefficient' => 'required|numeric|min:0.5|max:5',
            'guests_per_volunteer' => 'required|integer|min:1|max:500',
        ]));

        return back()->with('status', 'Категория обновлена.');
    }

    public function destroy(Category $category)
    {
        if ($category->events()->exists()) {
            return back()->with('error', 'Нельзя удалить категорию, в которой есть мероприятия.');
        }
        $category->delete();

        return back()->with('status', 'Категория удалена.');
    }
}
