@extends('layouts.app')

@section('title', 'Danh sách khóa học')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-center mb-4">{{ __('Hệ thống quản lý bài giảng') }}</h2>
            <div class="p-4 bg-light rounded shadow-sm text-center">
                <h4>{{ __('Chào mừng đến với hệ thống quản lý bài giảng trực tuyến') }}</h4>
                <p class="lead">{{ __('Nơi học tập, chia sẻ và tương tác hiệu quả') }}</p>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Danh sách khóa học') }}</span>
                    @if(Auth::check() && (Auth::user()->isContentUser() || Auth::user()->isAdmin()))
                        <a href="{{ route('courses.create') }}" class="btn btn-success btn-sm">{{ __('Tạo khóa học mới') }}</a>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($courses->isEmpty())
                        <div class="alert alert-info" role="alert">
                            {{ __('Chưa có khóa học nào.') }}
                        </div>
                    @else
                        <div class="row">
                            @foreach ($courses as $course)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $course->name }}</h5>
                                            <h6 class="card-subtitle mb-2 text-muted">{{ __('Giảng viên: ') }} {{ $course->contentUser->name }}</h6>
                                            <p class="card-text text-muted">{{ __('Thời lượng:') }} {{ $course->duration }}</p>
                                            <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">{{ __('Xem chi tiết') }}</a>
                                            
                                            @if(Auth::check())
                                                @php
                                                    $isRegistered = Auth::user()->registeredCourses()->where('courses.id', $course->id)->exists();
                                                @endphp
                                                
                                                @if (!$isRegistered)
                                                    <form action="{{ route('course-registrations.register', $course) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">{{ __('Đăng ký') }}</button>
                                                    </form>
                                                @else
                                                    <span class="badge bg-success">{{ __('Đã đăng ký') }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $courses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fa fa-book"></i> {{ __('Học viên') }}
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ __('Dành cho học viên') }}</h5>
                    <p class="card-text">{{ __('Truy cập các khóa học, làm bài tập và tham gia nhóm chat để trao đổi kiến thức') }}</p>
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">{{ __('Đăng nhập ngay') }}</a>
                    @else
                        <a href="{{ route('course-registrations.index') }}" class="btn btn-outline-primary">{{ __('Khóa học của tôi') }}</a>
                    @endguest
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fa fa-chalkboard-teacher"></i> {{ __('Giảng viên') }}
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ __('Dành cho giảng viên') }}</h5>
                    <p class="card-text">{{ __('Quản lý khóa học, tạo bài giảng, ra đề bài tập và duyệt học viên') }}</p>
                    @if(Auth::check() && Auth::user()->isContentUser())
                        <a href="{{ route('user-approvals.index') }}" class="btn btn-outline-success">{{ __('Duyệt người dùng') }}</a>
                    @else
                        <p class="text-muted">{{ __('Chỉ dành cho giảng viên') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fa fa-users"></i> {{ __('Cộng đồng') }}
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ __('Kết nối & Trao đổi') }}</h5>
                    <p class="card-text">{{ __('Tham gia các nhóm chat để trao đổi kiến thức và kết nối với cộng đồng học tập') }}</p>
                    @auth
                        <a href="{{ route('chat-groups.index') }}" class="btn btn-outline-info">{{ __('Vào nhóm chat') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-info">{{ __('Đăng nhập để tham gia') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 