@extends('layouts.app')

@section('title', 'Báo cáo thống kê')

@section('styles')
<style>
    .stats-card {
        transition: all 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Báo cáo thống kê') }}</div>

                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white stats-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Tổng người dùng') }}</h5>
                                    <h2 class="display-4">{{ $totalUsers }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white stats-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Tổng khóa học') }}</h5>
                                    <h2 class="display-4">{{ $totalCourses }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white stats-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Tổng đăng ký') }}</h5>
                                    <h2 class="display-4">{{ $totalRegistrations }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white stats-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Người dùng mới (30 ngày)') }}</h5>
                                    <h2 class="display-4">{{ $newUsers }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Role Distribution -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">{{ __('Phân bố vai trò người dùng') }}</div>
                                <div class="card-body">
                                    <div id="roleDistributionChart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">{{ __('Đăng ký khóa học theo tháng') }}</div>
                                <div class="card-body">
                                    <div id="registrationChart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Generate Reports Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">{{ __('Tạo báo cáo mới') }}</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ __('Báo cáo người dùng') }}</h5>
                                                    <p class="card-text">{{ __('Thống kê chi tiết về người dùng, đăng ký và hoạt động.') }}</p>
                                                    <form action="{{ route('admin.reports.user-stats') }}" method="POST">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="user-start-date" class="form-label">{{ __('Từ ngày') }}</label>
                                                            <input type="date" class="form-control" id="user-start-date" name="start_date">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="user-end-date" class="form-label">{{ __('Đến ngày') }}</label>
                                                            <input type="date" class="form-control" id="user-end-date" name="end_date">
                                                        </div>
                                                        <button type="submit" class="btn btn-primary w-100">{{ __('Tạo báo cáo') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ __('Báo cáo khóa học') }}</h5>
                                                    <p class="card-text">{{ __('Thống kê về khóa học, số lượng đăng ký, và phổ biến.') }}</p>
                                                    <form action="{{ route('admin.reports.course-stats') }}" method="POST">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="course-start-date" class="form-label">{{ __('Từ ngày') }}</label>
                                                            <input type="date" class="form-control" id="course-start-date" name="start_date">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="course-end-date" class="form-label">{{ __('Đến ngày') }}</label>
                                                            <input type="date" class="form-control" id="course-end-date" name="end_date">
                                                        </div>
                                                        <button type="submit" class="btn btn-success w-100">{{ __('Tạo báo cáo') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ __('Báo cáo hoạt động') }}</h5>
                                                    <p class="card-text">{{ __('Thống kê về hoạt động như nộp bài tập, tham gia chat.') }}</p>
                                                    <form action="{{ route('admin.reports.activity-stats') }}" method="POST">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="activity-start-date" class="form-label">{{ __('Từ ngày') }}</label>
                                                            <input type="date" class="form-control" id="activity-start-date" name="start_date">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="activity-end-date" class="form-label">{{ __('Đến ngày') }}</label>
                                                            <input type="date" class="form-control" id="activity-end-date" name="end_date">
                                                        </div>
                                                        <button type="submit" class="btn btn-info w-100">{{ __('Tạo báo cáo') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Reports -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">{{ __('Báo cáo gần đây') }}</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('ID') }}</th>
                                                    <th>{{ __('Loại báo cáo') }}</th>
                                                    <th>{{ __('Ngày tạo') }}</th>
                                                    <th>{{ __('Thao tác') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($reports as $report)
                                                    <tr>
                                                        <td>{{ $report->id }}</td>
                                                        <td>{{ $report->getReportTypeName() }}</td>
                                                        <td>{{ $report->generated_at->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i> {{ __('Xem') }}
                                                            </a>
                                                            @if($report->file_url)
                                                                <a href="{{ route('admin.reports.download', $report) }}" class="btn btn-sm btn-success">
                                                                    <i class="fas fa-download"></i> {{ __('Tải về') }}
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">{{ __('Không có báo cáo nào') }}</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Role Distribution Chart
        const roleData = @json($usersByRole);
        
        const roleSeries = roleData.map(item => item.count);
        const roleLabels = roleData.map(item => {
            switch(item.role) {
                case 'ADMIN':
                    return 'Admin';
                case 'CONTENT_USER':
                    return 'Content User';
                case 'USER':
                    return 'Regular User';
                default:
                    return item.role;
            }
        });
        
        const roleChart = new ApexCharts(document.querySelector("#roleDistributionChart"), {
            series: roleSeries,
            chart: {
                type: 'pie',
                height: 300
            },
            labels: roleLabels,
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        });
        
        roleChart.render();
        
        // Registration Chart
        const regData = @json($registrationsByMonth);
        const regCategories = regData.map(item => `${item.month}/${item.year}`);
        const regSeries = regData.map(item => item.count);
        
        const regChart = new ApexCharts(document.querySelector("#registrationChart"), {
            series: [{
                name: 'Đăng ký',
                data: regSeries
            }],
            chart: {
                type: 'bar',
                height: 300
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: regCategories,
            }
        });
        
        regChart.render();
    });
</script>
@endsection 