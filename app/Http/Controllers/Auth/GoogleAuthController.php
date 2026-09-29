<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect pengguna ke halaman persetujuan (OAuth consent) Google.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback OAuth 2.0 dari Google.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('login')->with('error', 'Autentikasi Google dibatalkan atau ditolak.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed: '.$e->getMessage(), [
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->with('error', 'Gagal memproses autentikasi Google. Silakan coba kembali.');
        }

        $googleEmail = Str::lower(trim((string) $googleUser->getEmail()));
        $googleId = (string) $googleUser->getId();

        if ($googleEmail === '' || $googleId === '') {
            return redirect()->route('login')->with('error', 'Informasi akun Google tidak lengkap.');
        }

        $isNewUser = false;

        // 1. Cari user berdasarkan google_id
        $user = User::where('google_id', $googleId)->first();

        if (! $user) {
            // 2. Jika belum ada google_id, cari berdasarkan email terverifikasi Google
            $user = User::where('email', $googleEmail)->first();

            if ($user) {
                // Tautkan akun yang sudah ada dengan Google ID
                $user->google_id = $googleId;
                if (! $user->avatar_url) {
                    $user->avatar_url = $googleUser->getAvatar();
                }
                if (! $user->email_verified_at) {
                    $user->email_verified_at = now();
                }
                $user->save();

                AuditLog::log(
                    action: 'user.linked_google',
                    auditable: $user,
                    oldValues: null,
                    newValues: ['google_id' => $googleId],
                    notes: 'Akun berhasil ditautkan dengan Google ID.'
                );
            } else {
                $isNewUser = true;

                // 3. Pengguna baru: daftarkan secara otomatis sebagai Peserta
                $user = User::create([
                    'name' => $googleUser->getName() ?: 'Peserta ASN',
                    'email' => $googleEmail,
                    'google_id' => $googleId,
                    'avatar_url' => $googleUser->getAvatar(),
                    'role' => Role::Peserta,
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(32)),
                ]);

                AuditLog::log(
                    action: 'user.registered_via_google',
                    auditable: $user,
                    oldValues: null,
                    newValues: [
                        'email' => $user->email,
                        'name' => $user->name,
                        'google_id' => $googleId,
                    ],
                    notes: 'Pendaftaran akun baru peserta secara otomatis melalui Google OAuth.'
                );
            }
        }

        // Login pengguna dan regenerasi ID session (mencegah session fixation)
        Auth::login($user, remember: true);
        $request->session()->regenerate();

        // Perbarui metadata aktivitas login
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'last_login_user_agent' => $request->userAgent(),
        ])->save();

        AuditLog::log(
            action: 'user.login_google',
            auditable: $user,
            oldValues: null,
            newValues: [
                'role' => $user->role?->value ?? (string) $user->role,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            notes: 'Pengguna berhasil masuk melalui Google OAuth.'
        );

        $roleName = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        // Notifikasi toast reusable (menggunakan komponen admin.toast yang terpasang di seluruh layout)
        if ($isNewUser) {
            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Pendaftaran Berhasil',
                'message' => 'Pendaftaran akun melalui Google berhasil! Selamat datang di SIMPEL BKPSDM, '.$user->name.'.',
            ]);
        } else {
            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'berhasil login selamat datang '.$roleName,
            ]);
        }

        // Jika user internal (Admin), arahkan ke dashboard manajemen
        if ($user->hasAdminAccess()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Peserta diarahkan ke Halaman Landing
        return redirect()->route('landing');
    }
}
