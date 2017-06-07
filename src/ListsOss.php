<?php

namespace Stevenyangecho\UEditor;


use OSS\OssClient;

class ListsOss
{
    public function __construct($allowFiles, $listSize, $path, $request)
    {
        $this->allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
        $this->listSize = $listSize;
        $this->path = ltrim($path,'/');
        $this->request = $request;
    }

    public function getList()
    {
        $size = $this->request->get('size', $this->listSize);
        $start = $this->request->get('start', '');
        $ossClient = new OssClient(config('UEditorUpload.core.oss.access_key_id'), config('UEditorUpload.core.oss.access_key_secret'), config('UEditorUpload.core.oss.endpoint'), config('UEditorUpload.core.oss.isCName'));
        $bucket = config('UEditorUpload.core.oss.bucket');
        $prefix = $this->path;
        $delimiter = '/';
        $nextMarker = '';
        $maxkeys = $this->listSize;

        $options = array(
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $maxkeys,
            'marker' => $nextMarker,
        );

        try {
            $listObjectInfo = $ossClient->listObjects($bucket, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        $listObject = $listObjectInfo->getObjectList();

        if (empty($listObject)) {
            return [
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => 0
            ];
        }

        $files = [];
        foreach ($listObject as $v) {
            if (preg_match("/\.(" . $this->allowFiles . ")$/i", $v->getKey())) {
                $files[] = array(
                    'url' => rtrim(config('UEditorUpload.core.oss.endpoint'), '/') . '/' . $v->getKey(),
//                        'mtime' => $v['mimeType'],
                );
            }
        }

        if (empty($files)) {
            return [
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => 0
            ];
        }
        /* 返回数据 */
        $result = [
            "state" => "SUCCESS",
            "list" => $files,
            "start" => $start,
            "total" => count($files)
        ];

        return $result;

    }
}