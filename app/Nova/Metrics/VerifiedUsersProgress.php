<?php

namespace App\Nova\Metrics;

use App\Models\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Progress;
use LucaCermenati\NovaGlobalFilters\Metrics\RespondsToGlobalFilters;

class VerifiedUsersProgress extends Progress
{
    use RespondsToGlobalFilters;

    public function globalFilters(): array
    {
        return ['date_range'];
    }

    public function calculate(NovaRequest $request)
    {
        $query = $this->applyGlobalFilters($request, User::query());
        $total = (clone $query)->count();

        return $this->count($request, $query, fn ($q) => $q->whereNotNull('email_verified_at'), target: max($total, 1));
    }

    public function uriKey(): string
    {
        return 'verified-users-progress';
    }
}
