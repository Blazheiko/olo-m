<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use function Symfony\Component\String\s;

class HomeController extends Controller
{
    public function index(){
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    public function saveVideo(Request $request){
        Log::info('saveVideo');
        Log::info($request->all());
        @$data = file_get_contents("php://input") or $data = '';
//        $flName = date("ymd-His").".webm";
        $flName = "testVideo.webm";

        if ($data) {
            file_put_contents(storage_path('app/public/video/'.$flName), $data,FILE_APPEND | LOCK_EX);
        }
        return response('ok');
    }
}
