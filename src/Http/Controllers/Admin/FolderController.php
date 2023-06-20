<?php

namespace DnSoft\Media\Http\Controllers\Admin;

use DnSoft\Media\Models\Folder;
use DnSoft\Media\Models\Media;
use DnSoft\Media\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class FolderController extends Controller
{
  /**
   * Get all parent folder
   */
  public function list(Request $request)
  {
    $folderId = $request->folder;
    $breadcrumbsReq = $request->breadcumbs;
    if (!$breadcrumbsReq) {
      $breadcrumbs = [
        [
          'id' => 0,
          'name' => 'All media'
        ]
      ];
    }
    if ($folderId == 0) {
      if (Cache::has('folder') && Cache::has('images')) {
        $folders = Cache::get('folder');
        $images = Cache::get('images');
      } else {
        $images = Media::where('folder_id', null);
        if ($request->search !== 'null') {
          $images = $images->where('name', 'like', '%' . $request->search . '%');
        }
        $images = $images->get();
        logger('image', [$images]);
        Cache::put('images', $images);
        $folders = Folder::where('parent_id', null)->get();
        Cache::put('folder', $folders);
      }
      $selectedFolder = null;
    } else {
      $images = Media::where('folder_id', $folderId);
      if ($request->search) {
        $images = $images->where('name', 'like', '%' . $request->search . '%');
      }
      $images = $images->get();
      /** @var Folder */
      $folder = Folder::find($folderId);
      $folders = $folder->subFolders()->get();
      if ($breadcrumbsReq) {
        $breadcrumbsReq[] = [
          'id' => $folder->id,
          'name' => $folder->name,
        ];
        $breadcrumbs = $breadcrumbsReq;
      } else {
        $breadcrumbs[] = [
          'id' => $folder->id,
          'name' => $folder->name,
        ];
      }
      $selectedFolder = $folder;
    }
    return response()->json([
      'breadcrumbs' => $breadcrumbs,
      'folders' => $folders,
      'files' => MediaResource::collection($images),
      'selectedFolder' => $selectedFolder,
    ]);
  }

  /**
   * Create folder
   */
  public function create(Request $request)
  {
    $selectedFolder = json_decode($request->selectedFolder);
    $folderObj = $this->castToFolder($selectedFolder);
    $count = Folder::where(['name' => $request->name, 'parent_id' => $folderObj->id])->count();
    if ($count > 0) {
      return response()->json([
        'message' => 'Folder has been existed',
      ], 400);
    }
    $data = $request->all();
    $folderName = $request->name;
    if ($folderObj) {
      $data['parent_id'] = $folderObj->id;
      $folderName = $folderObj->name . '/' . $folderName;
    }
    $item = Folder::create($data);
    $path = storage_path("app/public/$folderName");
    File::makeDirectory($path, $mode = 0777, true, true);
    Cache::forget('folder');
    return response()->json($item);
  }

  protected function castToFolder($object)
  {
    $folder = new Folder();
    if (!$object) {
      return $folder;
    }
    foreach ($object as $property => $value) {
      $folder->$property = $value;
    }
    return $folder;
  }

  /**
   * Delete folder
   */
  public function deleteFolder(Request $request)
  {
    $directory = $request->selectedItem;
    Folder::destroy($directory['id']);
    if (!$directory['parent_id']) {
      Storage::disk('public')->deleteDirectory($directory['name']);
    }
    Cache::forget('folder');
    return response()->json([
      'message' => 'Deleted folder successfully!',
      'error' => false
    ]);
  }
}
