<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginForm extends Component
{
    public string $login = '';
    public string $password = '';
    public bool $remember = false;

    /**
     * Get the validation rules.
     */
    protected function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Custom validation messages in Indonesian matching the current system style.
     */
    protected function messages(): array
    {
        return [
            'login.required' => 'NIP atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Handle Livewire asynchronous login form submission.
     */
    public function login()
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $fieldType = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';

        $credentials = [
            $fieldType => $this->login,
            'password' => $this->password,
            'is_active' => true,
        ];

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            $this->addError('login', trans('auth.failed'));
            return;
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->login).'|'.request()->ip());
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
