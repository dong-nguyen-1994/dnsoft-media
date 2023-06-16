<?php

namespace DnSoft\Media\Resources;

use DnSoft\Media\Models\Folder;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
  public function toArray($request)
  {
    $folder = $this->folder()->first() ?? new Folder();
    $response = [
      'id'    => $this->id,
      'name'  => $this->name,
      'url' => $this->getUrl($folder),
      'folder_id' => $this->folder_id,
      'created_at' => $this->created_at,
    ];
    $notShowImage = ['video/mp4'];
    if (!in_array($this->mime_type, $notShowImage)) {
      $response['thumb'] = $this->getUrl($folder, 'thumb');
    } else {
      $response['thumb'] = config('media.video_default');
    }
    return $response;
  }
}
