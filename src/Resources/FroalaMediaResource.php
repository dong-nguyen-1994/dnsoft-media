<?php

namespace DnSoft\Media\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DnSoft\Media\Models\Media;

/**
 * Class FroalaMediaResource
 *
 * @package DnSoft\Media\Resources
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
