<?php

namespace EW\Putio;

class Downloader
{
    const DIR_MIME_TYPE = 'application/x-directory';

    protected $fileId = null;
    protected $finalFileIds = null;
    protected $mimeTypePattern = null;
    protected $downloadCallback = null;
    protected $accessToken = null;

    protected function getFileId() {
        return $this->fileId;
    }

    public function __construct($accessToken, $fileId, $mimeTypePattern, callable $downloadCallback) {
        $this->fileId = $fileId;
        $this->mimeTypePattern = $mimeTypePattern;
        $this->downloadCallback = $downloadCallback;
        $this->accessToken = $accessToken;
    }

    public function getFinalFileIds() {
        $mimeTypePattern = $this->mimeTypePattern;

        if(is_null($this->finalFileIds)) {
            $fileRequest = new FileRequest($this->accessToken, $this->getFileId());
            $properties = $fileRequest->getResponse();

            if ($properties->file->content_type != self::DIR_MIME_TYPE) {
                $this->finalfileIds = [$this->getFileId()];
            } else {
                $listRequest = new ListRequest($this->accessToken, $this->getFileId());
                $response = $listRequest->getResponse();

                $fileIds = [];

                foreach($response->files as $file) {
                    if(preg_match($mimeTypePattern, $file->content_type)) {
                        $fileIds[] = $file->id;
                    }
                }

                $this->finalFileIds = $fileIds;
            }
            
            Log::log('Found final file IDs:');
            Log::log($this->finalFileIds);
        }

        return $this->finalFileIds;
    }

    public function download() {
        $fileIds = $this->getFinalFileIds();

        foreach($fileIds as $fileId) {
            $downloadRequest = new DownloadRequest($this->accessToken, $fileId);
            $url = $downloadRequest->getDownloadUrl();

            Log::log("Attempting to download URL '$url'.");

            $this->downloadCallback->__invoke($url, $fileId, $this->fileId);
        }
    }
}