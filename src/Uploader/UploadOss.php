<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/24
 * Time: 14:18
 */

namespace Stevenyangecho\UEditor\Uploader;

use OSS\OssClient;
use Oss\Core\OssException;

trait UploadOss
{
    public function uploadOss()
    {
        $ossClient = new OssClient(config('UEditorUpload.core.oss.access_key_id'), config('UEditorUpload.core.oss.access_key_secret'), config('UEditorUpload.core.oss.endpoint'), config('UEditorUpload.core.oss.isCName'));

        try {
            $ossClient->uploadFile(config('UEditorUpload.core.oss.bucket'), $this->fullName, $this->file->getPathName());
        } catch (OssException $e) {

            $this->stateInfo = $e->getMessage();
            return false;
        }

        $url = rtrim(strtolower(config('UEditorUpload.core.oss.endpoint')), '/');
        $fullName = ltrim($this->fullName, '/');
        $this->fullName = $url . '/' . $fullName;
        $this->stateInfo = $this->stateMap[0];
        return true;
    }


    public function uploadOss__()
    {
        $ossClient = new OssClient(config('UEditorUpload.core.oss.access_key_id'), config('UEditorUpload.core.oss.access_key_secret'), config('UEditorUpload.core.oss.endpoint'), config('UEditorUpload.core.oss.isCName'));
        //获得文件类型
        $type = '.' . $this->file->getClientOriginalExtension();
        $this->fileType = $type;//设置UEditor的文件类型
        //生成随机文件名
        $object = substr(str_shuffle("qwertyuiopasdfghjklzxcvbnm0123456789"), 0, 5) . time();
        $object = $object . $type;//拼接到后缀名的文件名
        $this->fullName = $object;//设置UEditor的文件名

        try {
            $ossClient->uploadFile(config('UEditorUpload.core.oss.bucket'), 'test/' . $object, $this->file->getPathName());
        } catch (OssException $e) {
            //设置错误消息为未知错误
            $this->stateInfo = $this->stateMap[14];
            return false;
        }

        $url = rtrim(strtolower(config('UEditorUpload.core.oss.endpoint')), '/');
        $fullName = ltrim($this->fullName, '/');
        $this->fullName = $url . '/test/postImage/' . $fullName;
        $this->stateInfo = $this->stateMap[0];
        return true;
    }
}