@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class="mb-4">{{ __('Bảng điều khiển Admin') }}</h1>
            
            <div class="row">
                <!-- Thống kê người dùng -->
                <div class="col-md-4 mb-4">
                    <div class="card border-primary h-100">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-users me-2"></i>{{ __('Quản lý người dùng') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Quản lý toàn bộ người dùng') }}</h5>
                            <p class="card-text">{{ __('Thêm, sửa, xóa và quản lý thông tin người dùng hệ thống.') }}</p>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary mt-2">
                                {{ __('Quản lý người dùng') }} <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Báo cáo và thống kê -->
                <div class="col-md-4 mb-4">
                    <div class="card border-success h-100">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-chart-bar me-2"></i>{{ __('Báo cáo & Thống kê') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Phân tích dữ liệu hệ thống') }}</h5>
                            <p class="card-text">{{ __('Xem thống kê về người dùng, khóa học, và hoạt động hệ thống.') }}</p>
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-success mt-2">
                                {{ __('Xem báo cáo') }} <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Phê duyệt người dùng -->
                <div class="col-md-4 mb-4">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-warning text-dark">
                            <i class="fas fa-user-check me-2"></i>{{ __('Phê duyệt người dùng') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Phê duyệt đăng ký người dùng') }}</h5>
                            <p class="card-text">{{ __('Xem và duyệt yêu cầu đăng ký từ người dùng mới.') }}</p>
                            <a href="{{ route('user-approvals.index') }}" class="btn btn-outline-warning mt-2">
                                {{ __('Phê duyệt người dùng') }} <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <!-- Quản lý khóa học -->
                <div class="col-md-6 mb-4">
                    <div class="card border-info h-100">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-book me-2"></i>{{ __('Quản lý khóa học') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Quản lý các khóa học') }}</h5>
                            <p class="card-text">{{ __('Xem, thêm, sửa, xóa và quản lý tất cả khóa học trong hệ thống.') }}</p>
                            <div class="mt-3">
                                <a href="{{ route('courses.index') }}" class="btn btn-outline-info me-2">
                                    {{ __('Danh sách khóa học') }}
                                </a>
                                <a href="{{ route('courses.create') }}" class="btn btn-outline-info">
                                    {{ __('Tạo khóa học mới') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quản lý chat nhóm -->
                <div class="col-md-6 mb-4">
                    <div class="card border-secondary h-100">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-comments me-2"></i>{{ __('Quản lý chat nhóm') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Quản lý các nhóm chat') }}</h5>
                            <p class="card-text">{{ __('Xem, thêm, sửa, xóa và quản lý tất cả nhóm chat trong hệ thống.') }}</p>
                            <div class="mt-3">
                                <a href="{{ route('chat-groups.index') }}" class="btn btn-outline-secondary me-2">
                                    {{ __('Danh sách nhóm chat') }}
                                </a>
                                <a href="{{ route('chat-groups.create') }}" class="btn btn-outline-secondary">
                                    {{ __('Tạo nhóm chat mới') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection 