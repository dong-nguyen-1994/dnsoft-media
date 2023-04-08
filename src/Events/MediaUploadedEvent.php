<?php

namespace DnSoft\Media\Events;

use DnSoft\Media\Models\Folder;
use Illuminate\Queue\SerializesModels;
use DnSoft\Media\Models\Media;

class MediaUploadedEvent
{
  use SerializesModels;

  /** @var Media */
  public $media;

  /** @var Folder */
  public $selectedFolder;

  public function __construct(Media $media, Folder $selectedFolder)
  {
    $this->media = $media;
    $this->selectedFolder = $selectedFolder;
  }
}
