<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Media;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'dashboard', 'profile', 'updateProfile']);
        $this->middleware('auth')->only(['logout', 'dashboard', 'profile', 'updateProfile']);
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Google Recaptcha Validation
        $recaptchaSiteKey = \App\Models\SiteSetting::getValue('recaptcha_site_key');
        $recaptchaSecretKey = \App\Models\SiteSetting::getValue('recaptcha_secret_key');

        if (!app()->isLocal() && !in_array($request->ip(), ['127.0.0.1', '::1']) && !empty($recaptchaSiteKey) && !empty($recaptchaSecretKey)) {
            $recaptchaResponse = $request->input('g-recaptcha-response');

            if (empty($recaptchaResponse)) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'Silakan selesaikan validasi Recaptcha.',
                ]);
            }

            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecretKey,
                'response' => $recaptchaResponse,
                'remoteip' => $request->ip(),
            ]);

            $result = $response->json();

            if (!$result['success']) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'Validasi Recaptcha gagal. Silakan coba lagi.',
                ]);
            }
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Check if user exists and is active
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak terdaftar.',
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ]);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password salah.',
            ]);
        }

        Auth::login($user, $remember);

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Selamat datang, ' . $user->name . '!');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil logout.');
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Get statistics
        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::published()->count(),
            'draft_posts' => Post::where('is_published', false)->count(),
            'total_categories' => Category::count(),
            'active_categories' => Category::active()->count(),
            'total_media' => Media::count(),
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
        ];

        // Get recent posts
        $recentPosts = Post::with(['category', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        // Get popular posts (by views)
        $popularPosts = Post::published()
            ->with(['category', 'user'])
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        // Get recent media
        $recentMedia = Media::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Get posts by type for chart
        $postsByType = Post::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Get posts by month for chart (last 6 months)
        $postsByMonth = Post::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return view('admin.dashboard', compact(
            'stats',
            'recentPosts',
            'popularPosts',
            'recentMedia',
            'postsByType',
            'postsByMonth'
        ));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'bio.max' => 'Bio maksimal 1000 karakter.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'profile_photo.image' => 'File harus berupa gambar.',
            'profile_photo.mimes' => 'Gambar harus berformat: jpeg, png, jpg, gif.',
            'profile_photo.max' => 'Ukuran gambar maksimal 2MB.',
            'current_password.required_with' => 'Password saat ini wajib diisi jika ingin mengubah password.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'Password saat ini salah.',
                ]);
            }
        }

        $data = $request->only(['name', 'email', 'bio', 'phone']);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $fileUploadService = app(\App\Services\FileUploadService::class);

            // Delete old profile photo
            if ($user->profile_photo) {
                \Storage::disk('public')->delete($user->profile_photo);
            }

            $media = $fileUploadService->uploadImage(
                $request->file('profile_photo'),
                'profiles',
                $user->id,
                ['width' => 300, 'height' => 300]
            );
            $data['profile_photo'] = $media->file_path;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
