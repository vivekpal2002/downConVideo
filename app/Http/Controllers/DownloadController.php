<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DownloadController extends Controller
{
    public function link_download(Request $request)
{
    $videoUrl = $request->input('link_video');

    $response = Http::withHeaders([
        'x-rapidapi-host' => 'pinterest-video-and-image-downloader.p.rapidapi.com',
        'x-rapidapi-key'  => env('RAPIDAPI_KEY'),
    ])->get('https://pinterest-video-and-image-downloader.p.rapidapi.com/pinterest', [
        'url' => $videoUrl,
    ]);

    if ($response->successful()) {
        $data = $response->json();
        if ($data['type'] === 'video') {

            return '<video width="400" controls>
            <source src="'.$data['data']['url'].'" type="video/mp4">
            Your browser does not support the video tag.</video>';

        }

        if ($data['type'] === 'image') {

            return '<img src="'.$data['data']['url'].'" alt="Pinterest Image" width="400">';

        }

        return 'No media found in response.';
    }

    return 'API request failed.';
}

 
}
