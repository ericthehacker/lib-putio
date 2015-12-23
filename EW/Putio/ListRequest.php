<?php

namespace EW\Putio;

/**
 * Represents a put.io list request
 *
 * @package EW\Putio
 */
class ListRequest extends Request {
    protected $fileId = null;

    /**
     * Target file ID
     *
     * @return string
     */
    protected function getFileId() {
        return $this->fileId;
    }

    /**
     * Create list request instance
     *
     * @param string $accessToken
     * @param string $fileId
     */
    public function __construct($accessToken, $fileId) {
        parent::__construct($accessToken);

        $this->fileId = $fileId;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequestPath() {
        return 'files/list';
    }

    /**
     * {@inheritdoc}
     */
    protected function getQueryParams() {
        $params = parent::getQueryParams();

        $params['parent_id'] = $this->getFileId();

        return $params;
    }
}