<?php

use App\Enums\RoleAccessLevelEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $allowedLevels = RoleAccessLevelEnum::toArray();

        // Update any existing roles that might have invalid access levels to a default value
        DB::table('roles')
            ->whereNotIn('access_level', $allowedLevels)
            ->update(['access_level' => RoleAccessLevelEnum::FULL_ACCESS->value]);

        Schema::table('roles', function (Blueprint $table) use ($allowedLevels) {
            $levelsString = implode("','", $allowedLevels);
            DB::statement("ALTER TABLE roles MODIFY COLUMN access_level ENUM('{$levelsString}')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE roles MODIFY COLUMN access_level VARCHAR(255)");
    }
};
