<?php

namespace DnSoft\Media\Http\Controllers\Api;

use DnSoft\Media\Models\Media;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoController extends Controller
{
  /**
   * Get secret key of video
   * @param string $key
   */
  public function getVideoSecret($key)
  {
    return Storage::disk('secrets')->download($key);
  }

  /**
   * Play video
   */
  public function playVideo($playlist)
  {
    /** @var Media */
    // $media = Media::find($id);
    // $path = $media->getM3u8Path();
    return FFMpeg::dynamicHLSPlaylist()
      ->fromDisk($media->disk)
      ->open($path)
      ->setKeyUrlResolver(function ($key) {
        return route('web.media.video.key', ['key' => $key]);
      })
      ->setMediaUrlResolver(function ($mediaFilename) use ($media) {
        $x = Storage::disk($media->disk)->url($mediaFilename);
        logger($x);
        return $x;
      })
      ->setPlaylistUrlResolver(function ($playlistFilename) {
        // dd($playlistFilename);
        return route('web.media.video.playlist', ['playlist' => $playlistFilename]);
      });
  }

  /**
   * Get processing percentage of the video
   * @param int $fileId
   */
  public function getHandleProgress($fileId)
  {
    $media = Media::find($fileId);
    if ($media->processed) {
      return response()->json([
        'status' => 200,
        'processed' => 100,
      ]);
    }
    return response()->json([
      'status' => 200,
      'processed' => (int) $media->processing_percentage,
    ]);
  }
}
