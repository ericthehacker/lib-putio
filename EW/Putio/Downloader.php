<?php

namespace EW\Putio;

/**
 * Convenience class to download file(s)
 *
 * @package EW\Putio
 */
class Downloader
{
    const DIR_MIME_TYPE = 'application/x-directory';

    protected $fileId = null;
    protected $finalFileIds = null;
    protected $mimeTypePattern = null;
    protected $downloadCallback = null;
    protected $accessToken = null;

    /**
     * Get target file ID
     *
     * @return string
     */
    protected function getFileId() {
        return $this->fileId;
    }

    /**
     * Instantiate downloader instance
     *
     * @param string $accessToken
     * @param string $fileId Target file ID or ID of parent dir
     * @param string $mimeTypePattern If $fileId is actually a dir, pattern for files within to download
     * @param callable $downloadCallback For each download file, this callback will be called with download URL
     */
    public function __construct($accessToken, $fileId, $mimeTypePattern, callable $downloadCallback) {
        $this->fileId = $fileId;
        $this->mimeTypePattern = $mimeTypePattern;
        $this->downloadCallback = $downloadCallback;
        $this->accessToken = $accessToken;
    }

    /**
     * Determines final file ID(s). If file ID is dir,
     * scan it for files which match mime type pattern and return those.
     * Otherwise, return file ID.
     *
     * @return array
     */
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

    /**
     * Actually download file(s)
     *
     * @throws \Exception
     */
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