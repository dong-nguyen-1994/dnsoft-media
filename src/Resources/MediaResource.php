<?php

namespace Dnsoft\Media\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'url'   => $this->getUrl(),
            'thumb' => $this->getUrl(),
        ];
    }
}
