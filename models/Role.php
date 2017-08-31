<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Role extends ActiveRecord
{
    const CACHE_KEY_ALL = 'all_roles';
    const AUTHENTICATION_STATUS_NO_APPLICATION = 3;
    const AUTHENTICATION_STATUS_IN_PROCESS = 0;
    const AUTHENTICATION_STATUS_APPROVED = 2;
    const AUTHENTICATION_STATUS_REJECTED = 1;

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
        $cache->delete(self::CACHE_KEY_ALL);
    }

    /**
     * Get all roles
     *
     * @param bool $onlyApp if only app fields
     * @return array|mixed|ActiveRecord[]
     */
    public static function allRoles($onlyApp = false)
    {
        $key = self::CACHE_KEY_ALL;
        $cache = Yii::$app->cache;
        $roles = $cache->get($key);
        if (!$roles) {
            $where = $onlyApp
                ? ['not in', 'id', [Yii::$app->params['lhzzRoleId']]]
                : [];
            $roles = self::find()->where($where)->asArray()->orderBy('id')->all();
            if ($roles) {
                $cache->set($key, $roles);
            }
        }
        return $roles;
    }

    /**
     * Check if available role id
     *
     * @param int $roleId role id
     * @return bool
     */
    public static function activeRole($roleId)
    {
        $roleId = (int) $roleId;
        if ($roleId <= 0) {
            return false;
        }

        foreach (self::allRoles() as $role) {
            if ($role['id'] == $roleId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get roles for app
     *
     * @return array
     */
    public static function appRoles()
    {
        return self::allRoles(true);
//        return array_slice(self::allRoles(), 1);
    }

    /**
     * 
     * [CheckUserRole description]
     * @param [type] $role [description]
     */
    public  static  function CheckUserRole($role)
    {
        switch ($role){
            case 2:
                $model=Worker::find();
                break;
            case 3:
                $model=Designer::find();
                break;
            case 4:
                $model=Manager::find();
                break;
            case 5:
                $model=DecorationCompany::find();
                break;
            case 6:
                $model=Supplier::find();
                break;
            case 7:
                $model=User::find();
                break;
        }
        return $model;
    }
}
