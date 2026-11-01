<?php

use App\Enums\ProjectStatusEnum;
use App\Models\Project;
use Illuminate\Database\Migrations\Migration;

/**
 * Projects added from the app since the review flow stopped calling /publish
 * were left as drafts, which the dashboard list hides. Move them to pending
 * so they show up for review; is_approved still controls who can see them.
 */
return new class extends Migration
{
    public function up(): void
    {
        Project::where('status', ProjectStatusEnum::DRAFT->value)
            ->where('created_at', '>=', '2026-10-01')
            ->update(['status' => ProjectStatusEnum::PENDING->value]);
    }

    public function down(): void
    {
        //
    }
};
