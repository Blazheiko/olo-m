<?php

namespace App\Http\Controllers;

use App\Models\MyVideo;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use function Symfony\Component\String\s;

class HomeController extends Controller
{
    public function index(){
        return redirect(\route('dashboard'));
//        return Inertia::render('Welcome', [
//            'canLogin' => Route::has('login'),
//            'canRegister' => Route::has('register'),
////            'laravelVersion' => Application::VERSION,
////            'phpVersion' => PHP_VERSION,
//        ]);
    }

    public function myVideo() {
        $user = Auth::user();
        $myVideos = $user->myVideos;
    return Inertia::render('MyVideos',[
        'myVideos'=>$myVideos
    ]);
}
    public function startRecordVideo(){
        Log::info('startRecordVideo');
//        Log::info($request->all());
        $user = Auth::user();
        Storage::disk('public')->makeDirectory('video/'.$user->id);
        $src = 'video/'.$user->id.'/'.time().'.webm';
        MyVideo::create([
            'user_id'=>$user->id,
            'src'=>$src,
        ]);

        return response(['src'=>$src]);
    }
    public function deleteVideo($id){
        $video = MyVideo::find($id);

        Storage::disk('public')->delete($video->src);
        $video->delete();

        return response(['status'=>'ok']);

    }

    public function saveVideo($name,Request $request){
        Log::info('saveVideo');
//        Log::info($request->all());
        $user = Auth::user();
        @$data = file_get_contents("php://input") or $data = '';
//        $flName = date("ymd-His").".webm";
//        $flName = "testVideo.webm";

        if ($data) {
            file_put_contents(storage_path('app/public/video/'.$user->id.'/'.$name), $data,FILE_APPEND | LOCK_EX);
        }
        return response(['status'=>'ok']);
    }
}
