<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class CorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_submit_correction_request()
    {
        /** @var User $staff */
        $staff = User::factory()->create(['role' => 0]);
        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
        ]);

        $this->actingAs($staff)->patch('/attendance/detail/' . $attendance->id, [
            'comment' => '電車遅延のため',
            'check_in_at' => '09:30',
            'check_out_at' => '18:30',
        ]);
        $response = $this->actingAs($staff)->get('/stamp_correction_request/list');
        $response->assertSee('承認待ち');
    }

    public function test_admin_can_approve_correction_request()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $staff = User::factory()->create(['role' => 0]);
        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
            'check_in_at' => '09:00',
        ]);

        $response = $this->actingAs($admin)->post('/stamp_correction_request/approve/' . $attendance->id);
        $response->assertStatus(302);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => '承認済み',
        ]);
    }
}