<?php

namespace DnSoft\Media\Http\Controllers\Admin;

use DnSoft\Media\Interface\FolderInterface;
use DnSoft\Media\Models\Folder;
use DnSoft\Media\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
  /** @var FolderInterface */
  private $folderInterface;

  public function __construct(FolderInterface $folderInterface)
  {
    $this->folderInterface = $folderInterface;
  }

  /**
   * Get all parent folder
   */
  public function list(Request $request)
  {
    $folderId = $request->folder;
    $breadcrumbsReq = $request->breadcumbs;
    $isFromBreadcumb = $request->isFromBreadcumb;
    $result = $this->folderInterface->handleGetList($folderId, $breadcrumbsReq, $isFromBreadcumb);
    return response()->json([
      'breadcrumbs' => $result['breadcrumbs'],
      'folders' => $result['folders'],
      'files' => MediaResource::collection($result['images']),
      'selectedFolder' => $result['selectedFolder'],
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
    $item->author()->associate(Auth::guard('admin')->user());
    $item->save();
    $path = storage_path("app/public/$folderName");
    File::makeDirectory($path, $mode = 0777, true, true);
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
    return response()->json([
      'message' => 'Deleted folder successfully!',
      'error' => false
    ]);
  }
}
