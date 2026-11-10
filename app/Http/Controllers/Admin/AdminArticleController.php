<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminArticleController extends Controller
{
    public function index(): View
    {
        return view('admin.articles.index', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.articles.title', [], 'id'),
            'articles' => Article::query()->latest()->paginate(12),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.articles.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.articles.create_title', [], 'id'),
            'article' => new Article(['status' => 'draft']),
            'action' => route('admin.articles.store'),
            'method' => 'POST',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Article::uniqueSlug($data['title']);
        $data['published_at'] = $data['status'] === 'published' ? now() : null;
        $article = Article::query()->create($data);

        return redirect()->route('admin.articles.edit', $article)
            ->with('success', __('admin.articles.created', [], 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article): View
    {
        return view('admin.articles.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.articles.edit_title', [], 'id'),
            'article' => $article,
            'action' => route('admin.articles.update', $article),
            'method' => 'PUT',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $data = $request->validated();

        if ($data['title'] !== $article->title) {
            $data['slug'] = Article::uniqueSlug($data['title'], $article->id);
        }

        if ($data['status'] === 'published' && $article->published_at === null) {
            $data['published_at'] = now();
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')
            ->with('success', __('admin.articles.updated', [], 'id'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', __('admin.articles.deleted', [], 'id'));
    }
}
