@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Bài giảng - ') }} {{ $course->name }}</span>
                    <div>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">{{ __('Quay lại khóa học') }}</a>
                        @if(Auth::user()->isContentUser() && $course->content_user_id == Auth::id() || Auth::user()->isAdmin())
                            <a href="{{ route('lectures.create', $course) }}" class="btn btn-success btn-sm">{{ __('Tạo bài giảng mới') }}</a>
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

                    @if ($lectures->isEmpty())
                        <div class="alert alert-info" role="alert">
                            {{ __('Không có bài giảng nào.') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Tiêu đề') }}</th>
                                        <th>{{ __('Mô tả') }}</th>
                                        <th>{{ __('Ngày đăng tải') }}</th>
                                        <th>{{ __('Hành động') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lectures as $lecture)
                                        <tr>
                                            <td>{{ $lecture->title }}</td>
                                            <td>{{ Str::limit($lecture->description, 50) }}</td>
                                            <td>{{ $lecture->uploaded_at->format('d/m/Y H:i:s') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('lectures.show', [$course, $lecture]) }}" class="btn btn-primary btn-sm">{{ __('Xem') }}</a>
                                                    @if(Auth::user()->isContentUser() && $course->content_user_id == Auth::id() || Auth::user()->isAdmin())
                                                        <a href="{{ route('lectures.edit', [$course, $lecture]) }}" class="btn btn-warning btn-sm">{{ __('Sửa') }}</a>
                                                        <form action="{{ route('lectures.destroy', [$course, $lecture]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Bạn có chắc chắn muốn xóa bài giảng này?') }}')">{{ __('Xóa') }}</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 