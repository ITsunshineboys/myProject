<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/2 0002
 * Time: 上午 11:25
 */

namespace app\models;
use yii\db\ActiveRecord;

class WorkerRank extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'worker_rank';
    }

    public static function ByInsert($pid,$rank,$min,$max)
    {
        return \Yii::$app->db->createCommand()->insert(self::tableName(),[
            'worker_type_id' => $pid,
            'rank_name' => $rank,
            'min_value' => $min,
            'max_value' => $max,
        ])->execute();
    }

}
