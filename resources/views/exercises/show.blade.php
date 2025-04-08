@extends('layouts.app')

@section('title', $exercise->title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $exercise->title }}</h5>
                    <div>
                        <a href="{{ route('courses.exercises', $course) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-1"></i> {{ __('Danh sách bài tập') }}
                        </a>
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
                    
                    <div class="mb-4">
                        <h6>{{ __('Thông tin bài tập') }}</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>
                                <strong>{{ __('Khóa học:') }}</strong> 
                                <a href="{{ route('courses.show', $course) }}">{{ $course->name }}</a>
                            </span>
                            @if($exercise->deadline)
                                <span class="text-muted">
                                    <i class="fas fa-clock me-1"></i> 
                                    {{ __('Hạn nộp:') }} {{ $exercise->deadline->format('d/m/Y H:i') }}
                                    @if($exercise->deadline->isPast())
                                        <span class="badge bg-danger ms-1">{{ __('Đã quá hạn') }}</span>
                                    @elseif($exercise->deadline->diffInDays(now()) <= 3)
                                        <span class="badge bg-warning ms-1">{{ __('Sắp hết hạn') }}</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-muted">
                                    <i class="fas fa-clock me-1"></i> {{ __('Không có hạn nộp') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">{{ __('Nội dung bài tập') }}</h6>
                            </div>
                            <div class="card-body">
                                {!! nl2br(e($exercise->content)) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6>{{ __('Bài làm của bạn') }}</h6>
                        
                        @if($submission && $submission->is_correct !== null)
                            <div class="alert alert-info mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ __('Kết quả đánh giá:') }} 
                                        @if($submission->is_correct)
                                            <strong class="text-success">{{ __('Đúng') }}</strong>
                                        @else
                                            <strong class="text-danger">{{ __('Chưa đúng') }}</strong>
                                        @endif
                                        @if($submission->feedback)
                                            <div class="mt-2">
                                                <strong>{{ __('Feedback:') }}</strong> {{ $submission->feedback }}
                                            </div>
                                        @endif
                                    </span>
                                    <span class="text-muted small">
                                        {{ $submission->graded_at ? $submission->graded_at->format('d/m/Y H:i') : '' }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        
                        <form action="{{ route('exercises.submit', $exercise) }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <textarea class="form-control" id="answer_content" name="answer_content" 
                                        rows="8" placeholder="{{ __('Nhập bài làm của bạn ở đây...') }}" 
                                        {{ ($exercise->deadline && $exercise->deadline->isPast() && !$submission) ? 'disabled' : '' }}>{{ $submission ? $submission->answer_content : '' }}</textarea>
                                @error('answer_content')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    @if($submission)
                                        <span class="text-muted">
                                            {{ __('Đã nộp:') }} {{ $submission->submitted_at->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-primary" 
                                    {{ ($exercise->deadline && $exercise->deadline->isPast() && !$submission) ? 'disabled' : '' }}>
                                    <i class="fas fa-paper-plane me-1"></i>
                                    {{ $submission ? __('Cập nhật bài làm') : __('Nộp bài') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 