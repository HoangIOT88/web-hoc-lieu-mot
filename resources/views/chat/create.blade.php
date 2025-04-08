@extends('layouts.app')

@section('title', 'Create Chat Group')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Create New Chat Group</h1>
            <a href="{{ route('chat-groups.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Groups
            </a>
        </div>
        
        <div class="card-body">
            <form action="{{ route('chat-groups.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Group Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Briefly describe the purpose of this chat group</small>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Add Members</label>
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" id="userSearch" class="form-control" placeholder="Search users by name or email...">
                                    <button type="button" id="searchButton" class="btn btn-outline-secondary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div id="searchResults" class="list-group mb-3" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                            
                            <div id="selectedUsers" class="mb-0">
                                <p class="text-muted mb-2">Selected members will appear here. You will be added automatically as the group creator.</p>
                                <div id="selectedUsersList" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>
                    @error('members')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Group
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSearch = document.getElementById('userSearch');
        const searchResults = document.getElementById('searchResults');
        const selectedUsersList = document.getElementById('selectedUsersList');
        const searchButton = document.getElementById('searchButton');
        
        // Store selected users
        const selectedUsers = new Map();
        
        // Search for users
        const performSearch = () => {
            const query = userSearch.value.trim();
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            
            fetch(`/api/users/search?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="list-group-item text-center text-muted">No users found</div>';
                    } else {
                        data.forEach(user => {
                            // Skip users that are already selected
                            if (selectedUsers.has(user.id)) return;
                            
                            const userItem = document.createElement('button');
                            userItem.type = 'button';
                            userItem.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                            userItem.innerHTML = `
                                <div>
                                    <span class="fw-medium">${user.name}</span>
                                    <small class="text-muted d-block">${user.email}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">Add</span>
                            `;
                            
                            userItem.addEventListener('click', () => {
                                addUser(user);
                                searchResults.style.display = 'none';
                                userSearch.value = '';
                            });
                            
                            searchResults.appendChild(userItem);
                        });
                    }
                    
                    searchResults.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error searching users:', error);
                    searchResults.innerHTML = '<div class="list-group-item text-center text-danger">Error searching users</div>';
                    searchResults.style.display = 'block';
                });
        };
        
        // Add user to selected list
        const addUser = (user) => {
            if (selectedUsers.has(user.id)) return;
            
            selectedUsers.set(user.id, user);
            
            const userBadge = document.createElement('div');
            userBadge.className = 'badge bg-light text-dark p-2 d-flex align-items-center';
            userBadge.innerHTML = `
                <span>${user.name}</span>
                <button type="button" class="btn-close ms-2" aria-label="Remove"></button>
                <input type="hidden" name="members[]" value="${user.id}">
            `;
            
            const closeButton = userBadge.querySelector('.btn-close');
            closeButton.addEventListener('click', () => {
                selectedUsers.delete(user.id);
                userBadge.remove();
                
                // Update empty state
                updateEmptyState();
            });
            
            selectedUsersList.appendChild(userBadge);
            
            // Update empty state
            updateEmptyState();
        };
        
        // Update empty state message
        const updateEmptyState = () => {
            const emptyMessage = document.querySelector('#selectedUsers p.text-muted');
            if (selectedUsers.size > 0) {
                emptyMessage.style.display = 'none';
            } else {
                emptyMessage.style.display = 'block';
            }
        };
        
        // Set up event listeners
        userSearch.addEventListener('input', () => {
            if (userSearch.value.trim().length >= 2) {
                performSearch();
            } else {
                searchResults.style.display = 'none';
            }
        });
        
        searchButton.addEventListener('click', performSearch);
        
        // Close search results when clicking outside
        document.addEventListener('click', (event) => {
            if (!event.target.closest('#userSearch') && !event.target.closest('#searchResults') && !event.target.closest('#searchButton')) {
                searchResults.style.display = 'none';
            }
        });
    });
</script>
@endpush 