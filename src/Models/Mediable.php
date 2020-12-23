<?php

namespace Dnsoft\Media\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Dnsoft\Media\Models\Mediable
 *
 * @property int $id
 * @property int $media_id
 * @property string|null $mediable_type
 * @property int|null $mediable_id
 * @property string|null $group
 * @property-read \Dnsoft\Media\Models\Media $media
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $mediable
 * @method static \Illuminate\Database\Eloquent\Builder|\Dnsoft\Media\Models\Mediable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Dnsoft\Media\Models\Mediable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Dnsoft\Media\Models\Mediable query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Dnsoft\Media\Models\Mediable whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Dnsoft\Media\Models\Mediable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Dnsoft\Media\Models\Mediable whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Dnsoft\Media\Models\Mediable whereMediableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Dnsoft\Media\Models\Mediable whereMediableType($value)
 * @mixin \Eloquent
 */
class Mediable extends Model
{
    protected $table = 'mediables';

    protected $fillable = [
        'media_id',
        'group',
    ];

    public function mediable()
    {
        return $this->morphTo();
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id', 'id');
    }
}
