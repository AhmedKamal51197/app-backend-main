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

    /**
     * Determine if the user can delete the report.
     * The owner can delete their report at any status, including closed ones.
     *
     * @param User $user
     * @param Report $report
     * @return bool
     */
    public function delete(User $user, Report $report): bool
    {
        return $report->user_id === $user->id;
    }

    /**
     * Determine if the user can view the report.
     * The owner can view their report at any status, including closed ones.
     *
     * @param User $user
     * @param Report $report
     * @return bool
     */
    public function view(User $user, Report $report): bool
    {
        return $report->user_id === $user->id;
    }
}
