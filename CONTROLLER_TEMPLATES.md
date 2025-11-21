# TEMPLATE LENGKAP UNTUK SISA CONTROLLER

## Gunakan Template Ini untuk Controller Berikut:
- ArticleController
- EventController  
- SettingController
- TagArticleController
- TagEventController
- GradeController

---

# TEMPLATE 1: ARTICLE CONTROLLER

```php
<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!session('user_type') || session('user_type') !== 'employee') {
                return redirect()->route('employee.login');
            }
            return $next($request);
        });
    }

    public function index()
    {
        try {
            $articles = MstArticle::orderBy('created_at', 'DESC')->get();
            return view('articles.index', compact('articles'));
        } catch (\Exception $e) {
            Log::error('Error fetching articles: ' . $e->getMessage());
            return view('articles.index', ['articles' => collect([])]);
        }
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:mst_articles,slug',
                'content' => 'required|string',
                'image' => 'nullable|string|max:255',
                'article_type' => 'required|string|max:50',
                'status' => 'required|in:Active,Inactive'
            ]);

            MstArticle::create([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'content' => $validated['content'],
                'image' => $validated['image'] ?? null,
                'article_type' => $validated['article_type'],
                'status' => $validated['status'],
                'created_by' => session('employee_id') ?? 'SYSTEM',
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            Log::info('Article created: ' . $validated['title']);
            return redirect()->route('employee.articles.index')
                ->with('success', 'Article created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error creating article: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $article = MstArticle::findOrFail($id);
            return view('articles.edit', compact('article'));
        } catch (\Exception $e) {
            Log::warning('Article not found: ' . $id);
            return redirect()->route('employee.articles.index')
                ->with('error', 'Article not found!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:mst_articles,slug,' . $id . ',article_id',
                'content' => 'required|string',
                'image' => 'nullable|string|max:255',
                'article_type' => 'required|string|max:50',
                'status' => 'required|in:Active,Inactive'
            ]);

            $article = MstArticle::findOrFail($id);
            $article->update([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'content' => $validated['content'],
                'image' => $validated['image'] ?? $article->image,
                'article_type' => $validated['article_type'],
                'status' => $validated['status'],
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            Log::info('Article updated: ' . $id);
            return redirect()->route('employee.articles.index')
                ->with('success', 'Article updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating article: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $article = MstArticle::findOrFail($id);
            $article->delete();

            Log::info('Article deleted: ' . $id);
            return redirect()->route('employee.articles.index')
                ->with('success', 'Article deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting article: ' . $e->getMessage());
            return redirect()->route('employee.articles.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
```

---

# TEMPLATE 2: EVENT CONTROLLER

```php
<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!session('user_type') || session('user_type') !== 'employee') {
                return redirect()->route('employee.login');
            }
            return $next($request);
        });
    }

    public function index()
    {
        try {
            $events = MstEvent::orderBy('created_at', 'DESC')->get();
            return view('events.index', compact('events'));
        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());
            return view('events.index', ['events' => collect([])]);
        }
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'status' => 'required|in:Active,Inactive'
            ]);

            MstEvent::create([
                'event_name' => $validated['event_name'],
                'description' => $validated['description'] ?? null,
                'location' => $validated['location'] ?? null,
                'status' => $validated['status'],
                'created_by' => session('employee_id') ?? 'SYSTEM',
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            Log::info('Event created: ' . $validated['event_name']);
            return redirect()->route('employee.events.index')
                ->with('success', 'Event created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $event = MstEvent::findOrFail($id);
            return view('events.edit', compact('event'));
        } catch (\Exception $e) {
            return redirect()->route('employee.events.index')
                ->with('error', 'Event not found!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'event_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'status' => 'required|in:Active,Inactive'
            ]);

            $event = MstEvent::findOrFail($id);
            $event->update([
                'event_name' => $validated['event_name'],
                'description' => $validated['description'] ?? $event->description,
                'location' => $validated['location'] ?? $event->location,
                'status' => $validated['status'],
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            return redirect()->route('employee.events.index')
                ->with('success', 'Event updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            MstEvent::findOrFail($id)->delete();
            return redirect()->route('employee.events.index')
                ->with('success', 'Event deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('employee.events.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
```

---

# TEMPLATE 3: SETTING CONTROLLER (HeaderSetting)

```php
<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MstHeaderSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!session('user_type') || session('user_type') !== 'employee') {
                return redirect()->route('employee.login');
            }
            return $next($request);
        });
    }

    public function index()
    {
        try {
            $settings = MstHeaderSetting::orderBy('created_at', 'DESC')->get();
            return view('settings.index', compact('settings'));
        } catch (\Exception $e) {
            Log::error('Error fetching settings: ' . $e->getMessage());
            return view('settings.index', ['settings' => collect([])]);
        }
    }

    public function create()
    {
        return view('settings.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255'
            ]);

            MstHeaderSetting::create([
                'title' => $validated['title'],
                'created_by' => session('employee_id') ?? 'SYSTEM',
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            return redirect()->route('employee.settings.index')
                ->with('success', 'Setting created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $setting = MstHeaderSetting::findOrFail($id);
            return view('settings.edit', compact('setting'));
        } catch (\Exception $e) {
            return redirect()->route('employee.settings.index')
                ->with('error', 'Setting not found!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255'
            ]);

            $setting = MstHeaderSetting::findOrFail($id);
            $setting->update([
                'title' => $validated['title'],
                'updated_by' => session('employee_id') ?? 'SYSTEM'
            ]);

            return redirect()->route('employee.settings.index')
                ->with('success', 'Setting updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            MstHeaderSetting::findOrFail($id)->delete();
            return redirect()->route('employee.settings.index')
                ->with('success', 'Setting deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('employee.settings.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
```

---

**Note**: Apply yang sama untuk TagArticleController, TagEventController, dan GradeController dengan menyesuaikan model dan field yang relevan.
