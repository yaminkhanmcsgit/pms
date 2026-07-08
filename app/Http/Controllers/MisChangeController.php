<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MisChangeController extends Controller
{
    protected $table = 'mis_changes';

    public function index()
    {
        $query = DB::table($this->table)
            ->leftJoin('districts', 'mis_changes.district_id', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'mis_changes.tehsil_id', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'mis_changes.moza_id', '=', 'mozas.mozaId')
            ->leftJoin('operators', 'mis_changes.user_id', '=', 'operators.id');

        if (session('role_id') != 1) {
            $assignedTehsils = array_values(array_filter(array_map('trim', explode(',', (string) session('tehsil_id')))));
            $query->where('mis_changes.district_id', session('zila_id'))
                  ->whereIn('mis_changes.tehsil_id', $assignedTehsils);
        }

        $records = $query->select(
                'mis_changes.*',
                'districts.districtNameUrdu as district_name',
                'tehsils.tehsilNameUrdu as tehsil_name',
                'mozas.mozaNameUrdu as moza_name',
                'operators.full_name as user_name'
            )
            ->orderBy('mis_changes.change_id', 'desc')
            ->paginate(10);

        return view('mis_changes.index', compact('records'));
    }

    public function datatable(Request $request)
    {
        $columns = [
            'mis_changes.change_id',
            'mis_changes.change_time',
            'districts.districtNameUrdu',
            'tehsils.tehsilNameUrdu',
            'mozas.mozaNameUrdu',
            'mis_changes.family_id',
            'mis_changes.description',
            'operators.full_name'
        ];

        $query = DB::table($this->table)
            ->leftJoin('districts', 'mis_changes.district_id', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'mis_changes.tehsil_id', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'mis_changes.moza_id', '=', 'mozas.mozaId')
            ->leftJoin('operators', 'mis_changes.user_id', '=', 'operators.id');

        if (session('role_id') != 1) {
            $assignedTehsils = array_values(array_filter(array_map('trim', explode(',', (string) session('tehsil_id')))));
            $query->where('mis_changes.district_id', session('zila_id'))
                  ->whereIn('mis_changes.tehsil_id', $assignedTehsils);
        }

        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('mis_changes.change_id', 'like', '%' . $search . '%')
                  ->orWhere('districts.districtNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('tehsils.tehsilNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('mozas.mozaNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('mis_changes.family_id', 'like', '%' . $search . '%')
                  ->orWhere('mis_changes.description', 'like', '%' . $search . '%')
                  ->orWhere('operators.full_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDirection);
            }
        } else {
            $query->orderBy('mis_changes.change_id', 'desc');
        }

        $totalRecords = $query->count();
        $start = $request->start ?? 0;
        $length = $request->length ?? 25;
        $query->skip($start)->take($length);

        $records = $query->select(
            'mis_changes.*',
            'districts.districtNameUrdu as district_name',
            'tehsils.tehsilNameUrdu as tehsil_name',
            'mozas.mozaNameUrdu as moza_name',
            'operators.full_name as user_name'
        )->get();

        $data = [];
        foreach ($records as $index => $record) {
            $screenshotBefore = $record->screenshot_before_change ? url('assets/mis_changes/' . $record->screenshot_before_change) : '';
            $screenshotAfter = $record->screenshot_after_change ? url('assets/mis_changes/' . $record->screenshot_after_change) : '';

            $data[] = [
                'sno' => $start + $index + 1,
                'change_id' => $record->change_id,
                'change_time' => $record->change_time ? date('d-m-Y H:i:s', strtotime($record->change_time)) : '',
                'district_name' => $record->district_name ?? '',
                'tehsil_name' => $record->tehsil_name ?? '',
                'moza_name' => $record->moza_name ?? '',
                'family_id' => $record->family_id ?? '',
                'description' => $record->description ?? '',
                'user_name' => $record->user_name ?? '',
                'screenshot_before' => $screenshotBefore,
                'screenshot_after' => $screenshotAfter,
                'actions' => '<a href="' . route('mis_changes.edit', $record->change_id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> ترمیم</a> '
                    . '<button class="btn btn-sm btn-danger" onclick="deleteMisChange(' . $record->change_id . ')"><i class="fa fa-trash"></i> حذف</button>'
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    public function create()
    {
        $role_id = session('role_id');
        if ($role_id == 1) {
            $districts = DB::table('districts')->orderBy('districtId')->get();
            $tehsils = DB::table('tehsils')->orderBy('tehsilId')->get();
            $mozas = DB::table('mozas')->orderBy('mozaId')->get();
        } else {
            $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
            $assignedTehsils = explode(',', session('tehsil_id'));
            $tehsils = DB::table('tehsils')->whereIn('tehsilId', $assignedTehsils)->get();
            $mozas = DB::table('mozas')->whereIn('tehsilId', $assignedTehsils)->get();
        }

        return view('mis_changes.create', compact('districts', 'tehsils', 'mozas', 'role_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'district_id' => 'nullable|integer',
            'tehsil_id' => 'nullable|integer',
            'moza_id' => 'nullable|integer',
            'family_id' => 'nullable|integer',
            'description' => 'required|string|max:300',
            'screenshot_before_change' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'screenshot_after_change' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = [
            'district_id' => $request->district_id,
            'tehsil_id' => $request->tehsil_id,
            'moza_id' => $request->moza_id,
            'family_id' => $request->family_id,
            'description' => $request->description,
            'change_time' => now(),
            'user_id' => session('operator_id'),
        ];

        $uploadDir = base_path('assets/mis_changes');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($request->hasFile('screenshot_before_change')) {
            $file = $request->file('screenshot_before_change');
            $filename = time() . '_before_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['screenshot_before_change'] = $filename;
        }

        if ($request->hasFile('screenshot_after_change')) {
            $file = $request->file('screenshot_after_change');
            $filename = time() . '_after_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['screenshot_after_change'] = $filename;
        }

        DB::table($this->table)->insert($data);

        return redirect()->route('mis_changes.index')
                         ->with('success', 'MIS change record added successfully.');
    }

    public function edit($id)
    {
        $record = DB::table($this->table)->where('change_id', $id)->first();

        if (!$record) {
            return redirect()->route('mis_changes.index')
                             ->with('error', 'Record not found.');
        }

        $role_id = session('role_id');
        if ($role_id == 1) {
            $districts = DB::table('districts')->orderBy('districtId')->get();
            $tehsils = DB::table('tehsils')->orderBy('tehsilId')->get();
            $mozas = DB::table('mozas')->orderBy('mozaId')->get();
        } else {
            $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
            $assignedTehsils = explode(',', session('tehsil_id'));
            $tehsils = DB::table('tehsils')->whereIn('tehsilId', $assignedTehsils)->get();
            $mozas = DB::table('mozas')->whereIn('tehsilId', $assignedTehsils)->get();
        }

        return view('mis_changes.edit', compact('record', 'districts', 'tehsils', 'mozas', 'role_id'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'district_id' => 'nullable|integer',
            'tehsil_id' => 'nullable|integer',
            'moza_id' => 'nullable|integer',
            'family_id' => 'nullable|integer',
            'description' => 'required|string|max:300',
            'screenshot_before_change' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'screenshot_after_change' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $record = DB::table($this->table)->where('change_id', $id)->first();

        if (!$record) {
            return redirect()->route('mis_changes.index')
                             ->with('error', 'Record not found.');
        }

        $data = [
            'district_id' => $request->district_id,
            'tehsil_id' => $request->tehsil_id,
            'moza_id' => $request->moza_id,
            'family_id' => $request->family_id,
            'description' => $request->description,
            'user_id' => session('operator_id'),
        ];

        $uploadDir = base_path('assets/mis_changes');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($request->hasFile('screenshot_before_change')) {
            if ($record->screenshot_before_change) {
                $oldPath = $uploadDir . '/' . $record->screenshot_before_change;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $file = $request->file('screenshot_before_change');
            $filename = time() . '_before_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['screenshot_before_change'] = $filename;
        }

        if ($request->hasFile('screenshot_after_change')) {
            if ($record->screenshot_after_change) {
                $oldPath = $uploadDir . '/' . $record->screenshot_after_change;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $file = $request->file('screenshot_after_change');
            $filename = time() . '_after_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['screenshot_after_change'] = $filename;
        }

        DB::table($this->table)->where('change_id', $id)->update($data);

        return redirect()->route('mis_changes.index')
                         ->with('success', 'MIS change record updated successfully.');
    }

    public function destroy($id)
    {
        $record = DB::table($this->table)->where('change_id', $id)->first();

        if ($record) {
            $uploadDir = base_path('assets/mis_changes');
            if ($record->screenshot_before_change) {
                $oldPath = $uploadDir . '/' . $record->screenshot_before_change;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            if ($record->screenshot_after_change) {
                $oldPath = $uploadDir . '/' . $record->screenshot_after_change;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            DB::table($this->table)->where('change_id', $id)->delete();
        }

        return redirect()->route('mis_changes.index')
                         ->with('success', 'MIS change record deleted successfully.');
    }
}
