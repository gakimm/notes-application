<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ currentTab: '{{ $tab }}' }">
        <!-- Tabs -->
        <div class="mb-6">
            <div class="sm:hidden">
                <select x-model="currentTab" @change="window.location.href = '{{ route('notes.index') }}?tab=' + currentTab" class="block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="my-notes">My Notes</option>
                    <option value="shared-with-me">Shared with Me</option>
                    <option value="public">Public Notes</option>
                </select>
            </div>
            <div class="hidden sm:block">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('notes.index', ['tab' => 'my-notes']) }}" 
                       class="px-3 py-2 font-medium text-sm rounded-md transition-colors duration-200"
                       :class="currentTab === 'my-notes' ? 'bg-primary-100 text-primary-700 border-primary-500' : 'text-gray-500 hover:text-gray-700'">
                        My Notes
                        <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100">
                            {{ auth()->user()->notes()->count() }}
                        </span>
                    </a>
                    <a href="{{ route('notes.index', ['tab' => 'shared-with-me']) }}" 
                       class="px-3 py-2 font-medium text-sm rounded-md transition-colors duration-200"
                       :class="currentTab === 'shared-with-me' ? 'bg-primary-100 text-primary-700 border-primary-500' : 'text-gray-500 hover:text-gray-700'">
                        Shared with Me
                        <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100">
                            {{ auth()->user()->sharedNotes()->count() }}
                        </span>
                    </a>
                    <a href="{{ route('notes.index', ['tab' => 'public']) }}" 
                       class="px-3 py-2 font-medium text-sm rounded-md transition-colors duration-200"
                       :class="currentTab === 'public' ? 'bg-primary-100 text-primary-700 border-primary-500' : 'text-gray-500 hover:text-gray-700'">
                        Public Notes
                    </a>
                </nav>
            </div>
        </div>

        <!-- Notes Grid -->
        @if($notes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($notes as $note)
                    <div class="card hover:shadow-md transition-shadow duration-200 animate-fade-in">
                        <div class="card-body">
                            <!-- Note Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-2">
                                        <a href="{{ route('notes.show', $note->slug) }}" class="hover:text-primary-600 transition-colors">
                                            {{ $note->title }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        @if($tab === 'my-notes')
                                            {{ $note->created_at->diffForHumans() }}
                                        @else
                                            by {{ $note->user->name }} • {{ $note->created_at->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                                
                                <!-- Visibility Badge -->
                                <div class="ml-2">
                                    @if($note->visibility === 'public')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
                                            </svg>
                                            Public
                                        </span>
                                    @elseif($note->visibility === 'shared')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                            </svg>
                                            Shared
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Private
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Note Content Preview -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ $note->excerpt }}
                            </p>

                            <!-- Note Footer -->
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <div class="flex items-center space-x-3">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        {{ $note->comments()->count() }}
                                    </span>
                                    @if($note->visibility === 'shared' && $tab === 'my-notes')
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                            </svg>
                                            {{ $note->sharedWithUsers()->count() }}
                                        </span>
                                    @endif
                                </div>
                                
                                @if($tab === 'my-notes')
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('notes.edit', $note->slug) }}" 
                                           class="text-gray-400 hover:text-primary-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            {{ $notes->links() }}
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        @if($tab === 'my-notes')
                            No notes yet
                        @elseif($tab === 'shared-with-me')
                            No shared notes
                        @else
                            No public notes available
                        @endif
                    </h3>
                    <p class="text-gray-500 mb-6">
                        @if($tab === 'my-notes')
                            Get started by creating your first note!
                        @elseif($tab === 'shared-with-me')
                            Notes shared with you will appear here.
                        @else
                            Check back later for public notes from the community.
                        @endif
                    </p>
                    
                    @if($tab === 'my-notes')
                        <a href="{{ route('notes.create') }}" class="btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Your First Note
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-app-layout>