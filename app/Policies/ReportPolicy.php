<?php

namespace App\Policies;

use App\Enums\ReportStatusEnum;
use App\Models\Report;
use App\Models\User;

/**
 * A class defines the report policy
 */
class ReportPolicy
{
    /**
     * Determine if the user can respond to the report
     * Check if user owns the report AND report is not closed
     *
     * @param User $user
     * @param Report $report
     * @return bool
     */
    public function own(User $user, Report $report): bool
    {
        return $report->user_id === $user->id &&
            $report->status->value !== ReportStatusEnum::CLOSED->value;
    }
}
