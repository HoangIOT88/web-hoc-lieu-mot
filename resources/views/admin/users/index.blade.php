@extends('layouts.app')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Quản lý người dùng') }}</span>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Thêm mới') }}
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, email..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="role" class="form-select">
                                    <option value="">-- Tất cả vai trò --</option>
                                    <option value="{{ \App\Models\User::ROLE_ADMIN }}" {{ request('role') == \App\Models\User::ROLE_ADMIN ? 'selected' : '' }}>Admin</option>
                                    <option value="{{ \App\Models\User::ROLE_CONTENT_USER }}" {{ request('role') == \App\Models\User::ROLE_CONTENT_USER ? 'selected' : '' }}>Content User</option>
                                    <option value="{{ \App\Models\User::ROLE_USER }}" {{ request('role') == \App\Models\User::ROLE_USER ? 'selected' : '' }}>Regular User</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-search"></i> {{ __('Tìm kiếm') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="20%">Tên</th>
                                    <th width="25%">Email</th>
                                    <th width="15%">Vai trò</th>
                                    <th width="15%">Ngày tạo</th>
                                    <th width="20%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if ($user->isAdmin())
                                                <span class="badge bg-danger">Admin</span>
                                            @elseif ($user->isContentUser())
                                                <span class="badge bg-primary">Content User</span>
                                            @else
                                                <span class="badge bg-success">Regular User</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> {{ __('Sửa') }}
                                            </a>
                                            
                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                                        <i class="fas fa-trash"></i> {{ __('Xóa') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 