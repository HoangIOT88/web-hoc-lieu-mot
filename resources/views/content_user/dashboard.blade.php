@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class="mb-4">{{ __('Bảng điều khiển Quản trị nội dung') }}</h1>
            
            <div class="row">
                <!-- Quản lý khóa học -->
                <div class="col-md-4 mb-4">
                    <div class="card border-primary h-100">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-book me-2"></i>{{ __('Quản lý khóa học') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Quản lý khóa học của bạn') }}</h5>
                            <p class="card-text">{{ __('Xem, thêm, sửa, xóa và quản lý khóa học mà bạn phụ trách.') }}</p>
                            <div class="mt-2">
                                <a href="{{ route('courses.index') }}" class="btn btn-outline-primary me-2">
                                    {{ __('Danh sách khóa học') }}
                                </a>
                                <a href="{{ route('courses.create') }}" class="btn btn-outline-primary">
                                    {{ __('Tạo khóa học mới') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quản lý bài giảng -->
                <div class="col-md-4 mb-4">
                    <div class="card border-success h-100">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-file-alt me-2"></i>{{ __('Quản lý bài giảng') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Quản lý nội dung bài giảng') }}</h5>
                            <p class="card-text">{{ __('Xem, thêm, sửa, xóa và quản lý bài giảng trong khóa học của bạn.') }}</p>
                            <a href="{{ route('lectures.index') }}" class="btn btn-outline-success mt-2">
                                {{ __('Quản lý bài giảng') }} <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Quản lý đăng ký khóa học -->
                <div class="col-md-4 mb-4">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-warning text-dark">
                            <i class="fas fa-user-graduate me-2"></i>{{ __('Đăng ký khóa học') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Quản lý đăng ký khóa học') }}</h5>
                            <p class="card-text">{{ __('Xem và duyệt yêu cầu đăng ký khóa học từ học viên.') }}</p>
                            <a href="{{ route('course-registrations.index') }}" class="btn btn-outline-warning mt-2">
                                {{ __('Quản lý đăng ký') }} <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <!-- Quản lý bài tập -->
                <div class="col-md-6 mb-4">
                    <div class="card border-info h-100">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-tasks me-2"></i>{{ __('Quản lý bài tập') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Quản lý bài tập và bài nộp') }}</h5>
                            <p class="card-text">{{ __('Xem, thêm, sửa, xóa bài tập và chấm điểm bài nộp của học viên.') }}</p>
                            <div class="mt-3">
                                <a href="{{ route('exercises.index') }}" class="btn btn-outline-info me-2">
                                    {{ __('Danh sách bài tập') }}
                                </a>
                                <a href="{{ route('exercise-submissions.index') }}" class="btn btn-outline-info">
                                    {{ __('Bài nộp của học viên') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quản lý nhóm chat -->
                <div class="col-md-6 mb-4">
                    <div class="card border-secondary h-100">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-comments me-2"></i>{{ __('Nhóm chat khóa học') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Quản lý chat nhóm khóa học') }}</h5>
                            <p class="card-text">{{ __('Xem, tạo và quản lý các nhóm chat cho khóa học của bạn.') }}</p>
                            <div class="mt-3">
                                <a href="{{ route('chat-groups.index') }}" class="btn btn-outline-secondary me-2">
                                    {{ __('Danh sách nhóm chat') }}
                                </a>
                                <a href="{{ route('chat-groups.create') }}" class="btn btn-outline-secondary">
                                    {{ __('Tạo nhóm chat mới') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection 