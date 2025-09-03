<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConvertController;
use App\Http\Controllers\DownloadController;

Route::get('/', function () {
    return view('main.index');
});

Route::post('/download', [DownloadController::class, 'link_download'])->name('download'); 
Route::post('/convert-to-gif', [ConvertController::class, 'mp4ToGif'])->name('convert.gif');
