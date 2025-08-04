<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    public function store(Request $request, Note $note)
    {
        if (!$note->canBeEditedBy(Auth::user())) {
            abort(403, 'Kamu tidak bisa share note ini.');
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'permission' => 'required|in:read,comment',
        ]);

        $userToShare = User::where('email', $validated['email'])->first();

        if ($userToShare->id === Auth::id()) {
            return back()->withErrors(['email' => 'Kamu tidak bisa share ke diri sendiri!']);
        }

        // Check if already shared
        if ($note->sharedWithUsers()->where('users.id', $userToShare->id)->exists()) {
            return back()->withErrors(['email' => 'Note sudah di-share ke user ini.']);
        }

        $note->sharedWithUsers()->attach($userToShare->id, [
            'permission' => $validated['permission']
        ]);

        return back()->with('success', "Note berhasil di-share ke {$userToShare->name}!");
    }

    public function destroy(Note $note, User $user)
    {
        if (!$note->canBeEditedBy(Auth::user())) {
            abort(403, 'Kamu tidak bisa unshare note ini.');
        }

        $note->sharedWithUsers()->detach($user->id);

        return back()->with('success', 'Share berhasil dihapus!');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $users = User::where('id', '!=', Auth::id())
                    ->where(function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('email', 'LIKE', "%{$query}%");
                    })
                    ->limit(10)
                    ->get(['id', 'name', 'email']);
        
        return response()->json(['users' => $users]);
    }
}