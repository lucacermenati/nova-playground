<?php

namespace App\Nova\Metrics;

use App\Models\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use LucaCermenati\NovaGlobalFilters\Metrics\RespondsToGlobalFilters;

/**
 * A raw query that reads the parsed DateRange instead of using applyGlobalFilters().
 */
class AverageAccountAgeDays extends Value
{
    use RespondsToGlobalFilters;

    public $icon = 'clock';

    public function globalFilters(): array
    {
        return ['date_range'];
    }

    public function calculate(NovaRequest $request)
    {
        $query = User::query();

        if ($range = $this->globalFilterValue($request, 'date_range')) {
            $query->when($range->start, fn ($q) => $q->where('created_at', '>=', $range->startOfDay()))
                ->when($range->end, fn ($q) => $q->where('created_at', '<=', $range->endOfDay()));
        }

        $days = $query->selectRaw('AVG(DATEDIFF(NOW(), created_at)) as days')->value('days');

        return $this->result(round((float) $days, 1))->suffix('days')->withoutSuffixInflection();
    }

    public function ranges(): array
    {
        return [];
    }

    public function uriKey(): string
    {
        return 'average-account-age-days';
    }
}
