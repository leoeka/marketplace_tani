<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(): View
    {
        return view('marketplace.articles.index', [
            'articles' => Article::query()
                ->where('status', 'published')
                ->orderByDesc('published_at')
                ->paginate(9),
        ]);
    }

    public function show(string $slug): View
    {
        $article = Article::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('marketplace.articles.show', ['article' => $article]);
    }
}
