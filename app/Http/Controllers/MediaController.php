<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;

class MediaController extends Controller
{
    public function serveMedia($uuid)
    {
        if (!Auth::check()) abort(403);

        $mediaItem = Media::where('uuid', $uuid)->firstOrFail();

        return Response::file($mediaItem->getPath(), [
            'Content-Type' => $mediaItem->mime_type,
        ]);
    }

    public function getTemporaryUrl($uuid, $minutes = 1)
    {
        if (!Auth::check()) abort(403);

        return redirect(URL::temporarySignedRoute(
            'media.serve',
            now()->addMinutes($minutes),
            ['uuid' => $uuid]
        ));
    }
}
