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
use yii\helpers\Json;

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

        $upload = Yii::$app->params['download'];
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
        if (!$model->file || !$model->file->extension) {
            return $code;
        }

        if (!$model->validate()) {
            if (!self::checkUploadSize($model)) {
                $code = 1004;
                return $code;
            }

            if (!self::checkType($model)) {
                $code = 1021;
                return $code;
            }

            return $code;
        }

        $ymdDirs = self::makeYmdDirs();
        if (!$ymdDirs) {
            $code = 500;
            return $code;
        }

        $directory = Yii::getAlias('@webroot') . '/' . UploadForm::DIR_PUBLIC . '/' . $ymdDirs;

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

        $imagePath = '/' . UploadForm::DIR_PUBLIC . '/' . $ymdDirs . '/' . $file;
        if (!empty(Yii::$app->params['online']['commonApi']['user'])) {
            if (in_array($model->file->extension, Yii::$app->params['uploadPublic']['compress']['extensions'])
                && $model->file->size > Yii::$app->params['uploadPublic']['compress']['minSize']
            ) {
                $compressedImageUri = self::compressImage([Yii::$app->request->hostInfo . '/' . $imagePath]);
                $compressedImageUri && $imagePath = $compressedImageUri;
            }
        }

        return $imagePath;
    }

    /**
     * Check upload size
     *
     * @param UploadForm $model upload model
     * @return bool
     */
    public static function checkUploadSize(UploadForm $model)
    {
        return $model->file->size <= Yii::$app->params['uploadPublic']['maxSize'];
    }

    /**
     * Check file type
     *
     * @param UploadForm $model upload model
     * @return bool
     */
    public static function checkType(UploadForm $model)
    {
        return in_array($model->file->extension, Yii::$app->params['uploadPublic']['extensions']);
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
        $imagePath = Yii::getAlias('@webroot') . '/' . UploadForm::DIR_PUBLIC;
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
     * Compress images
     *
     * @param array $uris image uri list
     * @return mixed
     */
    public static function compressImage(array $uris)
    {
        $data = [
            'user' => Yii::$app->params['online']['commonApi']['user'],
            'pwd' => Yii::$app->params['online']['commonApi']['pwd'],
            'quality' => Yii::$app->params['uploadPublic']['compress']['quality'],
            'src' => Json::encode($uris),
        ];
        $res = StringService::httpPost(Yii::$app->params['online']['commonApi']['serviceUri'], $data);
        if ($res) {
            $res = json_decode($res, true);
            if (200 == $res['code'] && !empty($res['data'][0])) {
                return $res['data'][0];
            }
        }
    }

    /**
     * upload files
     * @return array|int
     */
    public static function uploadMore()
    {
        $model = new UploadForm;
        $model->file = UploadedFile::getInstances($model, 'file');
        if (!$model->file) {
            $code = 1000;
            return $code;

        }
        foreach ($model->file as &$model->file) {
            $code = 1000;
            if (!$model->file || !$model->file->extension) {
                return $code;
            }
            if (!$model->validate()) {
                if (!self::checkUploadSize($model)) {
                    $code = 1004;
                    return $code;
                }
                if (!self::checkType($model)) {
                    $code = 1021;
                    return $code;
                }
                return $code;
            }
            $ymdDirs = self::makeYmdDirs();
            if (!$ymdDirs) {
                $code = 500;
                return $code;
            }
            $directory = Yii::getAlias('@webroot') . '/' . UploadForm::DIR_PUBLIC . '/' . $ymdDirs;
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
            $data[] = UploadForm::DIR_PUBLIC . '/' . $ymdDirs . '/' . $file;
        }
        return $data;
    }

    /**
     * Delete file
     *
     * @param string $filepath upload file path
     */
    public static function deleteFile($filepath)
    {
        if (!StringService::checkUri($filepath) && self::existUploadFile($filepath)) {
            $file = Yii::getAlias('@webroot') . '/' . ltrim($filepath, '/');
            unlink($file);
            if (YII_DEBUG) {
                StringService::writeLog('test', $file);
            }
        }
    }

    /**
     * Check upload file existence
     *
     * @param string $filepath upload file path
     * @return bool
     */
    public static function existUploadFile($filepath)
    {
        return file_exists(Yii::getAlias('@webroot') . '/' . $filepath);
    }

    /**
     * Only authenticated user could download file
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
     * Only authenticated user could view file
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