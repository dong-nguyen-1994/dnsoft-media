<?php

namespace DnSoft\Media\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use DnSoft\Media\MediaUploader;
use DnSoft\Media\Models\Folder;
use DnSoft\Media\Resources\MediaResource;

class UploadController extends Controller
{
  /**
   * @var MediaUploader
   */
  private $mediaUploader;

  public function __construct(MediaUploader $mediaUploader)
  {
    $this->mediaUploader = $mediaUploader;
  }

  public function __invoke(Request $request)
  {
    $request->validate([
      'file' => 'required|file|max:204800', // 204800 represents 200 MB; you can adjust it as needed
    ]);
    $medias = new Collection();
    $file = $request->file('file');
    $selectedFolder = $request->selectedFolder;
    $folderObj = Folder::find($selectedFolder);
    $fileExisted = false;
    if (is_array($file)) {
      foreach ($file as $item) {
        $result = $this->handleUploadMedia($item, $folderObj);
        $media = $result;
        if (is_array($result)) {
          $media = $result[0];
          $fileExisted = true;
        }
        $medias->push($media);
      }
    } else {
      $result = $this->handleUploadMedia($file, $folderObj);
      $media = $result;
      if (is_array($result)) {
        $media = $result[0];
        $fileExisted = true;
      }
      $medias->push($media);
    }

    switch ($request->input('response')) {
      case 'tinymce':
        return $this->renderTinymce($medias);
        break;

      default:
        return $this->renderDefault($medias, $fileExisted);
    }
  }

  public function renderDefault(Collection $medias, $fileExisted = false)
  {
    return response()->json([
      'success' => true,
      'files'   => MediaResource::collection($medias),
      'file_existed' => $fileExisted,
    ]);
  }

  public function renderTinymce(Collection $medias)
  {
    return response()->json([
      'media-id' => $medias->first()->id,
      'name' => $medias->first()->name,
      'link' => $medias->first()->getUrl(),
      'thumb' => $medias->first()->getUrl(),
    ]);
  }

  protected function handleUploadMedia(UploadedFile $item, Folder $folder)
  {
    return $this->mediaUploader->setFile($item)->upload($folder);
  }
}
