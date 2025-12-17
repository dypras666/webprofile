<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgramStudiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programStudis = ProgramStudi::orderBy('sort_order', 'asc')->orderBy('name', 'asc')->paginate(10);
        return view('admin.program-studi.index', compact('programStudis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dosens = \App\Models\TeamMember::where('status', true)->orderBy('name')->get();
        return view('admin.program-studi.form', compact('dosens'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'uuid' => 'nullable|string|max:100',
            'program_head_id' => 'nullable|exists:team_members,id',
            'faculty' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:50',
            'accreditation' => 'nullable|string|max:50',
            'establishment_date' => 'nullable|date',
            'decree_number' => 'nullable|string|max:255',
            'decree_date' => 'nullable|date',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website_url' => 'nullable|url',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'competence' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'integer',
        ]);

        $data = $request->except('image');
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('program-studi', 'public');
        }

        ProgramStudi::create($data);

        return redirect()->route('admin.prodi.index')->with('success', 'Program Studi created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgramStudi $programStudi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgramStudi $prodi)
    {
        $programStudi = $prodi; // Alias for view consistency
        $dosens = \App\Models\TeamMember::where('status', true)->orderBy('name')->get();
        return view('admin.program-studi.form', compact('programStudi', 'dosens'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgramStudi $prodi)
    {
        $programStudi = $prodi; // Alias
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'program_head_id' => 'nullable|exists:team_members,id',
            'faculty' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:50',
            'accreditation' => 'nullable|string|max:50',
            'establishment_date' => 'nullable|date',
            'decree_number' => 'nullable|string|max:255',
            'decree_date' => 'nullable|date',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website_url' => 'nullable|url',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'competence' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'integer',
        ]);

        $data = $request->except('image');
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if ($programStudi->image) {
                Storage::disk('public')->delete($programStudi->image);
            }
            $data['image'] = $request->file('image')->store('program-studi', 'public');
        }

        $programStudi->update($data);

        return redirect()->route('admin.prodi.index')->with('success', 'Program Studi updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramStudi $programStudi)
    {
        if ($programStudi->image) {
            Storage::disk('public')->delete($programStudi->image);
        }

        $programStudi->delete();

        return redirect()->route('admin.prodi.index')->with('success', 'Program Studi deleted successfully.');
    }
}
