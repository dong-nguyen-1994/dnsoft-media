<?php

namespace DnSoft\Media\Services;

use DnSoft\Acl\Models\Admin;
use DnSoft\Media\Interface\FolderInterface;
use DnSoft\Media\Models\Folder;
use DnSoft\Media\Models\Media;

class FolderService implements FolderInterface
{
  /**
   * Handle get list of image, folder and breadcrumbs
   * @return array
   */
  public function handleGetList($folderId = 0, $breadcrumbsReq, $isFromBreadcumb): array
  {
    $keyword = request('search');
    $admin = auth('admin')->user();
    if ($folderId == 0) {
      if (!$admin->is_admin) {
        $images = Media::whereAuthorType(get_class(new Admin()))
          ->whereAuthorId(auth('admin')->id())->where('folder_id', null);
      } else {
        $images = Media::where('folder_id', null);
      }
      if ($keyword !== 'null') {
        $images = $images->where('name', 'like', '%' . $keyword . '%');
      }
      $images = $images->get();
      if (!$admin->is_admin) {
        $folders = Folder::whereAuthorType(get_class(new Admin()))
          ->whereAuthorId(auth('admin')->id())->where('parent_id', null)->get();
      } else {
        $folders = Folder::where('parent_id', null)->get();
      }
      $selectedFolder = null;
    } else {
      if (!$admin->is_admin) {
        $images = Media::whereAuthorType(get_class(new Admin()))
          ->whereAuthorId(auth('admin')->id())->where('folder_id', $folderId);
      } else {
        $images = Media::where('folder_id', $folderId);
      }
      if ($keyword !== 'null') {
        $images = $images->where('name', 'like', '%' . $keyword . '%');
      }
      $images = $images->get();
      /** @var Folder */
      $folder = Folder::find($folderId);
      if (!$folder) {
        if (!$admin->is_admin) {
          $folders = Folder::whereAuthorType(get_class(new Admin()))
            ->whereAuthorId(auth('admin')->id())->where('parent_id', null)->get();
        } else {
          $folders = Folder::where('parent_id', null)->get();
        }
      } else {
        $folders = $folder->subFolders()->get();
      }
      $selectedFolder = $folder;
    }
    return [
      'breadcrumbs' => $this->handleGetBreadcrumbs($breadcrumbsReq, $selectedFolder, $isFromBreadcumb),
      'folders' => $folders,
      'images' => $images,
      'selectedFolder' => $selectedFolder,
    ];
  }

  /**
   * Handle return breadcrumbs
   */
  private function handleGetBreadcrumbs($breadcrumbsReq, $selectedFolder, $isFromBreadcumb)
  {
    $breadcrumbs = [[
      'id' => 0,
      'name' => 'All media'
    ]];
    if (!$breadcrumbsReq) {
      return $breadcrumbs;
    }
    if ($isFromBreadcumb) {
      return $this->getBreadcrumbsFromBreadcrumb($breadcrumbsReq, $selectedFolder);
    }
    $breadcrumbsReq[] = [
      'id' => $selectedFolder ? $selectedFolder->id : null,
      'name' => $selectedFolder ? $selectedFolder->name : null,
    ];
    $breadcrumbs = $breadcrumbsReq;
    return $breadcrumbs;
  }

  /**
   * Handle get breadcrumb from breadcrumb
   */
  private function getBreadcrumbsFromBreadcrumb($breadcrumbsReq, $selectedFolder)
  {
    $breadcrumbs = [];
    $folderId = $selectedFolder ? $selectedFolder->id : 0;
    foreach ($breadcrumbsReq as $breadcrumbItem) {
      $breadcrumbs[] = $breadcrumbItem;
      if ($breadcrumbItem['id'] == $folderId) {
        break;
      }
    }
    return $breadcrumbs;
  }
}
