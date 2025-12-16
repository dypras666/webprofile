<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\SiteSetting;
use App\Helpers\CommentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // 1. Check if comments are enabled
        if (SiteSetting::getValue('comments_enabled') !== '1') {
            return response()->json(['message' => 'Komentar dinonaktifkan.'], 403);
        }

        // 2. Validation
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'content' => 'required|string|max:1000',
            'g-recaptcha-response' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 3. Google reCAPTCHA Check (Skip on Local)
        $recaptchaSecret = SiteSetting::getValue('recaptcha_secret_key');
        if (!app()->isLocal() && !empty($recaptchaSecret)) {
            $response = $request->input('g-recaptcha-response');
            if (empty($response)) {
                return response()->json(['message' => 'Silakan selesaikan validasi Recaptcha.'], 422);
            }

            $verifyResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $response,
                'remoteip' => $request->ip(),
            ]);

            if (!$verifyResponse->successful() || !$verifyResponse->json('success')) {
                return response()->json(['message' => 'Validasi Recaptcha gagal.'], 422);
            }
        }

        // 4. Content Filtering & Spam Check
        $status = 'approved';
        $content = $request->input('content');

        if ($badWord = CommentHelper::containsBadWords($content)) {
            // Option A: Reject immediately
            return response()->json(['message' => "Komentar mengandung kata terlarang: $badWord"], 422);
            // Option B: Set to rejected (if we want to keep record)
            // $status = 'rejected';
        }

        if (CommentHelper::isSpam($content)) {
            $status = 'spam';
        }

        // 5. Create Comment
        $comment = Comment::create([
            'post_id' => $request->input('post_id'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'content' => $content,
            'status' => $status,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // 6. Response
        if ($status === 'approved') {
            return response()->json(['message' => 'Komentar berhasil dikirim!', 'comment' => $comment], 200);
        } else if ($status === 'spam') {
            return response()->json(['message' => 'Komentar dideteksi sebagai spam dan menunggu moderasi.'], 200);
        } else {
            return response()->json(['message' => 'Komentar menunggu persetujuan admin.'], 200);
        }
    }
}
