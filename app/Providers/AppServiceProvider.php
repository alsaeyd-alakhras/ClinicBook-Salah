<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\ClinicDayConfig;
use App\Models\ClinicSetting;
use App\Models\Constant;
use App\Models\User;
use App\Observers\ClinicDayConfigObserver;
use App\Observers\ClinicSettingObserver;
use App\Observers\ConstantObserver;
use App\Observers\UserObserver;
use App\Policies\BookingPolicy;
use App\Policies\ClinicDayConfigPolicy;
use App\Policies\ClinicSettingPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind('abilities', function () {
            return include base_path('data/abilities.php');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // $baseUrl = request()->getSchemeAndHttpHost();
        // config([
        //     'app.url' => $baseUrl,
        //     'app.asset_url' => $baseUrl . '/public/'
        // ]);
        Paginator::useBootstrapFive();

        // Authouration
        Gate::before(function ($user, $ability) {
            if ($user instanceof User) {
                if ($user->super_admin) {
                    return true;
                }
                if ($user->user_type == 'employee') {
                    // if(in_array($ability,[])){
                    //     return true;
                    // }
                }
            }
        });
        // the Authorization for Report Page
        Gate::define('admins.super', function ($user) {
            if ($user instanceof User) {
                if ($user->roles->contains('role_name', 'admins.super')) {
                    return true;
                }

                return false;
            }
        });
        Gate::define('reports.view', function ($user) {
            if ($user instanceof User) {
                if ($user->roles->contains('role_name', 'reports.view')) {
                    return true;
                }

                return false;
            }
        });
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(ClinicSetting::class, ClinicSettingPolicy::class);
        Gate::policy(ClinicDayConfig::class, ClinicDayConfigPolicy::class);

        // Observe For Models
        User::observe(UserObserver::class);
        Constant::observe(ConstantObserver::class);
        ClinicSetting::observe(ClinicSettingObserver::class);
        ClinicDayConfig::observe(ClinicDayConfigObserver::class);
    }
}
