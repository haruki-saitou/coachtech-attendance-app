<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\User;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;
    public function test_attendance_page_displays_current_time(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Carbon::setTestNow(now());

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('id="real-time-date"', false);
        $response->assertStatus(200);
    }

    public function test_buttons_status_before_clock_in(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤');
    }

    public function test_clock_in_successfully(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/attendance/start/');
        $this->assertDatabaseHas('attendances',[
            'user_id' => $user->id,
            'status' => '出勤中'
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('退勤');
        $response->assertSee('休憩入');
    }

    public function test_break_in_successfully(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user)->post('/attendance/start/');
        $attendance = Attendance::where('user_id', $user->id)->first();
        $this->actingAs($user)->post('/rest/start/');
        $this->assertDatabaseHas('rests', ['attendance_id' => $attendance->id]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩戻');
    }

    public function test_break_out_successfully(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user)->post('/attendance/start/');
        $this->actingAs($user)->post('/rest/start/');
        $this->actingAs($user)->post('/rest/end/');
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩入');
        $response->assertSee('退勤');
    }

    public function test_clock_out_successfully(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user)->post('/attendance/start/');
        $this->actingAs($user)->post('/attendance/end/');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => '退勤済'
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('お疲れ様でした。');
    }
}