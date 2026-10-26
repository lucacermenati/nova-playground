<?php

namespace App\Nova\Metrics;

use App\Models\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use LucaCermenati\NovaGlobalFilters\Metrics\RespondsToGlobalFilters;

class TotalUsers extends Value
{
    use RespondsToGlobalFilters;

    public $icon = 'users';

    public function globalFilters(): array
    {
        return ['date_range', 'verification'];
    }

    public function calculate(NovaRequest $request)
    {
        return $this->count($request, $this->applyGlobalFilters($request, User::query()))->format('0');
    }

    public function ranges(): array
    {
        return [];
    }

    public function uriKey(): string
    {
        return 'total-users';
    }
}
