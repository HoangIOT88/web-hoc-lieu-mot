@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $lecture->title }}</span>
                    <div>
                        <a href="{{ route('lectures.index', $course) }}" class="btn btn-primary btn-sm">{{ __('Quay lại danh sách') }}</a>
                        @if(Auth::user()->isContentUser() && $course->content_user_id == Auth::id() || Auth::user()->isAdmin())
                            <a href="{{ route('lectures.edit', [$course, $lecture]) }}" class="btn btn-warning btn-sm">{{ __('Sửa') }}</a>
                            <form action="{{ route('lectures.destroy', [$course, $lecture]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Bạn có chắc chắn muốn xóa bài giảng này?') }}')">{{ __('Xóa') }}</button>
                            </form>
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

                    <h5 class="mb-3">{{ __('Thông tin bài giảng') }}</h5>
                    <dl class="row">
                        <dt class="col-sm-3">{{ __('Khóa học') }}</dt>
                        <dd class="col-sm-9">{{ $course->name }}</dd>
                        
                        <dt class="col-sm-3">{{ __('Tiêu đề') }}</dt>
                        <dd class="col-sm-9">{{ $lecture->title }}</dd>
                        
                        <dt class="col-sm-3">{{ __('Mô tả') }}</dt>
                        <dd class="col-sm-9">{{ $lecture->description ?? __('Không có mô tả') }}</dd>
                        
                        <dt class="col-sm-3">{{ __('Ngày đăng tải') }}</dt>
                        <dd class="col-sm-9">{{ $lecture->uploaded_at->format('d/m/Y H:i:s') }}</dd>
                    </dl>
                    
                    <div class="mt-4">
                        <h5 class="mb-3">{{ __('Xem tài liệu') }}</h5>
                        
                        <a href="{{ Storage::url($lecture->file_url) }}" target="_blank" class="btn btn-success">
                            <i class="fa fa-download"></i> {{ __('Xem/Tải xuống bài giảng') }}
                        </a>
                        
                        @php
                            $extension = pathinfo(Storage::url($lecture->file_url), PATHINFO_EXTENSION);
                        @endphp
                        
                        @if (in_array($extension, ['pdf']))
                            <div class="mt-3">
                                <div class="ratio ratio-16x9">
                                    <embed src="{{ Storage::url($lecture->file_url) }}" type="application/pdf" width="100%" height="600px" />
                                </div>
                            </div>
                        @elseif (in_array($extension, ['mp4']))
                            <div class="mt-3">
                                <div class="ratio ratio-16x9">
                                    <video controls>
                                        <source src="{{ Storage::url($lecture->file_url) }}" type="video/mp4">
                                        {{ __('Trình duyệt của bạn không hỗ trợ thẻ video.') }}
                                    </video>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 alert alert-info">
                                {{ __('Bài giảng này cần tải xuống để xem.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 