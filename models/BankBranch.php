<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5 0005
 * Time: 上午 11:52
 */
namespace app\models;
use Yii\db\ActiveRecord;
use Yii;

class BankBranch extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        if (empty(Yii::$app->params['online']['basicDbName'])) {
            return 'bank_branch';
        }
        return Yii::$app->params['online']['basicDbName'] . '.bank_branch';
    }


}