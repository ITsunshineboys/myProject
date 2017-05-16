<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/12/17
 * Time: 3:53 PM
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
    const DIR_PUBLIC = 'uploads';

    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => Yii::$app->params['uploadPublic']['extensions'], 'maxSize' => Yii::$app->params['uploadPublic']['maxSize']],
        ];
    }
}