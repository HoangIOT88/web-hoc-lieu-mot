@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>{{ __('Chấm điểm bài tập') }}</div>
                    <a href="{{ route('exercise-submissions.show', $submission->id) }}" class="btn btn-sm btn-secondary">Quay lại</a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <h4>{{ $submission->exercise->title }}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Học viên:</strong> {{ $submission->user->name }}</p>
                                <p><strong>Email:</strong> {{ $submission->user->email }}</p>
                                <p><strong>Khóa học:</strong> {{ $submission->exercise->course->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Ngày nộp:</strong> {{ $submission->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Điểm tối đa:</strong> {{ $submission->exercise->max_score }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Bài làm của học viên:</h5>
                        <div class="p-3 bg-light rounded">
                            @if ($submission->content)
                                {!! nl2br(e($submission->content)) !!}
                            @endif
                            
                            @if ($submission->file_path)
                                <div class="mt-3">
                                    <p><strong>File đính kèm:</strong></p>
                                    <a href="{{ Storage::url($submission->file_path) }}" class="btn btn-sm btn-info" target="_blank">
                                        <i class="fas fa-download"></i> Tải xuống file đính kèm
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('exercise-submissions.grade', $submission->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="score" class="form-label">Điểm (tối đa {{ $submission->exercise->max_score }} điểm)</label>
                            <input type="number" class="form-control @error('score') is-invalid @enderror" id="score" 
                                name="score" min="0" max="{{ $submission->exercise->max_score }}" 
                                value="{{ old('score', $submission->score ?? 0) }}" required>
                            @error('score')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="feedback" class="form-label">Nhận xét</label>
                            <textarea class="form-control @error('feedback') is-invalid @enderror" id="feedback" 
                                name="feedback" rows="5">{{ old('feedback', $submission->feedback ?? '') }}</textarea>
                            @error('feedback')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Lưu điểm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 