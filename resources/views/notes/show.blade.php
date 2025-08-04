<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-2 md:space-y-0">
            <div class="flex-1">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-1">
                    {{ $note->title }}
                </h2>
                <div class="flex items-center text-sm text-gray-500 space-x-4">
                    <span>by {{ $note->user->name }}</span>
                    <span>{{ $note->created_at->format('M j, Y') }}</span>
                    @if($note->updated_at != $note->created_at)
                        <span> &nbsp;- Updated {{ $note->updated_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                @if($note->canBeEditedBy(auth()->user()))
                    <a href="{{ route('notes.edit', $note->slug) }}" class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endif
                
                <a href="{{ route('notes.index') }}" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="lg:max-w-[1000px] mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar -->
            <div class="flex-shrink-0 lg:w-1/3 space-y-6">
                <!-- Note Info Card -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Note Info</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Author</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $note->user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $note->created_at->format('M j, Y \a\t g:i A') }}</dd>
                            </div>
                            @if($note->updated_at != $note->created_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $note->updated_at->format('M j, Y \a\t g:i A') }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Comments</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $note->comments->count() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if($note->canBeEditedBy(auth()->user()) && $note->visibility === 'shared')
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Shared With</h3>
                        @if($note->sharedWithUsers->isEmpty())
                            <p class="text-sm text-gray-500">This note hasn't been shared with anyone yet.</p>
                        @else
                            <ul class="space-y-2">
                                @foreach($note->sharedWithUsers as $user)
                                    <li class="flex items-center justify-between">
                                        <span class="text-sm text-gray-900">{{ $user->name }} <span class="text-gray-500">({{ $user->email }})</span></span>
                                        <form action="{{ route('notes.unshare', [$note->slug, $user->id]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline text-sm">Remove</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                @endif
            </div>
             <!-- Main Content -->
             <div class="flex-1 lg:w-2/3 space-y-6">
                <!-- Note Content -->
                <div class="card">
                    <div class="card-body">
                        <!-- Visibility Badge -->
                        <div class="mb-4">
                            @if($note->visibility === 'public')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
                                    </svg>
                                    Public Note
                                </span>
                            @elseif($note->visibility === 'shared')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                    Shared Note
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Private Note
                                </span>
                            @endif
                        </div>

                        <!-- Note Content -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-2">
                            {{ $note->title }}
                        </h3>
                        <div class="prose prose-lg max-w-none markdown-content">
                            {!! $note->markdown_content !!}
                        </div>
                    </div>
                </div>
            </div>

        </div>
         <!-- Comments Section -->
         <div class="flex-1 space-y-6 mt-6">
            <div class="card" x-data="{ showCommentForm: false }" @comment-added.window="showCommentForm = false; setTimeout(() => location.reload(), 1000)">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Comments ({{ $note->comments->count() }})
                        </h3>
                        <button @click="showCommentForm = !showCommentForm" 
                                class="btn-primary text-sm ">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Comment
                        </button>
                    </div>

                    <!-- Comment Form -->
                    <div x-show="showCommentForm" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="mb-6">
                        <form action="{{ route('comments.store', $note->slug) }}" method="POST" x-data="commentBox()">
                            @csrf
                            <div class="space-y-4">
                                <textarea name="content" 
                                          x-model="content"
                                          rows="3" 
                                          required
                                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                          placeholder="Write your comment..."></textarea>
                                <div class="flex items-center justify-end space-x-3">
                                    <button type="button" 
                                            @click="showCommentForm = false; content = ''" 
                                            class="btn-secondary text-sm">
                                        Cancel
                                    </button>
                                    <button type="submit" 
                                            :disabled="loading || !content.trim()"
                                            class="btn-primary text-sm"
                                            :class="{ 'opacity-50 cursor-not-allowed': loading || !content.trim() }">
                                        <span x-show="!loading">Post Comment</span>
                                        <span x-show="loading">Posting...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div class="space-y-4">
                        @forelse($note->comments as $comment)
                            <div class="flex space-x-3 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ substr($comment->user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $comment->user->name }}</span>
                                            <span class="text-sm text-gray-500 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($comment->user_id === auth()->id())
                                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        onclick="return confirm('Are you sure you want to delete this comment?')"
                                                        class="text-red-400 hover:text-red-600 text-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <p class="text-gray-700 leading-relaxed">{{ $comment->content }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No comments yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Be the first to comment on this note!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <x-modal ref="shareModal" name="shareNoteModal" maxWidth="sm" focusable>
        <x-slot name="title">Share Note</x-slot>
        <x-slot name="description">Share this note with other users by entering their email addresses.</x-slot>
        <x-slot name="actions">
            <button type="button" class="btn-secondary" @click="$refs.shareModal.close()">Cancel</button>
            <button type="submit" class="btn-primary" form="shareNoteForm">Share</button>
        </x-slot>       
        <form action="{{ route('notes.share', $note->slug) }}" method="POST">
            @csrf
            <div class="p-4 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">Share this note</h2>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">User Email</label>
                    <input type="email" name="email" id="email" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right">
                <button type="button" class="btn-secondary" @click="$refs.shareModal.close()">Cancel</button>
                <button type="submit" class="btn-primary ml-2">Share</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
