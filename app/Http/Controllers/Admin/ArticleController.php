<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'excerpt' => 'required|string|max:300',
            'body' => 'required|string',
            'publish' => 'nullable|boolean',
        ];
    }

    private function payload(array $data): array
    {
        $publish = $data['publish'] ?? false;
        unset($data['publish']);
        $data['published_at'] = $publish ? now() : null;

        return $data;
    }

    public function index()
    {
        return view('admin.articles.index', ['articles' => Article::latest()->paginate(20)]);
    }

    public function create()
    {
        return view('admin.articles.form', ['article' => new Article]);
    }

    public function store(Request $request)
    {
        $request->user()->articles()->create($this->payload($request->validate($this->rules())));

        return redirect()->route('admin.articles.index')->with('status', 'Статья добавлена.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.form', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->payload($request->validate($this->rules()));
        if ($data['published_at'] && $article->published_at) {
            $data['published_at'] = $article->published_at;
        }
        $article->update($data);

        return redirect()->route('admin.articles.index')->with('status', 'Статья обновлена.');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return back()->with('status', 'Статья удалена.');
    }
}
