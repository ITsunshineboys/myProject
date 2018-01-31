<?php

namespace app\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "app_version".
 *
 * @property integer $id
 * @property string $version_no
 * @property string $url
 * @property integer $create_time
 * @property integer $level
 * @property string $version_description
 */
class AppVersion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version_no', 'url', 'create_time', 'level'], 'required'],
            [['create_time', 'level'], 'integer'],
            [['version_no'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 100],
            [['version_description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version_no' => 'Version No',
            'url' => 'Url',
            'create_time' => 'Create Time',
            'level' => 'Level',
            'version_description' => 'Version Description',
        ];
    }


    /**
     * @param $postData
     * @return int
     * @throws Exception
     */
    public  static  function  AddNewAppVersion($postData)
    {
        if (!isset($postData['level'],$postData['url'],$postData['version_no']))
        {
            return 1000;
        }
        if (isset($postData['version_no']))
        {
            $data=self::find()
                ->where(['version_no'=>$postData['version_no']])
                ->one();
            if ($data)
            {
                $code=1000;
                return $code;
            }
        }
        $tran=\Yii::$app->db->beginTransaction();
        try{
            $version=new self();
            $version->version_no=$postData['version_no'];
            $version->level=$postData['level'];
            $version->url=$postData['url'];
            $version->create_time=time();
            if (isset($postData['version_description']))
            {
                $version->version_description=$postData['version_description'];
            }
            if (!$version->save(false))
            {
                $tran->rollBack();
                return 500;
            }
            $tran->commit();
            return 200;
        }catch (\Exception $e){
            $tran->rollBack();
            return 500;
        }
    }
}
