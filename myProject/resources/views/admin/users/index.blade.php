@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">
                            <i class="bi bi-people me-2"></i>User Management
                        </h2>
                        <p class="text-muted mb-0">Manage user accounts and permissions</p>
                    </div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i>Add New User
                    </a>
                </div>

                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search Users</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search by name or email...">
                            </div>
                            <div class="col-md-4">
                                <label for="role" class="form-label">Filter by Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="">All Roles</option>
                                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        @if($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Posts</th>
                                            <th>Joined</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle me-2">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold">{{ $user->name }}</div>
                                                            @if($user->id === auth()->id())
                                                                <small class="text-muted">(You)</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                                        <i class="bi bi-{{ $user->role === 'admin' ? 'shield-check' : 'person' }} me-1"></i>
                                                        {{ ucfirst($user->role) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $user->posts_count }} posts</span>
                                                </td>
                                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                                </a>
                                                            </li>
                                                            @if($user->id !== auth()->id())
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" 
                                                                          onsubmit="return confirm('Are you sure you want to delete this user? This will also delete all their posts.')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item text-danger">
                                                                            <i class="bi bi-trash me-2"></i>Delete
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center">
                                {{ $users->appends(request()->query())->links('pagination::bootstrap-4', ['class' => 'pagination-sm']) }}
                            </div>
                            
                            @if($users->hasPages())
                                <div class="text-center mt-2">
                                    <small class="text-muted">
                                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                                    </small>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-people display-1 text-muted"></i>
                                <h4 class="text-muted mt-3">No users found</h4>
                                <p class="text-muted">
                                    @if(request('search') || request('role'))
                                        Try adjusting your search criteria.
                                    @else
                                        Start by creating your first user.
                                    @endif
                                </p>
                                @if(!request('search') && !request('role'))
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                        <i class="bi bi-person-plus me-1"></i>Create User
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
    </style>
@endsection
