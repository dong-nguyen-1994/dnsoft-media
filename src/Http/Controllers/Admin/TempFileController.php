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
    $isExisted = DB::table('media__media_temps')
    ->where('media_id', $request->media_id)
    ->where('session_id', $request->session_id)
    ->where('is_collection', !$request->isCollection)->count();
    if ($isExisted > 0) {
      return response()->json([
        'message' => 'Inserted before',
      ]);
    }
    $isInTheSession = DB::table('media__media_temps')
    ->where('media_id', '<>',$request->media_id)
    ->where('session_id', $request->session_id)
    ->where('is_collection', !$request->isCollection);
    if ($isInTheSession->count() > 0) {
      $isInTheSession->delete();
    }
    DB::table('media__media_temps')->insert([
      'media_id' => $request->media_id,
      'session_id' => $request->session_id,
      'type' => $request->type,
      'is_collection' => $request->isCollection,
      'created_at' => now(),
      'updated_at' => now(),
    ]);
    return response()->json([
      'message' => 'Inserted successfully',
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
