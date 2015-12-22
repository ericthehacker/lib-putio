<?php

namespace EW\Putio;

abstract class Request {
    const API_BASE = 'https://api.put.io/v2/';

    protected $token = null;

    public function __construct($accessToken) {
        $this->token = $accessToken;
    }

    protected function getToken() {
        return $this->token;
    }

    protected function getQueryParams() {
        return [];
    }

    protected function getRequestUrl() {
        $queryParams = $this->getQueryParams();
        $queryString = '';

        foreach($queryParams as $key => $value) {
            $queryString .= '&' . urlencode($key) . '=' . urlencode($value);
        }

        $url = sprintf(
            '%s%s?oauth_token=%s%s',
            self::API_BASE,
            $this->getRequestPath(),
            $this->getToken(),
            $queryString
        );

        Log::log("Request URL: " . $url);

        return $url;
    }

    protected function getRawResponse() {
        $headers = array(
            'Accept: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headersRaw = substr($response, 0, $headerSize);
        $output = substr($response, $headerSize);

        $headers = [];

        foreach(explode("\n", $headersRaw) as $headerRaw) {
            $parts = [];

            preg_match('/(.+): (.+)/',$headerRaw, $parts);

            if(count($parts) == 3) {
                $headers[trim($parts[1])] = trim($parts[2]);
            }
        }

        curl_close($ch);

        $response = [
            'output' => $output,
            'headers' => $headers
        ];

        Log::log("Response output: ". $output);
        Log::log("Response headers: ");
        Log::log($headers);

        return $response;
    }

    public function getResponse() {
        $rawResponse = $this->getRawResponse()['output'];

        return json_decode($rawResponse);
    }

    /**
     * @return string
     */
    abstract protected function getRequestPath();
}
