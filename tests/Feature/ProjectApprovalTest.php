<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProjectApprovalTest extends TestCase
{
    use DatabaseTransactions;

    private Category $category;
    private SubCategory $subCategory;
    private User $seeker;
    private User $provider;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'title_en' => 'Design',
            'title_ar' => 'تصميم',
            'is_enabled' => true,
        ]);

        $this->subCategory = SubCategory::create([
            'title_en' => 'Logo',
            'title_ar' => 'لوجو',
            'category_id' => $this->category->id,
        ]);

        $this->seeker = $this->createUserWithRole('seeker');
        $this->provider = $this->createUserWithRole('provider');

        $this->admin = $this->createUserWithRole('root');
        $this->admin->givePermissionTo(['Approve Projects', 'Reject Projects']);
    }

    private function createUserWithRole(string $role): User
    {
        $random = Str::random(12);

        $user = User::create([
            'name' => 'Test User ' . $random,
            'email' => $random . '@test.com',
            'mobile' => '010' . substr($random, 0, 8),
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'mobile_verified_at' => now(),
            'status' => 'active',
            'active' => true,
        ]);

        $user->assignRole($role);

        return $user;
    }

    private function projectPayload(): array
    {
        return [
            'title' => 'Test Project ' . Str::random(6),
            'description' => 'Test description',
            'min_price' => 100,
            'max_price' => 500,
            'time' => 5,
            'category_id' => $this->category->uuid,
            'sub_category_id' => $this->subCategory->uuid,
        ];
    }

    private function createProject(User $user, array $overrides = []): Project
    {
        $project = Project::create([
            'title' => 'Test Project ' . Str::random(6),
            'description' => 'Test description',
            'min_price' => 100,
            'max_price' => 500,
            'time' => 5,
            'user_id' => $user->id,
            'category_id' => $this->category->id,
            'sub_category_id' => $this->subCategory->id,
            'status' => 'pending',
            'is_approved' => $overrides['is_approved'] ?? false,
        ]);

        return $project;
    }

    private function adminApprove(Project $project): \Illuminate\Testing\TestResponse
    {
        Passport::actingAs($this->admin);

        return $this->postJson("/admin/v1/projects/{$project->uuid}/approve");
    }

    public function test_new_project_starts_unapproved(): void
    {
        Passport::actingAs($this->seeker);

        $response = $this->postJson('/api/v1/projects/add', $this->projectPayload());

        $response->assertOk();
        $response->assertJson(['error' => false]);
        $response->assertJsonPath('data.is_approved', false);
        $response->assertJsonPath('data.status', 'draft');

        $this->assertDatabaseHas('projects', [
            'uuid' => $response->json('data.id'),
            'is_approved' => false,
        ]);
    }

    public function test_browse_hides_unapproved_projects(): void
    {
        $project = $this->createProject($this->seeker, ['is_approved' => false]);

        Passport::actingAs($this->seeker);

        $response = $this->getJson('/api/v1/projects');

        $response->assertOk();
        $this->assertNotContains($project->uuid, collect($response->json('data'))->pluck('id')->toArray());
    }

    public function test_browse_shows_project_after_admin_approval(): void
    {
        $project = $this->createProject($this->seeker, ['is_approved' => false]);

        $this->adminApprove($project);

        Passport::actingAs($this->seeker);

        $response = $this->getJson('/api/v1/projects');

        $response->assertOk();
        $this->assertContains($project->uuid, collect($response->json('data'))->pluck('id')->toArray());
    }

    public function test_admin_approve_endpoint_approves_project(): void
    {
        $project = $this->createProject($this->seeker, ['is_approved' => false]);

        $response = $this->adminApprove($project);

        $response->assertOk();
        $response->assertJsonPath('data.is_approved', true);

        $this->assertDatabaseHas('projects', [
            'uuid' => $project->uuid,
            'is_approved' => true,
        ]);
    }

    public function test_admin_approve_endpoint_toggles_back_to_false(): void
    {
        $project = $this->createProject($this->seeker, ['is_approved' => true]);

        $response = $this->adminApprove($project);

        $response->assertOk();
        $response->assertJsonPath('data.is_approved', false);
    }

    public function test_admin_reject_endpoint_sets_false(): void
    {
        $project = $this->createProject($this->seeker, ['is_approved' => true]);

        Passport::actingAs($this->admin);

        $response = $this->postJson("/admin/v1/projects/{$project->uuid}/reject");

        $response->assertOk();
        $response->assertJsonPath('data.is_approved', false);

        $this->assertDatabaseHas('projects', [
            'uuid' => $project->uuid,
            'is_approved' => false,
        ]);
    }

    public function test_my_projects_shows_own_projects_with_approval_state(): void
    {
        $this->createProject($this->seeker, ['is_approved' => false]);
        $this->createProject($this->seeker, ['is_approved' => true]);
        $this->createProject($this->provider, ['is_approved' => false]);

        Passport::actingAs($this->seeker);

        $response = $this->getJson('/api/v1/projects/my-projects');

        $response->assertOk();
        $this->assertEquals(2, $response->json('meta.total'));
    }
}