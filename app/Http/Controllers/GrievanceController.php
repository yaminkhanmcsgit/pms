<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrievanceController extends Controller
{
    public function index()
    {
        $query = DB::table('grievances')
            ->leftJoin('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
            ->leftJoin('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
            ->leftJoin('districts', 'grievances.district', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'grievances.tehsil', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'grievances.village_name', '=', 'mozas.mozaId');

        if (session('role_id') == 2) {
            $query->where('grievances.district', session('zila_id'))
                  ->where('grievances.tehsil', session('tehsil_id'));
        }

        $grievances = $query->select(
                'grievances.*',
                'grievance_types.name as grievance_type_name',
                'grievance_statuses.name as status_name',
                'grievance_statuses.color_class as status_color',
                'districts.districtNameUrdu as district_name',
                'tehsils.tehsilNameUrdu as tehsil_name',
                'mozas.mozaNameUrdu as moza_name'
            )
            ->latest()
            ->paginate(10);

        return view('grievances.index', compact('grievances'));
    }

    public function create()
    {
        $role_id = session('role_id');
        if ($role_id == 1) {
            $districts = DB::table('districts')->orderBy('districtId')->get();
            $tehsils   = DB::table('tehsils')->orderBy('tehsilId')->get();
            $mozas     = DB::table('mozas')->orderBy('mozaId')->get();
        } else {
            $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
            $tehsils   = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->get();
            $mozas     = DB::table('mozas')->where('tehsilId', session('tehsil_id'))->get();
        }

        $types = DB::table('grievance_types')->get();
        $statuses = DB::table('grievance_statuses')->get();

        return view('grievances.create', compact('types', 'statuses', 'districts', 'tehsils', 'mozas', 'role_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'district_id' => 'required|integer',
            'tehsil_id' => 'required|integer',
            'moza_id' => 'required|integer',
            'applicant_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'cnic' => 'required|string|max:20',
            'application_date' => 'required|date',
            'grievance_type_id' => 'required|integer',
            'status_id' => 'required|integer',
        ]);

        DB::table('grievances')->insert([
            'district' => $request->district_id,
            'tehsil' => $request->tehsil_id,
            'village_name' => $request->moza_id,
            'applicant_name' => $request->applicant_name,
            'father_name' => $request->father_name,
            'cnic' => $request->cnic,
            'address' => $request->address,
            'nature_of_grievance' => $request->nature_of_grievance,
            'grievance_type_id' => $request->grievance_type_id,
            'grievance_description' => $request->grievance_description,
            'status_id' => $request->status_id,
            'application_date' => $request->application_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('grievances.index')->with('success', 'Grievance submitted successfully.');
    }

    public function show($id)
    {
        $grievance = DB::table('grievances')
            ->leftJoin('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
            ->leftJoin('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
            ->leftJoin('districts', 'grievances.district', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'grievances.tehsil', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'grievances.village_name', '=', 'mozas.mozaId')
            ->select(
                'grievances.*',
                'grievance_types.name as grievance_type_name',
                'grievance_statuses.name as status_name',
                'grievance_statuses.color_class as status_color',
                'districts.districtNameUrdu as district_name',
                'tehsils.tehsilNameUrdu as tehsil_name',
                'mozas.mozaNameUrdu as moza_name'
            )
            ->where('grievances.id', $id)
            ->first();

        if (!$grievance) {
            return response()->json(['success' => false, 'message' => 'Grievance not found.']);
        }

        return response()->json(['success' => true, 'grievance' => $grievance]);
    }

    public function edit($id)
    {
        $grievance = DB::table('grievances')
            ->leftJoin('districts', 'grievances.district', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'grievances.tehsil', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'grievances.village_name', '=', 'mozas.mozaId')
            ->select(
                'grievances.*',
                'districts.districtNameUrdu as district_name',
                'tehsils.tehsilNameUrdu as tehsil_name',
                'mozas.mozaNameUrdu as moza_name'
            )
            ->where('grievances.id', $id)
            ->first();

        if (!$grievance) {
            return redirect()->route('grievances.index')->with('error', 'Grievance not found.');
        }

        $role_id = session('role_id');
        if ($role_id == 1) {
            $districts = DB::table('districts')->orderBy('districtId')->get();
            $tehsils   = DB::table('tehsils')->orderBy('tehsilId')->get();
            $mozas     = DB::table('mozas')->orderBy('mozaId')->get();
        } else {
            $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
            $tehsils   = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->get();
            $mozas     = DB::table('mozas')->where('tehsilId', session('tehsil_id'))->get();
        }

        $types = DB::table('grievance_types')->get();
        $statuses = DB::table('grievance_statuses')->get();

        return view('grievances.edit', compact('grievance', 'types', 'statuses', 'districts', 'tehsils', 'mozas', 'role_id'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'district_id' => 'required|integer',
            'tehsil_id' => 'required|integer',
            'moza_id' => 'required|integer',
            'applicant_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'cnic' => 'required|string|max:20',
            'application_date' => 'required|date',
            'grievance_type_id' => 'required|integer',
            'status_id' => 'required|integer',
        ]);

        DB::table('grievances')->where('id', $id)->update([
            'district' => $request->district_id,
            'tehsil' => $request->tehsil_id,
            'village_name' => $request->moza_id,
            'applicant_name' => $request->applicant_name,
            'father_name' => $request->father_name,
            'cnic' => $request->cnic,
            'address' => $request->address,
            'nature_of_grievance' => $request->nature_of_grievance,
            'grievance_type_id' => $request->grievance_type_id,
            'grievance_description' => $request->grievance_description,
            'status_id' => $request->status_id,
            'application_date' => $request->application_date,
            'updated_at' => now(),
        ]);

        return redirect()->route('grievances.index')->with('success', 'Grievance updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'tehsil_id' => 'nullable|integer',
            'moza_id' => 'nullable|integer',
            'applicant_name' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'cnic' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'moza_name' => 'nullable|string',
            'nature_of_grievance' => 'nullable|string',
            'grievance_type' => 'nullable|string',
            'grievance_description' => 'nullable|string',
            'application_date' => 'nullable|date',
            'forwarded_by' => 'nullable|string|max:100',
            'received_by_tehsildar_date' => 'nullable|date',
            'preliminary_remarks' => 'nullable|string',
            'action_proposed' => 'nullable|string',
            'field_verification_date' => 'nullable|date',
            'decision' => 'nullable|string',
            'disposal_date' => 'nullable|date',
            'tehsildar_signature' => 'nullable|string|max:100',
            'assistant_remarks' => 'nullable|string'
        ]);

        DB::table('grievances')->where('id', $id)->update([
            'district' => $request->district_id ?: $request->district,
            'tehsil' => $request->tehsil_id ?: $request->tehsil,
            'village_name' => $request->moza_id ?: $request->village_name,
            'applicant_name' => $request->applicant_name,
            'father_name' => $request->father_name,
            'cnic' => $request->cnic,
            'address' => $request->address,
            'nature_of_grievance' => $request->nature_of_grievance,
            'grievance_type_id' => $request->grievance_type ? DB::table('grievance_types')->where('name', 'like', '%' . explode(', ', $request->grievance_type)[0] . '%')->value('id') : null,
            'grievance_description' => $request->grievance_description,
            'status_id' => $request->status_id,
            'application_date' => $request->application_date,
            'forwarded_by' => $request->forwarded_by,
            'received_by_tehsildar_date' => $request->received_by_tehsildar_date,
            'preliminary_remarks' => $request->preliminary_remarks,
            'action_proposed' => $request->action_proposed,
            'field_verification_date' => $request->field_verification_date,
            'decision' => $request->decision,
            'disposal_date' => $request->disposal_date,
            'tehsildar_signature' => $request->tehsildar_signature,
            'assistant_remarks' => $request->assistant_remarks,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    public function destroy($id)
    {
        DB::table('grievances')->where('id', $id)->delete();
        return redirect()->route('grievances.index')->with('success', 'Grievance deleted successfully.');
    }

    public function getTypes()
    {
        $types = DB::table('grievance_types')->get();
        return response()->json(['types' => $types]);
    }

    public function getStatuses()
    {
        $statuses = DB::table('grievance_statuses')->get();
        return response()->json(['statuses' => $statuses]);
    }

    public function updateField(Request $request, $id)
    {
        $request->validate([
            'field_name' => 'required|string',
            'field_value' => 'nullable|string',
        ]);

        $allowedFields = [
            'forwarded_by',
            'received_by_tehsildar_date',
            'field_verification_date',
            'disposal_date',
            'preliminary_remarks',
            'action_proposed',
            'decision',
            'assistant_remarks',
            'status_id'
        ];

        if (!in_array($request->field_name, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Invalid field name.']);
        }

        DB::table('grievances')
            ->where('id', $id)
            ->update([$request->field_name => $request->field_value]);

        return response()->json(['success' => true, 'message' => 'Field updated successfully.']);
    }

    public function uploadSignature(Request $request, $id)
    {
        $request->validate([
            'signature_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $filename = time() . '_' . $id . '.' . $file->getClientOriginalExtension();

            // Create signatures directory if it doesn't exist
            $path = public_path('storage/signatures');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $file->move($path, $filename);

            DB::table('grievances')
                ->where('id', $id)
                ->update(['tehsildar_signature' => $filename]);

            return response()->json(['success' => true, 'message' => 'Signature uploaded successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded.']);
    }

    // DataTables server-side processing
    public function datatable(Request $request)
    {
        $columns = [
            'grievances.id',
            'grievances.applicant_name',
            'grievances.father_name',
            'grievances.cnic',
            'districts.districtNameUrdu',
            'tehsils.tehsilNameUrdu',
            'mozas.mozaNameUrdu',
            'grievance_types.name',
            'grievance_statuses.name',
            'grievances.application_date'
        ];

        $query = DB::table('grievances')
            ->leftJoin('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
            ->leftJoin('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
            ->leftJoin('districts', 'grievances.district', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'grievances.tehsil', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'grievances.village_name', '=', 'mozas.mozaId');

        // Role-based filtering
        if (session('role_id') == 2) {
            $query->where('districts.districtId', session('zila_id'))
                  ->where('tehsils.tehsilId', session('tehsil_id'));
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('grievances.id', 'like', '%' . $search . '%')
                  ->orWhere('grievances.applicant_name', 'like', '%' . $search . '%')
                  ->orWhere('grievances.father_name', 'like', '%' . $search . '%')
                  ->orWhere('grievances.cnic', 'like', '%' . $search . '%')
                  ->orWhere('districts.districtNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('tehsils.tehsilNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('mozas.mozaNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('grievance_types.name', 'like', '%' . $search . '%')
                  ->orWhere('grievance_statuses.name', 'like', '%' . $search . '%');
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
            $query->orderBy('grievances.id', 'desc');
        }

        // Pagination
        $totalRecords = $query->count();
        $start = $request->start ?? 0;
        $length = $request->length ?? 25;
        $query->skip($start)->take($length);

        $records = $query->select(
            'grievances.*',
            'grievance_types.name as grievance_type_name',
            'grievance_statuses.name as status_name',
            'grievance_statuses.color_class as status_color',
            'districts.districtNameUrdu as district_name',
            'tehsils.tehsilNameUrdu as tehsil_name',
            'mozas.mozaNameUrdu as moza_name'
        )->get();

        $data = [];
        foreach ($records as $record) {
            $actions = '<div class="dropdown">
                <button class="btn btn-sm btn-default dropdown-toggle actions-dropdown-btn" type="button" data-toggle="dropdown" data-grievance-id="' . $record->id . '">
                    <i class="fa fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu actions-dropdown-menu">
                    <li><a href="#" onclick="viewGrievance(' . $record->id . ')"><i class="fa fa-eye"></i> View</a></li>
                    <li><a href="' . route('grievances.edit', $record->id) . '"><i class="fa fa-edit"></i> Edit</a></li>
                    <li><a href="#" onclick="updateField(' . $record->id . ', \'forwarded_by\', \'' . addslashes($record->forwarded_by) . '\')"><i class="fa fa-user"></i> Forwarded By</a></li>
                    <li><a href="#" onclick="updateField(' . $record->id . ', \'received_by_tehsildar_date\', \'' . $record->received_by_tehsildar_date . '\')"><i class="fa fa-calendar"></i> Receipt Date</a></li>
                    <li><a href="#" onclick="updateField(' . $record->id . ', \'field_verification_date\', \'' . $record->field_verification_date . '\')"><i class="fa fa-search"></i> Field Verification</a></li>
                    <li><a href="#" onclick="updateField(' . $record->id . ', \'disposal_date\', \'' . $record->disposal_date . '\')"><i class="fa fa-check-circle"></i> Disposal Date</a></li>
                    <li><a href="#" onclick="updateField(' . $record->id . ', \'preliminary_remarks\', \'' . addslashes($record->preliminary_remarks) . '\')"><i class="fa fa-comment"></i> Preliminary Remarks</a></li>
                    <li><a href="#" onclick="updateField(' . $record->id . ', \'action_proposed\', \'' . addslashes($record->action_proposed) . '\')"><i class="fa fa-lightbulb-o"></i> Action Proposed</a></li>
                    <li><a href="#" onclick="updateField(' . $record->id . ', \'decision\', \'' . addslashes($record->decision) . '\')"><i class="fa fa-gavel"></i> Decision</a></li>
                    <li><a href="#" onclick="updateField(' . $record->id . ', \'assistant_remarks\', \'' . addslashes($record->assistant_remarks) . '\')"><i class="fa fa-user-md"></i> Assistant Remarks</a></li>
                    <li><a href="#" onclick="updateField(' . $record->id . ', \'status_id\', \'' . $record->status_id . '\')"><i class="fa fa-flag"></i> Update Status</a></li>
                    <li><a href="#" onclick="updateSignature(' . $record->id . ', \'' . $record->tehsildar_signature . '\')"><i class="fa fa-signature"></i> Tehsildar Signature</a></li>
                    <li class="divider"></li>
                    <li>
                        <form action="' . route('grievances.destroy', $record->id) . '" method="POST" style="display:inline;">
                             
                            
                            <button type="submit" class="btn btn-link" style="color: #d9534f; padding: 0; border: none; background: none;" onclick="return confirmDelete(event)">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                    </li>
                </ul>
            </div>';

            $data[] = [
                'id' => $record->id,
                'applicant_name' => $record->applicant_name,
                'father_name' => $record->father_name,
                'cnic' => $record->cnic,
                'district_name' => $record->district_name,
                'tehsil_name' => $record->tehsil_name,
                'moza_name' => $record->moza_name,
                'grievance_type_name' => $record->grievance_type_name,
                'status_name' => '<span class="label label-' . $record->status_color . '">' . $record->status_name . '</span>',
                'application_date' => $record->application_date ? date('d-m-Y', strtotime($record->application_date)) : '',
                'actions' => $actions
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