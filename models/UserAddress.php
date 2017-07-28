<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class UserAddress extends ActiveRecord
{
    const CACHE_KEY_PREFIX_ROLES_STATUS = 'roles_status_';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_address';
    }
}