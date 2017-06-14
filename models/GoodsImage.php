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
     * Check goods images number
     *
     * @param array $images
     * @return bool
     */
    public static function validateImages(array $images)
    {
        return count($images) <= Yii::$app->params['goods']['maxImagesCnt'];
    }

    /**
     * Add goods images by images etc.
     *
     * @param ActiveRecord $goods goods model
     * @param array $images images
     * @return int
     */
    public static function addByAttrs(ActiveRecord $goods, array $images)
    {
        $code = 1000;

        foreach ($images as $image) {
            $goodsImage = new self;
            $goodsImage->goods_id = $goods->id;
            $goodsImage->image = $image;

            if (!$goodsImage->validate()) {
                return $code;
            }

            if (!$goodsImage->save()) {
                $code = 500;
                return $code;
            }
        }

        $code = 200;
        return $code;
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
}