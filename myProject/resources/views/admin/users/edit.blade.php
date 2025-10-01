@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="mb-0">
                            <i class="bi bi-person-gear me-2"></i>Edit User
                        </h2>
                        <p class="text-muted mb-0">Update user information and permissions</p>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                            <div class="avatar-circle me-3">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <p class="text-muted mb-0">{{ $user->email }}</p>
                                <small class="text-muted">Member since {{ $user->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.users.update', $user) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $user->name) }}" 
                                               required 
                                               autofocus
                                               placeholder="Enter full name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               required
                                               placeholder="Enter email address">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password"
                                               placeholder="Leave blank to keep current password">
                                        <div class="form-text">Leave blank if you don't want to change the password</div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <input type="password" 
                                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                                               id="password_confirmation" 
                                               name="password_confirmation"
                                               placeholder="Confirm new password">
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="role" class="form-label">User Role</label>
                                <select name="role" 
                                        id="role" 
                                        class="form-select @error('role') is-invalid @enderror" 
                                        required
                                        @if($user->id === auth()->id()) disabled title="You cannot change your own role" @endif>
                                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                        User - Regular user with limited permissions
                                    </option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                        Admin - Full administrative access
                                    </option>
                                </select>
                                @if($user->id === auth()->id())
                                    <div class="form-text text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        You cannot change your own role for security reasons.
                                    </div>
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                @endif
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- User Statistics -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart me-2"></i>User Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="bi bi-file-text display-4 text-primary"></i>
                                    <h4 class="mt-2">{{ $user->posts()->count() }}</h4>
                                    <p class="text-muted mb-0">Total Posts</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="bi bi-check-circle display-4 text-success"></i>
                                    <h4 class="mt-2">{{ $user->posts()->where('status', 'approved')->count() }}</h4>
                                    <p class="text-muted mb-0">Approved Posts</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="bi bi-chat-dots display-4 text-info"></i>
                                    <h4 class="mt-2">{{ $user->comments()->count() }}</h4>
                                    <p class="text-muted mb-0">Comments</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 24px;
        }
    </style>
@endsection
