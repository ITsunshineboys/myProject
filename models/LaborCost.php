<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/13 0013
 * Time: 下午 15:34
 */
namespace app\models;

use yii\db\ActiveRecord;

class LaborCost extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'labor_cost';
    }

    /**
     * 根据地名查询单价
     * @param string $toponymy
     */
    public static function univalence($province ='四川',$toponymy = '成都',$worker_kind = '',$rank = '白银')
    {
        if(!$province == null && !$toponymy == null) {
            $labors = self::find()->where(['and', ['province' => $province], ['city' => $toponymy], ['worker_kind' => $worker_kind],['rank'=>$rank]])->one();
        }
        return $labors;
    }
}

