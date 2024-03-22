<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BannerTypeEnum;
use App\Http\Controllers\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageController extends Controller
{
    public function makeHero(Media $media)
    {
        $media->model->media()->update(["collection_name" => "default"]);
        $media->update(['collection_name' => 'hero']);

        return redirect()->back()->withBanner('Image set to hero');
    }

    public function destroy(Media $media)
    {
        $media->delete();

        return redirect()->back()
            ->withBanner('Image deleted', BannerTypeEnum::danger);
    }
}
