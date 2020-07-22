<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class ImageController extends Controller
{
    public function downloadImages($imageUrl){
        $path = storage_path("images/$imageUrl");
        if(is_file($path)){
            return Image::make($path)->response();
        } else {
           abort(404);
       }
    }

    public function uploadImages(Request $request, $guid){

        $allowedFileTypeExtensions = Setting::where('name', 'allowedImageTypes')->get()->pluck('value')->toArray();

        function generateRandomString() {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            $length = rand(20,60);
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
        $rndm = generateRandomString();
        $foldername =  date("F", strtotime(now())).date("Y");
        $file = $request->file('uploadFile');
        if(!$file){
            return response() ->json(['Error' => 'Please select an image for upload!'], 400);
        }

        $filenameWithExtension = $rndm.".".$file->getClientOriginalExtension();
        if(in_array($file->getClientOriginalExtension(), $allowedFileTypeExtensions)){
            if($file->move(storage_path('images/questions/'.$foldername), $filenameWithExtension)){
                $fileUrl = 'images/questions/'.$foldername.'/'. $filenameWithExtension;
                return response() ->json(['image' => $fileUrl, 'guid' => $guid ], 200);
            }else {
                return response() ->json(['Error' => 'File upload error!'], 400);
            }
        }else {
            return response() ->json(['Error' => 'File extension not allowed!'], 415);
        }
    }

}
