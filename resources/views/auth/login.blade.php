<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Kelulusan SKADA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>

<body>
    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <div class="login-wrapper">
        <div class="login-card">

            <div class="text-center mb-4">
                <div class="brand-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </div>
                <h5 class="fw-semibold mb-1" style="color:#1a1a2e;">Selamat Datang</h5>
                <p class="text-muted" style="font-size:13px;">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            @if ($errors->has('failed'))
                <div class="alert-custom mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $errors->first('failed') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="formLogin">
                @csrf

                <div class="mb-3 field-group">
                    <label class="form-label" style="font-size:13px; font-weight:500; color:#6b6b80;">Email</label>
                    <div class="input-icon-wrap">
                        <span class="icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </span>
                        <input type="email" name="email" class="form-control-custom" value="{{ old('email') }}"
                            placeholder="nama@email.com" required autofocus>
                    </div>
                </div>

                <div class="mb-3 field-group">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0"
                            style="font-size:13px; font-weight:500; color:#6b6b80;">Password</label>
                        <a href="#" style="font-size:12px; color:#534AB7; text-decoration:none;">Lupa
                            password?</a>
                    </div>
                    <div class="input-icon-wrap">
                        <span class="icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <input type="password" name="password" class="form-control-custom" placeholder="••••••••"
                            required>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-center gap-2 field-group">
                    <input type="checkbox" name="remember" class="form-check-input m-0" id="remember"
                        style="accent-color:#534AB7; width:15px; height:15px; cursor:pointer;">
                    <label class="form-check-label" for="remember"
                        style="font-size:13px; color:#6b6b80; cursor:pointer;">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <div class="field-group">
                    <button type="submit" class="btn-login" id="btnSubmit">Masuk</button>
                </div>
            </form>

            <div class="divider text-center">
                <p class="mb-0" style="font-size:12px; color:#a0a0b0;">
                    Dengan masuk, Anda menyetujui
                    <a href="#" style="color:#534AB7; text-decoration:none;">Syarat & Ketentuan</a> kami
                </p>
            </div>

        </div>
    </div>

    <script src="{{ asset('assets/js/login.js') }}"></script>
</body>

</html>
