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
    const FIELDS_ROLES = ['id', 'name'];

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
     * @param bool $includeOwnerRole if includes owner role
     * @return array|mixed|ActiveRecord[]
     */
    public static function allRoles($onlyApp = false, $includeOwnerRole = false)
    {
        $key = self::CACHE_KEY_ALL;
        $cache = Yii::$app->cache;
        $roles = $cache->get($key);
        if (!$roles) {
            if ($onlyApp) {
                if (!$includeOwnerRole) {
                    $where = ['not in', 'id', [Yii::$app->params['ownerRoleId'], Yii::$app->params['lhzzRoleId']]];
                } else {
                    $where = ['not in', 'id', [Yii::$app->params['lhzzRoleId']]];
                }
            } else {
                $where = [];
            }
            $roles = self::find()->select(self::FIELDS_ROLES)->where($where)->asArray()->orderBy('id')->all();
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
     * @param bool $includeOwnerRole if includes owner role
     * @return array
     */
    public static function appRoles($includeOwnerRole = false)
    {
        return self::allRoles(true, $includeOwnerRole);
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


       public  static  function    GetRoleByRoleId($role_id,$user)
    {
        switch ($role_id){
            case 2:
                $model=Worker::find()->where(['uid'=>$user->id])->one();
                break;
            case 3:
                $model=Designer::find()->where(['uid'=>$user->id])->one();
                break;
            case 4:
                $model=Manager::find()->where(['uid'=>$user->id])->one();
                break;
            case 5:
                $model=DecorationCompany::find()->where(['uid'=>$user->id])->one();
                break;
            case 6:
                $model=Supplier::find()->where(['uid'=>$user->id])->one();
                break;
            case 7:
                $model=User::find()->where(['id'=>$user->id])->one();
                break;
        }
        return $model;
    }
}
