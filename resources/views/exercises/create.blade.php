@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Tạo bài tập mới') }}</span>
                        <a href="{{ route('exercises.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> {{ __('Quay lại') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('exercises.store') }}">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="course_id" class="col-md-3 col-form-label text-md-right">{{ __('Khóa học') }}</label>
                            <div class="col-md-8">
                                <select id="course_id" class="form-control @error('course_id') is-invalid @enderror" name="course_id" required>
                                    <option value="">{{ __('-- Chọn khóa học --') }}</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="title" class="col-md-3 col-form-label text-md-right">{{ __('Tiêu đề') }}</label>
                            <div class="col-md-8">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="content" class="col-md-3 col-form-label text-md-right">{{ __('Nội dung') }}</label>
                            <div class="col-md-8">
                                <textarea id="content" class="form-control @error('content') is-invalid @enderror" name="content" rows="6" required>{{ old('content') }}</textarea>
                                @error('content')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="deadline" class="col-md-3 col-form-label text-md-right">{{ __('Hạn nộp') }}</label>
                            <div class="col-md-8">
                                <input id="deadline" type="datetime-local" class="form-control @error('deadline') is-invalid @enderror" name="deadline" value="{{ old('deadline') }}">
                                <small class="text-muted">{{ __('Để trống nếu không có hạn chót') }}</small>
                                @error('deadline')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Tạo bài tập') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 