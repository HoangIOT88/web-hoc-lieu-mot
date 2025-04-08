@extends('layouts.app')

@section('title', __('Bài tập') . ' - ' . $course->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Bài tập cho khóa học:') }} {{ $course->name }}</h5>
                    <div>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> {{ __('Quay lại khóa học') }}
                        </a>
                        @if(Auth::user()->is_content_user && $course->content_user_id == Auth::id() || Auth::user()->is_admin)
                            <a href="#" class="btn btn-primary btn-sm ms-2">
                                <i class="fas fa-plus me-1"></i> {{ __('Thêm bài tập mới') }}
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($exercises->isEmpty())
                        <div class="alert alert-info">
                            {{ __('Chưa có bài tập nào được tạo cho khóa học này.') }}
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($exercises as $exercise)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $exercise->title }}</h5>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            @if($exercise->deadline)
                                                {{ __('Hạn nộp:') }} {{ $exercise->deadline->format('d/m/Y H:i') }}
                                                @if($exercise->deadline->isPast())
                                                    <span class="badge bg-danger ms-1">{{ __('Đã quá hạn') }}</span>
                                                @elseif($exercise->deadline->diffInDays(now()) <= 3)
                                                    <span class="badge bg-warning ms-1">{{ __('Sắp hết hạn') }}</span>
                                                @endif
                                            @else
                                                {{ __('Không có hạn nộp') }}
                                            @endif
                                        </small>
                                    </div>
                                    
                                    <p class="mb-1">{{ Str::limit($exercise->content, 150) }}</p>
                                    
                                    @php
                                        $submission = Auth::user()->exerciseAnswers()
                                            ->where('exercise_id', $exercise->id)
                                            ->first();
                                    @endphp
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div>
                                            @if($submission)
                                                @if($submission->is_correct !== null)
                                                    <span class="badge bg-success me-2">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        {{ __('Đã nộp & chấm điểm:') }} 
                                                        @if($submission->is_correct)
                                                            {{ __('Đúng') }}
                                                        @else
                                                            {{ __('Chưa đúng') }}
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="badge bg-info me-2">
                                                        <i class="fas fa-paper-plane me-1"></i>
                                                        {{ __('Đã nộp:') }} {{ $submission->submitted_at->format('d/m/Y H:i') }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        
                                        <a href="{{ route('exercises.show', $exercise) }}" class="btn btn-sm btn-primary">
                                            @if($submission)
                                                <i class="fas fa-edit me-1"></i> {{ __('Xem/Chỉnh sửa bài nộp') }}
                                            @else
                                                <i class="fas fa-paper-plane me-1"></i> {{ __('Làm bài') }}
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 