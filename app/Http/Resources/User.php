<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $need_points = config('levels.need_points')[$this->level] ?? 0;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'level' => $this->level,
            'tasks' => UserTask::collection($this->whenLoaded('tasks')),
            'points' => $this->points,
            'wallet' => $this->wallet,
            'avatar' => $this->whenLoaded('media', function () {
                return optional($this->media->first())->getUrl();
            }),
            'skills' => UserSkill::collection($this->whenLoaded('skills')),
            'country' => $this->whenLoaded('country', function () {
                return $this->country->country->name;
            }),
            'languages' => UserLanguage::collection($this->whenLoaded('languages')),
            'activities' => UserActivity::collection($this->whenLoaded('activities')),
            'activity_links' => UserActivityLink::collection($this->whenLoaded('activityLinks')),
            'social_links' => UserSocialLink::collection($this->whenLoaded('socialLinks')),
            'social_providers' => SocialProvider::collection($this->whenLoaded('socialProviders')),
            'next_level' => !($need_points === 0) && ($this->points >= $need_points || (!is_null($this->position) && $this->position <= config('app.minimum_leaderboard_place_level_up'))),
            'need_points' => $need_points,
            'total_points' => $this->total_points,
            'total_balance' => 0,
            'registered_at' => $this->created_at,
            'set_up_profile' => $this->set_up_profile,
            'has_task_limit' => $this->when(isset($this->tasks_in_work_count), function () {
               return $this->tasks_in_work_count >= config('app.maximum_tasks_in_work');
            }, false),
        ];
    }
}
