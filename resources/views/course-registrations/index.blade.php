@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Khóa học của tôi') }}</div>

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

                    @if ($courses->isEmpty())
                        <div class="alert alert-info" role="alert">
                            {{ __('Bạn chưa đăng ký khóa học nào.') }}
                            <a href="{{ route('courses.index') }}" class="alert-link">{{ __('Xem danh sách khóa học') }}</a>
                        </div>
                    @else
                        <div class="row">
                            @foreach ($courses as $course)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $course->name }}</h5>
                                            <p class="card-text text-muted">{{ __('Thời lượng:') }} {{ $course->duration }}</p>
                                            <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">{{ __('Xem chi tiết') }}</a>
                                                <a href="{{ route('courses.exercises', $course) }}" class="btn btn-success btn-sm">{{ __('Bài tập') }}</a>
                                                <form action="{{ route('course-registrations.unregister', $course) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Bạn có chắc chắn muốn hủy đăng ký khóa học này?') }}')">{{ __('Hủy đăng ký') }}</button>
                                                </form>
                                            </div>
                                        </div>
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