<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->middleware('auth');
        $this->middleware('permission:view users', ['only' => ['index', 'show']]);
        $this->middleware('permission:create users', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit users', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete users', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $data['email_verified_at'] = now();

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $uploadResult = $this->fileUploadService->uploadImage(
                    $request->file('profile_photo'),
                    'profiles',
                    300,
                    300
                );
                $data['profile_photo'] = $uploadResult['path'];
            }

            $user = User::create($data);

            // Assign role
            if ($request->filled('role')) {
                $user->assignRole($request->role);
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                           ->with('success', 'User berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'Gagal membuat user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles', 'permissions');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRole = $user->roles->first();
        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle password update
            if ($request->filled('password')) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($user->profile_photo) {
                    $this->fileUploadService->delete($user->profile_photo);
                }
                
                $uploadResult = $this->fileUploadService->uploadImage(
                    $request->file('profile_photo'),
                    'profiles',
                    300,
                    300
                );
                $data['profile_photo'] = $uploadResult['path'];
            }

            $user->update($data);

            // Update role
            if ($request->filled('role')) {
                $user->syncRoles([$request->role]);
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                           ->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting current user
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        try {
            DB::beginTransaction();

            // Delete profile photo if exists
            if ($user->profile_photo) {
                $this->fileUploadService->delete($user->profile_photo);
            }

            // Delete user
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')
                           ->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        // Prevent deactivating current user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menonaktifkan akun sendiri.'
            ], 400);
        }

        try {
            $user->update(['is_active' => !$user->is_active]);
            
            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
            
            return response()->json([
                'success' => true,
                'message' => "User berhasil {$status}.",
                'is_active' => $user->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        // Remove current user from deletion list
        $userIds = array_filter($request->user_ids, function ($id) {
            return $id != auth()->id();
        });

        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada user yang dapat dihapus.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $users = User::whereIn('id', $userIds)->get();
            
            foreach ($users as $user) {
                if ($user->profile_photo) {
                    $this->fileUploadService->delete($user->profile_photo);
                }
                $user->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($userIds) . ' user berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }
}
