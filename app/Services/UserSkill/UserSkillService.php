<?php

namespace App\Services\UserSkill;

use App\Events\LogExceptionEvent;
use App\Models\Skill;
use App\Models\SkillUser;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the user skill service
 */
class UserSkillService
{
    /**
     * Store user skills without detaching the old ones.
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function storeUserSkills(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            $skills = Skill::whereIn('uuid', $data['skill_ids'])->get();

            foreach ($skills as $skill) {
                $alreadyAttached = $user->userSkills()
                    ->where('skill_id', $skill->id)
                    ->exists();

                if (!$alreadyAttached) {
                    SkillUser::create([
                        'skill_id' => $skill->id,
                        'user_id' => $user->getAttribute('id')
                    ]);
                }
            }

            $user->load(['userSkills', 'userSkills.skill', 'userSkills.skill.category']);

            DB::commit();

            return $user;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Remove user skills.
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function removeUserSkills(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            $skills = Skill::whereIn('uuid', $data['skill_ids'])->get();
            $skillIds = $skills->pluck('id')->toArray();

            $user->userSkills()
                ->whereIn('skill_id', $skillIds)
                ->delete();

            $user->load(['userSkills', 'userSkills.skill', 'userSkills.skill.category']);

            DB::commit();

            return $user;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
