<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Note
            </h2>
            <a href="{{ route('notes.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Notes
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('notes.store') }}" method="POST" x-data="noteEditor()" class="space-y-6">
            @csrf
            
            <!-- Title Input -->
            <div class="card">
                <div class="card-body">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                           placeholder="Enter note title...">
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Content Editor -->
            <div class="card">
                <div class="card-body">

                    <!-- Editor/Preview Area -->
                    <div class="min-h-96">
                        <!-- Editor -->
                        <div x-show="!preview" class="space-y-2">
                            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                            <textarea name="content" 
                                      id="content" 
                                      x-ref="textarea"
                                      x-model="content"
                                      rows="20" 
                                      required
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 font-mono text-sm"
                                      placeholder="Write your content here...">{{ old('content') }}</textarea>
                        </div>
                    </div>
                    
                    @error('content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Visibility & Actions -->
            <div class="card">
                <div class="card-body">
                    <div x-data="{ 
                        visibility: '{{ old('visibility', 'private') }}',
                        selectedUsers: {{ json_encode(old('shared_users', [])) }},
                        searchQuery: '',
                        searchResults: [],
                        isSearching: false,
                        
                        searchUsers() {
                            if (this.searchQuery.length < 2) {
                                this.searchResults = [];
                                return;
                            }
                            
                            this.isSearching = true;
                            
                            fetch(`/api/users/search?q=${encodeURIComponent(this.searchQuery)}`)
                                .then(response => response.json())
                                .then(data => {
                                    this.searchResults = data.users || [];
                                    this.isSearching = false;
                                })
                                .catch(error => {
                                    console.error('Error searching users:', error);
                                    this.isSearching = false;
                                });
                        },
                        
                        addUser(user) {
                            if (!this.selectedUsers.find(u => u.id === user.id)) {
                                this.selectedUsers.push(user);
                            }
                            this.searchQuery = '';
                            this.searchResults = [];
                        },
                        
                        removeUser(userId) {
                            this.selectedUsers = this.selectedUsers.filter(u => u.id !== userId);
                        }
                    }">
                        <div class="flex flex-col md:flex-row md:items-start justify-between space-y-4 md:space-y-0 md:space-x-8">
                            <!-- Visibility Options -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-4">Visibility</label>
                                <div class="flex flex-col gap-4">
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="visibility" 
                                               value="private" 
                                               x-model="visibility"
                                               class="form-radio text-primary-600">
                                        <span class="ml-2 text-sm text-gray-700">
                                            <span class="font-medium">Private</span> - Only you can see this note
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="visibility" 
                                               value="shared" 
                                               x-model="visibility"
                                               class="form-radio text-primary-600">
                                        <span class="ml-2 text-sm text-gray-700">
                                            <span class="font-medium">Shared</span> - Share with specific people
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="visibility" 
                                               value="public" 
                                               x-model="visibility"
                                               class="form-radio text-primary-600">
                                        <span class="ml-2 text-sm text-gray-700">
                                            <span class="font-medium">Public</span> - Anyone can view this note
                                        </span>
                                    </label>
                                </div>
                                @error('visibility')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- User Selection (shown when 'shared' is selected) -->
                            <div x-show="visibility === 'shared'" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 class="flex-1 md:max-w-md">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Share with Users</label>
                                
                                <!-- Search Input -->
                                <div class="relative mb-4">
                                    <input type="text" 
                                           x-model="searchQuery"
                                           @input.debounce.300ms="searchUsers()"
                                           placeholder="Search users by name or email..."
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 pl-10">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    
                                    <!-- Loading indicator -->
                                    <div x-show="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Search Results -->
                                <div x-show="searchResults.length > 0" 
                                     class="mb-4 border border-gray-200 rounded-md max-h-48 overflow-y-auto">
                                    <template x-for="user in searchResults" :key="user.id">
                                        <button type="button"
                                                @click="addUser(user)"
                                                class="w-full px-4 py-3 text-left hover:bg-gray-50 border-b border-gray-100 last:border-b-0 flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                                    <span class="text-primary-600 text-sm font-medium" x-text="user.name.charAt(0).toUpperCase()"></span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900" x-text="user.name"></p>
                                                <p class="text-sm text-gray-500 truncate" x-text="user.email"></p>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                <!-- Selected Users -->
                                <div x-show="selectedUsers.length > 0" class="space-y-2">
                                    <p class="text-sm font-medium text-gray-700">Selected Users:</p>
                                    <div class="space-y-2">
                                        <template x-for="user in selectedUsers" :key="user.id">
                                            <div class="flex items-center justify-between bg-primary-50 px-3 py-2 rounded-md">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                                                        <span class="text-primary-600 text-xs font-medium" x-text="user.name.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900" x-text="user.name"></p>
                                                        <p class="text-xs text-gray-500" x-text="user.email"></p>
                                                    </div>
                                                </div>
                                                <button type="button" 
                                                        @click="removeUser(user.id)"
                                                        class="text-red-500 hover:text-red-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <!-- Hidden input for selected user -->
                                        </template>
                                    </div>
                                </div>
                                <input type="hidden" name="shared_users[]" x-model="selectedUsers.map(user => user.id).join(',')">

                                @error('shared_users')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('notes.index') }}" class="btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn-primary">
                                Create Note
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>