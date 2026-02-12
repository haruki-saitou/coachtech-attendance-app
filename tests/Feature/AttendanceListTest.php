<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_my_attendance_is_displayed_on_list()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in_at' => Carbon::now()->setTime(9, 0, 0),
            'check_out_at' => Carbon::now()->setTime(18, 0, 0),
        ]);
        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        }

    public function test_can_navigate_to_previous_and_next_month()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $lastMonth = Carbon::now()->subMonth();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in_at' => $lastMonth->copy()->setTime(9, 0, 0),
        ]);
        $response = $this->actingAs($user)->get('/attendance/list?month=' . $lastMonth->format('Y-m'));
        $response->assertStatus(200);
        $response->assertSee($lastMonth->format('Y/m'));
    }


    public function test_can_navigate_to_attendance_detail_page() {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in_at' => Carbon::now()->setTime(9, 0, 0),
        ]);
        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee('/attendance/detail/' . $attendance->id);
        $detailResponse = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('勤怠詳細');
    }

}
