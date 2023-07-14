<?php

namespace App\Repositories;

include_once public_path('vender/aliyun-php-sdk') . '/aliyun-php-sdk-core/Config.php';
use Illuminate\Support\Facades\Config;
use vod\Request\V20170321 as vod;

class AliyunVodRepository
{
    // 获取阿里云播放凭证
    public static function aliyunPlayAuth($videoid){
        $liveConfig = Config::get('vod.alipay_vod');
        $regionId = $liveConfig['region_id'];
        $accessKeyId = $liveConfig['access_key'];
        $accessKeySecret = $liveConfig['access_key_secret'];
        $profile = \DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
        $client = new \DefaultAcsClient($profile);

        $req = new vod\GetVideoPlayAuthRequest();
        $req->setAcceptFormat('JSON');
        $req->setRegionId($regionId);
        $req->setVideoId($videoid);
        $response = $client->getAcsResponse($req);
        return $response;
    }

    // 获取阿里云点播视频信息 含视频图片,转码状态等 这个就是可以查询上传视频的信息比如封面图片视频大小,转码信息等
    public static function aliyunVodInfo($videoid){
        $liveConfig = Config::get('vod.alipay_vod');
        $regionId = $liveConfig['region_id'];
        $accessKeyId = $liveConfig['access_key'];
        $accessKeySecret = $liveConfig['access_key_secret'];
        $profile = \DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
        $client = new \DefaultAcsClient($profile);

        $req = new vod\GetVideoInfoRequest();
        $req->setAcceptFormat('JSON');
        $req->setRegionId($regionId);
        $req->setVideoId($videoid);
        $response = $client->getAcsResponse($req);
        return $response;
    }

    // 获取阿里云视频播放信息
    public static function aliyunPlayInfo($videoid){
        $liveConfig = Config::get('vod.alipay_vod');
        $regionId = $liveConfig['region_id'];
        $accessKeyId = $liveConfig['access_key'];
        $accessKeySecret = $liveConfig['access_key_secret'];
        $profile = \DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
        $client = new \DefaultAcsClient($profile);

        $req = new vod\GetPlayInfoRequest();
        $req->setAcceptFormat('JSON');
        $req->setRegionId($regionId);
        $req->setVideoId($videoid);
        $req->setFormats('mp4');
        $response = $client->getAcsResponse($req);
        return $response;
    }
}