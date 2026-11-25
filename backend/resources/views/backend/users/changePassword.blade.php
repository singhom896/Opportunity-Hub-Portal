@extends('backend.layouts.app')

@section('title', 'Change Password')

@section('content')
    <div class="container-fluid password-page">
        <header class="password-heading">
            <div>
                <p class="password-eyebrow mb-1">ACCOUNT SECURITY</p>
                <h1 class="password-title mb-1">Change password</h1>
                <p class="password-subtitle mb-0">Choose a new password to protect your portal account.</p>
            </div>
            <div class="password-account">
                <span>Signed in as</span>
                <strong>{{ auth()->user()->email }}</strong>
            </div>
        </header>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">Please check the password fields and try again.</div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="password-form">
            @csrf
            @method('PUT')

            <div class="password-fields">
                <div class="password-field">
                    <label for="current_password" class="form-label">Current password</label>
                    <input class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="password-field">
                    <label for="password" class="form-label">New password</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" minlength="12" maxlength="72" autocomplete="new-password" aria-describedby="password-guidance" required>
                    <div id="password-guidance" class="form-text">Use at least 12 characters. Avoid reusing your current password.</div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="password-field">
                    <label for="password_confirmation" class="form-label">Confirm new password</label>
                    <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="12" maxlength="72" autocomplete="new-password" required>
                </div>
            </div>

            <div class="password-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-shield-lock me-1" aria-hidden="true"></i>Update password</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .password-page {
        --password-ink: #14233f;
        --password-muted: #68758b;
        --password-line: #e3e8ef;
        color: var(--password-ink);
        padding: 0.5rem 0.25rem 1.5rem;
    }

    .password-heading,
    .password-actions {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
    }

    .password-heading {
        margin-bottom: 1.5rem;
    }

    .password-eyebrow {
        color: #526a91;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.08em;
    }

    .password-title {
        color: var(--color-secondary);
        font-size: 1.65rem;
        font-weight: 700;
    }

    .password-subtitle,
    .password-account span {
        color: var(--password-muted);
        font-size: 0.875rem;
    }

    .password-account {
        display: grid;
        gap: 0.2rem;
        text-align: right;
    }

    .password-account strong {
        color: var(--password-ink);
        font-size: 0.9rem;
    }

    .password-form {
        max-width: 760px;
        background: #fff;
        border: 1px solid var(--password-line);
        border-radius: 6px;
    }

    .password-fields {
        display: grid;
        gap: 1.1rem;
        padding: 1.25rem;
    }

    .password-field .form-label {
        margin-bottom: 0.35rem;
        color: var(--password-ink);
        font-size: 0.84rem;
        font-weight: 600;
    }

    .password-actions {
        justify-content: flex-end;
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--password-line);
    }

    @media (max-width: 575.98px) {
        .password-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .password-account {
            text-align: left;
        }

        .password-fields,
        .password-actions {
            padding: 1rem;
        }
    }
</style>
@endpush
