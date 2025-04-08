@extends('layouts.app')

@section('title', 'Chi tiết báo cáo: ' . $report->getReportTypeName())

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Chi tiết báo cáo: ') }} {{ $report->getReportTypeName() }}</span>
                    <div>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Quay lại') }}
                        </a>
                        @if($report->file_url)
                            <a href="{{ route('admin.reports.download', $report) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> {{ __('Tải về') }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>{{ __('Thông tin báo cáo') }}</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <td>{{ $report->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Loại báo cáo') }}</th>
                                    <td>{{ $report->getReportTypeName() }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Tạo bởi') }}</th>
                                    <td>{{ $report->admin->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Thời gian tạo') }}</th>
                                    <td>{{ $report->generated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Trạng thái') }}</th>
                                    <td>
                                        @if($report->file_url)
                                            <span class="badge bg-success">{{ __('Hoàn thành') }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ __('Đang xử lý') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">{{ __('Nội dung báo cáo') }}</div>
                                <div class="card-body">
                                    @if($report->file_url)
                                        <div class="alert alert-info">
                                            {{ __('Báo cáo đã được tạo thành công và sẵn sàng để tải về.') }}
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            {{ __('Báo cáo đang được tạo. Vui lòng quay lại sau.') }}
                                        </div>
                                        
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 75%"></div>
                                        </div>
                                    @endif
                                    
                                    @if($report->report_type === \App\Models\AdminReport::TYPE_USER_STATS)
                                        <h4 class="mt-4">{{ __('Thống kê người dùng') }}</h4>
                                        <div id="userStatsChart" style="height: 350px;"></div>
                                    @elseif($report->report_type === \App\Models\AdminReport::TYPE_COURSE_STATS)
                                        <h4 class="mt-4">{{ __('Thống kê khóa học') }}</h4>
                                        <div id="courseStatsChart" style="height: 350px;"></div>
                                    @elseif($report->report_type === \App\Models\AdminReport::TYPE_ACTIVITY_STATS)
                                        <h4 class="mt-4">{{ __('Thống kê hoạt động') }}</h4>
                                        <div id="activityStatsChart" style="height: 350px;"></div>
                                    @endif
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
        @if($report->report_type === \App\Models\AdminReport::TYPE_USER_STATS)
            // Example User Stats Chart (replace with actual data when available)
            const userChart = new ApexCharts(document.querySelector("#userStatsChart"), {
                series: [{
                    name: 'Người dùng mới',
                    data: [30, 40, 45, 50, 49, 60, 70, 91, 125]
                }],
                chart: {
                    type: 'area',
                    height: 350
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
                },
                yaxis: {
                    title: {
                        text: 'Số người dùng'
                    }
                },
                stroke: {
                    curve: 'smooth'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'vertical',
                        shadeIntensity: 0.5,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                    }
                }
            });
            userChart.render();
        @elseif($report->report_type === \App\Models\AdminReport::TYPE_COURSE_STATS)
            // Example Course Stats Chart
            const courseChart = new ApexCharts(document.querySelector("#courseStatsChart"), {
                series: [{
                    name: 'Đăng ký',
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: ['Khóa 1', 'Khóa 2', 'Khóa 3', 'Khóa 4', 'Khóa 5', 'Khóa 6', 'Khóa 7', 'Khóa 8', 'Khóa 9'],
                },
                yaxis: {
                    title: {
                        text: 'Số lượng đăng ký'
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "horizontal",
                        shadeIntensity: 0.25,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 0.85,
                        opacityTo: 0.85,
                    },
                }
            });
            courseChart.render();
        @elseif($report->report_type === \App\Models\AdminReport::TYPE_ACTIVITY_STATS)
            // Example Activity Stats Chart
            const activityChart = new ApexCharts(document.querySelector("#activityStatsChart"), {
                series: [{
                    name: 'Bài nộp',
                    data: [31, 40, 28, 51, 42, 109, 100]
                }, {
                    name: 'Tin nhắn',
                    data: [11, 32, 45, 32, 34, 52, 41]
                }],
                chart: {
                    height: 350,
                    type: 'line',
                },
                stroke: {
                    width: [4, 4]
                },
                xaxis: {
                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                },
                yaxis: {
                    title: {
                        text: 'Số lượng'
                    },
                },
                legend: {
                    position: 'top'
                }
            });
            activityChart.render();
        @endif
    });
</script>
@endsection 