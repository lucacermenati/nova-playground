<?php

namespace App\Nova\Metrics;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use LucaCermenati\NovaGlobalFilters\Metrics\RespondsToGlobalFilters;

class UsersPerVerification extends Partition
{
    use RespondsToGlobalFilters;

    public function globalFilters(): array
    {
        return ['date_range'];
    }

    public function calculate(NovaRequest $request)
    {
        $query = $this->applyGlobalFilters($request, User::query());

        return $this->count($request, $query, DB::raw('IF(email_verified_at IS NULL, "unverified", "verified")'))
            ->label(fn ($value) => ucfirst((string) $value));
    }

    public function uriKey(): string
    {
        return 'users-per-verification';
    }
}
