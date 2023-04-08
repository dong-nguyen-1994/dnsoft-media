<?php

namespace DnSoft\Media\Http\Controllers\Admin;

use DnSoft\Media\Models\Folder;
use DnSoft\Media\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
  /**
   * Rename of the image
   */
  public function rename(Request $request)
  {
    $imageName = $request->imageName;
    $selectedImage = $request->selectedImage;
    $imageId = $selectedImage['id'];
    $folderId = $selectedImage['folder_id'];
    $isExisted = Media::where('name', $imageName)->where('folder_id', $folderId);
    if ($isExisted->count() > 0) {
      return response()->json([
        'message' => 'The image name has been exist, please use another',
        'error' => true
      ], 400);
    }
    $media = Media::find($imageId);
    $extension = $media->getExtensionAttribute();
    $olderName = $media->getPath($media->folder ?? new Folder());
    $olderNameThumb = $media->getPath($media->folder ?? new Folder(), 'thumb');
    $media->update([
      'name' => $imageName,
      'file_name' => "$imageName.$extension"
    ]);
    $newName = $media->getPath($media->folder ?? new Folder());
    $newNameThumb = $media->getPath($media->folder ?? new Folder(), 'thumb');
    // Rename in folder
    Storage::disk($media->disk)->move($olderName, $newName);
    Storage::disk($media->disk)->move($olderNameThumb, $newNameThumb);

    return response()->json([
      'message' => 'Update image name successfully',
      'error' => false,
    ], 200);
  }

  /**
   * Delete image in disk and database
   */
  public function delete(Request $request)
  {
    $selectedImage = $request->selectedItem;
    $imageId = $selectedImage['id'];
    /** @var Media */
    $media = Media::find($imageId);
    // Remove in disk
    $disk = $media->disk;
    $directory = $media->getDirectory($media->folder ? $media->folder->name : '');
    Storage::disk($disk)->deleteDirectory($directory);
    $media->destroy($imageId);

    return response()->json([
      'message' => 'Deleted image successfully',
      'error' => false,
    ], 200);
  }

  /**
   * Set alt of image
   */
  public function setAlt(Request $request)
  {
    $imageAlt = $request->imageAlt;
    $selectedImage = $request->selectedImage;
    $imageId = $selectedImage['id'];
    $media = Media::find($imageId);
    if ($media->count() == 0) {
      return response()->json([
        'message' => 'The file didn"t exist, please try again',
        'error' => true
      ], 400);
    }
    $media->update([
      'alt' => $imageAlt,
    ]);

    return response()->json([
      'message' => 'Update alt name successfully',
      'error' => false,
    ], 200);
  }
}
