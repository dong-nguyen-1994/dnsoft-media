<?php

namespace Dnsoft\Media\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Dnsoft\Media\Models\Media;

/**
 * Class FroalaMediaResource
 *
 * @package Dnsoft\Media\Resources
 * @mixin Media
 */
class FroalaMediaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'media-id' => $this->id,
            'name'     => $this->name,
            'url'      => $this->getUrl(),
            'thumb'    => $this->getUrl('thumb'),
        ];
    }
}
