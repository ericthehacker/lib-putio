# Simple PHP SDK for put.io API

This is a simple PHP library to access certain [put.io API](https://put.io/v2/docs/index.html) capabilities easily.

Currently, this library is well suited to allow a consuming endpoint configured as a callback for new downloads on put.io.

# Example Usage

This is an example usage for a PHP endpoint configured as the callback for a new download on put.io.

```
<?php

//********** SETTINGS

define('ACCESS_TOKEN', 'CHANGEME');
define('DOWNLOAD_DIR', '/imma/download/new/files/here');
define('LOG_FILE', '/var/log/putio.log'); //CHANGEME
define('FILE_PATTERN', '#video/.+#');
define('LOG_LEVEL', 0); //see \EW\Putio\Log

//********** BOOTSTRAP

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//CHANGEME: bootstrap PSR-0 autoloader or require_once all files in this library

\EW\Putio\Log::setCallback(function($message, $level) {
    $results = file_put_contents(LOG_FILE, $message, FILE_APPEND);

    if($results === false) {
        trigger_error('Unable to write to log file: "'. LOG_FILE .'"');
    }
});
\EW\Putio\Log::setLogLevel(LOG_LEVEL);

//********** DO THINGS

$fileInfo = $_POST;

$downloader = new \EW\Putio\Downloader(
    ACCESS_TOKEN,
    $fileInfo['file_id'],
    FILE_PATTERN,
    function($url, $fileId, $fileId) {
        chdir(DOWNLOAD_DIR);
        \EW\Putio\Log::log("Current working directory: " . getcwd());

        //CHANGEME: this shell command is just an example
        $command = sprintf(
            'wget --content-disposition -nv "%s" > /dev/null &',
            $url
        );

        \EW\Putio\Log::log("Running command: " . $command);

        $returnValue = null;
        $output = [];

        exec($command, $output, $returnValue);

        if($returnValue != 0) {
            $message = "Exec error: " . implode("\n", $output);
            \EW\Putio\Log::log($message, \EW\Putio\Log::LEVEL_INFO);
            throw new \Exception($message);
        }

        \EW\Putio\Log::log("Downloading file $fileId.", \EW\Putio\Log::LEVEL_INFO);
        if(!empty($output)) {
            \EW\Putio\Log::log("Command output:");
            \EW\Putio\Log::log($output);
        }
    }
);

$downloader->download();
```
