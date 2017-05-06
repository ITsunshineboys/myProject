<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Carousel extends ActiveRecord
{
    const CACHE_KEY = 'carousel';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'carousel';
    }

    /**
     * Set cache after updated user model
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $key = self::CACHE_KEY;
        $cache = Yii::$app->cache;
        $cache->delete($key);
    }

    /**
     * Get carousel list
     *
     * @return array carousel
     */
    public static function carousel()
    {
        $key = self::CACHE_KEY;
        $cache = Yii::$app->cache;
        $carousel = $cache->get($key);
        if (!$carousel) {
            $carousel = self::_carousel();
            if ($carousel) {
                $cache->set($key, $carousel);
            }
        }
        return $carousel;
    }

    /**
     * Get carousel list
     *
     * @access private
     * @return array carousel
     */
    public static function _carousel()
    {
        $data = [];

        $carouselList = self::find()->where([])->all();
        foreach ($carouselList as $carousel) {
            $goods = Goods::find()->where(['sku' => $carousel->sku])->one();
            if ($goods) {
                $data[] = [
                    'image' => $carousel->image,
                    'goods_id' => $goods->id,
                ];
            }
        }

        return $data;
    }
}