<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\Filesystem;

class UploadController extends Controller
{
    /**
     * ajax上传文件
     */
    public function uploadImages(Request $request)
    {

/*        if ($request->isMethod('post')) {
            //Storage::disk('ftp')->put('file.txt','Content');

            $file = $request->file('uploads');

            //文件是否上传成功：
            //扩展名：
            $ext = $file->getClientOriginalExtension();
            //临时绝对路径：
            $realPath = $file->getRealPath();
            $filename = date('YmdHis') . uniqid() . '.' . $ext;
            $bool = Storage::disk('sftp')->put($filename, file_get_contents($realPath));
            dd($bool);
            $img="<img src='".config('app.imgurl').$filename."'/>";
            return $img;
        } else {
            return view('demos');
        }*/

        $filesystem = new Filesystem(new SftpAdapter([
            'host' => '120.132.53.213',
            'port' => 22,
            'username' => 'userupload',
            'password' => 'L4ZknTKmGqy9VUtskdDf',
            'privateKey' => '',
            'root' => '/data/wwwroot/bee.feixiong.tv/Public/upload/user/',
            'timeout' => 10,
//            'directoryPerm' => 0755
        ]));
        $file = $request->file('uploads');
        $ext = $file->getClientOriginalExtension();
        $filename = date('Ym',strtotime('-1day')).'.'.$ext;


//        $start = date('Y-m-d',strtotime('- 1day'));
//        $over = date('Y-m-d');


        //写入文件
        $file = $realPath = $file->getRealPath();

/*        $result = $filesystem->put(
            $filename,
            $file
        );*/
        dd($result);exit();
        if($result){
            fclose($file);
        }


    }
}
