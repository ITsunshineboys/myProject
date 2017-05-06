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
use yii\helpers\Url;

class GoodsRecommend extends ActiveRecord
{
    const RECOMMEND_GOODS_TYPE_FIRST = 1;
    const RECOMMEND_GOODS_TYPE_SECOND = 2;
    const CACHE_KEY_FIRST = 'recommend_goods_first';
    const CACHE_KEY_SECOND = 'recommend_goods_second';
    const PAGE_SIZE_DEFAULT = 12;

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_recommend';
    }

    /**
     * Get recommended goods for type first
     *
     * @return array
     */
    public static function first()
    {
        $key = self::CACHE_KEY_FIRST;
        $cache = Yii::$app->cache;
        $recommendGoods = $cache->get($key);
        if (!$recommendGoods) {
            $recommendGoods = self::_first();
            if ($recommendGoods) {
                $cache->set($key, $recommendGoods);
            }
        }

        return $recommendGoods;
    }

    /**
     * Get recommended goods for type first
     *
     * @access private
     * @return array
     */
    public static function _first()
    {
        $recommendGoods = [];

        $goodsRecommend = GoodsRecommend::find()->where(['type' => self::RECOMMEND_GOODS_TYPE_FIRST])->one();
        if ($goodsRecommend) {
            $goods = Goods::find()->where(['sku' => $goodsRecommend->sku])->one();
            if ($goods) {
                $goodsId = $goods->id;
                $platformPrice = $goods->platform_price / 100;
                $title = $goodsRecommend->title;
                $image = $goodsRecommend->image;
                $description = $goodsRecommend->description;
                $recommendGoods[] = [
                    'title' => $title,
                    'image' => $image,
                    'description' => $description,
                    'goods_id' => $goodsId,
                    'platform_price' => $platformPrice,
                ];
            }
        }

        return $recommendGoods;
    }

    /**
     * Get recommended goods for type second
     *
     * @param int $page page default 1
     * @param int $size page size default 12
     * @return array
     */
    public static function second($page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $page <= 0 && $page = 1;
        $size <= 0 && $size = self::PAGE_SIZE_DEFAULT;
        $offset = ($page - 1) * $size;
        return array_slice(self::secondAll(), $offset, $size);
    }

    /**
     * Get all recommended goods for type second
     *
     * @return array
     */
    public static function secondAll()
    {
        $key = self::CACHE_KEY_SECOND;
        $cache = Yii::$app->cache;
        $recommendGoods = $cache->get($key);
        if (!$recommendGoods) {
            $recommendGoods = self::_secondAll();
            if ($recommendGoods) {
                $cache->set($key, $recommendGoods);
            }
        }

        return $recommendGoods;
    }

    /**
     * Get all recommended goods for type second
     *
     * @access private
     * @return array
     */
    private static function _secondAll()
    {
        $recommendGoods = [];

        $goodsRecommendList = GoodsRecommend::find()->where(['type' => self::RECOMMEND_GOODS_TYPE_SECOND])->all();
        foreach ($goodsRecommendList as $goodsRecommend) {
            $goods = Goods::find()->where(['sku' => $goodsRecommend->sku])->one();
            if ($goods) {
                $goodsId = $goods->id;
                $platformPrice = $goods->platform_price / 100;
                $title = $goodsRecommend->title;
                $image = $goodsRecommend->image;
                $description = $goodsRecommend->description;
                $recommendGoods[] = [
                    'title' => $title,
                    'image' => $image,
                    'description' => $description,
                    'goods_id' => $goodsId,
                    'platform_price' => $platformPrice,
                ];
            }
        }

        return $recommendGoods;
    }
}