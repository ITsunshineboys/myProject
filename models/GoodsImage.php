<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class GoodsImage extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_image';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['goods_id', 'image'], 'required']
        ];
    }

    /**
     * Check goods images number
     *
     * @param array $images
     * @return bool
     */
    public static function validateImages(array $images)
    {
        return count($images) <= Yii::$app->params['goods']['maxImagesCnt'];
    }
}