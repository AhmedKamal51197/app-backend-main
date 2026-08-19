<?php

namespace App\Services\Skill;

use App\Events\LogExceptionEvent;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Skill;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the skill service
 */
class SkillService
{
    /**
     * Get all skills
     *
     * @param Category $category
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(Category $category, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Skill::query()
            ->where('category_id', $category->getAttribute('id'))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new skill
     *
     * @param Category $category
     * @param array $data
     *
     * @return Skill
     *
     * @throws Exception
     */
    public function store(Category $category, array $data): Skill
    {
        DB::beginTransaction();
        try {
            $skill = Skill::create([
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'category_id' => $category->getAttribute('id')
            ]);

            DB::commit();

            return $skill;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update skill
     *
     * @param Skill $skill
     * @param array $data
     *
     * @return Skill
     *
     * @throws Exception
     */
    public function update(Skill $skill, array $data): Skill
    {
        DB::beginTransaction();
        try {
            $skill->update([
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
            ]);

            DB::commit();

            return $skill;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete skill
     *
     * @param Skill $skill
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Skill $skill): bool
    {
        DB::beginTransaction();
        try {
            $result = $skill->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
