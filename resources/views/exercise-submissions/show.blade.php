@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>{{ __('Chi tiết bài tập đã nộp') }}</div>
                    <a href="{{ route('exercise-submissions.index') }}" class="btn btn-sm btn-secondary">Quay lại</a>
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
                                <p><strong>Khóa học:</strong> {{ $submission->exercise->course->name }}</p>
                                <p><strong>Ngày nộp:</strong> {{ $submission->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <strong>Trạng thái:</strong> 
                                    @if ($submission->is_graded)
                                        <span class="badge bg-success">Đã chấm</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Đang chờ</span>
                                    @endif
                                </p>
                                @if ($submission->is_graded)
                                <p><strong>Điểm:</strong> {{ $submission->score }}/{{ $submission->exercise->max_score }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Mô tả bài tập:</h5>
                        <div class="p-3 bg-light rounded">
                            {!! $submission->exercise->description !!}
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Bài làm của bạn:</h5>
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

                    @if ($submission->is_graded)
                    <div class="mb-4">
                        <h5>Nhận xét từ giáo viên:</h5>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($submission->feedback)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 