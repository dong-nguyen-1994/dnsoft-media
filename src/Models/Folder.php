<?php

namespace DnSoft\Media\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
  protected $table = 'media__folders';

  protected $fillable = [
    'name',
    'parent_id',
  ];

  public function subFolders()
  {
    return $this->hasMany(Folder::class, 'parent_id', 'id');
  }
}
