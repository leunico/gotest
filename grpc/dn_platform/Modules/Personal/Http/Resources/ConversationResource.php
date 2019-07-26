<?php

namespace Modules\Personal\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Personal\Entities\Conversation;
use App\Traits\Transform;

class ConversationResource extends JsonResource
{
    use Transform;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'creator_id' => (int) $this->creator_id,
            'creator' => (object) $this->transformItem($this->whenLoaded('creator'), UserResource::class),
            'user_id' => (int) $this->user_id,
            'user' => (object) $this->transformItem($this->whenLoaded('user'), UserResource::class),
            'content' => (string) $this->content,
            'type' => (int) $this->type,
            'type_msg' => (string) Conversation::$conversationMap[$this->type] ?? '',
            'conversation_at' => (string) $this->conversation_at,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
