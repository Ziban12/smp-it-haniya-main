# PANDUAN IMPLEMENTASI LENGKAP MASTER CRUD

## Status Implementasi

### ✅ SELESAI (4/12)
1. Employee Controller - DONE
2. Teacher Controller - DONE
3. Student Controller - DONE
4. Class Controller - DONE

### ⏳ DALAM PROSES
5. Subject Controller - Partial

### ⚠️ TEMPLATE TERSEDIA
- Gunakan template dari file: `SubjectControllerTemplate.php` untuk controller lain

## Model-Model yang Sudah Benar

Semua model sudah memiliki:
- `public $timestamps = true;`
- Fillable array lengkap dengan timestamp fields
- Primary key configuration yang tepat

### Model Relationships Needed:

```
MstClass -> MstTeacher (homeroom_teacher_id)
TxnGrade -> MstStudent, MstSubject, MstAcademicYear
MstDetailSetting -> MstHeaderSetting (header_id)
MstTagArticle -> MstArticle (article_id)
MstTagEvent -> MstEvent (event_id)
```

## Langkah-Langkah Implementasi Cepat

### Untuk setiap Controller yang belum selesai:

1. **Update Model** - Pastikan fillable lengkap dengan timestamps
2. **Copy Template Controller** dari SubjectControllerTemplate.php
3. **Sesuaikan nama class dan validasi rules**
4. **Update routes di web.php**
5. **Buat views (index, create, edit)**

## Template untuk Controller Baru

Semua controller harus memiliki struktur:

```php
<?php
namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;
use App\Models\[ModelName];
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class [ControllerName] extends Controller
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

    public function index() // List all
    public function create() // Show create form
    public function store(Request $request) // Save
    public function edit($id) // Show edit form  
    public function update(Request $request, $id) // Update
    public function destroy($id) // Delete
}
```

## View Files Template Required

### For each entity, create blade files:

1. **index.blade.php** - List with DataTable
   - Show delete button with confirmation
   - Show edit button
   - Show create button
   - Use SweetAlert2 for delete confirmation

2. **create.blade.php** - Form to create
   - All fillable fields
   - CSRF token
   - Submit button

3. **edit.blade.php** - Form to edit
   - Prefilled values
   - CSRF token + hidden _method=PUT
   - Submit button

### SweetAlert2 Implementation for Notifications:

```html
@if ($message = Session::get('success'))
    <script>
        Swal.fire('Success', '{{ $message }}', 'success');
    </script>
@endif

@if ($message = Session::get('error'))
    <script>
        Swal.fire('Error', '{{ $message }}', 'error');
    </script>
@endif
```

## Priority Order untuk Implementasi Sisa

1. **Subject** - Simple CRUD (needed soon)
2. **Academic Year** - Simple CRUD
3. **Article** - Include slug generation
4. **Event** - Similar to Article
5. **Setting** (HeaderSetting + DetailSetting) - Linked tables
6. **Tag Article** - Linked to Article
7. **Tag Event** - Linked to Event
8. **Grade** - Transaction table, complex relationships

## Common Validations Used

```php
'field_id' => 'required|string|max:50|unique:table_name,field_id',
'name' => 'required|string|max:100',
'email' => 'required|email|unique:users,email',
'date_field' => 'required|date',
'status' => 'required|in:Active,Inactive',
'decimal_field' => 'nullable|numeric|min:0|max:999.99',
```

## Error Handling Pattern

```php
try {
    // Validation
    $validated = $request->validate([...]);
    
    // Operation
    Model::create($validated);
    
    // Success
    return redirect()->route('route.name')
        ->with('success', 'Message');
        
} catch (\Illuminate\Validation\ValidationException $e) {
    return back()->withInput()->withErrors($e->errors());
} catch (\Exception $e) {
    Log::error('Error message: ' . $e->getMessage());
    return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
}
```

## Session/User Info Available

```php
session('employee_id')     // Current employee ID
session('user_type')       // 'employee', 'teacher', 'student'
auth()->user()             // Current authenticated user
```

## Next Steps

1. ✅ Run `php artisan migrate` to ensure all tables exist
2. ✅ Create routes in `routes/web.php`
3. ✅ Create blade templates for each entity
4. ⏳ Complete remaining controllers following template
5. ⏳ Test each CRUD operation
6. ⏳ Add SweetAlert2 notifications

## Database Check

Verify all tables exist:
```sql
SELECT * FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'SMP_IT_HANIYA';
```

Expected tables:
- mst_employees ✓
- mst_teachers ✓
- mst_students ✓
- mst_classes ✓
- mst_subjects ✓
- mst_academic_year ✓
- mst_articles ✓
- mst_events ✓
- mst_header_settings ✓
- mst_detail_settings ✓
- mst_tag_articles ✓
- mst_tag_events ✓
- txn_grades ✓
- mst_student_classes ✓
