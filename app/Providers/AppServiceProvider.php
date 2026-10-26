<?php

namespace App\Providers;

use App\Policies\SettingGroupPolicy;
use App\Policies\SettingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use LucaCermenati\NovaSettings\Models\Setting;
use LucaCermenati\NovaSettings\Models\SettingGroup;

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
        Gate::policy(SettingGroup::class, SettingGroupPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
    }
}
