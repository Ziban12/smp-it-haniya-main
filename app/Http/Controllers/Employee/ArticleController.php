<?php

namespace App\Http\Controllers\Employee;

use App\Models\MstArticle;
use App\Models\MstTagArticle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    // Check authentication before accessing
    public function __construct()
    {
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }
    }

    // ============ ARTICLE CRUD ============

    /**
     * Display all articles with tag count
     */
    public function index()
    {
        $articles = DB::select('
            SELECT a.*, 
                   COUNT(t.tag_id) as tag_count
            FROM mst_articles a
            LEFT JOIN mst_tag_articles t ON a.article_id = t.article_id
            GROUP BY a.article_id, a.title, a.slug, a.content, a.image, 
                     a.article_type, a.status, a.created_at, a.updated_at, a.created_by, a.updated_by
            ORDER BY a.article_id DESC
        ');

        return view('employee.articles.index', compact('articles'));
    }

    /**
     * Show form for creating new article
     */
    public function create()
    {
        return view('employee.articles.create');
    }

    /**
     * Store new article in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'article_id' => 'required|string|max:50|unique:mst_articles',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:mst_articles',
            'content' => 'required|string',
            'image' => 'nullable|string|max:500',
            'article_type' => 'required|string|max:50',
            'status' => 'required|in:Draft,Published,Archived',
        ]);

        $validated['created_by'] = session('employee_id');
        $validated['updated_by'] = session('employee_id');
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        MstArticle::create($validated);

        return redirect()->route('employee.articles.index')
                       ->with('success', 'Article created successfully!');
    }

    /**
     * Show form for editing article
     */
    public function edit($id)
    {
        $article = DB::select(
            'SELECT * FROM mst_articles WHERE article_id = ?',
            [$id]
        );

        if (empty($article)) {
            return redirect()->route('employee.articles.index')
                           ->with('error', 'Article not found!');
        }

        return view('employee.articles.edit', ['article' => $article[0]]);
    }

    /**
     * Update article in database
     */
    public function update(Request $request, $id)
    {
        $article = MstArticle::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:mst_articles,slug,' . $id . ',article_id',
            'content' => 'required|string',
            'image' => 'nullable|string|max:500',
            'article_type' => 'required|string|max:50',
            'status' => 'required|in:Draft,Published,Archived',
        ]);

        $validated['updated_by'] = session('employee_id');
        $validated['updated_at'] = now();

        $article->update($validated);

        return redirect()->route('employee.articles.index')
                       ->with('success', 'Article updated successfully!');
    }

    /**
     * Delete article and all its tags
     */
    public function destroy($id)
    {
        $article = MstArticle::findOrFail($id);

        // Delete all tags first
        MstTagArticle::where('article_id', $id)->delete();

        // Delete article
        $article->delete();

        return redirect()->route('employee.articles.index')
                       ->with('success', 'Article and all tags deleted successfully!');
    }

    // ============ TAG ARTICLE CRUD ============

    /**
     * Display all tags for an article
     */
    public function indexTag($articleId)
    {
        // Check if article exists
        $article = DB::select(
            'SELECT * FROM mst_articles WHERE article_id = ?',
            [$articleId]
        );

        if (empty($article)) {
            return redirect()->route('employee.articles.index')
                           ->with('error', 'Article not found!');
        }

        $tags = DB::select(
            'SELECT * FROM mst_tag_articles WHERE article_id = ? ORDER BY tag_id DESC',
            [$articleId]
        );

        // Get all available tag codes for the select2 dropdown
        $availableTags = DB::select('
            SELECT DISTINCT tag_code FROM mst_tag_articles
            WHERE tag_code IS NOT NULL
            ORDER BY tag_code
        ');

        return view('employee.articles.index-tag', [
            'article' => $article[0],
            'tags' => $tags,
            'availableTags' => $availableTags
        ]);
    }

    /**
     * Show form for creating multiple tags at once (Select2 multiple)
     */
    public function createTag($articleId)
    {
        // Check if article exists
        $article = DB::select(
            'SELECT * FROM mst_articles WHERE article_id = ?',
            [$articleId]
        );

        if (empty($article)) {
            return redirect()->route('employee.articles.index')
                           ->with('error', 'Article not found!');
        }

        // Get all available tag codes
        $availableTags = DB::select('
            SELECT DISTINCT tag_code FROM mst_tag_articles
            WHERE tag_code IS NOT NULL
            ORDER BY tag_code
        ');

        // Get already assigned tags
        $assignedTags = DB::select(
            'SELECT tag_code FROM mst_tag_articles WHERE article_id = ?',
            [$articleId]
        );

        $assignedTagCodes = array_map(function($tag) {
            return $tag->tag_code;
        }, $assignedTags);

        return view('employee.articles.create-tag', [
            'article' => $article[0],
            'availableTags' => $availableTags,
            'assignedTags' => $assignedTagCodes
        ]);
    }

    /**
     * Store multiple tags for article (handles Select2 multiple selection)
     */
    public function storeTag(Request $request, $articleId)
    {
        $validated = $request->validate([
            'tag_codes' => 'required|array|min:1',
            'tag_codes.*' => 'required|string|max:50',
        ]);

        // Get existing tags for this article
        $existingTags = DB::select(
            'SELECT tag_code FROM mst_tag_articles WHERE article_id = ?',
            [$articleId]
        );

        $existingTagCodes = array_map(function($tag) {
            return $tag->tag_code;
        }, $existingTags);

        // Determine which tags to add and which to remove
        $tagsToAdd = array_diff($validated['tag_codes'], $existingTagCodes);
        $tagsToRemove = array_diff($existingTagCodes, $validated['tag_codes']);

        // Remove old tags
        if (!empty($tagsToRemove)) {
            DB::delete(
                'DELETE FROM mst_tag_articles WHERE article_id = ? AND tag_code IN (' . 
                implode(',', array_fill(0, count($tagsToRemove), '?')) . ')',
                array_merge([$articleId], $tagsToRemove)
            );
        }

        // Add new tags
        foreach ($tagsToAdd as $tagCode) {
            $tagId = $articleId . '_' . $tagCode;

            MstTagArticle::create([
                'tag_id' => $tagId,
                'article_id' => $articleId,
                'tag_code' => $tagCode,
                'created_by' => session('employee_id'),
                'updated_by' => session('employee_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('employee.articles.tag', $articleId)
                       ->with('success', count($tagsToAdd) . ' tag(s) added successfully!');
    }

    /**
     * Delete a specific tag from article
     */
    public function destroyTag($articleId, $tagId)
    {
        $tag = MstTagArticle::findOrFail($tagId);
        $tag->delete();

        return redirect()->route('employee.articles.tag', $articleId)
                       ->with('success', 'Tag deleted successfully!');
    }
}
