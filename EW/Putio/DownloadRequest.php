<?php

namespace EW\Putio;

class DownloadRequest extends Request
{
    protected $fileId = null;

    protected function getFileId() {
        return $this->fileId;
    }

    public function __construct($accessToken, $fileId) {
        parent::__construct($accessToken);

        $this->fileId = $fileId;
    }

    protected function getRequestPath() {
        return sprintf(
            'files/%s/download',
            $this->getFileId()
        );
    }

    public function getDownloadUrl() {
        $rawResponse = $this->getRawResponse();

        if(!isset($rawResponse['headers']['Location'])) {
            throw new \Exception('Unable to find "Location" header in raw response headers');
        }

        return $rawResponse['headers']['Location'];
    }
}