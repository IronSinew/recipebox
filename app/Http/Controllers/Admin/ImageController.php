<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BannerTypeEnum;
use App\Http\Controllers\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageController extends Controller
{
    public function destroy(Media $media)
    {
        $media->delete();

        return redirect()->back()
            ->withBanner('Image deleted', BannerTypeEnum::danger);
    }
}
