<?php

namespace DnSoft\Media\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class TempFileController extends Controller
{
  /**
   * Store temp file when select a new
   */
  public function store(Request $request)
  {
    if ($request->data) {
      foreach ($request->data as $file) {
        $media_id = $file['media_id'];
        $session_id = $file['session_id'];
        $isCollection = $file['isCollection'];
        $type = $file['type'];
        $this->storeFile($media_id, $session_id, $isCollection, $type);
      }
    } else {
      $media_id = $request->media_id;
      $session_id = $request->session_id;
      $isCollection = $request->isCollection;
      $type = $request->type;
      $this->storeFile($media_id, $session_id, $isCollection, $type);
    }
    return response()->json([
      'message' => 'Inserted successfully',
    ]);
  }

  /**
   * This function store file
   * 
   * @param string
   */
  private function storeFile($media_id, $session_id, $isCollection, $type)
  {
    $isExisted = DB::table('media__media_temps')
    ->where('media_id', $media_id)
    ->where('session_id', $session_id)
    ->where('is_collection', !$isCollection)->count();
    if ($isExisted > 0) {
      return response()->json([
        'message' => 'Inserted before',
      ]);
    }
    $isInTheSession = DB::table('media__media_temps')
    ->where('media_id', '<>',$media_id)
    ->where('session_id', $session_id)
    ->where('is_collection', !$isCollection);
    if ($isInTheSession->count() > 0) {
      $isInTheSession->delete();
    }
    DB::table('media__media_temps')->insert([
      'media_id' => $media_id,
      'session_id' => $session_id,
      'type' => $type,
      'is_collection' => $isCollection,
      'created_at' => now(),
      'updated_at' => now(),
    ]);
  }

  /**
   * Remove tmp by media id and session id
   */
  public function remove(Request $request)
  {
    $media_id = $request->media_id;
    $session_id = $request->session_id;
    DB::table('media__media_temps')
      ->where('media_id', $media_id)
      ->where('session_id', $session_id)->delete();
    return response()->json([
      'message' => 'Deleted successfully',
    ]); 
  }
}
