<?php

namespace App\Providers;

use App\Models\CourseUser;
use App\Policies\RegistrationPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(CourseUser::class, RegistrationPolicy::class);

        // 1. Rate limiter untuk percobaan Login (5 per menit per IP + email/identitas)
        RateLimiter::for('login', function (Request $request) {
            $identifier = (string) ($request->input('identifier') ?: $request->input('email', ''));

            return Limit::perMinute(5)->by($request->ip().'|'.strtolower($identifier));
        });

        // 2. Rate limiter untuk Presensi QR / token attendance (15 per menit per user/IP)
        RateLimiter::for('attendance', function (Request $request) {
            return Limit::perMinute(15)->by($request->user()?->id ?: $request->ip());
        });

        // 3. Rate limiter untuk Upload Media Materi (20 per menit per user/IP)
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        // 4. Rate limiter untuk Ekspor Data Rekapitulasi (10 per menit per user)
        RateLimiter::for('export', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
