<?php

namespace DnSoft\Media\Events;

use Illuminate\Queue\SerializesModels;
use DnSoft\Media\Models\Media;

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
