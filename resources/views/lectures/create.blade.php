@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Tạo bài giảng mới') }} - {{ $course->name }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('lectures.store', $course) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Tiêu đề') }} <span class="text-danger">*</span></label>
                            <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required autofocus>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Mô tả') }}</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">{{ __('File bài giảng') }} <span class="text-danger">*</span></label>
                            <input id="file" type="file" class="form-control @error('file') is-invalid @enderror" name="file" required>
                            <small class="text-muted">{{ __('Định dạng hỗ trợ: PDF, DOC, DOCX, PPT, PPTX, MP4, ZIP. Dung lượng tối đa: 50MB.') }}</small>
                            @error('file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('lectures.index', $course) }}" class="btn btn-secondary">{{ __('Hủy') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Tạo bài giảng') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 