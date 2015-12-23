<?php

namespace EW\Putio;

/**
 * Represents a request to get file properties.
 *
 * @package EW\Putio
 */
class FileRequest extends Request {
    protected $fileId = null;

    /**
     * Get target file ID
     *
     * @return string
     */
    protected function getFileId() {
        return $this->fileId;
    }

    /**
     * Create file request instance
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
        return sprintf('files/%s', $this->getFileId());
    }
}
