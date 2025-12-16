<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $query = Comment::with('post');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(20);
        $pendingCount = Comment::where('status', 'pending')->count();

        return view('admin.comments.index', compact('comments', 'status', 'pendingCount'));
    }

    /**
     * Update the specified resource status.
     */
    public function updateStatus(Request $request, Comment $comment)
    {
        $status = $request->input('status');
        if (in_array($status, ['approved', 'pending', 'rejected', 'spam'])) {
            $comment->update(['status' => $status]);
            return redirect()->back()->with('success', 'Status komentar berhasil diperbarui.');
        }
        return redirect()->back()->with('error', 'Status tidak valid.');
    }

    /**
     * Rescan all comments for spam.
     */
    public function rescan()
    {
        $count = 0;

        // Process in chunks to handle large number of comments
        Comment::whereNotIn('status', ['spam', 'rejected'])
            ->chunk(100, function ($comments) use (&$count) {
                foreach ($comments as $comment) {
                    if (\App\Helpers\CommentHelper::containsBadWords($comment->content)) {
                        $comment->update(['status' => 'spam']);
                        $count++;
                    }
                }
            });

        return redirect()->back()->with('success', "Scan selesai. {$count} komentar ditandai sebagai spam.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }
}
