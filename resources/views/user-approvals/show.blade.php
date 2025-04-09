@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết yêu cầu đăng ký khóa học') }}</span>
                    <a href="{{ route('user-approvals.index') }}" class="btn btn-sm btn-secondary">{{ __('Quay lại danh sách') }}</a>
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

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin học viên') }}</h5>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Tên học viên:') }}</div>
                            <div class="col-md-8">{{ $approval->user->name }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Email:') }}</div>
                            <div class="col-md-8">{{ $approval->user->email }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Ngày tham gia:') }}</div>
                            <div class="col-md-8">{{ $approval->user->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin khóa học') }}</h5>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Tên khóa học:') }}</div>
                            <div class="col-md-8">{{ $approval->course->name }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Mô tả:') }}</div>
                            <div class="col-md-8">{{ $approval->course->description }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Người quản lý:') }}</div>
                            <div class="col-md-8">{{ $approval->course->contentUser->name }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">{{ __('Thông tin yêu cầu') }}</h5>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Trạng thái:') }}</div>
                            <div class="col-md-8">
                                @if($approval->isPending())
                                    <span class="badge bg-warning">{{ __('Đang chờ duyệt') }}</span>
                                @elseif($approval->isApproved())
                                    <span class="badge bg-success">{{ __('Đã duyệt') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Đã từ chối') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Ngày yêu cầu:') }}</div>
                            <div class="col-md-8">{{ $approval->created_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                        
                        @if($approval->reviewed_at)
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Ngày duyệt:') }}</div>
                            <div class="col-md-8">{{ $approval->reviewed_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Người duyệt:') }}</div>
                            <div class="col-md-8">{{ $approval->reviewer ? $approval->reviewer->name : 'N/A' }}</div>
                        </div>
                        @endif
                        
                        @if($approval->comment)
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">{{ __('Ghi chú:') }}</div>
                            <div class="col-md-8">{{ $approval->comment }}</div>
                        </div>
                        @endif
                    </div>

                    @if($approval->isPending())
                    <div class="d-flex justify-content-center mt-4">
                        <button type="button" class="btn btn-success me-3" data-bs-toggle="modal" data-bs-target="#approveModal">
                            {{ __('Duyệt yêu cầu') }}
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            {{ __('Từ chối yêu cầu') }}
                        </button>
                    </div>

                    <!-- Approve Modal -->
                    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('user-approvals.approve', $approval) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="approveModalLabel">{{ __('Duyệt yêu cầu đăng ký khóa học') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{{ __('Bạn có chắc chắn muốn duyệt yêu cầu đăng ký khóa học') }} <strong>{{ $approval->course->name }}</strong> của học viên <strong>{{ $approval->user->name }}</strong>?</p>
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
                    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('user-approvals.reject', $approval) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectModalLabel">{{ __('Từ chối yêu cầu đăng ký khóa học') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{{ __('Bạn có chắc chắn muốn từ chối yêu cầu đăng ký khóa học') }} <strong>{{ $approval->course->name }}</strong> của học viên <strong>{{ $approval->user->name }}</strong>?</p>
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
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 