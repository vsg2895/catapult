<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Project extends JsonResource
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
            'logo' => $this->whenLoaded('media', function () {
                return optional($this->media->first(function ($media) {
                    return $media->collection_name === 'logo';
                }))->getUrl();
            }),
            'banner' => $this->whenLoaded('media', function () {
                return optional($this->media->first(function ($media) {
                    return $media->collection_name === 'banner';
                }))->getUrl();
            }),
            'tags' => ProjectTag::collection($this->whenLoaded('tags')),
            'tasks' => Task::collection($this->whenLoaded('showcaseTasks')),
            'public' => $this->public,
            'reported' => $this->when(isset($this->reported), function () {
                return (bool) $this->reported;
            }, false),
            'coin_type' => new CoinType($this->whenLoaded('coinType')),
            'blockchain' => new Blockchain($this->whenLoaded('blockchain')),
            'description' => $this->description,
            'pool_amount' => $this->pool_amount,
            'social_links' => ProjectSocialLink::collection($this->whenLoaded('socialLinks')),
            'medium_username' => $this->medium_username,
        ];
    }
}
