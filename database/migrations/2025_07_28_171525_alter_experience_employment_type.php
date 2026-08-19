<?php

use App\Enums\EmploymentTypeEnum;
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
        $allowedStatuses = EmploymentTypeEnum::toArray();

        DB::table('experiences')
            ->whereNotIn('employment_type', $allowedStatuses)
            ->update(['employment_type' => EmploymentTypeEnum::FULL_TIME->value]);

        Schema::table('experiences', function (Blueprint $table) use ($allowedStatuses) {
            $employmentTypeEnumValuesString = implode("','", $allowedStatuses);
            DB::statement("ALTER TABLE experiences MODIFY COLUMN employment_type ENUM('{$employmentTypeEnumValuesString}')");
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
