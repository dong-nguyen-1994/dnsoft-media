<?php

namespace DnSoft\Media\Services;

use DnSoft\Media\Interface\FolderInterface;
use DnSoft\Media\Models\Folder;
use DnSoft\Media\Models\Media;
use Illuminate\Support\Facades\Cache;

class FolderService implements FolderInterface
{
  public function handleGetList($folderId = 0, $breadcrumbsReq): array
  {
    if (!$breadcrumbsReq) {
      $breadcrumbs = [
        [
          'id' => 0,
          'name' => 'All media'
        ]
      ];
    }
    $keyword = request('search');
    if ($folderId == 0) {
      $images = Media::where('folder_id', null);
      if ($keyword !== 'null') {
        $images = $images->where('name', 'like', '%' . $keyword . '%');
      }
      $images = $images->get();
      $folders = Folder::where('parent_id', null)->get();
      $selectedFolder = null;
    } else {
      $images = Media::where('folder_id', $folderId);
      if ($keyword !== 'null') {
        $images = $images->where('name', 'like', '%' . $keyword . '%');
      }
      $images = $images->get();
      /** @var Folder */
      $folder = Folder::find($folderId);
      if (!$folder) {
        $folders = Folder::where('parent_id', null)->get();
      } else {
        $folders = $folder->subFolders()->get();
      }
      if ($breadcrumbsReq) {
        $breadcrumbsReq[] = [
          'id' => $folder ? $folder->id : null,
          'name' => $folder ? $folder->name : null,
        ];
        $breadcrumbs = $breadcrumbsReq;
      } else {
        $breadcrumbs[] = [
          'id' => $folder ? $folder->id : null,
          'name' => $folder ? $folder->name : null,
        ];
      }
      $selectedFolder = $folder;
    }
    return [
      'breadcrumbs' => $breadcrumbs,
      'folders' => $folders,
      'images' => $images,
      'selectedFolder' => $selectedFolder,
    ];
  }
}
