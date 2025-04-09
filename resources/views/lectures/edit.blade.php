@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Chỉnh sửa bài giảng') }} - {{ $course->name }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('courses.lectures.update', [$course, $lecture]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Tiêu đề') }} <span class="text-danger">*</span></label>
                            <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $lecture->title) }}" required autofocus>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Mô tả') }}</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $lecture->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">{{ __('File bài giảng') }}</label>
                            <input id="file" type="file" class="form-control @error('file') is-invalid @enderror" name="file">
                            <small class="text-muted">{{ __('Định dạng hỗ trợ: PDF, DOC, DOCX, PPT, PPTX, MP4, ZIP. Dung lượng tối đa: 50MB.') }}</small>
                            <div class="mt-2">
                                <span class="text-info">{{ __('File hiện tại:') }} <a href="{{ Storage::url($lecture->file_url) }}" target="_blank">{{ basename($lecture->file_url) }}</a></span>
                            </div>
                            @error('file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('courses.lectures.index', $course) }}" class="btn btn-secondary">{{ __('Hủy') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Cập nhật bài giảng') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection