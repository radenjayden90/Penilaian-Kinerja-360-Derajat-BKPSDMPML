<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
        ], [
            'email.required' => 'NIP atau Email wajib diisi.',
        ]);

        $input = trim($request->input('email'));

        $employee = Employee::where('email', $input)
            ->orWhere('nip', $input)
            ->first();

        if (!$employee || !$employee->email) {
            throw ValidationException::withMessages([
                'email' => ['NIP atau Email tidak ditemukan dalam sistem.'],
            ]);
        }

        $status = Password::sendResetLink([
            'email' => $employee->email
        ]);

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', 'Tautan untuk reset password telah dikirim ke email Anda (' . $this->maskEmail($employee->email) . ').')
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }

    /**
     * Mask email address for privacy in notification message.
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) < 2) return $email;
        $name = $parts[0];
        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '*';
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', min($len - 2, 5));
        }
        return $maskedName . '@' . $parts[1];
    }
}
