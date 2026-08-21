<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\PrivateWing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:الأجنحة الخاصة', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $query = Doctor::with('moh', 'fck');

        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                $query->where('moh_id', $currentUser->mohcode);
            } else {
                $query->where('fck_id', $currentUser->fckid);
            }
        }

        $doctors = $query->paginate(20);
        return view('doctors.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('doctors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'job_title' => 'required|string|max:255',
        ]);

        $data = $request->only(['name', 'address', 'job_title']);
        $data['institution'] = Auth::user()->fck->Fckname ?? 'غير محدد';
        $data['moh_id'] = Auth::user()->mohcode;
        $data['fck_id'] = Auth::user()->fckid;

        Doctor::create($data);

        return redirect()->route('doctors.index')->with('success', 'Doctor created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $currentUser = Auth::user();
        $doctor = Doctor::with('moh', 'fck')->findOrFail($id);

        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                if ($doctor->moh_id != $currentUser->mohcode) abort(403);
            } else {
                if ($doctor->fck_id != $currentUser->fckid) abort(403);
            }
        }

        return view('doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $currentUser = Auth::user();
        $doctor = Doctor::findOrFail($id);

        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                if ($doctor->moh_id != $currentUser->mohcode) abort(403);
            } else {
                if ($doctor->fck_id != $currentUser->fckid) abort(403);
            }
        }

        return view('doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'job_title' => 'required|string|max:255',
        ]);

        $currentUser = Auth::user();
        $doctor = Doctor::findOrFail($id);

        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                if ($doctor->moh_id != $currentUser->mohcode) abort(403);
            } else {
                if ($doctor->fck_id != $currentUser->fckid) abort(403);
            }
        }
        $data = $request->only(['name', 'address', 'job_title']);
        $data['institution'] = Auth::user()->fck->Fckname ?? 'غير محدد';
        $data['moh_id'] = Auth::user()->mohcode;
        $data['fck_id'] = Auth::user()->fckid;
        $doctor->update($data);

        return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currentUser = Auth::user();
        $doctor = Doctor::findOrFail($id);

        if (!in_array("Admin", $currentUser->roles_name) && !in_array("mohst", $currentUser->roles_name)) {
            if ($currentUser->roles_name === ["stat-doh"]) {
                if ($doctor->moh_id != $currentUser->mohcode) abort(403);
            } else {
                if ($doctor->fck_id != $currentUser->fckid) abort(403);
            }
        }

        $doctor->delete();

        return redirect()->route('doctors.index')->with('success', 'Doctor deleted successfully.');
    }
}
