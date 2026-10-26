<?php

namespace App\Nova\Metrics;

use App\Models\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use LucaCermenati\NovaGlobalFilters\Metrics\RespondsToGlobalFilters;

class NewUsersPerDay extends Trend
{
    use RespondsToGlobalFilters;

    public function globalFilters(): array
    {
        return ['verification'];
    }

    public function calculate(NovaRequest $request)
    {
        return $this->countByDays($request, $this->applyGlobalFilters($request, User::query()))->showSumValue();
    }

    public function ranges(): array
    {
        return [30 => '30 Days', 60 => '60 Days', 90 => '90 Days'];
    }

    public function uriKey(): string
    {
        return 'new-users-per-day';
    }
}
