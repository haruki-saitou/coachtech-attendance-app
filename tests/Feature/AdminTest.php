<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_access_staff_list()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $response = $this->actingAs($admin)->get('/admin/staff/list');
        $response->assertStatus(200);
        $response->assertSee('スタッフ一覧');
    }

    public function test_staff_cannot_access_admin_page()
    {
        /** @var User $staff */
        $staff = User::factory()->create(['role' => 0]);
        $response = $this->actingAs($staff)->get('/admin/staff/list');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_all_attendance_list()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $response = $this->actingAs($admin)->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response->assertSee('勤怠一覧');
    }

    public function test_admin_can_access_specific_staff_attendance_list()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $staff = User::factory()->create(['role' => 0, 'name' => 'テストスタッフ']);
        $response = $this->actingAs($admin)->get('/admin/attendance/staff/' . $staff->id);
        $response->assertStatus(200);
        $response->assertSee('テストスタッフ');
    }
}