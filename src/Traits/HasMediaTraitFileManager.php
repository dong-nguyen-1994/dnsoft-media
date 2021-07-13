<?php

namespace Dnsoft\Media\Traits;

use Dnsoft\Media\Models\MediaFileManager;
use Illuminate\Support\Facades\Auth;

trait HasMediaTraitFileManager
{
    protected $mediaAttributes = [];

    public static function bootHasMediaTraitFileManager()
    {
        static::deleting(function (self $model) {
//            if ($model->forceDeleteMedia()) {
//                foreach ($model->media as $media) {
//                    $media->delete();
//                }
//            } else {
//                $model->media()->detach();
//            }
        });

        static::saved(function (self $model) {
            foreach ($model->mediaAttributes as $value) {
                $model->attachMediaFileManager($value);
            }
        });
    }

    /**
     * Attach media to the specified group.
     * @param mixed $media
     * @param string $group
     * @param array $conversions
     * @return void
     */
    public function attachMediaFileManager($value)
    {
        MediaFileManager::where(['table_type' => get_class($this), 'table_id' => $this->id])->delete();
        if ($value) {
            $values = explode(',', $value);
            foreach ($values as $v) {
                $fileName = explode('/', $v);
                $media = MediaFileManager::create([
                    'file_name' => $fileName[count($fileName) - 1],
                    'url' => $v,
                ]);
                $media->table()->associate($this);
                $media->author()->associate(Auth::guard('admin')->user());
                $media->save();
            }
        }
    }

    public function getFirstMedia($convension = 'thumbs')
    {
        $media = MediaFileManager::select('url', 'file_name')->where([
            'table_type' => get_class($this),
            'table_id' => $this->id,
        ])->first();
        if ($media) {
            if ($convension == 'thumbs') {
                $url = $this->analyticUrl($media->url);
                $url = $url.'/'.$convension.'/'.$media->file_name;
            } else {
                $url = $media->url;
            }
            return $url;
        }
        return null;
    }

    public function getAllMedias($convension = 'thumbs')
    {
        $medias = MediaFileManager::select('url', 'file_name')->where([
                'table_type' => get_class($this),
                'table_id' => $this->id,
            ])->get();
        $arrGallery = [];
        if ($medias) {
            foreach ($medias as $media) {
                if ($convension == 'thumbs') {
                    $url = $this->analyticUrl($media->url);
                    $url = $url.'/'.$convension.'/'.$media->file_name;
                } else {
                    $url = $media->url;
                }
                if ($url) {
                    array_push($arrGallery, $url);
                }
            }
        }
        return $arrGallery;
    }

    private function analyticUrl($url)
    {
        $urls = explode('/', $url);
        unset($urls[count($urls) - 1]);
        return implode('/', $urls);
    }
}
