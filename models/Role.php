<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Role extends ActiveRecord
{
    const CACHE_KEY_APP = 'app_roles';
    const CACHE_KEY_ALL = 'all_roles';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'role';
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

        $cache = Yii::$app->cache;
        foreach ([self::CACHE_KEY_APP, self::CACHE_KEY_ALL] as $key) {
            $cache->delete($key);
        }
    }
}
