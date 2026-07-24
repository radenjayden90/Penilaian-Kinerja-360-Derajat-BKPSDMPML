<div class="login-card" id="loginCard">
    <img src="{{ asset('images/logo-bkpsdm.png') }}" alt="Logo BKPSDM Pemalang" class="login-logo stagger-item">

    @if (session('status'))
        <div class="alert-box alert-success stagger-item">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-box alert-error stagger-item">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group stagger-item">
            <label for="login" class="form-label">NIP / Email</label>
            <div class="input-group">
                <i class="bi bi-person input-icon"></i>
                <input type="text" id="login" name="login" value="{{ old('login') }}" class="form-input" placeholder="Masukkan NIP atau Email" required autofocus>
            </div>
        </div>

        <div class="form-group stagger-item">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <i class="bi bi-lock input-icon"></i>
                <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan Password" required>
                <button type="button" class="btn-eye" onclick="togglePassword()" aria-label="Toggle password visibility">
                    <i class="bi bi-eye-slash" id="toggleIcon"></i>
                </button>
            </div>
        </div>

        <div class="form-options stagger-item">
            <label class="checkbox-label">
                <input type="checkbox" id="remember_me" name="remember" class="checkbox-input">
                Ingat sesi saya
            </label>
            @if (Route::has('password.request'))
                <a wire:navigate href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="btn-submit stagger-item" id="btn-masuk">
            <span>Masuk</span>
        </button>

        <div class="footer-text stagger-item">
            &copy; 2026 BKPSDM Kabupaten Pemalang
        </div>
    </form>
</div>

