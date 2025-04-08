@extends('layouts.app')

@section('title', 'Chat Groups')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Chat Groups</h1>
        <a href="{{ route('chat-groups.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Group
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($groups->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>You are not a member of any chat groups yet. Create a new group or ask someone to add you to their group.
        </div>
    @else
        <div class="row">
            @foreach($groups as $chatGroup)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $chatGroup->name }}</h5>
                            <p class="card-text text-muted small">
                                <i class="fas fa-user me-1"></i> Created by: {{ $chatGroup->creator->name }}
                            </p>
                            <p class="card-text">{{ Str::limit($chatGroup->description, 100) }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge bg-primary">
                                    <i class="fas fa-users me-1"></i> {{ $chatGroup->members->count() }} members
                                </span>
                                <span class="text-muted small">
                                    <i class="fas fa-clock me-1"></i> {{ $chatGroup->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <a href="{{ route('chat-groups.show', $chatGroup) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-comments me-1"></i> View Group
                            </a>
                            
                            @if(Auth::id() == $chatGroup->created_by || Auth::user()->is_admin)
                                <div class="btn-group">
                                    <a href="{{ route('chat-groups.edit', $chatGroup) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteChatGroupModal{{ $chatGroup->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteChatGroupModal{{ $chatGroup->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Chat Group</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete the chat group "{{ $chatGroup->name }}"?</p>
                                                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone and will delete all messages in this group.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('chat-groups.destroy', $chatGroup) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete Group</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $groups->links() }}
        </div>
    @endif
</div>
@endsection 