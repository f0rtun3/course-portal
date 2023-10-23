<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Comment;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Classes\AchievementHandler;

use Tests\TestCase;

class AchievementHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_endpoint_returns_successful_response(): void {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");

        $response
            ->assertStatus(200)
            ->assertJson([
                'unlocked_achievements'=> [],
                'next_available_achievements' => [
                        'First Lesson Watched',
                        'First Comment Written'
                ],
                'current_badge'=> "Beginner",
                'next_badge'=> "Intermediate",
                'remaining_to_unlock_next_badge'=> 4,
            ]);
    }

    public function test_no_comment_no_lesson(): void
    {
        $achievement_handler = new AchievementHandler(0,0);
        $user_activity = $achievement_handler->get_calculated_user_achievements();
        self::assertEquals([], $user_activity['unlocked_achievements']);
        self::assertEquals(['First Lesson Watched', 'First Comment Written'], $user_activity['next_available_achievements']);
        self::assertEquals("Beginner", $user_activity['current_badge']);
        self::assertEquals("Intermediate", $user_activity['next_badge']);
        self::assertEquals(4, $user_activity['remaining_to_unlock_next_badge']);
    }

    public function test_all_achievements_reached(): void
    {
        $achievement_handler = new AchievementHandler(50,20);
        $user_activity = $achievement_handler->get_calculated_user_achievements();
        self::assertEquals([
            "First Lesson Watched",
            "5 Lessons Watched",
            "10 Lessons Watched",
            "25 Lessons Watched",
            "50 Lessons Watched",
            "First Comment Written",
            "3 Comments Written",
            "5 Comments Written",
            "10 Comments Written",
            "20 Comments Written"
        ], $user_activity['unlocked_achievements']);
        self::assertEquals([], $user_activity['next_available_achievements']);
        self::assertEquals("Master", $user_activity['current_badge']);
        self::assertEquals("", $user_activity['next_badge']);
        self::assertEquals(0, $user_activity['remaining_to_unlock_next_badge']);
    }

    public function test_intermediate_badge_reached(): void {
        $achievement_handler = new AchievementHandler(5,5);
        $user_activity = $achievement_handler->get_calculated_user_achievements();
        self::assertEquals([
            "First Lesson Watched",
            "5 Lessons Watched",
            "First Comment Written",
            "3 Comments Written",
            "5 Comments Written"
        ], $user_activity['unlocked_achievements']);
        self::assertEquals([ "10 Lessons Watched", "10 Comments Written"], $user_activity['next_available_achievements']);
        self::assertEquals("Intermediate", $user_activity['current_badge']);
        self::assertEquals("Advanced", $user_activity['next_badge']);
        self::assertEquals(3, $user_activity['remaining_to_unlock_next_badge']);
    }

}

