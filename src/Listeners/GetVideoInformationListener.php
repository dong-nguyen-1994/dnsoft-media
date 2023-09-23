<?php

namespace DnSoft\Media\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Process;
use DnSoft\Media\Events\GetVideoInformationEvent;
use DnSoft\Media\Services\FileService;
use Illuminate\Support\Facades\File;

class GetVideoInformationListener implements ShouldQueue
{
  use InteractsWithQueue;

  /**
   * Handle the event.
   *
   * @param  object  $event
   * @return void
   */
  public function handle(GetVideoInformationEvent $event)
  {
    $media = $event->media;
    $duration = app(FileService::class)->getVideoDuration($media);
    if ($duration) {
      $media->update([
        'duration' => $duration,
      ]);
    }
    $path = $media->getFullPath($media->folder);
    $this->getFrameFromVideo($path);
  }

  /**
   * Get frame from video in the first video
   */
  private function getFrameFromVideo($path)
  {
    $arrFolder = explode('/', $path);
    $fileName = $arrFolder[count($arrFolder) - 1];
    unset($arrFolder[count($arrFolder) - 1]);
    $newPath = implode('/', $arrFolder);
    $arrFileName = explode('.', $fileName);
    $fullPathName = $newPath .'/'. $arrFileName[0];
    $cmd = 'ffmpeg -i '. $path .' -vf "select=eq(n\,34)" -vframes 1 '. $fullPathName .'.png';
    $result = Process::forever()->run($cmd);
    if ($result->successful()) {
      $this->storeImage($newPath, $fullPathName, $arrFileName[0]);
    }
  }

  /**
   * Store image and create folder
   * @param string
   */
  private function storeImage($conversionPath, $fullPathName, $fileName)
  {
    File::makeDirectory($conversionPath.'/conversions/thumb', 0777, true, true);
    $thumSize = config('media.thumbsize');
    $strScale = join(':', $thumSize);
    $cmd = "ffmpeg -i $fullPathName.png -vf scale=$strScale $conversionPath/conversions/thumb/$fileName.png";
    $result = Process::forever()->run($cmd);
    if ($result->successful()) {
      // TODO create folder and also create record in DB
      logger("CREATE NEW RECORD IN DB");
    }
  }
}
