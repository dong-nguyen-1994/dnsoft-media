<?php

namespace DnSoft\Media\Resources;

use DnSoft\Media\Models\Folder;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
  public function toArray($request)
  {
    $folder = $this->folder()->first() ?? new Folder();
    return [
      'id'    => $this->id,
      'name'  => $this->name,
      'url'   => $this->getUrl($folder),
      'thumb' => $this->getUrl($folder, 'thumb'),
      'folder_id' => $this->folder_id,
      'created_at' => $this->created_at,
    ];
  }
}
