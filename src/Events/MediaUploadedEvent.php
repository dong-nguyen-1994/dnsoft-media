<?php

namespace Dnsoft\Media\Events;

use Illuminate\Queue\SerializesModels;
use Dnsoft\Media\Models\Media;

class MediaUploadedEvent
{
    use SerializesModels;

    /**
     * @var Media
     */
    public $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }
}
