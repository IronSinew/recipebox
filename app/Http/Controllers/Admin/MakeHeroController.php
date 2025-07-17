<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** @codeCoverageIgnore */
class MakeHeroController extends Controller
{
    public function __invoke(Media $media)
    {
        /** @phpstan-ignore-next-line  */
        $media->model->media()->update(['collection_name' => 'default']);
        $media->update(['collection_name' => 'hero']);

        return redirect()->back()->withBanner('Image set to hero');
    }
}
