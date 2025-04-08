@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $course->name }}</span>
                    <div>
                        @if (Auth::check())
                            @php
                                $isRegistered = Auth::user()->registeredCourses()->where('courses.id', $course->id)->exists();
                            @endphp
                            
                            @if ($isRegistered)
                                <span class="badge bg-success me-2">{{ __('Đã đăng ký') }}</span>
                                <a href="{{ route('courses.exercises', $course) }}" class="btn btn-primary btn-sm">{{ __('Xem bài tập') }}</a>
                            @else
                                <form action="{{ route('course-registrations.register', $course) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">{{ __('Đăng ký khóa học') }}</button>
                                </form>
                            @endif
                            
                            @if (Auth::user()->isContentUser() && $course->content_user_id == Auth::id() || Auth::user()->isAdmin())
                                <a href="{{ route('courses.edit', $course) }}" class="btn btn-warning btn-sm ms-2">{{ __('Sửa') }}</a>
                                <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm ms-1" onclick="return confirm('{{ __('Bạn có chắc chắn muốn xóa khóa học này?') }}')">{{ __('Xóa') }}</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-success btn-sm">{{ __('Đăng nhập để đăng ký') }}</a>
                        @endif
                    </div>
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
                    
                    @if (session('warning'))
                        <div class="alert alert-warning" role="alert">
                            {{ session('warning') }}
                        </div>
                    @endif

                    <h5>{{ __('Thông tin khóa học') }}</h5>
                    <dl class="row">
                        <dt class="col-sm-3">{{ __('Tên khóa học') }}</dt>
                        <dd class="col-sm-9">{{ $course->name }}</dd>
                        
                        <dt class="col-sm-3">{{ __('Mô tả') }}</dt>
                        <dd class="col-sm-9">{{ $course->description }}</dd>
                        
                        <dt class="col-sm-3">{{ __('Thời lượng') }}</dt>
                        <dd class="col-sm-9">{{ $course->duration }}</dd>
                        
                        <dt class="col-sm-3">{{ __('Giảng viên') }}</dt>
                        <dd class="col-sm-9">{{ $course->contentUser->name }}</dd>
                    </dl>
                    
                    @if (Auth::check() && ($isRegistered ?? false))
                        <div class="mt-4">
                            <h5>{{ __('Bài giảng') }}</h5>
                            @if ($course->lectures->isEmpty())
                                <p class="text-muted">{{ __('Chưa có bài giảng nào.') }}</p>
                            @else
                                <ul class="list-group">
                                    @foreach ($course->lectures as $lecture)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $lecture->title }}</h6>
                                                <small class="text-muted">{{ __('Đăng tải:') }} {{ $lecture->uploaded_at->format('d/m/Y') }}</small>
                                            </div>
                                            <a href="{{ Storage::url($lecture->file_url) }}" class="btn btn-outline-primary btn-sm" target="_blank">{{ __('Xem') }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 