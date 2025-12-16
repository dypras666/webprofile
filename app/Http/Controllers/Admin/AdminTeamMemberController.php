<?php

namespace App\Http\Controllers\Admin;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTeamMemberController extends BaseAdminController
{
    protected $model = TeamMember::class;
    protected $viewPath = 'admin.team';
    protected $routePrefix = 'admin.team';

    public function index(Request $request)
    {
        $query = $this->getIndexQuery($request);
        $query = $this->applyFilters($query, $request);
        $query->orderBy('order', 'asc');

        $members = $query->paginate($this->perPage);

        return view($this->viewPath . '.index', compact('members'));
    }

    public function create()
    {
        return view($this->viewPath . '.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'social_links' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            'status' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            // If image is an ID (from MediaPicker), resolve it to path
            if (!empty($data['image']) && is_numeric($data['image'])) {
                $media = \App\Models\Media::find($data['image']);
                if ($media) {
                    $data['image'] = $media->file_path;
                }
            }

            TeamMember::create($data);

            DB::commit();

            return redirect()->route($this->routePrefix . '.index')
                ->with('success', 'Team member created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating team member: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $member = TeamMember::findOrFail($id);
        return view($this->viewPath . '.form', compact('member'));
    }

    public function update(Request $request, $id)
    {
        $member = TeamMember::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'social_links' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            'status' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            // If image is an ID (from MediaPicker), resolve it to path
            if (!empty($data['image']) && is_numeric($data['image'])) {
                $media = \App\Models\Media::find($data['image']);
                if ($media) {
                    $data['image'] = $media->file_path;
                }
            }

            $member->update($data);

            DB::commit();

            return redirect()->route($this->routePrefix . '.index')
                ->with('success', 'Team member updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating team member: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $member = TeamMember::findOrFail($id);
            $member->delete();

            return redirect()->route($this->routePrefix . '.index')
                ->with('success', 'Team member deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting team member: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->items as $index => $id) {
                TeamMember::where('id', $id)->update(['order' => $index]);
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function toggleStatus($id)
    {
        try {
            $member = TeamMember::findOrFail($id);
            $member->status = !$member->status;
            $member->save();

            return response()->json([
                'success' => true,
                'status' => $member->status,
                'message' => 'Status berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
