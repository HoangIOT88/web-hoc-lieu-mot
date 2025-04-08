@extends('layouts.app')

@section('title', 'Chỉnh sửa nhóm chat')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Chỉnh sửa nhóm chat') }}</span>
                        <div>
                            <a href="{{ route('chat-groups.show', $chatGroup->id) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-eye"></i> {{ __('Xem nhóm') }}
                            </a>
                            <a href="{{ route('chat-groups.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('chat-groups.update', $chatGroup->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Tên nhóm') }} <span class="text-danger">*</span></label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $chatGroup->name) }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Mô tả') }}</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $chatGroup->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        @if(Auth::id() == $chatGroup->creator_id)
                        <div class="mb-3">
                            <label for="members" class="form-label">{{ __('Thành viên') }}</label>
                            <select id="members" name="members[]" class="form-select @error('members') is-invalid @enderror" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, old('members', $currentMembers)) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('Giữ phím Ctrl (hoặc Command trên Mac) để chọn nhiều thành viên.') }}</div>
                            @error('members')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('is_private') is-invalid @enderror" type="checkbox" value="1" id="is_private" name="is_private" {{ old('is_private', $chatGroup->is_private) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_private">
                                    {{ __('Nhóm riêng tư') }}
                                </label>
                                <div class="form-text">{{ __('Nhóm riêng tư chỉ hiển thị với thành viên được thêm vào.') }}</div>
                                @error('is_private')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Cập nhật nhóm') }}
                            </button>
                        </div>
                    </form>

                    @if(Auth::id() == $chatGroup->creator_id)
                    <hr>
                    <div class="mt-3">
                        <form method="POST" action="{{ route('chat-groups.destroy', $chatGroup->id) }}" onsubmit="return confirm('{{ __('Bạn có chắc chắn muốn xóa nhóm chat này?') }}')">
                            @csrf
                            @method('DELETE')
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> {{ __('Xóa nhóm chat') }}
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add select2 or any other multi-select enhancement library if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('#members').select2({
                placeholder: "{{ __('Chọn thành viên...') }}",
                allowClear: true
            });
        }
    });
</script>
@endsection 