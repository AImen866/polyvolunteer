<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::whereNotNull('published_at')->with('author')->latest('published_at')->paginate(6);

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        abort_if(is_null($article->published_at), 404);
        $others = Article::whereNotNull('published_at')->whereKeyNot($article->id)->latest('published_at')->take(3)->get();

        return view('articles.show', compact('article', 'others'));
    }
}
