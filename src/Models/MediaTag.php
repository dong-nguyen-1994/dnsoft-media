<?php

namespace Dnsoft\Media\Models;

use Illuminate\Database\Eloquent\Model;

class MediaTag extends Model
{
    protected $table = 'media_tags';
    protected $fillable = ['media_id', 'label', 'title', 'content', 'entity'];


    public function tagImage()
    {
        return $this->hasMany(Media::class, 'media_id');
    }

    public function MediaTags()
    {
        return $this->morphTo('tags');
    }

}
