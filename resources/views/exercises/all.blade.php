@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Tất cả bài tập') }}</span>
                    @if(Auth::user()->isContentUser() || Auth::user()->isAdmin())
                        <a href="{{ route('exercises.create') }}" class="btn btn-primary btn-sm">{{ __('Tạo bài tập mới') }}</a>
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

                    @if ($exercises->isEmpty())
                        <div class="alert alert-info" role="alert">
                            {{ __('Không có bài tập nào.') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Tiêu đề') }}</th>
                                        <th>{{ __('Khóa học') }}</th>
                                        <th>{{ __('Hạn nộp') }}</th>
                                        <th>{{ __('Đã nộp') }}</th>
                                        <th>{{ __('Hành động') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($exercises as $exercise)
                                        <tr>
                                            <td>{{ $exercise->title }}</td>
                                            <td>{{ $exercise->course->name }}</td>
                                            <td>{{ $exercise->deadline ? $exercise->deadline->format('d/m/Y H:i') : 'Không có hạn nộp' }}</td>
                                            <td>
                                                @if(Auth::user()->hasSubmittedExercise($exercise->id))
                                                    <span class="badge bg-success">Đã nộp</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Chưa nộp</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('exercises.show', $exercise->id) }}" class="btn btn-primary btn-sm">{{ __('Xem') }}</a>
                                                    
                                                    @if(Auth::user()->isContentUser() && $exercise->course->content_user_id == Auth::id() || Auth::user()->isAdmin())
                                                        <a href="{{ route('exercises.edit', $exercise->id) }}" class="btn btn-warning btn-sm">{{ __('Sửa') }}</a>
                                                        <form action="{{ route('exercises.destroy', $exercise->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Bạn có chắc chắn muốn xóa bài tập này?') }}')">{{ __('Xóa') }}</button>
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
                            {{ $exercises->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 