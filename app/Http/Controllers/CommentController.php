<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Note $note)
    {
        // Check if user can view the note (required to comment)
        if (!$note->canBeViewedBy(Auth::user())) {
            abort(403, 'Kamu tidak bisa comment di note ini.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $note->comments()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment->load('user'),
                'message' => 'Comment berhasil ditambahkan!'
            ]);
        }

        return back()->with('success', 'Comment berhasil ditambahkan!');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak bisa hapus comment ini.');
        }

        $comment->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment berhasil dihapus!'
            ]);
        }

        return back()->with('success', 'Comment berhasil dihapus!');
    }
}