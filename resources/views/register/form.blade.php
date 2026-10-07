<x-layout title="Daftar - {{ config('app.name', 'Laravel') }}">
    <style>
        .auth-card {
            max-width: 28rem;
            margin-inline: auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .auth-title {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            color: #111827;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
        }

        .form-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            color: #1f2937;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-input:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px #bfdbfe;
        }

        .btn-primary {
            display: block;
            width: 100%;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-align: center;
            color: #ffffff;
            background-color: #2563eb;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .auth-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: #4b5563;
        }

        .auth-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .error-message {
            margin-top: 0.25rem;
            font-size: 0.75rem;
            color: #dc2626;
        }
    </style>

    <div class="auth-card">
        <h1 class="auth-title">Daftar Akun Baru</h1>

        <!-- Placeholder Action Route Form -->
        <form action="{{ route('register.store') }}" method="POST">
            <!-- CSRF Token Laravel -->
            @csrf

            <!-- Kolom Nama -->
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="form-input"
                    placeholder="Masukkan nama lengkap Anda" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Email -->
            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="nama@example.com"
                    value="{{ old('email') }}" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Password -->
            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="••••••••"
                    required>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kolom Konfirmasi Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                    placeholder="••••••••" required>
            </div>

            <!-- Tombol Daftar -->
            <div class="form-group" style="margin-top: 1.5rem;">
                <button type="submit" class="btn-primary">
                    Daftar
                </button>
            </div>
        </form>

        <!-- Tautan ke Halaman Login -->
        <div class="auth-footer">
            Sudah memiliki akun?
            <!-- Placeholder Tautan Login -->
            <a href="{{ route('login.form') }}" class="auth-link">Masuk di sini</a>
        </div>
    </div>
</x-layout>
