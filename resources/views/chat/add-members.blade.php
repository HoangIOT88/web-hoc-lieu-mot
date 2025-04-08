@extends('layouts.app')

@section('title', 'Thêm thành viên vào nhóm chat')

@section('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        padding: 0.375rem 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Thêm thành viên vào: {{ $chatGroup->name }}</h1>
            <div>
                <a href="{{ route('chat-groups.show', $chatGroup) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
        
        <div class="card-body">
            @if($users->isEmpty())
                <div class="alert alert-info">
                    Tất cả người dùng đã được thêm vào nhóm chat này.
                </div>
            @else
                <form action="{{ route('chat-groups.members.add', $chatGroup) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="members" class="form-label">Chọn thành viên để thêm vào</label>
                        <select id="members" name="members[]" class="form-select select2-multiple @error('members') is-invalid @enderror" 
                                multiple="multiple" data-placeholder="Tìm và chọn thành viên..." required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('members')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Bạn có thể chọn nhiều thành viên cùng lúc</small>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i> Thêm thành viên
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Tìm kiếm thành viên...',
            allowClear: true,
            language: {
                noResults: function() {
                    return "Không tìm thấy thành viên";
                },
                searching: function() {
                    return "Đang tìm...";
                }
            },
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: formatUser,
            templateSelection: formatUserSelection
        });
        
        function formatUser(user) {
            if (!user.id) return user.text;
            
            return $(`
                <div class="d-flex align-items-center">
                    <div class="avatar bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                         style="width: 28px; height: 28px; font-size: 12px;">
                        ${user.text.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <span class="d-block">${user.text.split(' (')[0]}</span>
                        <small class="text-muted">${user.text.split(' (')[1]?.replace(')', '') || ''}</small>
                    </div>
                </div>
            `);
        }
        
        function formatUserSelection(user) {
            if (!user.id) return user.text;
            return user.text.split(' (')[0];
        }
    });
</script>
@endsection 