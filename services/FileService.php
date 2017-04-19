<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/11/17
 * Time: 11:53 AM
 */

namespace app\services;

use Yii;

class FileService
{
    /**
     * File relative path
     *
     * @var string
     */
    private $_filePath;

    /**
     * File absolute path
     *
     * @var string
     */
    private $_fullPath;

    /**
     * Construct function
     *
     * @param string $filepath file path
     */
    public function __construct($filepath)
    {
        if (!$filepath) {
            return;
        }

        $upload = Yii::$app->params['upload'];
        $fullPath = Yii::$app->basePath . '/' . $upload['directory'] . '/' . $filepath;
        if (!file_exists($fullPath)) {
            return;
        }

        $this->_filePath = $filepath;
        $this->_fullPath = $fullPath;
    }

    /**
     * Only authenticated user could download file     *
     */
    public function download()
    {
        $download = Yii::$app->params['download'];
        header('X-Accel-Redirect: /' . $download['directory'] . '/' . $this->_filePath);
        header('X-Accel-Buffering: ' . $download['enableBuffering']);
        header('X-Accel-Limit-Rate : ' . $download['rate']);
        Yii::$app->response->xSendFile($this->_fullPath);
    }

    /**
     * Only authenticated user could view file     *
     */
    public function show()
    {
        $fileType = pathinfo($this->_fullPath, PATHINFO_EXTENSION);
        $contentType = '';
        switch ($fileType) {
            case 'jpg':
                $contentType = 'image/jpeg';
                break;
            case 'png':
                $contentType = 'image/png';
                break;
        }

        if (!$contentType) {
            return;
        }

        header("Content-Type: {$contentType}");
        echo file_get_contents($this->_fullPath);
    }
}