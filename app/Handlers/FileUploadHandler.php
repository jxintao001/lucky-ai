<?php

namespace App\Handlers;

use Dingo\Api\Exception\ResourceException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadHandler
{

    public function saveImage($file, $folder = 'images/')
    {
        // 限制上传图片大小
        if ($file->getSize() > config('filesystems.disks.cosv5.uploadImgMaxSize')) {
            throw new ResourceException('上传图片不能大于10M');
        }
        // 限制上传图片类型
        $extension = strtolower($file->getClientOriginalExtension());
        // 如果上传的不是图片将终止操作
        if (!$extension || !in_array($extension, ["png", "jpg", "gif", 'jpeg'])) {
            throw new ResourceException('图片类型不支持');
        }
        // 文件目录
        $path = $folder . date("Ym", time()) . '/' . date("d", time());
        // 文件名
        $disk = Storage::disk('cosv5');
        $result = $disk->putFile($path, $file);
        if (!$result) {
            throw new ResourceException('上传图片异常，请稍后再试');
        }
        return $result;
    }

    public function saveAudio($file, $folder = 'audios/')
    {
        // 限制上传音频大小
        if ($file->getSize() > config('filesystems.disks.cosv5.uploadAudioMaxSize')) {
            throw new ResourceException('上传音频不能大于100M');
        }
        // 限制上传音频类型
        $extension = strtolower($file->getClientOriginalExtension());
        // 如果上传的不是音频将终止操作
        if (!$extension || !in_array($extension, ['mp3', 'avi', 'silk'])) {
            throw new ResourceException('音频类型不支持');
        }
        // 文件目录
        $path = $folder . date("Ym", time()) . '/' . date("d", time());
        // 文件名
        $file_name = Str::random(40) . '.' . $extension;
        $disk = Storage::disk('cosv5');
        $result = $disk->putFileAs($path, $file, $file_name);
        if (!$result) {
            throw new ResourceException('上传音频异常，请稍后再试');
        }
        return $result;
    }

    public function saveVideo($file, $folder = 'videos/')
    {
        // 限制上传视频大小
        if ($file->getSize() > config('filesystems.disks.cosv5.uploadVideoMaxSize')) {
            throw new ResourceException('上传视频不能大于500M');
        }
        // 限制上传视频类型
        $extension = strtolower($file->getClientOriginalExtension());
        // 如果上传的不是视频将终止操作
        if (!$extension || !in_array($extension, ['mp4', 'wmv', 'rmvb', 'flv', 'mov'])) {
            throw new ResourceException('视频类型不支持');
        }
        // 文件目录
        $path = $folder . date("Ym", time()) . '/' . date("d", time());
        // 文件名
        $disk = Storage::disk('cosv5');
        $result = $disk->putFile($path, $file);
        if (!$result) {
            throw new ResourceException('上传视频异常，请稍后再试');
        }
        return $result;
    }

}