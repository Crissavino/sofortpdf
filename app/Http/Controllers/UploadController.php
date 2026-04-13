<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1|max:20',
            'files.*' => 'file|max:' . ((int) env('MAX_UPLOAD_SIZE_MB', 50) * 1024),
            'tool' => 'required|string',
        ]);

        $fileIds = [];

        foreach ($request->file('files') as $file) {
            $uuid = Str::uuid();
            $ext = $file->getClientOriginalExtension();
            $filename = "{$uuid}.{$ext}";
            $file->storeAs('temp', $filename);

            $fileIds[] = [
                'id' => (string) $uuid,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'path' => storage_path("app/temp/{$filename}"),
            ];
        }

        // Store in session for the conversion step
        session()->put('upload_files', $fileIds);
        session()->put('upload_tool', $request->input('tool'));

        return response()->json([
            'file_ids' => collect($fileIds)->pluck('id')->toArray(),
            'message' => 'Dateien erfolgreich hochgeladen.',
        ]);
    }
}
