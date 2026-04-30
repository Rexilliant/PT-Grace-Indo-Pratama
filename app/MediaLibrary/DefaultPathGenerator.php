<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class DefaultPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->basePath($media);
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->basePath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->basePath($media).'responsive-images/';
    }

    private function basePath(Media $media): string
    {
        $modelId = $media->model_id; // id model
        $collection = $media->collection_name; // profile_images, gallery, etc
        $mediaId = $media->id; // id media (biar unik, aman dari nama file sama)

        return "{$collection}/{$mediaId}/";
    }
}
