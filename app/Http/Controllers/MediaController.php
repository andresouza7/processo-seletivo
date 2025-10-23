<?php

namespace App\Http\Controllers;

use App\Models\ProcessAttachment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
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

    public function showProcessAttachment(ProcessAttachment $attachment)
    {
        // Increment the view count
        $attachment->increment('views');

        $systemMigrationReferenceDate = Carbon::parse('2024-11-01');

        // Check if `data_publicacao` is older than the reference date
        if (Carbon::parse($attachment->created_at)->lt($systemMigrationReferenceDate)) {

            $oldFilePath = $attachment->process->type->slug . '/' .
                $attachment->process->directory . '/' .
                optional($attachment->arquivo)->codname . '.pdf';

            return redirect(Storage::url($oldFilePath));
        }

        // Otherwise, return the URL from Spatie Media Library
        return redirect($attachment->getFirstMediaUrl());
    }
}
