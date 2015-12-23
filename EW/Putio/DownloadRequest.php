<?php

namespace EW\Putio;

/**
 * Represents a download put.io request
 *
 * @package EW\Putio
 */
class DownloadRequest extends Request
{
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
     * Instantiate download request instance
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
        return sprintf(
            'files/%s/download',
            $this->getFileId()
        );
    }

    /**
     * Get actual HTTPS download URL
     *
     * @return string
     * @throws \Exception
     */
    public function getDownloadUrl() {
        $rawResponse = $this->getRawResponse();

        if(!isset($rawResponse['headers']['Location'])) {
            throw new \Exception('Unable to find "Location" header in raw response headers');
        }

        return $rawResponse['headers']['Location'];
    }
}