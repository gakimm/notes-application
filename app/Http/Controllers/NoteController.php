<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'my-notes');
        
        $notes = collect();
        
        switch ($tab) {
            case 'my-notes':
                $notes = $user->notes()
                    ->latest()
                    ->paginate(12);
                break;
                
            case 'shared-with-me':
                $notes = $user->sharedNotes()
                    ->with('user')
                    ->latest()
                    ->paginate(12);
                break;
                
            case 'public':
                $notes = Note::public()
                    ->with('user')
                    ->latest()
                    ->paginate(12);
                break;
        }
        
        return view('notes.index', compact('notes', 'tab'));
    }

    public function create()
    {
        return view('notes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visibility' => 'required|in:private,shared,public',
            'shared_users' => 'required_if:visibility,shared|array',
            'shared_users.*' => 'exists:users,id'
        ]);
    
        try {
            DB::beginTransaction();
    
            // Buat note
            $note = Auth::user()->notes()->create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'visibility' => $validated['visibility'],
            ]);
    
            // Jika visibility shared, sync user yang dishare
            if ($validated['visibility'] === 'shared' && !empty($validated['shared_users'])) {
                $this->syncNoteShares($note, $validated['shared_users']);
            }
    
            DB::commit();
    
            return redirect()
                ->route('notes.show', $note->slug)
                ->with('success', 'Note berhasil dibuat!');
        } catch (\Throwable $e) {
            DB::rollBack();
    
            return back()->withInput()->withErrors([
                'error' => 'Terjadi kesalahan saat menyimpan note: ' . $e->getMessage(),
            ]);
        }
    }    

    public function show(Note $note)
    {
        // Check if user can view this note
        if (!$note->canBeViewedBy(Auth::user())) {
            abort(403, 'Kamu tidak punya akses untuk melihat note ini.');
        }

        $note->load(['user', 'comments.user', 'sharedWithUsers']);
        
        return view('notes.show', compact('note'));
    }

    public function edit(Note $note)
    {
        if (!$note->canBeEditedBy(Auth::user())) {
            abort(403, 'Kamu tidak bisa edit note ini.');
        }

        // Load current shared users for the form
        $note->load('sharedWithUsers');

        return view('notes.edit', compact('note'));
    }

    public function update(Request $request, Note $note)
    {
        if (!$note->canBeEditedBy(Auth::user())) {
            abort(403, 'Kamu tidak bisa edit note ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visibility' => 'required|in:private,shared,public',
            'shared_users' => 'required_if:visibility,shared|array',
            'shared_users.*' => 'exists:users,id'
        ]);

        DB::transaction(function () use ($validated, $note) {
            // Update the note
            $note->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'visibility' => $validated['visibility'],
            ]);

            // Handle sharing
            if ($validated['visibility'] === 'shared') {
                // Sync shared users (add new ones, remove old ones)
                $sharedUsers = $validated['shared_users'] ?? [];
                $this->syncNoteShares($note, $sharedUsers);
            } else {
                // If visibility changed from shared to private/public, remove all shares
                $note->shares()->delete();
            }
        });

        return redirect()
            ->route('notes.show', $note->slug)
            ->with('success', 'Note berhasil diupdate!');
    }

    public function destroy(Note $note)
    {
        if (!$note->canBeEditedBy(Auth::user())) {
            abort(403, 'Kamu tidak bisa hapus note ini.');
        }

        $note->delete();

        return redirect()
            ->route('notes.index')
            ->with('success', 'Note berhasil dihapus!');
    }

    /**
     * Search users for sharing (API endpoint)
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['users' => []]);
        }

        $currentUserId = Auth::id();
        
        $users = User::where('id', '!=', $currentUserId) // Exclude current user
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json(['users' => $users]);
    }

    private function syncNoteShares(Note $note, array $userIds)
    {
        try {
            $userIds = array_map('intval', $userIds);
            $userIds = array_filter($userIds, fn($id) => $id !== Auth::id());
    
            $currentSharedUserIds = $note->shares()->pluck('shared_with_user_id')->toArray();
            $usersToAdd = array_diff($userIds, $currentSharedUserIds);
            $usersToRemove = array_diff($currentSharedUserIds, $userIds);
    
            if (!empty($usersToRemove)) {
                $note->shares()
                    ->whereIn('shared_with_user_id', $usersToRemove)
                    ->delete();
            }
    
            foreach ($usersToAdd as $userId) {
                $note->shares()->create([
                    'shared_with_user_id' => $userId,
                    'permission' => 'read',
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Gagal sync share: " . $e->getMessage());
            throw $e; 
        }
    }
    

    // Custom method for route model binding dengan slug
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('slug', $value)->firstOrFail();
    }
}