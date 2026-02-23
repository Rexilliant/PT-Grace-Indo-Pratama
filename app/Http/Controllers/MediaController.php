<?php

namespace App\Http\Controllers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function delete($mediaId)
    {
        $media = Media::findOrFail($mediaId);
        $media->delete();

        return redirect()->back()->with('success', 'File berhasil dihapus');
    }
}
