<?php

namespace App\Providers;

use App\Models\User;
use App\Nova\Dashboards\Main;
use App\Nova\Dashboards\Insights;
use Illuminate\Contracts\Database\Eloquent\Builder;
use LucaCermenati\NovaGlobalFilters\Facades\NovaGlobalFilters;
use LucaCermenati\NovaGlobalFilters\Filters\DateRangeFilter;
use LucaCermenati\NovaGlobalFilters\Filters\GlobalFilter;
use LucaCermenati\NovaGlobalFilters\Inputs\SelectInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Features;
use Laravel\Nova\Dashboard;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Tool;
use LucaCermenati\NovaSettings\Facades\NovaSettings as Settings;
use LucaCermenati\NovaSettings\NovaSettings;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        $this->settings();
        $this->globalFilters();
    }

    /**
     * Global filters used by the Insights dashboard.
     */
    protected function globalFilters(): void
    {
        NovaGlobalFilters::register([
            DateRangeFilter::make(),

            GlobalFilter::make('verification', 'Verification')
                ->withInputs([
                    SelectInput::make('state', 'State')->options(['verified' => 'Verified', 'unverified' => 'Unverified']),
                ])
                ->applyUsing(fn (Builder $query, array $value) => $value['state'] === 'verified'
                    ? $query->whereNotNull('email_verified_at')
                    : $query->whereNull('email_verified_at')),
        ]);
    }

    /**
     * Settings defined in code (synced into the settings tables, locked in the UI).
     */
    protected function settings(): void
    {
        Settings::group('general', 'General', fn () => [
            Text::make('Site name', 'site_name')
                ->rules('required', 'max:120')
                ->help('Shown in the header of the website.'),

            Image::make('Logo', 'logo')
                ->disk('public')
                ->path('settings')
                ->prunable(),

            Boolean::make('Maintenance mode', 'maintenance_mode'),

            Number::make('Items per page', 'items_per_page')
                ->min(1)->max(100)->step(1)
                ->default(25)
                ->rules('required', 'integer'),

            Select::make('Default locale', 'locale')
                ->options(['en' => 'English', 'nl' => 'Dutch', 'it' => 'Italian'])
                ->displayUsingLabels()
                ->default('en'),

            Date::make('Launch date', 'launch_date')->nullable(),
        ])->icon('cog-6-tooth')->order(1);

        Settings::group('mail', 'E-mail', fn () => [
            Email::make('From address', 'mail_from')->rules('required', 'email'),

            Trix::make('Footer', 'mail_footer')->nullable(),

            KeyValue::make('Social links', 'social_links')
                ->keyLabel('Network')
                ->valueLabel('URL')
                ->nullable(),

            Code::make('Extra config', 'extra_config')->json()->nullable(),
        ])->icon('envelope')->order(2);
    }

    /**
     * Register the configurations for Laravel Fortify.
     */
    protected function fortify(): void
    {
        Nova::fortify()
            ->features([
                Features::updatePasswords(),
            ])
            ->register();
    }

    /**
     * Register the Nova routes.
     */
    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes(default: true)
            ->withPasswordResetRoutes()
            ->withoutEmailVerificationRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     */
    protected function gate(): void
    {
        Gate::define('viewNova', function (User $user) {
            return true;
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array<int, Dashboard>
     */
    protected function dashboards(): array
    {
        return [
            new Main,
            new Insights,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array<int, Tool>
     */
    public function tools(): array
    {
        return [
            NovaSettings::make()
                ->canSee(fn (Request $request) => $request->user()?->canEditSettingValues() ?? false),
        ];
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();
    }
}
