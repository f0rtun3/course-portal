<?php

namespace App\Classes;

class AchievementHandler {

    /**
     * @var int lessons achievement unlocked by the user
     */
    private int $lessons_unlocked = 0;

    /**
     * @var int comments written achievement unlocked by the user
     */
    private int $comments_written_unlocked = 0;

    /**
     * @var int total number of lessons watched by the user
     */
    private int $lessons_watched;

    /**
     * @var int total number of comments written by the user
     */
    private int $comments_written;

    /**
     * @var int total number of achievements available to the user (comments and lessons)
     */
    private int $total_achievements;

    /**
     * @var int total number of achievements (comments and lessons) achieved by the user
     */
    private int $total_unlocked_achievements = 0;

    /**
     * @var array the user’s unlocked achievements by name
     */
    private array $unlocked_achievements = [];

    /**
     * @var array the next achievements the user can unlock by name.
     */
    private array $next_available_achievements = [];

    /**
     * @var int The number of additional achievements the user must unlock to earn the next badge.
     */
    private int $remaining_to_unlock_next_badge = 0;

    /**
     * @var string The name of the user’s current badge.
     */
    private string $current_badge = "";

    /**
     * @var string The name of the next badge the user can earn.
     */
    private string $next_badge = "";



    /**
     * class constructor
     *
     * @param int $lessons_watched The number of lessons watched
     * @param int $comments_written The number of comments written
     */
    public function __construct(int $lessons_watched, int $comments_written) {
        $this->lessons_watched = $lessons_watched;
        $this->comments_written = $comments_written;
        $this->total_achievements = count(config('constants.COMMENT-ACHIEVEMENTS')) + count(config('constants.LESSON-ACHIEVEMENTS'));
    }

    /**
     * get the lesson achievement number
     *
     * @return int
     */
    public function get_lessons_unlocked(): int
    {
        foreach (config('constants.LESSON-ACHIEVEMENTS') as $requirement) {
            if ($this->lessons_watched >= $requirement) {
                if($requirement == 1) {
                    $this->unlocked_achievements[] = "First Lesson Watched";
                    $this->total_unlocked_achievements++;
                } else {
                    $this->unlocked_achievements[] = $requirement. " Lessons Watched";
                    $this->total_unlocked_achievements++;
                }
                $this->lessons_unlocked = $requirement;

            }
        }

        return $this->lessons_unlocked;
    }

    /**
     * get the comment achievement number
     *
     * @return int
     */
    public function get_comments_unlocked(): int {
        foreach (config('constants.COMMENT-ACHIEVEMENTS') as $requirement) {
            if ($this->comments_written >= $requirement) {
                if($requirement == 1) {
                    $this->unlocked_achievements[] = "First Comment Written";
                    $this->total_unlocked_achievements++;
                } else {
                    $this->unlocked_achievements[] = $requirement. " Comments Written";
                    $this->total_unlocked_achievements++;
                }
                $this->comments_written_unlocked = $requirement;
            }
        }

        return $this->comments_written_unlocked;
    }

    /**
     * get the current and next badge
     *
     * @return array
     */
    private function check_badge_requirements(): array {
        foreach (config('constants.BADGE-ACHIEVEMENTS') as $badge => $requirement) {
            if ($this->total_unlocked_achievements >= $requirement) {
                $this->current_badge = $badge;
            } else {
                $this->next_badge = $badge;
                break;
            }
        }

        return [
            "current_badge" => $this->current_badge,
            "next_badge" => $this->next_badge,
        ];
    }

    /**
     * get the next available achievements
     *
     * @return void
     */
    private function get_next_available_achievements(): void {
        foreach (config('constants.LESSON-ACHIEVEMENTS') as $requirement) {
            if ($this->lessons_unlocked < $requirement && $this->lessons_unlocked == 0) {
                $this->next_available_achievements[] = "First Lesson Watched";
                break;
            }

            if ($this->lessons_unlocked < $requirement) {
                $this->next_available_achievements[] = $requirement . " Lessons Watched";
                break;
            }
        }

        foreach (config('constants.COMMENT-ACHIEVEMENTS') as $requirement) {
            if ($this->comments_written_unlocked < $requirement && $this->comments_written_unlocked == 0) {
                $this->next_available_achievements[] = "First Comment Written";
                break;
            }

            if ($this->comments_written_unlocked < $requirement) {
                $this->next_available_achievements[] = $requirement . " Comments Written";
                break;
            }
        }
    }

    /**
     * get the remaining achievements
     *
     * @return void
     */
    private function remaining_achievements_to_unlock_next_badge(): void {
        $badge_requirements = config('constants.BADGE-ACHIEVEMENTS');
        if($this->total_unlocked_achievements < $this->total_achievements) {
            $this->remaining_to_unlock_next_badge = $badge_requirements[$this->next_badge] - $this->total_unlocked_achievements;
        }

    }

    /**
     * @return array
     */
    public function get_calculated_user_achievements() {
        $this->get_lessons_unlocked();
        $this->get_comments_unlocked();
        $this->check_badge_requirements();
        $this->get_next_available_achievements();
        $this->remaining_achievements_to_unlock_next_badge();
        $this->remaining_achievements_to_unlock_next_badge();

        return [
            "unlocked_achievements" => $this->unlocked_achievements,
            "next_available_achievements" => $this->next_available_achievements,
            "current_badge" => $this->current_badge,
            "next_badge" => $this->next_badge,
            "remaining_to_unlock_next_badge" => $this->remaining_to_unlock_next_badge,
        ];
    }

}
