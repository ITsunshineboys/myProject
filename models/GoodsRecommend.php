<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\models\Goods;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

class GoodsRecommend extends ActiveRecord
{
    const RECOMMEND_GOODS_TYPE_FIRST = 1;
    const RECOMMEND_GOODS_TYPE_SECOND = 2;
    const CACHE_KEY_PREFIX_FIRST = 'recommend_goods_first_';

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
        $key = self::CACHE_KEY_PREFIX_FIRST;
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
     * @return array
     */
    public static function _first()
    {
        $recommendGoods = [];

        $goodsRecommend = GoodsRecommend::find()->where(['type' => self::RECOMMEND_GOODS_TYPE_FIRST])->one();
        if ($goodsRecommend) {
            $goods = Goods::find()->where(['sku' => $goodsRecommend])->one();
            if ($goods) {
                $link = Url::to([Goods::GOODS_DETAIL_URL_PREFIX . $goods->id], true);
                $platformPrice = $goods->platform_price;
                $title = $goodsRecommend->title;
                $image = $goodsRecommend->image;
                $description = $goodsRecommend->description;
                $recommendGoods[] = compact('title', 'image', 'description', 'link', 'platformPrice');
            }
        }

        return $recommendGoods;
    }
}