<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Storage;

class FileController extends Controller
{
    public function downloadFiles($fileUrl){
        $path = storage_path("files/$fileUrl");
        return response()->file($path);
    }

    public function uploadFiles(Request $request, $guid){

        $allowedFileTypeExtensions = Setting::where('name', 'allowedImageTypes')->orWhere('name', 'allowedFileTypes')->get()->pluck('value')->toArray();

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
            return response() ->json(['Error' => 'Please select a file for upload!'], 400);
        }


        $filenameWithExtension = $rndm.".".$file->getClientOriginalExtension();
        if(in_array(strtolower($file->getClientOriginalExtension()), $allowedFileTypeExtensions)){
            if($file->move(storage_path('files/questions/'.$foldername), $filenameWithExtension)){
                $fileUrl = 'files/questions/'.$foldername.'/'. $filenameWithExtension;
                return response() ->json(['file' => $fileUrl, 'guid' => $guid ], 200);
            }else {
                return response() ->json(['Error' => 'File upload error!'], 400);
            }
        }else {
            return response() ->json(['Error' => 'File extension not allowed!'], 415);
        }
    }
}
