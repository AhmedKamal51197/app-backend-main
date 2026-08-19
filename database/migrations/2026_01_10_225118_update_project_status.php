<?php

use App\Enums\ProjectStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $allowedStatuses = ProjectStatusEnum::toArray();

        DB::table('projects')
            ->whereNotIn('status', $allowedStatuses)
            ->update(['status' => ProjectStatusEnum::DRAFT->value]);

        Schema::table('projects', function (Blueprint $table) use ($allowedStatuses) {
            $projectEnumValuesString = implode("','", $allowedStatuses);
            DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('{$projectEnumValuesString}') DEFAULT 'draft'");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      //
    }
};
