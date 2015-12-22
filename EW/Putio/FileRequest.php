<?php

namespace EW\Putio;

class FileRequest extends Request {
    protected $fileId = null;

    protected function getFileId() {
        return $this->fileId;
    }

    public function __construct($accessToken, $fileId) {
        parent::__construct($accessToken);

        $this->fileId = $fileId;
    }

    protected function getRequestPath() {
        return sprintf('files/%s', $this->getFileId());
    }
}
