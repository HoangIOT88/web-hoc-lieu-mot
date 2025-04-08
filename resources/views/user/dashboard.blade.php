@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="container">
    <h1 class="mb-4">{{ __('Bảng điều khiển') }}</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Khóa học đã đăng ký') }}</h5>
                </div>
                <div class="card-body">
                    @if ($registeredCourses->count() > 0)
                        <div class="row">
                            @foreach ($registeredCourses as $registration)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $registration->name }}</h5>
                                            <p class="card-text text-muted">{{ __('Đăng ký vào:') }} {{ \Carbon\Carbon::parse($registration->pivot->registered_at)->format('d/m/Y') }}</p>
                                            <p class="card-text">{{ Str::limit($registration->description, 100) }}</p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="{{ route('courses.show', $registration) }}" class="btn btn-sm btn-primary">{{ __('Xem chi tiết') }}</a>
                                            <a href="{{ route('courses.exercises', $registration) }}" class="btn btn-sm btn-outline-primary">{{ __('Bài tập') }}</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">{{ __('Bạn chưa đăng ký khóa học nào.') }}</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary">{{ __('Khám phá khóa học') }}</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Khóa học khuyến nghị') }}</h5>
                </div>
                <div class="card-body">
                    @if ($availableCourses->count() > 0)
                        <div class="list-group">
                            @foreach ($availableCourses->take(5) as $course)
                                <a href="{{ route('courses.show', $course) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $course->name }}</h6>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($course->description, 60) }}</p>
                                </a>
                            @endforeach
                        </div>
                        
                        @if ($availableCourses->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-primary">{{ __('Xem tất cả') }}</a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">{{ __('Không có khóa học khuyến nghị.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 