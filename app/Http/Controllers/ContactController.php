<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    protected $table = 'contactus';

    // List all records with pagination
    public function index()
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        $records = DB::table($this->table)->orderBy('id', 'desc')->paginate(10);
        return view('contactus.index', compact('records'));
    }

    // Show form to create a new record
    public function create()
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        return view('contactus.create');
    }

    // Store a new record
    public function store(Request $request)
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $validated['created_at'] = now();

        DB::table($this->table)->insert($validated);

        return redirect()->route('contactus.index')
                         ->with('success', 'Contact message added successfully.');
    }

    // Show form to edit a record
    public function edit($id)
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        $record = DB::table($this->table)->where('id', $id)->first();

        if (!$record) {
            return redirect()->route('contactus.index')->with('error', 'Record not found.');
        }

        return view('contactus.edit', compact('record'));
    }

    // Update a record
    public function update(Request $request, $id)
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        DB::table($this->table)->where('id', $id)->update($validated);

        return redirect()->route('contactus.index')
                         ->with('success', 'Contact message updated successfully.');
    }

    // Delete a record
    public function destroy($id)
    {
        if (session('role_id') != 1) {
            return redirect()->route('dashboard');
        }

        DB::table($this->table)->where('id', $id)->delete();
        return redirect()->route('contactus.index')
                         ->with('success', 'Contact message deleted successfully.');
    }

    // DataTables server-side processing
    public function datatable(Request $request)
    {
        if (session('role_id') != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $columns = [
            'contactus.id',
            'contactus.created_at',
            'contactus.name',
            'contactus.email',
            'contactus.subject',
            'contactus.message'
        ];

        $query = DB::table($this->table);

        // Search functionality
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('contactus.id', 'like', '%' . $search . '%')
                  ->orWhere('contactus.name', 'like', '%' . $search . '%')
                  ->orWhere('contactus.email', 'like', '%' . $search . '%')
                  ->orWhere('contactus.subject', 'like', '%' . $search . '%')
                  ->orWhere('contactus.message', 'like', '%' . $search . '%');
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
            $query->orderBy('contactus.id', 'desc');
        }

        // Pagination
        $totalRecords = $query->count();
        $start = $request->start ?? 0;
        $length = $request->length ?? 25;
        $query->skip($start)->take($length);

        $records = $query->select(
            'contactus.*'
        )->get();

        $data = [];
        foreach ($records as $record) {
            $data[] = [
                'id' => $record->id,
                'created_at' => $record->created_at ? date('d-m-Y H:i', strtotime($record->created_at)) : '',
                'name' => $record->name,
                'email' => $record->email,
                'subject' => $record->subject,
                'message' => $record->message,
                'actions' => '<a href="' . route('contactus.edit', $record->id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a> <a href="' . route('contactus.destroy', $record->id) . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')"><i class="fa fa-trash"></i> Delete</a>'
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