@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Tất cả bài giảng') }}</span>
                    <div>
                        @if(Auth::user()->isContentUser() || Auth::user()->isAdmin())
                            <div class="dropdown">
                                <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('Thêm bài giảng mới') }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @if(Auth::user()->isAdmin())
                                        @foreach(App\Models\Course::all() as $course)
                                            <li><a class="dropdown-item" href="{{ route('courses.lectures.create', $course) }}">{{ $course->name }}</a></li>
                                        @endforeach
                                    @else
                                        @foreach(App\Models\Course::where('content_user_id', Auth::id())->get() as $course)
                                            <li><a class="dropdown-item" href="{{ route('courses.lectures.create', $course) }}">{{ $course->name }}</a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
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
                                        <th>{{ __('Khóa học') }}</th>
                                        <th>{{ __('Mô tả') }}</th>
                                        <th>{{ __('Ngày đăng tải') }}</th>
                                        <th>{{ __('Hành động') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lectures as $lecture)
                                        <tr>
                                            <td>{{ $lecture->title }}</td>
                                            <td>{{ $lecture->course->name }}</td>
                                            <td>{{ Str::limit($lecture->description, 50) }}</td>
                                            <td>{{ $lecture->uploaded_at->format('d/m/Y H:i:s') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('courses.lectures.show', [$lecture->course, $lecture]) }}" class="btn btn-primary btn-sm">{{ __('Xem') }}</a>
                                                    @if(Auth::user()->isContentUser() && $lecture->course->content_user_id == Auth::id() || Auth::user()->isAdmin())
                                                        <a href="{{ route('courses.lectures.edit', [$lecture->course, $lecture]) }}" class="btn btn-warning btn-sm">{{ __('Sửa') }}</a>
                                                        <form action="{{ route('courses.lectures.destroy', [$lecture->course, $lecture]) }}" method="POST" class="d-inline">
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
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $lectures->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 