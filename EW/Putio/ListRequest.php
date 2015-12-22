<?php

namespace EW\Putio;

class ListRequest extends Request {
    protected $fileId = null;

    protected function getFileId() {
        return $this->fileId;
    }

    public function __construct($accessToken, $fileId) {
        parent::__construct($accessToken);

        $this->fileId = $fileId;
    }

    protected function getRequestPath() {
        return 'files/list';
    }

    protected function getQueryParams() {
        $params = parent::getQueryParams();

        $params['parent_id'] = $this->getFileId();

        return $params;
    }
}