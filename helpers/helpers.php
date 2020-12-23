<?php

use Dnsoft\Media\Repositories\MediaRepositoryInterface;

if (!function_exists('get_media')) {
    /**
     * Get Media
     *
     * @param $mediaId
     *
     * @return \Dnsoft\Media\Models\Media|null
     */
    function get_media($mediaId)
    {
        if ($mediaId) {
            return app(MediaRepositoryInterface::class)->find($mediaId);
        }

        return null;
    }

}

if (!function_exists('imageProxy')){
    function imageProxy($url, $width, $height, $format = 'jpg', $quality = 80)
    {
        if (config('media.imageproxy.enable') === true){
            $urlCdn = config('media.imageproxy.server');
            return "{$urlCdn}/{$width}x{$height},q{$quality},{$format}/".$url;
        }else{
            return $url;
        }
    }
}
