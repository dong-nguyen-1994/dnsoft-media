<?php

namespace Dnsoft\Media\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFileManager extends Model
{
    protected $table = 'media__file_manager';

    protected $fillable = [
        'name',
        'file_name',
        'url',
        'author_id',
        'author_type',
        'table_id',
        'table_type',
        'group',
        'origin_path',
        'thumbnail_path',
    ];

    public function author()
    {
        return $this->morphTo();
    }

    public function table()
    {
        return $this->morphTo();
    }
}
