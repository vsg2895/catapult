<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserWalletHistory extends JsonResource
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
            'date' => $this->created_at,
            'task' => new Task($this->whenLoaded('task')),
            'value' => $this->value,
            'points' => $this->points,
            'user_wallet' => new UserWallet($this->whenLoaded('wallet')),
            'value_in_usd' => 0,
        ];
    }
}
