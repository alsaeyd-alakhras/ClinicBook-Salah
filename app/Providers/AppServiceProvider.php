<?php

namespace App\Providers;

use App\Models\AidDistribution;
use App\Models\Constant;
use App\Models\Currency;
use App\Models\Executive;
use App\Models\Institution;
use App\Models\Office;
use App\Models\Project;
use App\Models\User;
use App\Observers\AidDistributionObserver;
use App\Observers\ConstantObserver;
use App\Observers\CurrencyObserver;
use App\Observers\ExecutiveObserver;
use App\Observers\InstitutionObserver;
use App\Observers\OfficeObserver;
use App\Observers\ProjectObserver;
use App\Observers\UserObserver;
use App\Policies\AidDistributionPolicy;
use App\Policies\InstitutionPolicy;
use App\Policies\ProjectPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind('abilities', function() {
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


        //Authouration
        Gate::before(function ($user, $ability) {
            if($user instanceof User) {
                if($user->super_admin) {
                    return true;
                }
                if($user->user_type == 'employee') {
                    // if(in_array($ability,[])){
                    //     return true;
                    // }
                }
            }
        });
        // the Authorization for Report Page
        Gate::define('admins.super', function ($user) {
            if($user instanceof User) {
                if($user->roles->contains('role_name', 'admins.super')) {
                    return true;
                }
                return false;
            }
        });
        Gate::define('reports.view', function ($user) {
            if($user instanceof User) {
                if($user->roles->contains('role_name', 'reports.view')) {
                    return true;
                }
                return false;
            }
        });
        Gate::policy(AidDistribution::class, AidDistributionPolicy::class);
        Gate::policy(Institution::class, InstitutionPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);




        // Observe For Models
        User::observe(UserObserver::class);
        Constant::observe(ConstantObserver::class);
        Currency::observe(CurrencyObserver::class);
        Office::observe(OfficeObserver::class);
        Institution::observe(InstitutionObserver::class);
        AidDistribution::observe(AidDistributionObserver::class);
        Project::observe(ProjectObserver::class);
    }
}
