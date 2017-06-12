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
    public static function univalence($arr = [],$jobs= '',$rank = '银')
    {
        if(!$arr == null && !$jobs == null) {
            $province = $arr['province'] ?? 510000;
            $city = $arr['city'] ?? 510100;

            $labors = self::find()->asArray()->where(['and', ['province' => $province], ['city' => $city], ['worker_kind' => $jobs],['rank'=>$rank]])->one();
        }
        return $labors;
    }
}

