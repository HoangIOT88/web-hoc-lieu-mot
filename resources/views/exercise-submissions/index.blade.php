@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Bài tập đã nộp') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Bài tập</th>
                                <th>Đã nộp lúc</th>
                                <th>Điểm</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($submissions as $submission)
                                <tr>
                                    <td>{{ $submission->exercise->title }}</td>
                                    <td>{{ $submission->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($submission->is_graded)
                                            {{ $submission->score }}/{{ $submission->exercise->max_score }}
                                        @else
                                            <span class="badge bg-secondary">Chưa chấm</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($submission->is_graded)
                                            <span class="badge bg-success">Đã chấm</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Đang chờ</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('exercise-submissions.show', $submission->id) }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Bạn chưa nộp bài tập nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="mt-3">
                        {{ $submissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 