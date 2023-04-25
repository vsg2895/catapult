<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Task extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'text' => $this->text,
            'images' => File::collection($this->whenLoaded('media')),
            'in_work' => $this->whenLoaded('inWorkByUser', true, false),
            'project' => new Project($this->whenLoaded('project')),
            'rewards' => TaskReward::collection($this->whenLoaded('rewards')),
            'verifier' => new TaskVerifier($this->whenLoaded('verifier')),
            'activity' => new Activity($this->whenLoaded('activity')),
            'priority' => $this->priority,
            'coin_type' => new CoinType($this->whenLoaded('coinType')),
            'conditions' => TaskCondition::collection($this->whenLoaded('conditions')),
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'number_of_invites' => $this->number_of_invites,
            'is_invite_friends' => $this->is_invite_friends,
            'autovalidate' => $this->autovalidate,
            'working_users' => $this->whenLoaded('userTasksInWork', function () {
                return User::collection($this->userTasksInWork->map(function ($userTaskInWork) {
                    return $userTaskInWork->user;
                }));
            }),
            'verifier_driver' => $this->verifier_driver,
            'status_by_dates' => $this->status_by_dates,
        ];
    }
}
