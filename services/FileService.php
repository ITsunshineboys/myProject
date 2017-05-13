<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/11/17
 * Time: 11:53 AM
 */

namespace app\services;

use app\models\UploadForm;
use Yii;
use yii\web\UploadedFile;
use yii\helpers\Url;

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
     * Upload file
     *
     * @return mixed int|array
     */
    public static function upload()
    {
        $model = new UploadForm;
        $model->file = UploadedFile::getInstance($model, 'file');

        $code = 1000;

        if (!$model->file || !$model->validate()) {
            return $code;
        }

        if ($model->file->size > Yii::$app->params['uploadPublic']['maxSize']) {
            $code = 1004;
            return $code;
        }

        $ymdDirs = self::makeYmdDirs();
        if (!$ymdDirs) {
            $code = 500;
            return $code;
        }

        $directory = Yii::$app->basePath . '/' . Yii::$app->params['uploadPublic']['directory'] . '/' . $ymdDirs;

        $filename = self::generateFilename($directory);
        if ($filename === false) {
            $code = 500;
            return $code;
        }

        $file = $filename . '.' . $model->file->extension;
        if (!$model->file->saveAs($directory . '/' . $file)) {
            $code = 500;
            return $code;
        }
print_r($model->file);
        return [
            'fileRelativePath' => $ymdDirs . '/' . $file,
            'fileUrl' => Url::to(self::uploadUrlDir() . '/' . $ymdDirs . '/' . $file, true),
        ];
    }

    /**
     * Make "year/month/day" directories
     *
     * @return bool|string
     */
    public static function makeYmdDirs()
    {
        $ymd = date('Y-m-d');
        list($year, $month, $day) = explode('-', $ymd);
        $imagePath = Yii::$app->basePath . '/' . Yii::$app->params['uploadPublic']['directory'];
        $ymdDirs = $year . '/' . $month . '/' . $day;
        $directory = $imagePath . '/' . $ymdDirs;

        if (!file_exists($directory)) {
            if (!mkdir($directory, 0777, true)) {
                return false;
            }
        }

        return $ymdDirs;
    }

    /**
     * Generate random filename
     *
     * @param string $directory directory
     * @return mixed int|bool
     */
    public static function generateFilename($directory)
    {
        if (!file_exists($directory)) {
            return false;
        }

        while (true) {
            $filename = time() + rand(1000, 9999);
            if (!file_exists($directory . '/' . $filename)) {
                return $filename;
            }
        }
    }

    /**
     * Get upload url directory
     *
     * @return string
     */
    public static function uploadUrlDir()
    {
        list($_, $urlDir) = explode('/', Yii::$app->params['uploadPublic']['directory']);
        return $urlDir;
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