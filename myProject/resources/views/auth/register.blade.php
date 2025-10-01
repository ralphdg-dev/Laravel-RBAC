<x-guest-layout>
    <div class="text-center mb-4">
        <h1 class="h4 fw-semibold mb-2">Create Account</h1>
        <p class="text-muted mb-0">Join us today</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" 
                   class="form-control @error('name') is-invalid @enderror" 
                   id="name" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Enter your full name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username"
                   placeholder="Enter your email">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   id="password" 
                   name="password" 
                   required 
                   autocomplete="new-password"
                   placeholder="Create a password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" 
                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   placeholder="Confirm your password">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Role Selection -->
        <div class="mb-4">
            <label for="role" class="form-label">Account Type</label>
            <select name="role" 
                    id="role" 
                    class="form-select @error('role') is-invalid @enderror" 
                    required>
                <option value="">Choose account type</option>
                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                    <i class="bi bi-person"></i> User
                </option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                    <i class="bi bi-shield-check"></i> Admin
                </option>
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>Create Account
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <span class="text-muted">Already have an account?</span>
            <a href="{{ route('login') }}" class="auth-link ms-1">Sign in</a>
        </div>
    </form>
</x-guest-layout>