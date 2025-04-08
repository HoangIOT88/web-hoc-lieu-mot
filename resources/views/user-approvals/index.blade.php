@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Duyệt người dùng') }}</div>

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

                    @if ($pendingUsers->isEmpty())
                        <div class="alert alert-info" role="alert">
                            {{ __('Không có người dùng nào cần duyệt.') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Tên') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Ngày đăng ký') }}</th>
                                        <th>{{ __('Hành động') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingUsers as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#approveModal{{ $user->id }}">
                                                        {{ __('Duyệt') }}
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $user->id }}">
                                                        {{ __('Từ chối') }}
                                                    </button>
                                                </div>
                                                
                                                <!-- Approve Modal -->
                                                <div class="modal fade" id="approveModal{{ $user->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $user->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('user-approvals.approve', $user) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="approveModalLabel{{ $user->id }}">{{ __('Duyệt người dùng') }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>{{ __('Bạn có chắc chắn muốn duyệt người dùng') }} <strong>{{ $user->name }}</strong>?</p>
                                                                    <div class="mb-3">
                                                                        <label for="comment" class="form-label">{{ __('Ghi chú (tùy chọn)') }}</label>
                                                                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
                                                                    <button type="submit" class="btn btn-success">{{ __('Duyệt') }}</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $user->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('user-approvals.reject', $user) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="rejectModalLabel{{ $user->id }}">{{ __('Từ chối người dùng') }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>{{ __('Bạn có chắc chắn muốn từ chối người dùng') }} <strong>{{ $user->name }}</strong>?</p>
                                                                    <div class="mb-3">
                                                                        <label for="comment" class="form-label">{{ __('Lý do từ chối') }} <span class="text-danger">*</span></label>
                                                                        <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="3" required></textarea>
                                                                        @error('comment')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
                                                                    <button type="submit" class="btn btn-danger">{{ __('Từ chối') }}</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
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