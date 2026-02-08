<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function index()
    {
        $disk = Storage::disk('local');

        $files = collect($disk->files('exports'))
            ->filter(fn ($path) => str_ends_with($path, '.csv') || str_ends_with($path, '.xlsx'))
            ->map(function ($path) use ($disk) {
                return [
                    'name' => basename($path),
                    'path' => $path,
                    'size' => $disk->size($path),
                    'modified' => $disk->lastModified($path),
                ];
            })
            ->sortByDesc('modified')
            ->values();

        return view('exports.index', [
            'files' => $files,
        ]);
    }

    public function download(string $filename)
    {
        $safe = basename($filename);
        $path = 'exports/'.$safe;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path);
    }
}
