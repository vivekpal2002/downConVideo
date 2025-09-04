<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use FFMpeg;

class ConvertController extends Controller
{
    public function mp4ToGif(Request $request)
    {
        // dd($request->all());
        // dd($request->all(), $request->file('upload_video'));

        $filePath = null;
        $fileName = uniqid().'.mp4';

        // CASE 1: User gives social media link
        if ($request->has('link_video_1') && $request->link_video_1 !== null) {
            $videoUrl = $request->link_video_1;

            // Example: Instagram API call
            $response = Http::withHeaders([
                'x-rapidapi-host' => 'pinterest-video-and-image-downloader.p.rapidapi.com',
                'x-rapidapi-key'  => env('RAPIDAPI_KEY'),
            ])->get('https://pinterest-video-and-image-downloader.p.rapidapi.com/pinterest', [
                'url' => $videoUrl,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // Example response might have "media" or "download_url"
                $downloadUrl = $data['data']['url'] ?? ($data['download_url'] ?? null);

                if ($downloadUrl) {
                    $filePath = storage_path('app/public/'.$fileName);
                    file_put_contents($filePath, file_get_contents($downloadUrl));
                } else {
                    return back()->with('error', 'Could not fetch video from social media link.');
                }
            } else {
                return back()->with('error', 'API request failed.');
            }
        }

        // CASE 2: User uploads video
        elseif ( $request->file('upload_video')) {
            $file = $request->file('upload_video');
            $filePath = storage_path('app/public/'.$fileName);
            $file->move(storage_path('app/public/'), $fileName);
        }

        if (!$filePath || !file_exists($filePath)) {
            return back()->with('error', 'No valid video provided.');
        }

        // Output GIF path
        $gifName = uniqid().'.gif';
        $gifPath = storage_path('app/public/'.$gifName);

        // Convert MP4 to GIF using FFMpeg
        $ffmpeg = \FFMpeg\FFMpeg::create();
        $video = $ffmpeg->open($filePath);

        $video->gif(
            \FFMpeg\Coordinate\TimeCode::fromSeconds(0), // start time
            new \FFMpeg\Coordinate\Dimension(320, 240), // resolution
            10 // duration in seconds
        )->save($gifPath);

        return '<div>
        <img src="'.asset('storage/'.$gifName).'" style="max-width:100%;border:2px solid #222;"/><br>
         <a href="'.asset('storage/'.$gifName).'" download="'.$gifName.'" class="btn btn-primary">Download GIF</a>
         </div>';
}
}