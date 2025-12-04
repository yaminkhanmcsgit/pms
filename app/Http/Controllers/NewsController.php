<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    protected $table = 'latest_news';

    // List all records with pagination
    public function index()
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        $records = DB::table($this->table)->orderBy('id', 'desc')->paginate(10);
        return view('news.index', compact('records'));
    }

    // Show form to create a new record
    public function create()
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        return view('news.create');
    }

    // Store a new record
    public function store(Request $request)
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'detail' => 'required|string',
            'created_at' => 'required|date',
            'colorder' => 'required|integer|min:1|max:12',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Generate slug
        $slug = Str::slug($validated['title']) . '-' . rand(10, 100);

        $uploadDir = file_exists(base_path('../assets')) ? base_path('../assets/img/news') : base_path('assets/news');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Handle file uploads
        $imagePaths = [];
        for ($i = 1; $i <= 4; $i++) {
            $fieldName = 'image' . $i;
            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                $filename = $i . round(microtime(true)) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $imagePaths[$fieldName] = $uploadDir . '/' . $filename;
            } else {
                $imagePaths[$fieldName] = '';
            }
        }

        DB::table($this->table)->insert([
            'title' => $validated['title'],
            'detail' => $validated['detail'],
            'created_at' => $validated['created_at'],
            'colorder' => $validated['colorder'],
            'image1' => $imagePaths['image1'],
            'image2' => $imagePaths['image2'],
            'image3' => $imagePaths['image3'],
            'image4' => $imagePaths['image4'],
            'status' => 1,
        ]);

        return redirect()->route('news.index')
                         ->with('success', 'News added successfully.');
    }

    // Show a single record
    public function show($id)
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        $record = DB::table($this->table)->where('id', $id)->first();

        if (!$record) {
            return redirect()->route('news.index')->with('error', 'News not found.');
        }

        return view('news.show', compact('record'));
    }

    // Show form to edit a record
    public function edit($id)
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        $record = DB::table($this->table)->where('id', $id)->first();

        if (!$record) {
            return redirect()->route('news.index')->with('error', 'News not found.');
        }

        return view('news.edit', compact('record'));
    }

    // Update a record
    public function update(Request $request, $id)
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'detail' => 'required|string',
            'created_at' => 'required|date',
            'colorder' => 'required|integer|min:1|max:12',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $record = DB::table($this->table)->where('id', $id)->first();
        if (!$record) {
            return redirect()->route('news.index')->with('error', 'News not found.');
        }

        // Generate slug
        $slug = Str::slug($validated['title']) . '-' . rand(10, 100);

        $uploadDir = file_exists(base_path('../assets')) ? base_path('../assets/img/news') : base_path('assets/news');
        $relativeDir = file_exists(base_path('../assets')) ? 'img/news' : 'news';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Handle file uploads
        $imagePaths = [];
        for ($i = 1; $i <= 4; $i++) {
            $fieldName = 'image' . $i;
            if ($request->hasFile($fieldName)) {
                // Delete old file if exists
                if ($record->{'image' . $i} && file_exists($record->{'image' . $i})) {
                    unlink($record->{'image' . $i});
                }

                $file = $request->file($fieldName);
                $filename = $i . round(microtime(true)) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $imagePaths[$fieldName] = $uploadDir . '/' . $filename;
            } else {
                $imagePaths[$fieldName] = $record->{'image' . $i};
            }
        }

        DB::table($this->table)->where('id', $id)->update([
            'title' => $validated['title'],
            'detail' => $validated['detail'],
            'created_at' => $validated['created_at'],
            'colorder' => $validated['colorder'],
            'image1' => $imagePaths['image1'],
            'image2' => $imagePaths['image2'],
            'image3' => $imagePaths['image3'],
            'image4' => $imagePaths['image4'],
        ]);

        return redirect()->route('news.index')
                         ->with('success', 'News updated successfully.');
    }

    // Delete a record
    public function destroy($id)
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        $record = DB::table($this->table)->where('id', $id)->first();

        if (!$record) {
            return redirect()->route('news.index')->with('error', 'News not found.');
        }

        // Delete associated images
        for ($i = 1; $i <= 4; $i++) {
            if ($record->{'image' . $i} && file_exists($record->{'image' . $i})) {
                unlink($record->{'image' . $i});
            }
        }

        DB::table($this->table)->where('id', $id)->delete();
        return redirect()->route('news.index')
                         ->with('success', 'News deleted successfully.');
    }

    // DataTables server-side processing
    public function datatable(Request $request)
    {
        if (session('role_id') != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $columns = [
            'latest_news.id',
            'latest_news.created_at',
            'latest_news.title'
        ];

        $query = DB::table($this->table);

        // Search functionality
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('latest_news.id', 'like', '%' . $search . '%')
                  ->orWhere('latest_news.title', 'like', '%' . $search . '%')
                  ->orWhere('latest_news.detail', 'like', '%' . $search . '%');
            });
        }

        // Ordering
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDirection);
            }
        } else {
            $query->orderBy('latest_news.id', 'desc');
        }

        // Pagination
        $totalRecords = $query->count();
        $start = $request->start ?? 0;
        $length = $request->length ?? 25;
        $query->skip($start)->take($length);

        $records = $query->select(
            'latest_news.*'
        )->get();

        $data = [];
        $i = $start + 1;
        foreach ($records as $record) {
            $data[] = [
                'sn' => $i++,
                'created_at' => $record->created_at ? date('d-m-Y', strtotime($record->created_at)) : '',
                'title' => htmlspecialchars($record->title),
                'actions' => '<a href="' . route('news.show', $record->id) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a> <a href="' . route('news.edit', $record->id) . '" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></a> <form method="POST" action="' . route('news.destroy', $record->id) . '" style="display:inline;" id="delete-form-' . $record->id . '"> <input type="hidden" name="_method" value="DELETE"> <input type="hidden" name="_token" value="' . csrf_token() . '"> <button type="button" class="btn btn-sm btn-danger" onclick="deleteNews(' . $record->id . ')" title="Delete"><i class="fa fa-trash"></i></button> </form>'
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }
}