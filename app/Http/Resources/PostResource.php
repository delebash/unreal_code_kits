<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        try {
            if (env('RESIZE_IMAGE') === true) {
                $image = $this->getMedia('*')[0]->getUrl('resized-image');
            } else {
                $image = $this->getMedia('*')[0]->getUrl();
            }
        } catch (Exception $e) {
            $image = "";
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'categories' => $this->categories()->select('id', 'name')->get(),
            'versions' => $this->versions()->select('id', 'name')->get(),
            'content' => $this->content,
            'reviews' => $this->reviews,
            'type' => $this->type,
            'details' => $this->details,
            'average_rating' => floatval($this->reviews_avg_rating),
            'image' => $image,
            'user' => $this->user,
            'created_at' => $this->created_at->toDateString()
        ];
    }
}
