<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\AverageAccountAgeDays;
use App\Nova\Metrics\NewUsersPerDay;
use App\Nova\Metrics\TotalUsers;
use App\Nova\Metrics\UsersPerVerification;
use App\Nova\Metrics\VerifiedUsersProgress;
use Laravel\Nova\Dashboard;
use LucaCermenati\NovaGlobalFilters\Cards\GlobalFilterCard;
use LucaCermenati\NovaGlobalFilters\Cards\SectionHeader;

class Insights extends Dashboard
{
    public function name(): string
    {
        return 'Insights';
    }

    public function cards(): array
    {
        return [
            GlobalFilterCard::make('date_range')->width('1/4'),
            GlobalFilterCard::make('verification')->width('1/4'),

            SectionHeader::make('Users', 'Counts respond to the date range and the verification filter'),
            (new TotalUsers)->width('1/2'),
            (new UsersPerVerification)->width('1/2'),

            SectionHeader::make('Activity'),
            (new NewUsersPerDay)->width('1/2'),
            (new AverageAccountAgeDays)->width('1/4'),
            (new VerifiedUsersProgress)->width('1/4'),
        ];
    }

    public function uriKey(): string
    {
        return 'insights';
    }
}
